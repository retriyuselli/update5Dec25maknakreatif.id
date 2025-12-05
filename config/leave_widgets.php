<?php

// Widget Registration Configuration
// Add this to your Filament Panel Provider: app/Providers/Filament/AdminPanelProvider.php

return [
    'widgets' => [
        // Leave Management Widgets
        \App\Filament\Widgets\LeaveBalanceWidget::class,
        \App\Filament\Widgets\LeaveUsageChartWidget::class,
        \App\Filament\Widgets\RecentLeaveRequestsWidget::class,
    ],

    'widget_settings' => [
        'leave_balance' => [
            'cache_duration' => 300, // 5 minutes
            'enable_polling' => true,
            'poll_interval' => '30s',
        ],
        'leave_chart' => [
            'default_filter' => 'year',
            'max_height' => '400px',
            'enable_export' => false,
        ],
        'recent_requests' => [
            'limit' => 10,
            'enable_quick_actions' => true,
            'poll_interval' => '30s',
        ],
        'employee_overview' => [
            'per_page' => 15,
            'enable_pagination' => true,
            'enable_search' => true,
        ],
    ],

    'permissions' => [
        'view_leave_widgets' => [
            'roles' => ['admin', 'hr_manager', 'supervisor'],
            'permissions' => ['view_leave_data'],
        ],
        'manage_leave_requests' => [
            'roles' => ['admin', 'hr_manager'],
            'permissions' => ['approve_leave_requests'],
        ],
    ],

    'database_requirements' => [
        'models' => [
            'User' => \App\Models\User::class,
            'LeaveBalance' => \App\Models\LeaveBalance::class,
            'LeaveRequest' => \App\Models\LeaveRequest::class,
            'LeaveType' => \App\Models\LeaveType::class,
        ],
        'relationships' => [
            'User.leaveBalances' => 'hasMany:LeaveBalance',
            'User.leaveRequests' => 'hasMany:LeaveRequest',
            'LeaveRequest.user' => 'belongsTo:User',
            'LeaveRequest.leaveType' => 'belongsTo:LeaveType',
            'LeaveRequest.approvedBy' => 'belongsTo:User',
        ],
        'required_columns' => [
            'leave_balances' => [
                'user_id', 'leave_type', 'total_days', 'remaining_days', 'year',
            ],
            'leave_requests' => [
                'user_id', 'leave_type_id', 'start_date', 'end_date',
                'days_requested', 'status', 'reason', 'approved_by', 'approved_at',
            ],
        ],
    ],
];
