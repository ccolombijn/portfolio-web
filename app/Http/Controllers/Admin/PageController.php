<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PageController extends Controller
{
    protected $projects;
    protected $pages;

    public function __construct(array $projects, array $pages)
    {
        //parent::__construct($content);
        $this->projects = $projects;
        $this->pages = $pages;
    }
    public function show(array $page) {}
    private function getPages()
    {
        return json_decode(File::get(storage_path('app/public/json/pages.json')), true);
        //return $this->pages;
    }

    private function savePages(array $pages)
    {
        File::put(storage_path('app/public/json/pages.json'), json_encode($pages, JSON_PRETTY_PRINT));
    }

    public function index()
    {
        $pages = $this->getPages();
        return view('admin.pages.index', compact('pages'));
    }

    public function edit($pageName)
    {
        $pages = $this->getPages();
        $page = collect($pages)->firstWhere('name', $pageName);

        if (!$page) {
            abort(404);
        }

        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, $pageName)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'route' => 'required|string|max:255',
        ]);

        $pages = $this->getPages();

        $pageIndex = collect($pages)->search(fn($p) => $p['name'] === $pageName);

        if ($pageIndex === false) {
            abort(404);
        }

        $pages[$pageIndex]['title'] = $validated['title'];
        $pages[$pageIndex]['route'] = $validated['route'];

        $this->savePages($pages);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully!');
    }
}