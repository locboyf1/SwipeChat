<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Đăng ký route xác thực cho kênh Private (/broadcasting/auth)
        Broadcast::routes();

        // 2. Nạp các định nghĩa kênh từ file routes/channels.php
        require base_path('routes/channels.php');
    }
}
