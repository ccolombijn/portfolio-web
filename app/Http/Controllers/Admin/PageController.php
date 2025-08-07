<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController as Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{

    protected $pages;

    public function __construct(array $pages)
    {
        $this->pages = $pages;
    }
    /**
     * 
     */
    private function getPages(): array
    {
        return $this->pages;
    }
    /**
     * 
     */
    private function savePages(array $pages): void
    {
        $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
        File::put(storage_path('app/public/json/pages.json'), json_encode($pages, $options));
        Cache::forget('pages.json.data');
    }
    /**
     * 
     */
    private function saveParts(Request $request) : void 
    {
        foreach($this->parts as $part) {
            if(strlen($this->getMarkdownContent($part,['name' => 'default'])) !== strlen(isset($request[$part]) ? $request[$part] : '')){
                File::put(storage_path('app/public/md/' . $part . '/'. $request['name'] . '.md'),$request[$part]);
            }
        }
    }
    /**
     * 
     */
    public function index(): \Illuminate\Contracts\View\View
    {
        $pages = $this->getPages();
        return view('admin.pages.index', compact('pages'));
    }
    /**
     * 
     */
    private function getPageIndex($pageName)
    {
        $pages = $this->getPages();

        $pageNameParts = explode('.',$pageName);
        if(isset($pageNameParts[1])){
            $page = collect($pages)->where('name', $pageNameParts[0])->where('method',$pageNameParts[1]);
        }else {
            $page = collect($pages)->where('name', $pageNameParts[0]);
        }

        return array_keys($page->toArray())[0];
    }
    /**
     * 
     */
    public function edit($pageName): \Illuminate\Contracts\View\View
    {
        $pages = $this->getPages();
        if(str_contains($pageName,'.')){
            $pageNameParts = explode('.',$pageName);
            $page = collect($pages)->where('name', $pageNameParts[0])->where('method',$pageNameParts[1])->first();
        } else {
            $page = collect($pages)->firstWhere('name', $pageName);
        }

        if (!$page) {
            abort(404);
        }
        $header = $this->getMarkdownContent('header',$page);
        $content = $this->getMarkdownContent('content',$page);
        $footer = $this->getMarkdownContent('footer',$page);
        $parts = isset($page['parts']) ? $page['parts'] : $this->parts;
        $sectionComponentFiles = scandir(resource_path('views/components/sections'));
        $sections = [];
        foreach($sectionComponentFiles as $file) {
            if(str_contains($file,'blade.php')){
                $sections[] = str_replace('.blade.php','',$file);
            }
        }
        $selected = array_intersect($parts, $sections);
        $remainder = array_diff($sections,$parts);
        $sorted = array_merge($selected,$remainder);
        return view('admin.pages.edit',[
            'page' => $page,
            'parts' => $parts,
            'selected_parts' => $selected,
            'sorted_sections' => $sorted,
            'sections' => $sections,
            'header' => $header,
            'content' => $content,
            'footer' => $footer
        ]);
    }
    /**
     * 
     */
    public function update(Request $request, $pageName)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'route' => 'string|max:255|nullable',
            'controller' => 'string|max:255|nullable',
            'method' => 'string|max:255|nullable',
            'view' => 'sometimes|string|max:255|nullable',
            'exclude_nav' => 'boolean',
            'parts' => 'sometimes|array',
            'parts_order' => 'required|string',
        ]);

        $pages = $this->getPages();

        $pageIndex = $this->getPageIndex($pageName);
        if ($pageIndex === false) {
            abort(404);
        }
        $pages[$pageIndex]['name'] = $validated['name'];
        $pages[$pageIndex]['title'] = $validated['title'];

        $optionalFields = ['route', 'controller', 'method', 'view'];
        foreach ($optionalFields as $field) {
            if (!empty($validated[$field])) {
                $pages[$pageIndex][$field] = $validated[$field];
            } else {
                unset($pages[$pageIndex][$field]);
            }
        }

        $selectedParts = $validated['parts'] ?? [];
        $orderedPartNames = explode(',', $validated['parts_order']);
        $finalOrderedParts = collect($orderedPartNames)
            ->filter(fn($partName) => in_array($partName, $selectedParts))
            ->values()
            ->all();
        if($finalOrderedParts !== $this->parts) {
            $pages[$pageIndex]['parts'] = $finalOrderedParts;
        } else {
            unset($pages[$pageIndex]['parts']);
        }

        $this->saveParts($request);
        $this->savePages($pages);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully!');
    }
    /**
     * 
     */
    public function create()
    {
        $header = $this->getMarkdownContent('header',['name' => 'default']);
        $content = $this->getMarkdownContent('content',['name' => 'default']);
        $footer = $this->getMarkdownContent('footer',['name' => 'default']);

        $parts = $this->parts;
        $sectionComponentFiles = scandir(resource_path('views/components/sections'));
        $sections = [];
        foreach($sectionComponentFiles as $file) {
            if(str_contains($file,'blade.php')){
                $sections[] = str_replace('.blade.php','',$file);
            }
        }
        $selected = array_intersect($parts, $sections);
        $remainder = array_diff($sections,$parts);
        $sorted = array_merge($selected,$remainder);

        return view('admin.pages.create',[
            'parts' => $parts,
            'selected_parts' => $selected,
            'sorted_sections' => $sorted,
            'sections' => $sections,
            'header' => $header,
            'content' => $content,
            'footer' => $footer
        ]);
    }
    /**
     * 
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'route' => 'string|max:255|nullable',
            'controller' => 'string|max:255|nullable',
            'method' => 'string|max:255|nullable',
            'view' => 'sometimes|string|max:255|nullable',
            'exclude_nav' => 'boolean',
            'parts' => 'sometimes|array',
            'parts_order' => 'required|string',
        ]);

        $pages = $this->getPages();
        $page = [];
        $page['name'] = $validated['name'];
        $page['title'] = $validated['title'];

        $optionalFields = ['route', 'controller', 'method', 'view'];
        foreach ($optionalFields as $field) {
            if (!empty($validated[$field])) {
                $page[$field] = $validated[$field];
            }
        }
        $pages[] = $page;
        $selectedParts = $validated['parts'] ?? [];
        $orderedPartNames = explode(',', $validated['parts_order']);
        $finalOrderedParts = collect($orderedPartNames)
            ->filter(fn($partName) => in_array($partName, $selectedParts))
            ->values()
            ->all();
        if($finalOrderedParts !== $this->parts) {
            $page['parts'] = $finalOrderedParts;
        }

        $this->saveParts($request);
        $this->savePages($pages);

        return redirect()->route('admin.pages.index')->with('success', 'Page added successfully!');
    }
    /**
     * 
     */
    public function destroy(string $pageName): void
    {
        $pages = $this->getPages();
        $pageIndex = $this->getPageIndex($pageName);
        unset($pages[$pageIndex]);
        $this->savePages($pages);
    }
}