<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct(array $content)
    {
        parent::__construct($content);
    }

    public function show(array $page) 
    {
        $header = $this->getPugMarkdownHTML('header',$page);
        $footer = $this->getPugMarkdownHTML('footer',$page);
        return view('pages.contact', [
            'page' => $page,
            'header' => $header,
            'footer' => $footer
        ]);
    }
}
