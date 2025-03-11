<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;


class UsersController extends Controller 
{
    public function index()
    {
        $users = User::all();
        return Inertia::render('');
    }
}

?>