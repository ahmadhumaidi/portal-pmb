<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    protected function resolvePerPage(Request $request, int $default = 10): int
    {
        $perPage = $request->query('per_page', $default);

        if ($perPage === 'semua') {
            return 1000000;
        }

        $perPage = (int) $perPage;

        return in_array($perPage, [10, 50, 100], true) ? $perPage : $default;
    }
}
