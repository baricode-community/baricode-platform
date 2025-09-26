<?php

namespace App\Http\Controllers;

use App\Models\User\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where(['email_verified_at' => null])->get();
        return view('pages.users.index', compact('users'));
    }
}
