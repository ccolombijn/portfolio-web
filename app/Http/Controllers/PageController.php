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
            $this->data[$part] = $this->getMarkdownHTML($part, $page) ?? '';
        }
        return view('pages.default', $this->data);
    }

}
