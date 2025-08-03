<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    protected $projects;

    public function __construct(array $projects, array $content)
    {
        parent::__construct($content);
        $this->projects = $projects;
    }
    public function show(array $page) {}
    public function index()
    {
        return view('admin.dashboard');
    }
}