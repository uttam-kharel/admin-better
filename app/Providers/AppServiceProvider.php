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
            'cpu-chip' => 'cpu',
            'trophy' => 'trophy',
            'star' => 'star',
            'heart-pulse' => 'heart-pulse',
            'flask-conical' => 'flask-conical',
            'flask' => 'flask-conical',
            'shield-check' => 'shield-check',
            'phone' => 'phone',
            'ambulance' => 'ambulance',
            'search' => 'search',
            'eye' => 'eye',
            'brain' => 'brain',
            'bone' => 'bone',
            'baby' => 'baby',
            'pill' => 'pill',
            'atom' => 'atom',
            'bot' => 'bot',
            'badge-check' => 'badge-check',
            'activity' => 'activity',
            'scan-line' => 'scan',
            'radio' => 'radio',
            'video' => 'video',
            'file-text' => 'file-text',
            'microchip' => 'cpu',
            'heart' => 'heart',
            'stethoscope' => 'stethoscope',
            'chat-bubble-left-right' => 'message-square-quote',
            'photo' => 'image',
            'gift' => 'package',
            'beaker' => 'beaker',
            'document' => 'file-text',
            'bars-3' => 'menu',
            'cog-6-tooth' => 'settings',
            'shield-exclamation' => 'shield-alert',
            'squares-2x2' => 'layout-dashboard',
            'calendar-days' => 'calendar-days',
            'user-group' => 'users',
            'list-bullet' => 'list-checks',
            'newspaper' => 'newspaper',
            'users' => 'users',
            'inbox' => 'inbox',
            'bolt' => 'activity',
            'chart-bar' => 'bar-chart-3',
            'question-mark-circle' => 'help-circle',
            'building-office' => 'building-2',
            'globe' => 'globe',
            'envelope' => 'mail',
            'map-pin' => 'map-pin',
            // Database icon names that need lucide equivalents:
            'bell-alert' => 'bell',
            'document-magnifying-glass' => 'search',
            'clipboard-document-check' => 'clipboard-check',
            'video-camera' => 'video',
            'arrow-trending-up' => 'trending-up',
            'clipboard-document-list' => 'clipboard-list',
            'check-badge' => 'badge-check',
            'truck' => 'truck',
            'document-text' => 'file-text',
            'magnifying-glass' => 'search',
            'signal' => 'signal',
            'sparkles' => 'sparkles',
            'calendar' => 'calendar',
            'check-circle' => 'check-circle',
            'clock' => 'clock',
            'x-circle' => 'x-circle',
            'exclamation-circle' => 'alert-circle',
            'plus-circle' => 'plus-circle',
            'minus-circle' => 'minus-circle',
            'information-circle' => 'info',
            'receipt-percent' => 'receipt',
            'academic-cap' => 'graduation-cap',
        ]);
    }
}
