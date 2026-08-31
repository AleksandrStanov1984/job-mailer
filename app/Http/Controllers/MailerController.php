<?php

namespace App\Http\Controllers;

use App\Models\CampaignRecipient;
use Illuminate\Http\Request;
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

    public function history(Request $request): View
    {
        $status = $request->string('status')->toString();
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();
        $sort = $request->string('sort')->toString();
        $search = trim(
            $request->string('search')->toString()
        );

        $status = $status !== ''
            ? $status
            : 'all';

        $sort = $sort === 'asc'
            ? 'asc'
            : 'desc';

        $query = CampaignRecipient::query()
            ->with('campaign');

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query
                    ->where(
                        'email',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'normalized_email',
                        'like',
                        '%' . mb_strtolower($search) . '%'
                    )
                    ->orWhere(
                        'company',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'vacancy',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'contact_name',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        if ($status !== 'all') {
            if ($status === 'skipped') {
                $query->whereIn('status', [
                    CampaignRecipient::STATUS_SKIPPED_RECENTLY_SENT,
                    CampaignRecipient::STATUS_DUPLICATE_IN_FILE,
                ]);
            } else {
                $query->where('status', $status);
            }
        }

        if ($dateFrom !== '') {
            $query->whereDate(
                'created_at',
                '>=',
                $dateFrom
            );
        }

        if ($dateTo !== '') {
            $query->whereDate(
                'created_at',
                '<=',
                $dateTo
            );
        }

        $historyRecipients = $query
            ->orderBy('created_at', $sort)
            ->orderBy('id', $sort)
            ->get();

        return view('index', [
            'page' => 'history',
            'title' => 'История рассылок',

            'historyRecipients' =>
                $historyRecipients,

            'historyFilters' => [
                'status' => $status,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'sort' => $sort,
                'search' => $search,
            ],
        ]);
    }
}
