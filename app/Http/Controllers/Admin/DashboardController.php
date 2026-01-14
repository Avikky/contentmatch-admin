<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $admin = $request->user('admin');

        return Inertia::render('Admin/Dashboard', [
            'admin' => [
                'name' => $admin?->full_name ?? $admin?->display_name ?? $admin?->user_name,
                'role' => $admin?->role,
            ],
            'metrics' => Collection::make([
                ['label' => 'Team Members', 'value' => 3],
                ['label' => 'Active Workspaces', 'value' => 7],
                ['label' => 'Pending Approvals', 'value' => 2],
            ]),
        ]);
    }
}
