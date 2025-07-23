<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gemini\Laravel\Facades\Gemini;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
        $model = isset($data['model']) ? $data['model'] : 'gemini-2.5-flash';
        $word = $data['word'];
        $prompt = 'Leg kort (in niet al te veel woorden), en in zo eenvoudig mogelijke bewoordingen, voor een leek (de lezer aan wie je dit uitlegt), uit wat ' . $word . ' betekent - in zover relevant, met betrekking tot web development, grafische vormgeving of aanverwante software voor teams (je hoeft dit verder niet te benoemen)';
        
        if(isset($data['stream'])){
            $stream = Gemini::generativeModel(model: $model)
                ->streamGenerateContent($prompt);
            return new StreamedResponse(function () use ($stream) {
                foreach ($stream as $response) {
                    if (!empty($text = $response->text())) {
                        echo $text;
                        // Flush the output buffer to send the chunk immediately
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }
                }
            }, 200, [
                // These headers are crucial for streaming
                'Content-Type' => 'text/plain',
                'X-Accel-Buffering' => 'no', // Disables buffering in Nginx
                'Cache-Control' => 'no-cache',
            ]);
            // return response()->stream(function () use ($stream): void {
            //     foreach ($stream as $response) {
            //         echo $response->text();
            //     }
            // });
        } else {
            $model = Gemini::generativeModel($model);
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
}
