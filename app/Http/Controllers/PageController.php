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
         $parts = isset($page['parts']) ? $page['parts'] : $this->parts;
         foreach ($parts as $part) {
             $contentData[$part] = $this->getPugMarkdownHTML($part, $page) ?? '';
         }

         $data = [
             'page' => $page,
             'parts' => $parts,
             'content' => $contentData,
         ];
 
         return view('pages.default', $data);
    }

}
