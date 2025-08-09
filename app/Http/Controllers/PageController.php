<?php

namespace App\Http\Controllers;

class PageController extends Controller
{

    /**
     * Returns default page view
     */
    public function show(array $page): \Illuminate\Contracts\View\View
    {

         $contentData = [];
         $parts = isset($page['parts']) ? $page['parts'] : $this->parts;
         
         foreach ($parts as $part) { 

            $pageContent = $this->getPugMarkdownHTML($part, $page); // md/pug part content 

            $contentData[$part] = $pageContent;
            if(isset($page[$part])) {
                $viewData = $page[$part]; // component data in page 
            } else {
                $viewData = app('content.data')['sections.' . $part] ?? []; // global
            }

            $partComponent = 'components.sections.' . $part;
            if(view()->exists($partComponent) && !in_array($part,$this->parts)) {
                $contentData[$part] = view($partComponent, $viewData)->render(); // Add part as rendered view
            }
         }

         $data = [
             'page' => $page,
             'parts' => $parts,
             'content' => $contentData,
         ];

         return view(isset($page['view']) ? $page['view'] : 'pages.default', $data);

    }

}
