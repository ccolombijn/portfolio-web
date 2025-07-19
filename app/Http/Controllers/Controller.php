<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use Pug\Pug;
use Pug\Filter\Markdown as MarkdownFilter; // Import the filter

abstract class Controller
{
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

    public function __construct(array $content)
    {
        $this->content = $content; // Get content data 
    }

    abstract public function show(array $page);


    public function page(array $page) 
    {
        // Corrected this line to pass the required argument to the abstract show method.
        $this->show($page);
    }

    /**
     * Processes content to HTML, supporting Pug-to-HTML and Markdown-to-HTML workflows.
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
                    // Create the Pug instance with base options.
                    $pug = new Pug([
                        'basedir' => resource_path(),
                        'pretty' => true,
                        'cache' => false, // Force re-compilation of templates
                        'filters' => [
                            'markdown' => new MarkdownFilter(),
                        ],
                    ]);
                    
                    // Explicitly add the markdown filter to the instance.
                    // $pug->addFilter('markdown', new MarkdownFilter());
                    // Pug renders the file. :markdown filter inside the .pug file converts markdown to HTML.
                    $output = $pug->render($path, ['page' => $page, 'content' => $this->content]);
                    
                    //return $this->insertComponents($htmlFromPug);
                } catch (\Exception $e) {
                    Log::error("Pug rendering failed for '{$path}': " . $e->getMessage());
                    return "[Pug Error]";
                }
            }
        }

        if(!$output){

        
            $possibleMdPaths = [
                storage_path("app/public/md/{$part}/{$pageName}.md"),
                storage_path("app/public/md/{$part}/default.md"),
                resource_path("md/{$part}/{$pageName}.md"),
                resource_path("md/{$part}/default.md"),
            ];

            $filePath = '';
            foreach ($possibleMdPaths as $path) {
                if (File::exists($path)) {
                    // In the original code, this was assigned File::get($path), which is a string.
                    // It should be just the path string. I've corrected this.
                    $filePath = $path;
                    break; 
                }
            }

            if (empty($filePath)) {
                return '';
            }
            $output = File::get($filePath);
        }       

        $contentWithComponents = $this->insertComponents($output);

        $environment = new Environment([
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $converter = new MarkdownConverter($environment);
        
        return $converter->convert($contentWithComponents);
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

                $defaultData = $this->content[$componentName] ?? [];
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
