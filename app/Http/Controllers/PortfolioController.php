<?php

namespace App\Http\Controllers;

use App\Models\Project; // Make sure to import your Project model
use Illuminate\Http\Request;

class PortfolioController extends Controller
{

    public function __construct()
    {
        // This constructor is intentionally empty.
    }
    /**
     * Show the main portfolio page or a single project detail page.
     * This single method handles both routes.
     */
    public function show(array $page)
    {
        // Laravel's Route Model Binding provides the project model if the {project:slug}
        // parameter exists in the URL. We can get it from the request's route.
        $project = request()->route('project');

        // If a project model was found, we are on the detail page.
        if ($project instanceof Project) {
            return view('pages.detail', [
                'page' => $page, // The base portfolio page data
                'item' => $project, // The specific project model
            ]);
        }

        // Otherwise, we are on the main portfolio overview page.
        // Fetch all projects to display in the list.
        $projects = Project::all(); // You might want to add ordering or pagination here.

        return view('pages.portfolio', [
            'page' => $page,
            'projects' => $projects,
        ]);
    }

    public function index(array $page)
    {

        // $projects = Project::latest()->get();
        $projects = [ /* temp dummy data */ ];

        return view('pages.overview', [
            'page' => $page,
            'items' => $projects,
        ]);
    }
}