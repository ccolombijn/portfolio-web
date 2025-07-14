<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class PortfolioController extends Controller
{

    public function index(array $page)
    {

        // $projects = Project::latest()->get();
        $projects = [ /* temp dummy data */ ];

        return view('pages.overview', [
            'page' => $page,
            'items' => $projects,
        ]);
    }
    /**
     * @param {array}
     */
    public function show(array $page, Project $project)
    {
         return view('pages.detail', [
            'page' => $page,
            'item' => $project,
         ]);
    }
}
