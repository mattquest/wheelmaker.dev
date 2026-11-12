<?php

namespace App\Http\Controllers;

use App\Services\Recaptcha;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function resume(Request $request, Recaptcha $recaptcha)
    {
        if (! $recaptcha->passes($request->input('token'), $request->ip())) {
            return redirect('/')
                ->with('resume_error', 'The bot check did not pass — please try the download again.');
        }

        return redirect(asset('MattRademacher.pdf'));
    }
}
