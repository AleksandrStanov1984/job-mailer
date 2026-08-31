<?php

use App\Http\Controllers\MailerController;
use App\Http\Controllers\RecipientImportController;
use App\Http\Controllers\TestMailController;
use App\Http\Controllers\CampaignController;

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

Route::post(
    '/campaigns',
    [CampaignController::class, 'store']
)->name('campaigns.store');

Route::post(
    '/campaigns/{campaign}/recipients/{recipient}/send',
    [CampaignController::class, 'sendRecipient']
)->name('campaigns.recipients.send');

Route::post(
    '/campaigns/{campaign}/recipients/{recipient}/retry',
    [CampaignController::class, 'retryRecipient']
)->name('campaigns.recipients.retry');
