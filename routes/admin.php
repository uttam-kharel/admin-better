<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Loaded by bootstrap/app.php inside the `admin` URL prefix and `admin.`
| route-name prefix. Kept separate from routes/web.php (public site) so the
| two surfaces stay manageable.
|
| Conventions:
|   - Everything except /login and /logout requires the admin guard.
|   - Generic CRUD resources all render through the shared resource-manager
|     component; add a new one by appending to the $resources array
|     (name = path = resource key).
|--------------------------------------------------------------------------
*/

// Guest — admin login
Route::livewire('/login', 'admin::admin-login.index')->name('login');

// Signed-in admin area
Route::middleware(['auth:admin'])->group(function () {
    Route::livewire('/', 'admin::dashboard.index')->name('dashboard');
    Route::livewire('/analytics', 'admin::analytics.index')->name('analytics');
    Route::livewire('/menus', 'admin::menus.index')->name('menus');

    // Generic CRUD resources — all served by the shared resource-manager.
    $resources = [
        'appointments',
        'contact-submissions',
        'doctors',
        'departments',
        'services',
        'health-packages',
        'blogs',
        'authors',
        'gallery',
        'hero-slides',
        'quick-actions',
        'stats',
        'testimonials',
        'stories',
        'treatments',
        'technologies',
        'awards',
        'insurance',
        'faqs',
        'job-openings',
        'job-applications',
        'pages',
        'settings',
        'admin-users',
    ];

    foreach ($resources as $resource) {
        Route::livewire("/{$resource}", 'admin::resource-manager.index')
            ->defaults('resource', $resource)
            ->name($resource);
    }
});

// Logout (POST only — no auth needed, it just ends the session)
Route::post('/logout', function () {
    auth('admin')->logout();

    return redirect('/admin/login');
})->name('logout');
