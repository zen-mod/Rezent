<?php

namespace App\Http\Controllers;

use App\Build;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke()
    {
        $builds = Build::latest()->take(5)->get();

        /** @var \App\User $user */
        $user = auth()->user();

        $tokens = $user->tokens->where('revoked', false);

        return view('dashboard', compact('builds', 'tokens'));
    }
}
