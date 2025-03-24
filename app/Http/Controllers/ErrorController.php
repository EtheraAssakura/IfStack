<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ErrorController extends Controller
{
    public function notFound()
    {
        return Inertia::render('Errors/404');
    }

    public function forbidden()
    {
        return Inertia::render('Errors/403');
    }

    public function expired()
    {
        return Inertia::render('Errors/419');
    }

    public function tooManyRequests()
    {
        return Inertia::render('Errors/429');
    }

    public function serverError()
    {
        return Inertia::render('Errors/500');
    }

    public function serviceUnavailable()
    {
        return Inertia::render('Errors/503');
    }
}
