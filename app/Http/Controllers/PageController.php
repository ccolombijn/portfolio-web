<?php

namespace App\Http\Controllers;

class PageController extends Controller
{

    /**
     * Returns default page view
     */
    public function show(array $page)
    {
        $this->data = ['page' => $page]; 
        foreach ($this->parts as $part) { 
            $this->data[$part] = $this->getPugMarkdownHTML($part, $page) ?? '';
        }
        return view('pages.default', $this->data);
    }

}
