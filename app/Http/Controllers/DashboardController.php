<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $isAdmin = $request->user()->is_admin;
        $now = now();
        $months = collect(config('quiz.periods'))->map(function ($period, $slug) use ($isAdmin, $now) {
            $availableAt = Carbon::parse($period['opens_on']);

            return [
                'slug' => $slug,
                'label' => $period['label'],
                'availableAt' => $availableAt,
                'enabled' => $isAdmin || $now->gte($availableAt),
            ];
        });

        return view('dashboard', ['months' => $months]);
    }
}
