<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

class PageController extends Controller
{
    /**
     *
     */
    public function show(array $page): View
    {
       
        return view('pages.default', [
            'page' => $page,
            'header' => $this->getMarkdownHTML('header',$page),
            'content' => $this->getMarkdownHTML('content',$page),
            'footer' => $this->getMarkdownHTML('footer',$page)
        ]);
    }
    /**
     * Get the page markdown content and parse to html
     */
    private function getMarkdownHTML(string $part,array $page) : string
    {
        $filePath = storage_path('app/public/md/' . $part . '/' . $page['name'] . '.md');

        if (!File::exists($filePath)) {
            //abort(404, 'Content file not found.');
            $filePath = storage_path('app/public/md/' . $part . '/default.md');
        }
        $markdownContent = File::get($filePath);
        $environment = new Environment(); // Set environment
        $environment->addExtension(new CommonMarkCoreExtension()); // add CommonMark
        $converter = new MarkdownConverter($environment); // define converter
        $htmlContent = $converter->convert($markdownContent); // convert to hml
        return $htmlContent;
    }
}
