<?php

namespace App\Http\Controllers;

use App\Contracts\PageRepositoryInterface;
use App\Services\PageContentService;

class PageController extends Controller
{
    public function __construct(
        private PageContentService $pageContentService
    ) {}
    /**
     * Returns default page view
     */
    public function show(array $page): \Illuminate\Contracts\View\View
    {

        if (!$page) {
            abort(404);
        }

        $content = [];
        $parts = $page['parts'] ?? ['header', 'content', 'footer'];
        
        foreach ($parts as $part) { 
            $content[$part] = $this->pageContentService->getRenderedPartContent($part, $page);
        }

        $view = $page['view'] ?? 'pages.default';
        
        return view($view, [
            'page' => $page,
            'content' => $content,
            'parts' => $parts, 
        ]);
    }
}
