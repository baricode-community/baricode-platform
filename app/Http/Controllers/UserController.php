<?php

namespace App\Http\Controllers;

use App\Models\User\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereNotNull('email_verified_at')->get();
        return view('pages.users.index', compact('users'));
    }
}
