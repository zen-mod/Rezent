<?php

namespace App\Http\Controllers;

use Laravel\Passport\Token;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        return view('tokens.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        /** @var \App\User @user */
        $user = auth()->user();

        $token = $user->createToken($request->name);

        return back()->with('token', $token->accessToken);
    }

    public function destroy(Token $token)
    {
        $revoked = $token->revoke();

        return back()->with('revoked', $revoked);
    }
}
