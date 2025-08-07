<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Vite;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use Pug\Pug;
use Pug\Filter\Markdown as MarkdownFilter; 

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @var array
     */
    protected $content;

    /**
     * The parts in each page
     * * @var list<string>
     */
    protected $parts = [
        'header',
        'content',
        'footer'
    ];
    protected $data;

    // public function __construct(array $content)
    // {
    //     $this->content = app('content.data'); // Get content data 
    // }

    abstract public function show(array $page);


    public function page(array $page) 
    {
        $this->show($page);
    }

    /**
     * Get singleton data
     */
    public function appData($key): array
    {
        return app($key . '.data');
    }

    public function getMarkdownPath(string $part, array $page): string 
    {
        $possibleMdPaths = [
            storage_path("app/public/md/{$part}/{$page['name']}.md"),
            storage_path("app/public/md/{$part}/default.md"),
            resource_path("md/{$part}/{$page['name']}.md"),
            resource_path("md/{$part}/default.md"),
        ];

        $filePath = '';
        foreach ($possibleMdPaths as $path) {
            if (File::exists($path)) {
                $filePath = $path;
                break; 
            }
        }
        return $filePath;

    }
    /**
     * 
     */
    public function getMarkdownContent(string $part, array $page): string
    {
        $filePath = $this->getMarkdownPath($part, $page);

        if (empty($filePath)) {
            return '';
        }
        $output = File::get($filePath);
        return $output;
    }

    /**
     * Processes content to HTML, supporting Pug-to-HTML and Markdown-to-HTML
     */
    public function getPugMarkdownHTML(string $part, array $page): string
    {
        $pageName = $page['name'];
        $output = null;
        $possiblePugPaths = [
            storage_path("app/public/pug/{$part}/{$pageName}.pug"),
            storage_path("app/public/pug/{$part}/default.pug"),
            resource_path("pug/{$part}/{$pageName}.pug"),
            resource_path("pug/{$part}/default.pug"),
        ];

        foreach ($possiblePugPaths as $path) {
            if (File::exists($path)) {
                try {
                    $pug = new Pug([
                        'basedir' => resource_path(),
                        'pretty' => true,
                        'cache' => false, 
                        'filters' => [
                            'markdown' => new MarkdownFilter(),
                        ],
                    ]);
                    
                    $output = $pug->render($path, ['page' => $page, 'content' => $this->content]);

                } catch (\Exception $e) {
                    Log::error("Pug rendering failed for '{$path}': " . $e->getMessage());
                    return "[Pug Error]";
                }
            }
        }

        if(!$output){

            // $possibleMdPaths = [
            //     storage_path("app/public/md/{$part}/{$pageName}.md"),
            //     storage_path("app/public/md/{$part}/default.md"),
            //     resource_path("md/{$part}/{$pageName}.md"),
            //     resource_path("md/{$part}/default.md"),
            // ];

            // $filePath = '';
            // foreach ($possibleMdPaths as $path) {
            //     if (File::exists($path)) {
            //         $filePath = $path;
            //         break; 
            //     }
            // }

            // if (empty($filePath)) {
            //     return '';
            // }
            // $output = File::get($filePath);
            $output = $this->getMarkdownContent($part, $page);
        }       
        $pattern = '/!\[(.*?)\]\(\/images\/(.*?)\)/';
        // $output = preg_replace_callback($pattern, function ($matches) {
        //     $altText = $matches[1];
        //     $filename = $matches[2];
        //     $viteUrl = Vite::asset('resources/images/' . $filename);
            
        //     return "![{$altText}]({$viteUrl})";
        // }, $output);
        $output = $this->insertComponents($output);

        $environment = new Environment([
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $converter = new MarkdownConverter($environment);
        $output = $converter->convert($output);
        $output = $this->processImagePathsInHtml($output);
        //dd($output);
        return $output;
    }
    /**
     * Replaces image paths in Html with Vite resource asset paths
     */
    function processImagePathsInHtml(string $htmlContent): string 
    {
        $pattern = '/<img src="\/images\/(.*?)"(.*?)>/';

        return preg_replace_callback($pattern, function ($matches) {
            $filename = $matches[1];
            $otherAttributes = $matches[2];
            $viteUrl = Vite::asset('resources/images/' . $filename);
            $physicalPath = resource_path('images/' . $filename);
            $dimensions = '';
            if (file_exists($physicalPath)) {
                $imageSize = getimagesize($physicalPath);
                if ($imageSize) {
                    $width = $imageSize[0];
                    $height = $imageSize[1];
                    $dimensions = " width=\"{$width}\" height=\"{$height}\"";
                }
            }

            return '<img src="' . $viteUrl . '"' . $dimensions . $otherAttributes . '>';
        }, $htmlContent);
    }

    /**
     * Inserts Blade components into a string.
     */
    private function insertComponents(string $htmlContent): string
    {
        $pattern = '/{([a-zA-Z0-9_-]+)\s*([^}]*)?}/';
        preg_match_all($pattern, $htmlContent, $matches, PREG_SET_ORDER);

        if (!empty($matches)) {
            $search = [];
            $replace = [];

            foreach ($matches as $match) {
                $fullTag = $match[0];
                $componentName = $match[1];
                $attributesString = $match[2] ?? '';

                $defaultData = app('content.data')[$componentName] ?? [];
                $overrides = $this->parseAttributes($attributesString);
                $componentData = array_merge($defaultData, $overrides);

                $replacement = '';
                $bladeComponentView = 'components.' . $componentName;

                if (view()->exists($bladeComponentView)) {
                    $replacement = view($bladeComponentView, $componentData)->render();
                } else {
                    Log::warning("Blade component view not found: '{$bladeComponentView}'. Tag '{$fullTag}' was removed.");
                }
                
                $search[] = $fullTag;
                $replace[] = $replacement;
            }

            $htmlContent = str_replace($search, $replace, $htmlContent);
        }
        return $htmlContent;
    }

    /**
     * Parses a string of HTML-like attributes into an associative array
     * * @return array<string, mixed>
     */
    private function parseAttributes(string $str): array
    {
        $attributes = [];
        $pattern = '/([a-zA-Z0-9_-]+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s}]+))/';
        preg_match_all($pattern, $str, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $key = $match[1];
            $value = $match[2] ?: ($match[3] ?: $match[4]);
            $attributes[$key] = $value;
        }

        return $attributes;
    }
}
