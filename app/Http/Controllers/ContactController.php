<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        // dd($request->input('email'));
        
        
        // Валидация полей формы
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        // Данные для письма
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'userMessage' => $request->input('message'),
        ];

        // Отправка письма
        Mail::send('emails.contact', $data, function ($mailMessage) use ($data) {
            $mailMessage->to('dialectik1980@gmail.com')
                ->subject('Новое сообщение с сайта Andrew Old')
                ->from($data['email'], $data['name']);
        });

        // Mail::raw($request->message, function ($mailMessage) use ($request) {
        //     $mailMessage->to('dialectik1980@gmail.com')
        //         ->subject('Новое сообщение с сайта Andrew Old')
        //         ->from($request->email);
        // });

        return back()->with('success', __('Ваше сообщение успешно отправлено!'));
    }
}
