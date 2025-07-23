<?php

namespace App\Http\Controllers;

class PortfolioController extends Controller
{

    protected $projects;

    public function __construct(array $projects, array $content)
    {
        parent::__construct($content);
        $this->projects = $projects;
    }
    public function show(array $page) {}
    public function project(string $project, array $page)
    {
        // retrieve project 
       
        $project = $this->projects[array_search(request()->route('project'), 
            array_column($this->projects, 'name'))];
        $project['header'] = $this->getPugMarkdownHTML('header/projects', $project);
        $project['description'] = $this->getPugMarkdownHTML('content/projects', $project);
        // construct data
        $this->data = [
            'page' => $page,
            'project' => (object) $project,
        ];
        // add parts
        foreach ($this->parts as $part) { 
            $this->data[$part] = $this->getPugMarkdownHTML($part, $page) ?? '';
        }
        // return view, overview as fallback if project not found
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
            'items' => $this->projects,
        ];
        foreach ($this->parts as $part) { 
            $this->data[$part] = $this->getPugMarkdownHTML($part, $page) ?? '';
        }
        return view('pages.overview', $this->data);
    }
}