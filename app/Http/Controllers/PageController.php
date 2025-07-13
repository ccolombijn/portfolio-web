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
            'content' => $this->getContent($page),
        ]);
    }
    /**
     * Get the page markdown content and parse to html
     */
    private function getContent(array $page) : string
    {
        $filePath = storage_path('app/public/md/' . $page['name'] . '.md');

        if (!File::exists($filePath)) {
            abort(404, 'Content file not found.');
        }
        $markdownContent = File::get($filePath);
        $environment = new Environment(); // Set environment
        $environment->addExtension(new CommonMarkCoreExtension()); // add CommonMark
        $converter = new MarkdownConverter($environment); // define converter
        $htmlContent = $converter->convert($markdownContent); // convert to hml
        return $htmlContent;
    }
}
