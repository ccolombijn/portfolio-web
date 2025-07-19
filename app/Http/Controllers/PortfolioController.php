<?php

namespace App\Http\Controllers;

use App\Models\Project; // Make sure to import your Project model
use Illuminate\Http\Request;

class PortfolioController extends Controller
{

    protected $projects;

    public function __construct(array $projects, array $content)
    {
        // Geef de $content array door aan de parent Controller.
        parent::__construct($content);

        // Sla de $projects array op voor eigen gebruik.
        $this->projects = $projects;
    }

    /**
     * Show the main portfolio page or a single project detail page.
     * This single method handles both routes.
     */
    public function show(array $page)
    {
        // Laravel's Route Model Binding provides the project model if the {project:slug}
        // parameter exists in the URL. We can get it from the request's route.
        $projectRoute = request()->route('project');
        $projectName = $projectRoute->getName();
        $projectKey = array_search($projectName, 
            array_column($this->projects, 'name'));
        $project = $this->projects[$projectKey];
        // If a project model was found, we are on the detail page.
        // if ($project instanceof Project) {
        if($project) { 
            return view('pages.detail', [
                'page' => $page, // The base portfolio page data
                'item' => $project, // The specific project model
            ]);
        }

        // Otherwise, we are on the main portfolio overview page.
        // Fetch all projects to display in the list.
        // $projects = Project::all(); // You might want to add ordering or pagination here.

        return view('pages.overview', [
            'page' => $page,
            'items' => $this->projects,
        ]);
    }

    public function index(array $page)
    {

        // $projects = Project::latest()->get();
        // $projects = [ /* temp dummy data */ ];

        return view('pages.overview', [
            'page' => $page,
            'items' => $this->projects,
        ]);
    }
}