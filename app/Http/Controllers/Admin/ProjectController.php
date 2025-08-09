<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController as Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class ProjectController extends Controller
{

    protected $projects;

    public function __construct()
    {
        $this->projects = app('projects.data');
    }
    public function show(array $page) {}
    /**
     * 
     */
    private function getProjects(): array
    {
        return $this->projects;
    }
    /**
     * 
     */
    private function saveProjects(array $projects): void
    {
        $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
        File::put(storage_path('app/public/json/projects.json'), json_encode($projects, $options));
        Cache::forget('projects.json.data');
    }
    /**
     * 
     */
    public function index(): \Illuminate\Contracts\View\View
    {
        $projects = $this->getProjects();
        return view('admin.projects.index', compact('projects'));
    }
    /**
     * 
     */
    private function getProjectIndex($projectName): int
    {
        $projects = $this->getProjects();
        $project = collect($projects)->where('name', $projectName);
        return array_keys($project->toArray())[0];
    }
    /**
     * 
     */
    public function edit($projectName): \Illuminate\Contracts\View\View
    {
        $projects = $this->getProjects();
        $project = collect($projects)->firstWhere('name', $projectName);

        if (!$project) {
            abort(404);
        }
        $header = $this->getMarkdownContent('header/projects',$project);
        $content = $this->getMarkdownContent('content/projects',$project);
        $footer = $this->getMarkdownContent('footer/projects',$project);
        
        return view('admin.projects.edit',[
            'project' => $project,
            'header' => $header,
            'content' => $content,
            'footer' => $footer
        ]);
    }
    /**
     * 
     */
    public function update(Request $request, $projectName): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'slug' => 'string|max:255|nullable',
            'source' => 'string|max:255|nullable',
            'intro' => 'string|max:255|nullable',
            'image_url' => 'string|max:255|nullable',
        ]);

        $projects = $this->getProjects();

        $projectIndex = $this->getProjectIndex($projectName);
        if ($projectIndex === false) {
            abort(404);
        }

        $projects[$projectIndex]['name'] = $validated['name'];
        $projects[$projectIndex]['title'] = $validated['title'];

        $optionalFields = ['slug', 'source', 'intro', 'image_url',];
        foreach ($optionalFields as $field) {
            if (!empty($validated[$field])) {
                $projects[$projectIndex][$field] = $validated[$field];
            } else {
                unset($projects[$projectIndex][$field]);
            }
        }

        $this->saveProjects($projects);

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully!');
    }
    /**
     * 
     */
    public function create(): \Illuminate\Contracts\View\View
    {
        return view('admin.projects.create');
    }
    /**
     * 
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'slug' => 'string|max:255|nullable',
            'source' => 'string|max:255|nullable',
            'intro' => 'string|max:255|nullable',
            'image_url' => 'string|max:255|nullable',
        ]);
        $projects = $this->getProjects();
        if(in_array($validated['name'], array_column($projects,'name'))){
            return redirect()->back()
            ->withErrors(['name' => 'This name is already in use. Please choose a different one.'])
            ->withInput();
        }
        if(in_array($validated['slug'], array_column($projects,'route'))){
            return redirect()->back()
            ->withErrors(['slug' => 'This route is already in use. Please choose a different one.'])
            ->withInput();
        }
        $project = [];
        $project['name'] = $validated['name'];
        $project['title'] = $validated['title'];
        $optionalFields = ['slug', 'source', 'intro', 'image_url',];
        foreach ($optionalFields as $field) {
            if (!empty($validated[$field])) {
                $project[$field] = $validated[$field];
            }
        }
        $projects[] = $project;
        $this->saveProjects($projects);
        return redirect()->route('admin.projects.index')->with('success', 'Project added successfully!');

    }
    public function destroy(string $projectName)
    {
        $projects = $this->getProjects();
        $projectIndex = $this->getProjectIndex($projectName);
        unset($projects[$projectIndex]);
        $this->saveProjects($projects);
        return redirect()->route('admin.projects.index')->with('success', 'Project removed successfully!');
    }
}
