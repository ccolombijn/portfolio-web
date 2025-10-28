<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\Content;
use Gemini\Enums\Role;
use Gemini\Exceptions\NoPartsFoundException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use Log;

class GeminiController extends Controller
{
    /**
     * 
     */
    public function show(array $page) {}
    /**
     * Default request
     */
    public function generate(Request $request)
    {
        $data = $request->json()->all();
        $model = isset($data['model']) ? $data['model'] : 'gemini-2.5-flash-lite';
        $prompt = $this->prompt($data);
        Log::debug($prompt);
        if (isset($data['stream'])) {
            $stream = Gemini::generativeModel(model: $model)
                ->streamGenerateContent($prompt);
            return new StreamedResponse(function () use ($stream) {
                try {
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
                } catch (\Throwable $e) {
                    if (str_contains($e->getMessage(), 'part')) {
                        echo "[ERROR: The response was blocked or contained no content.]";
                    } else {
                        echo "[ERROR: An unexpected error occurred during the stream.]";
                    }

                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                }
            }, 200, [
                'Content-Type' => 'text/plain',
                'X-Accel-Buffering' => 'no', // Disables buffering in Nginx
                'Cache-Control' => 'no-cache',
            ]);
            // return response()->stream(function () use ($stream): void {
            //     foreach ($stream as $response) {
            //         echo $response->text();
            //     }
            // });
        }
        // else { // non-streaming 
        //     $model = Gemini::generativeModel($model);
        //     $result = $model->generateContent( $prompt . ' : ' . $word);
        //     $environment = new Environment([
        //         'html_input' => 'allow',
        //         'allow_unsafe_links' => false,
        //     ]);
        //     $environment->addExtension(new CommonMarkCoreExtension());
        //     $converter = new MarkdownConverter($environment);
        //     return str_replace('&quot;','"',strip_tags($converter->convert($result->text())));
        // }
    }

    /**
     * Build prompt with given data
     */
    private function prompt(array $data)
    {
        if (isset($data['history'])) {
            $newPrompt = $data['prompt'];
            $historyPayload = $data['history'];
            $history = [];
            foreach ($historyPayload as $item) {
                $role = ($item['role'] ?? 'user') === 'model' ? Role::MODEL : Role::USER;
                $history[] = Content::parse(part: $item['text'], role: $role);
            }

            $response = [
                ...$history,
                Content::parse(part: $newPrompt, role: Role::USER)
            ];
        } else {
            $prompts = [
                'explanation' => 'Leg kort (in niet al te veel woorden), en in zo eenvoudig mogelijke bewoordingen, voor een leek (de lezer aan wie je dit uitlegt), uit wat ' . $data['input'] . ' betekent - in zover relevant, met betrekking tot web development, grafische vormgeving of aanverwante software voor teams (je hoeft dit verder niet te benoemen)',
                'summarize' => 'Geef een korte samenvatting (in niet al te veel woorden, maximaal enkele regels) van de volgende tekst alsof ik het aan iemand vertel over mijn tekst : ' . $data['input']

            ];
            $response = $prompts[$data['prompt']];
        }
        return $response;
    }
}
