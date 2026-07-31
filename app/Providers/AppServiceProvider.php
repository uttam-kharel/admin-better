<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        view()->share('lucideMap', [
            'academic-cap' => 'graduation-cap',
            'building-office-2' => 'building-2',
            'building-office' => 'building-2',
            'cpu-chip' => 'cpu',
            'microchip' => 'cpu',
            'trophy' => 'trophy',
            'star' => 'star',
            'flask' => 'flask-conical',
            'chat-bubble-left-right' => 'message-square-quote',
            'photo' => 'image',
            'gift' => 'package',
            'document' => 'file-text',
            'document-text' => 'file-text',
            'bars-3' => 'menu',
            'cog-6-tooth' => 'settings',
            'squares-2x2' => 'layout-dashboard',
            'user-group' => 'users',
            'list-bullet' => 'list-checks',
            'chart-bar' => 'bar-chart-3',
            'question-mark-circle' => 'help-circle',
            'envelope' => 'mail',
            'bolt' => 'activity',
            'scan-line' => 'scan',
            'bell-alert' => 'bell',
            'document-magnifying-glass' => 'search',
            'clipboard-document-check' => 'clipboard-check',
            'clipboard-document-list' => 'clipboard-list',
            'check-badge' => 'badge-check',
            'arrow-trending-up' => 'trending-up',
            'magnifying-glass' => 'search',
            'receipt-percent' => 'receipt',
            'video-camera' => 'video',
            'exclamation-circle' => 'alert-circle',
            'check-circle' => 'check-circle',
            'x-circle' => 'x-circle',
            'information-circle' => 'info',
            'plus-circle' => 'plus-circle',
            'minus-circle' => 'minus-circle',
            'shield-exclamation' => 'shield-alert',
        ]);
    }
}
