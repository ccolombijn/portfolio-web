<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Vite;
class PortfolioController extends Controller
{

    protected $projects;

    public function __construct(array $projects, array $content)
    {
        parent::__construct($content);
        // foreach ($projects as &$project) { 
        //     if (!empty($project['image_url'])) {
        //         $project['image_url'] = asset('storage' . $project['image_url']);
        //     }
        // }
        $this->projects = $projects;
    }
    public function show(array $page) {}
    /**
     * @todo complete project view / include skills
     */
    public function project(string $project, array $page)
    {

        $project = $this->projects[array_search(request()->route('project'), 
            array_column($this->projects, 'name'))];
        $project['header'] = $this->getPugMarkdownHTML('header/projects', $project);
        $project['description'] = $this->getPugMarkdownHTML('content/projects', $project);
        $project['name'] = 'portfolio';

        $this->data = [
            'page' => $page,
            'name' => 'portfolio',
            'item' => (object) $project,
            'route' => 'portfolio.project',
            'key' => 'project'
        ];

        foreach ($this->parts as $part) { 
            $this->data[$part] = $this->getPugMarkdownHTML($part, $page) ?? '';
        }

        if($project) { 
            return view('pages.detail', $this->data);
        } else {
            $this->data['items'] = $this->projects;
            return view('pages.overview', $this->data);
        }
    }

    public function index(array $page)
    {
        $this->data = [
            'page' => $page,
            'name' => 'portfolio',
            'items' => $this->projects,
            'route' => 'portfolio.project',
            'key' => 'project'
        ];
        foreach ($this->parts as $part) { 
            $this->data[$part] = $this->getPugMarkdownHTML($part, $page) ?? '';
        }
        return view('pages.overview', $this->data);
    }
}