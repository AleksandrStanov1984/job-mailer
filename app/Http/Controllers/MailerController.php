<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class MailerController extends Controller
{
    public function index(): View
    {
        return view('index', [
            'page' => 'mailer',
            'title' => 'Рассылка',
        ]);
    }

    public function history(): View
    {
        return view('index', [
            'page' => 'history',
            'title' => 'История рассылок',
        ]);
    }
}
