<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

class PageController extends Controller
{

    /**
     * @var array
     */
    protected $content;

    public function __construct(array $content)
    {
        $this->content = $content; // Get content data 
    }

    /**
     * Returns default page view
     */
    public function show(array $page)
    {
        $data = ['page' => $page];
        $parts = [
            'header',
            'content',
            'footer'
        ];
        foreach ($parts as $part) {
            $data[$part] = $this->getMarkdownHTML($part, $page);
        }
        return view('pages.default', $data);
    }

    /**
     * Processes markdown and replaces component placeholders with arguments
     */
    private function getMarkdownHTML(string $part, array $page): string
    {
        $pageName = $page['name'];

        // Possible file paths in order of priority
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
        // Get the content
        $markdownContent = File::get($filePath);
        // Insert components
        $markdownContent = $this->insertComponents($markdownContent);
        // Convert to HTML
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $converter = new MarkdownConverter($environment);
        $htmlContent = $converter->convert($markdownContent);

        return $htmlContent;
    }

    /**
     * Inserts components in given markdown content
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
            return $markdownContent;
        }
    }

    /**
     * Parses a string of HTML-like attributes into an associative array
     */
    private function parseAttributes(string $str): array
    {
        $attributes = [];
        $pattern = '/([a-zA-Z0-9_-]+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s}]+))/';
        preg_match_all($pattern, $str, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $key = $match[1];
            // The value is in one of the capturing groups for ", ', or no quotes.
            $value = $match[2] ?: ($match[3] ?: $match[4]);
            $attributes[$key] = $value;
        }

        return $attributes;
    }
}
