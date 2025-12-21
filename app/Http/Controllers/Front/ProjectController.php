<?php

namespace App\Http\Controllers\Front;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects (orders).
     */
    public function index(Request $request)
    {
        // Base query for orders (projects)
        $query = Order::with(['user', 'employee', 'prospect'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by user (client)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by payment status
        if ($request->filled('is_paid')) {
            $query->where('is_paid', $request->is_paid);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('number', 'like', "%{$search}%")
                    ->orWhere('no_kontrak', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Paginate results
        $projects = $query->paginate(12)->withQueryString();

        // Statistics calculation
        $currentYear = Carbon::now()->year;

        $stats = [
            'total_projects' => Order::count(),
            'active_projects' => Order::where('status', OrderStatus::Processing)->count(),
            'completed_projects' => Order::where('status', OrderStatus::Done)->count(),
            'total_revenue_this_year' => Order::whereYear('created_at', $currentYear)->sum('total_price'),
            'paid_projects' => Order::where('is_paid', true)->count(),
            'unpaid_projects' => Order::where('is_paid', false)->count(),
            'average_project_value' => Order::avg('total_price'),
            'projects_this_month' => Order::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', $currentYear)
                ->count(),
        ];

        // Get filter options
        $users = User::select('id', 'name')->orderBy('name')->get();
        $employees = Employee::select('id', 'name')->orderBy('name')->get();

        // Status options from enum
        $statusOptions = collect(OrderStatus::cases())->mapWithKeys(function ($status) {
            return [$status->value => $status->getLabel()];
        })->toArray();

        // Check access permissions
        $allowedRoles = ['super_admin', 'Account Manager', 'Finance'];
        $hasAccess = false;
        $user = Auth::user();
        
        if ($user) {
            // Check if user has hasRole method (Spatie Permission)
            if (method_exists($user, 'hasRole')) {
                foreach ($allowedRoles as $role) {
                    if ($user->hasRole($role)) {
                        $hasAccess = true;
                        break;
                    }
                }
            } else {
                // Fallback: check user's role field directly
                if (in_array($user->role, $allowedRoles)) {
                    $hasAccess = true;
                }
            }
        }

        return view('front.project', compact(
            'projects',
            'stats',
            'users',
            'employees',
            'statusOptions',
            'hasAccess'
        ));
    }

    /**
     * Show the specified project.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'employee', 'prospect']);

        return view('orders.project', compact('order'));
    }

    /**
     * Get project statistics for AJAX requests
     */
    public function getStats(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $stats = [
            'total_projects' => Order::count(),
            'active_projects' => Order::where('status', OrderStatus::Processing)->count(),
            'completed_projects' => Order::where('status', OrderStatus::Done)->count(),
            'revenue_this_year' => Order::whereYear('created_at', $currentYear)->sum('total_price'),
            'revenue_this_month' => Order::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('total_price'),
            'paid_amount_total' => Order::sum('paid_amount'),
            'outstanding_amount' => Order::where('is_paid', false)->sum(DB::raw('total_price - paid_amount')),
        ];

        return response()->json($stats);
    }

    /**
     * Export projects data
     */
    public function export(Request $request)
    {
        $query = Order::with(['user', 'employee', 'prospect']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('is_paid')) {
            $query->where('is_paid', $request->is_paid);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('number', 'like', "%{$search}%")
                    ->orWhere('no_kontrak', 'like', "%{$search}%");
            });
        }

        $projects = $query->get();

        // Return CSV download
        $filename = 'projects_export_'.date('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($projects) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'No',
                'Project Name',
                'Number',
                'Client',
                'Employee',
                'Contract No',
                'Pax',
                'Total Price',
                'Paid Amount',
                'Status',
                'Is Paid',
                'Closing Date',
                'Created Date',
            ]);

            // CSV Data
            foreach ($projects as $index => $project) {
                fputcsv($file, [
                    $index + 1,
                    $project->name,
                    $project->number,
                    $project->user?->name ?? '-',
                    $project->employee?->name ?? '-',
                    $project->no_kontrak ?? '-',
                    $project->pax ?? '-',
                    number_format($project->total_price, 0),
                    number_format($project->paid_amount, 0),
                    $project->status,
                    $project->is_paid ? 'Yes' : 'No',
                    $project->closing_date ? $project->closing_date->format('Y-m-d') : '-',
                    $project->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
