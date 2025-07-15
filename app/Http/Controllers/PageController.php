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
        $data = [ 'page' => $page ];
        $parts = [
            'header',
            'content',
            'footer'
        ];
        foreach($parts as $part) {
            $data[$part] = $this->getMarkdownHTML($part,$page);
        }
        return view('pages.default',$data);
    }

    /**
     * Get markdown content and parse to html
     */
    private function getMarkdownHTML(string $part,array $page) : string
    {
        $filePath = storage_path('app/public/md/' . $part . '/' . $page['name'] . '.md');
        if (!File::exists($filePath)) {
            //abort(404, 'Content file not found.');
            $filePath = storage_path('app/public/md/' . $part . '/default.md');
        }
        $markdownContent = File::get($filePath);
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $converter = new MarkdownConverter($environment);
        $htmlContent = $converter->convert($markdownContent);
        $htmlContent = $this->insertComponents($htmlContent);
        return $htmlContent;
    }
    /**
     * Insert components with available content data
     */
    private function insertComponents(string $htmlContent): string 
    {
        // Find matches {[component]}
        $pattern = '/{\[\s*([a-zA-Z0-9_-]+)\s*([^\]]*)?\s*\]}/';

        preg_match_all($pattern, $htmlContent, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $fullTag = $match[0];
            $componentName = $match[1];
            $attributesString = $match[2] ?? '';
            $defaultData = $this->content[$componentName] ?? [];
            $overrides = $this->parseAttributes($attributesString);
            $componentData = array_merge($defaultData, $overrides);
            if (view()->exists('components.' . $componentName)) {
                $replacement = view('components.' . $componentName, $componentData)->render();
                $htmlContent = str_replace($fullTag, $replacement, $htmlContent);
            } else {
                Log::warning("Component view not found: 'components.{$componentName}'");
                $htmlContent = str_replace($fullTag, '', $htmlContent);
            }
        }
        return $htmlContent;
    }

    private function parseAttributes(string $str): array
    {
        $attributes = [];
        $pattern = '/([a-zA-Z0-9_-]+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s\]]+))/';
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