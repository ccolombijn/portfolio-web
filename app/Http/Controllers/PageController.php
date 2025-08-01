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
            $pageContent = $this->getPugMarkdownHTML($part, $page); // get .md or .pug content for part
            //if(!empty($pageContent)) 
            $contentData[$part] = $pageContent;
            $viewData = $this->content[$part] ?? [];
            $partComponent = 'components.' . $part;
            if(view()->exists($partComponent)) {
                $contentData[$part] = view($partComponent, $viewData)->render(); // Add part as rendered view
                //dd($contentData[$part] = view($partComponent, $viewData)->render()); 
            }
                
         }

         $data = [
             'page' => $page,
             'parts' => $parts,
             'content' => $contentData,
         ];
         //dd($data);
         return view('pages.default', $data);
    }

}
