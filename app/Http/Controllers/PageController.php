<?php

namespace App\Http\Controllers;

class PageController extends Controller
{

    /**
     * Returns default page view
     */
    public function show(array $page)
    {

         $contentData = [];
         foreach ($this->parts as $part) {
             $contentData[$part] = $this->getPugMarkdownHTML($part, $page) ?? '';
         }
 
         $data = [
             'page' => $page,
             'parts' => $this->parts,
             'content' => $contentData,
         ];
 
         return view('pages.default', $data);
    }

}
