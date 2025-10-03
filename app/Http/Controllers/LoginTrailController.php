<?php

namespace App\Http\Controllers;

use App\Models\LoginTrail;
use App\Services\LoginTrailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class LoginTrailController extends Controller
{
    protected LoginTrailService $loginTrailService;

    public function __construct(LoginTrailService $loginTrailService)
    {
        $this->loginTrailService = $loginTrailService;
    }

    /**
     * Display a listing of login trails
     */
    public function index(Request $request): Response
    {
        $filters = $request->only([
            'user_id', 'action', 'success', 'ip_address', 
            'date_from', 'date_to', 'days', 'search'
        ]);

        $perPage = $request->integer('per_page', 15);
        $trails = $this->loginTrailService->getTrails($filters, $perPage);

        return Inertia::render('Admin/LoginTrails/Index', [
            'trails' => $trails,
            'filters' => $filters,
            'actions' => LoginTrail::getActions(),
            'statistics' => $this->loginTrailService->getStatistics(),
        ]);
    }

    /**
     * Get login trails via API
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|integer|exists:users,id',
            'action' => 'sometimes|string|in:' . implode(',', LoginTrail::getActions()),
            'success' => 'sometimes|boolean',
            'ip_address' => 'sometimes|ip',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'days' => 'sometimes|integer|min:1|max:365',
            'search' => 'sometimes|string|max:255',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $filters = $request->only([
            'user_id', 'action', 'success', 'ip_address', 
            'date_from', 'date_to', 'days', 'search'
        ]);

        $perPage = $request->integer('per_page', 15);
        $trails = $this->loginTrailService->getTrails($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $trails,
            'filters' => $filters,
        ]);
    }

    /**
     * Get user's recent activity
     */
    public function userActivity(Request $request, int $userId): JsonResponse
    {
        $limit = $request->integer('limit', 10);
        $activity = $this->loginTrailService->getUserRecentActivity($userId, $limit);

        return response()->json([
            'success' => true,
            'data' => $activity,
        ]);
    }

    /**
     * Get failed attempts for monitoring
     */
    public function failedAttempts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|string|max:255',
            'ip_address' => 'sometimes|ip',
            'minutes' => 'sometimes|integer|min:1|max:1440', // Max 24 hours
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $attempts = $this->loginTrailService->getFailedAttempts(
            $request->input('username'),
            $request->input('ip_address'),
            $request->integer('minutes', 15)
        );

        return response()->json([
            'success' => true,
            'data' => $attempts,
            'count' => $attempts->count(),
        ]);
    }

    /**
     * Get login statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $days = $request->integer('days', 30);
        $statistics = $this->loginTrailService->getStatistics($days);

        return response()->json([
            'success' => true,
            'data' => $statistics,
            'period_days' => $days,
        ]);
    }

    /**
     * Export login trails
     */
    public function export(Request $request): JsonResponse
    {
        // This could be extended to support CSV, Excel exports
        $filters = $request->only([
            'user_id', 'action', 'success', 'ip_address', 
            'date_from', 'date_to', 'days', 'search'
        ]);

        $trails = $this->loginTrailService->getTrails($filters, 1000); // Limit for export

        return response()->json([
            'success' => true,
            'data' => $trails->items(),
            'total' => $trails->total(),
            'exported_at' => now(),
        ]);
    }

    /**
     * Clean old records
     */
    public function cleanup(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'days_to_keep' => 'required|integer|min:30|max:3650', // Min 30 days, max 10 years
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $deletedCount = $this->loginTrailService->cleanOldRecords(
            $request->integer('days_to_keep')
        );

        return response()->json([
            'success' => true,
            'message' => "Deleted {$deletedCount} old records",
            'deleted_count' => $deletedCount,
        ]);
    }
}