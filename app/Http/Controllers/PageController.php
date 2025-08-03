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

            $pageContent = $this->getPugMarkdownHTML($part, $page); // md/pug part content 
            $contentData[$part] = $pageContent;

            if(isset($page[$part])) {
                $viewData = $page[$part]; // component data in page 
            } else {
                $viewData = $this->content[$part] ?? []; // global
            }
            $partComponent = 'components.' . $part;
            if(view()->exists($partComponent)) {
                $contentData[$part] = view($partComponent, $viewData)->render(); // Add part as rendered view
            }
                
         }

         $data = [
             'page' => $page,
             'parts' => $parts,
             'content' => $contentData,
         ];

         return view('pages.default', $data);

    }

}
