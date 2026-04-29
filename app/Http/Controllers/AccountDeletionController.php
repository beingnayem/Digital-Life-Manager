<?php

namespace App\Http\Controllers;

use App\Models\PendingAccountDeletion;
use App\Notifications\AccountDeletionRequested;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountDeletionController extends Controller
{
    public function request(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        PendingAccountDeletion::where('user_id', $user->id)->delete();

        $token = Str::random(64);

        $pendingDeletion = PendingAccountDeletion::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addHour(),
        ]);

        $user->notify(new AccountDeletionRequested(
            deletionId: $pendingDeletion->id,
            token: $token,
            minutesValid: 60,
        ));

        return back()->with('status', 'account-deletion-link-sent');
    }

    public function confirm(Request $request, PendingAccountDeletion $deletion): RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 403);

        if ($deletion->confirmed_at !== null || $deletion->expires_at->isPast()) {
            abort(410);
        }

        $plainToken = (string) $request->query('token');

        if (! hash_equals($deletion->token_hash, hash('sha256', $plainToken))) {
            abort(403);
        }

        $user = $deletion->user;

        if (! $user) {
            abort(404);
        }

        $deletion->forceFill(['confirmed_at' => now()])->save();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'account-deleted');
    }
}