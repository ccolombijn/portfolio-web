<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
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
        preg_match_all('/{([\\s\\S]*?)}/', $htmlContent, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $index => $componentName) {
                $fullTag = $matches[0][$index];
                $component = 'components.' . $componentName;
                if(View::exists($component)){
                    if (isset($this->content[$componentName])) {
                        // Render the component view with content data
                        $replacement = view($component, $this->content[$componentName])->render(); 
                    } else {
                        $replacement = view('components.' . $componentName)->render();
                    }
                } else {
                    $replacement = '[error: ' . $component. ' does not exist]';
                }
                $htmlContent = str_replace($fullTag, $replacement, $htmlContent);
            }
        }
        return $htmlContent;
    }
}