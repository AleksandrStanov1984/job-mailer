<?php

use App\Http\Controllers\MailerController;
use App\Http\Controllers\RecipientImportController;
use App\Http\Controllers\TestMailController;

use Illuminate\Support\Facades\Route;

Route::get('/', [MailerController::class, 'index'])
    ->name('mailer.index');

Route::get('/history', [MailerController::class, 'history'])
    ->name('mailer.history');

Route::post(
    '/recipients/preview',
    [RecipientImportController::class, 'preview']
)->name('recipients.preview');

Route::post('/mail/test', [TestMailController::class, 'send'])
    ->name('mail.test');
