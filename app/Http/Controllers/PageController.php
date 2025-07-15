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
        $this->content = $content;
    }

    /**
     * Default show page
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
     * Processes markdown and replaces component placeholders with arguments.
     */
    private function getMarkdownHTML(string $part, array $page): string
    {
        $filePath = storage_path('app/public/md/' . $part . '/' . $page['name'] . '.md');
        if (!File::exists($filePath)) {
            $filePath = storage_path('app/public/md/' . $part . '/default.md');
        }
        // If even the default file doesn't exist, return an empty string.
        if (!File::exists($filePath)) {
            return '';
        }

        $markdownContent = File::get($filePath);

        // --- REVISED LOGIC ---
        // First, find and replace component tags in the RAW markdown content.
        // Corrected regex to use {component args} syntax, not {[...]}
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
                    // The replacement is the pre-rendered HTML from the component view.
                    $replacement = view('components.' . $componentName, $componentData)->render();
                } else {
                    Log::warning("Component view not found: 'components.{$componentName}'. Tag '{$fullTag}' was removed.");
                }
                
                $search[] = $fullTag;
                $replace[] = $replacement;
            }
            
            // Perform the replacement on the raw markdown string.
            $markdownContent = str_replace($search, $replace, $markdownContent);
        }

        // Second, convert the modified markdown (which now contains HTML snippets) to final HTML.
        // The converter will correctly leave the existing HTML from components untouched.
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $converter = new MarkdownConverter($environment);
        $htmlContent = $converter->convert($markdownContent);

        return $htmlContent;
    }

    /**
     * Parses a string of HTML-like attributes into an associative array.
     */
    private function parseAttributes(string $str): array
    {
        $attributes = [];
        // This pattern now correctly handles attributes within the { ... } syntax
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
