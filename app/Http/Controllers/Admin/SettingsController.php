<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController as Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    protected $content;
    /**
     * 
     */
    public function __construct()
    {
        $this->content = app('content.data');
    }
    /**
     * 
     */
    public function index()
    {
        return view('admin.settings',[
            'content' => $this->content
        ]);
    }
}
