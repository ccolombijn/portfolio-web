<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use Pug\Pug;

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
     * Processes content to HTML, supporting Pug-to-Markdown and Markdown-to-HTML workflows.
     */
    public function getPugMarkdownHTML(string $part, array $page): string
    {
        $pageName = $page['name'];
        $rawContent = '';
        $sourceFound = false;

        // Prioritize .pug files
        $possiblePugPaths = [
            storage_path("app/public/pug/{$part}/{$pageName}.pug"),
            storage_path("app/public/pug/{$part}/default.pug"),
            resource_path("pug/{$part}/{$pageName}.pug"),
            resource_path("pug/{$part}/default.pug"),
        ];

        foreach ($possiblePugPaths as $path) {
            if (File::exists($path)) {
                try {
                    $pug = new Pug(['basedir' => resource_path()]);
                    $rawContent = $pug->render($path, ['page' => $page, 'content' => $this->content]);
                    $sourceFound = true;
                    break;
                } catch (\Exception $e) {
                    Log::error("Pug rendering failed for '{$path}': " . $e->getMessage());
                    return "[Pug Error]";
                }
            }
        }

        // If no .pug file was found, fall back to .md file logic.
        if (!$sourceFound) {
            $possibleMdPaths = [
                storage_path("app/public/md/{$part}/{$pageName}.md"),
                storage_path("app/public/md/{$part}/default.md"),
                resource_path("md/{$part}/{$pageName}.md"),
                resource_path("md/{$part}/default.md"),
            ];

            foreach ($possibleMdPaths as $path) {
                if (File::exists($path)) {
                    $rawContent = File::get($path);
                    $sourceFound = true;
                    break; 
                }
            }
        }

        
        if (!$sourceFound) {
            return '';
        }

        // Insert {components}
        $contentWithComponents = $this->insertComponents($rawContent);
        
        // Convert Markdown to HTML
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $converter = new MarkdownConverter($environment);
        
        return $converter->convert($contentWithComponents);
    }

    /**
     * Inserts Blade {components}
     */
    private function insertComponents(string $markdownContent): string
    {
        $pattern = '/{([a-zA-Z0-9_-]+)\s*([^}]*)?}/';
        preg_match_all($pattern, $markdownContent, $matches, PREG_SET_ORDER);

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

            $markdownContent = str_replace($search, $replace, $markdownContent);
        }
        return $markdownContent;
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
