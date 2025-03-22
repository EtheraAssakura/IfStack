<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::orderBy('created_at', 'desc')->get();

        return Inertia::render('Rapports/Index', [
            'rapports' => $reports
        ]);
    }

    public function download(Report $report)
    {
        if (!Storage::exists('public/' . $report->path)) {
            return back()->with('error', 'Le fichier du rapport n\'existe plus.');
        }

        return Storage::download('public/' . $report->path);
    }
}
