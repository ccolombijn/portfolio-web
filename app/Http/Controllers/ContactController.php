<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class ContactController extends Controller
{
    // public function __construct(array $content)
    // {
    //     parent::__construct($content);
    // }

    public function show(array $page) 
    {
        $header = $this->getPugMarkdownHTML('header',$page);
        $footer = $this->getPugMarkdownHTML('footer',$page);
        return view('pages.contact', [
            'page' => $page,
            'header' => $header,
            'footer' => $footer
        ]);
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|min:2|string',
            'email' => 'required|email',
            'mobile' => 'required|digits:10',
            'message' => 'required|min:10'
        ]);

        if($validated) {
    
            $data = array(
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'bodyMessage' => $request->message
            );

            Mail::send('emails.contact', $data, function($message) use ($data){
                $message->from($data['email']);
                $message->to(app('contact.data')['email']);
                $message->subject($data['subject']);
            });
    
            Session::flash('success', 'Your email has been sent');
        }
        

        return redirect('contact');

    }
}
