<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminAccountCreatedMail;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AdminManagementController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function dashboard()
    {
        $admin = auth('admin')->user();

        return Inertia::render('Admin/Dashboard', [
            'admin' => [
                'id' => $admin->id,
                'full_name' => $admin->full_name,
                'email' => $admin->email,
                'role' => $admin->role,
                'profile_photo_url' => $admin->profile_photo_url,
                'last_login_at' => $admin->last_login_at?->format('M d, Y h:i A'),
            ],
        ]);
    }

    /**
     * Display list of admins.
     */
    public function index(Request $request)
    {
        $query = Admin::with('creator:id,full_name')
            ->orderBy('created_at', 'desc');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $admins = $query->paginate(15);

        return Inertia::render('Admin/AdminList', [
            'admins' => $admins,
            'filters' => $request->only(['search', 'role', 'status']),
        ]);
    }

    /**
     * Show create admin form.
     */
    public function create()
    {
        return Inertia::render('Admin/CreateAdmin');
    }

    /**
     * Store a new admin.
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'role' => ['required', Rule::in(['admin', 'superadmin', 'moderator'])],
        ]);

        // Generate secure random password
        $password = Str::random(12).rand(10, 99);

        // Create admin
        $admin = Admin::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role' => $request->role,
            'is_active' => true,
            'created_by' => auth('admin')->id(),
        ]);

        // Send credentials email
        try {
            Mail::to($admin->email)->send(new AdminAccountCreatedMail($admin, $password));
        } catch (\Exception $e) {
            Log::error('Failed to send admin account created email: '.$e->getMessage());

            // Don't fail the creation, just log it
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin created successfully. Credentials sent to their email.');
    }

    /**
     * Show edit admin form.
     */
    public function edit(Admin $admin)
    {
        return Inertia::render('Admin/EditAdmin', [
            'admin' => [
                'id' => $admin->id,
                'full_name' => $admin->full_name,
                'email' => $admin->email,
                'role' => $admin->role,
                'is_active' => $admin->is_active,
                'profile_photo_url' => $admin->profile_photo_url,
            ],
        ]);
    }

    /**
     * Update admin.
     */
    public function update(Request $request, Admin $admin)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('admins')->ignore($admin->id)],
            'role' => ['required', Rule::in(['admin', 'superadmin', 'moderator'])],
            'is_active' => 'required|boolean',
        ]);

        // Prevent self-deactivation
        if ($admin->id === auth('admin')->id() && ! $request->is_active) {
            return back()->withErrors(['is_active' => 'You cannot deactivate your own account.']);
        }

        $admin->update([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin updated successfully.');
    }

    /**
     * Delete admin.
     */
    public function destroy(Admin $admin)
    {
        // Prevent self-deletion
        if ($admin->id === auth('admin')->id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin deleted successfully.');
    }

    /**
     * Toggle admin status.
     */
    public function toggleStatus(Admin $admin)
    {
        // Prevent self-deactivation
        if ($admin->id === auth('admin')->id()) {
            return back()->withErrors(['error' => 'You cannot deactivate your own account.']);
        }

        $admin->update([
            'is_active' => ! $admin->is_active,
        ]);

        $status = $admin->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Admin {$status} successfully.");
    }
}
