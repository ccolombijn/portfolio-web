<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\ProjectRepositoryInterface;
use App\Services\PageContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends AdminController
{
    // Inject the repository and service via the constructor
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private PageContentService $contentService
    ) {}

    /**
     * Display a listing of the projects.
     */
    public function index(): View
    {
        return view('admin.projects.index', [
            'projects' => $this->projectRepository->all()
        ]);
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(): View
    {
        return view('admin.projects.create');
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'intro' => 'nullable|string|max:255',
            'image_url' => 'nullable|string|max:255',
        ]);

        // Check for duplicate name using the repository
        if ($this->projectRepository->findBy('name', $validated['name'])) {
            return redirect()->back()
                ->withErrors(['name' => 'This name is already in use.'])
                ->withInput();
        }
        
        // Use the repository to create the new project
        $this->projectRepository->create($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Project added successfully!');
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(string $projectName): View
    {
        $project = $this->projectRepository->findBy('name', $projectName);
        if (!$project) {
            abort(404);
        }
        
        return view('admin.projects.edit', [
            'project' => $project,
            'header' => $this->contentService->getMarkdownContent('header/projects', $project),
            'content' => $this->contentService->getMarkdownContent('content/projects', $project),
            'footer' => $this->contentService->getMarkdownContent('footer/projects', $project),
        ]);
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, string $projectName): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'intro' => 'nullable|string|max:255',
            'image_url' => 'nullable|string|max:255',
        ]);

        // Use the repository to update the project
        $this->projectRepository->update('name', $projectName, $validated);

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified project.
     */
    public function destroy(string $projectName): RedirectResponse
    {
        // Use the repository to delete the project
        $this->projectRepository->delete('name', $projectName);

        return redirect()->route('admin.projects.index')->with('success', 'Project removed successfully!');
    }
}