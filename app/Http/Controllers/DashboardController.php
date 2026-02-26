<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Подстрой под свою систему ролей:
        // 1) boolean поле:
        $isAdmin = (bool) ($user?->is_admin ?? false);

        // 2) Spatie (если нужно):
        // $isAdmin = $user?->hasRole('admin') ?? false;

        $year = 2026;

        $monthDefs = [
            ['slug' => 'february', 'month' => 2, 'label' => 'Февраль'],
            ['slug' => 'march',    'month' => 3, 'label' => 'Март'],
            ['slug' => 'april',    'month' => 4, 'label' => 'Апрель'],
            ['slug' => 'may',      'month' => 5, 'label' => 'Май'],
        ];

        $months = collect($monthDefs)->map(function ($m) use ($isAdmin, $year) {
            $availableAt = now()->setDate($year, $m['month'], 1)->startOfDay();
            $enabled = $isAdmin || now()->gte($availableAt);

            return [
                'slug'        => $m['slug'],
                'label'       => $m['label'],
                'availableAt' => $availableAt, // Carbon instance
                'enabled'     => $enabled,
                'admin'       => $isAdmin,
            ];
        })->values();

        return view('dashboard', [
            'months'  => $months,
            'isAdmin' => $isAdmin,
            'year'    => $year,
        ]);
    }
}
