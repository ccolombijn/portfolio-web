<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gemini\Laravel\Facades\Gemini;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

class GeminiController extends Controller
{
    public function __construct(array $content)
    {
        parent::__construct($content);
    }

    function show(array $page){}

    function generate(Request $request){
        $data = $request->json()->all();
        $word = $data['word'];
        $model = Gemini::generativeModel('gemini-2.5-flash');
        $prompt = 'Leg kort uit wat dit betekent (in de context van grafische vormgeving, web development of aanverwante software voor teams) in zo eenvoudig mogelijke bewoordingen';
        $result = $model->generateContent( $prompt . ' : ' . $word);
        
        $environment = new Environment([
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $converter = new MarkdownConverter($environment);
        
        return str_replace('&quot;','"',strip_tags($converter->convert($result->text())));

    }
}
