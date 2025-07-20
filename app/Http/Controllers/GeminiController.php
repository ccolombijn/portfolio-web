<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gemini\Laravel\Facades\Gemini;

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
        $model = Gemini::generativeModel('gemini-1.5-flash-latest');
        $prompt = 'Leg kort uit wat dit betekent (in de context van grafische vormgeving, web development of aanverwante software voor teams) in zo eenvoudig mogelijke bewoordingen';
        $result = $model->generateContent( $prompt . ' : ' . $word);

        return $result->text();
    }
}
