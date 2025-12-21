<?php

namespace App\Providers;

use App\Listeners\CheckUserExpirationOnLogin;
use App\Models\BankStatement;
use App\Models\LeaveRequest;
use App\Models\Order;
use App\Models\User;
use App\Models\Document;
use App\Observers\DocumentObserver;
use App\Observers\BankStatementObserver;
use App\Observers\LeaveRequestObserver;
use App\Observers\OrderObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register User Observer for auto-generating leave balances
        User::observe(UserObserver::class);

        // Register LeaveRequest Observer for auto-filling user_id
        LeaveRequest::observe(LeaveRequestObserver::class);

        // Register Order Observer for tracking last edited by
        Order::observe(OrderObserver::class);

        // Register BankStatement Observer for tracking last edited by
        BankStatement::observe(BankStatementObserver::class);

        // Register Document Observer for auto-numbering
        Document::observe(DocumentObserver::class);

        // Register login event listener for daily expiration welcome notifications
        Event::listen(
            Login::class,
            CheckUserExpirationOnLogin::class
        );
    }
}
