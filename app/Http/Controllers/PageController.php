<?php

namespace App\Http\Controllers;

class PageController extends Controller
{

    /**
     * Returns default page view
     */
    public function show(array $page)
    {
        $this->data = ['page' => $page]; // page data
        foreach ($this->parts as $part) { // page parts
            // Content can be .pug or .md file in resources
            $this->data[$part] = $this->getPugMarkdownHTML($part, $page) ?? '';
        }
        return view('pages.default', $this->data);
    }

}
