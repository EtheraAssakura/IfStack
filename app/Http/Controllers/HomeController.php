<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $locationId = request()->query('locationId');

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'locationId' => $locationId,
            'site' => $user->site ? [
                'id' => $user->site_id,
                'name' => $user->site->name,
                'locations' => $user->site->locations()->select('id', 'name')->get()
            ] : null
        ]);
    }
}
