<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        View::composer('layouts.navigation', function ($view) {
            $user = auth()->user();
            
            if ($user) {
                $totalTasks = $user->tasks()->count();
                $completedTasks = $user->tasks()->where('status', 'completed')->count();
                $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                
                $view->with('completionRate', $completionRate);
            }
        });
    }
}
