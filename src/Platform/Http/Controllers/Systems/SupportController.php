<?php

declare(strict_types=1);

namespace Orchid\Platform\Http\Controllers\Systems;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Orchid\Platform\Http\Controllers\Controller;
use Orchid\Platform\Mail\SupportMail;
use Orchid\Support\Facades\Alert;

class SupportController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        Mail::send(new SupportMail($request));

        Alert::success(__('Operation completed successfully.'));

        return back();
    }
}
