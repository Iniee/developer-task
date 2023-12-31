<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = User::find($request->route('id'));

        if ($user->hasVerifiedEmail()) {
            return "alsuccese";
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return "alsuccese";
    }
}