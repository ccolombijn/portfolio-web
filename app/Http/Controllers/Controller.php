<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

abstract class Controller
{
    /**
     * @var array
     */
    protected $content;

    /**
     * The parts in each page
     * 
     * @var list<string>
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
        //$this->data = ['page' => $page];
        $this->show();
    }
    //

    /**
     * Processes markdown to HTML
     */
    public function getMarkdownHTML(string $part, array $page): string
    {
        $pageName = $page['name'];
        $possiblePaths = [
            storage_path("app/public/md/{$part}/{$pageName}.md"),
            storage_path("app/public/md/{$part}/default.md"),
            resource_path("md/{$part}/{$pageName}.md"),
            resource_path("md/{$part}/default.md"),
        ];

        $filePath = '';

        foreach ($possiblePaths as $path) {
            if (File::exists($path)) {
                $filePath = $path;
                break; 
            }
        }

        if (empty($filePath)) {
            return '';
        }

        $markdownContent = File::get($filePath);
        $markdownContent = $this->insertComponents($markdownContent);
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $converter = new MarkdownConverter($environment);
        $htmlContent = $converter->convert($markdownContent);

        return $htmlContent;
    }

    /**
     * Inserts components 
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
                if (view()->exists('components.' . $componentName)) {
                    $replacement = view('components.' . $componentName, $componentData)->render();
                } else {
                    Log::warning("Component view not found: 'components.{$componentName}'. Tag '{$fullTag}' was removed.");
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
     * 
     * @return array<string, any>
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
