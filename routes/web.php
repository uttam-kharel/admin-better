<?php

use App\Livewire\Pages;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::livewire('/', Pages\HomepageIndex::class)->name('home');

Route::livewire('/services', Pages\ServicesIndex::class)->name('services.index');
Route::livewire('/services/{slug}', Pages\ServicesShow::class)->name('services.show');

Route::livewire('/doctors', Pages\DoctorsIndex::class)->name('doctors.index');
Route::livewire('/doctors/{slug}', Pages\DoctorsShow::class)->name('doctors.show');

Route::livewire('/departments', Pages\DepartmentsIndex::class)->name('departments.index');
Route::livewire('/departments/{slug}', Pages\DepartmentsShow::class)->name('departments.show');

Route::livewire('/blogs', Pages\BlogsIndex::class)->name('blogs.index');
Route::livewire('/blogs/{slug}', Pages\BlogsShow::class)->name('blogs.show');

Route::livewire('/health-packages', 'pages::health-packages.index')->name('health-packages');
Route::livewire('/gallery', Pages\GalleryIndex::class)->name('gallery');
Route::livewire('/careers', Pages\CareersIndex::class)->name('careers');
Route::livewire('/careers/{slug}', Pages\CareersShow::class)->name('careers.show');
Route::livewire('/contact', Pages\ContactIndex::class)->name('contact');
Route::livewire('/appointment', Pages\AppointmentIndex::class)->name('appointment');
Route::livewire('/pages/{slug}', 'pages::page.show')->name('page');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::livewire('/login', 'admin::admin-login.index')->name('login');

    Route::middleware(['auth:admin'])->group(function () {
        Route::livewire('/', 'admin::dashboard.index')->name('dashboard');
        Route::livewire('/analytics', 'admin::analytics.index')->name('analytics');
        Route::livewire('/appointments', 'admin::resource-manager.index')->defaults('resource', 'appointments')->name('appointments');
        Route::livewire('/contact-submissions', 'admin::resource-manager.index')->defaults('resource', 'contact-submissions')->name('contact-submissions');
        Route::livewire('/doctors', 'admin::resource-manager.index')->defaults('resource', 'doctors')->name('doctors');
        Route::livewire('/departments', 'admin::resource-manager.index')->defaults('resource', 'departments')->name('departments');
        Route::livewire('/services', 'admin::resource-manager.index')->defaults('resource', 'services')->name('services');
        Route::livewire('/health-packages', 'admin::resource-manager.index')->defaults('resource', 'health-packages')->name('health-packages');
        Route::livewire('/blogs', 'admin::resource-manager.index')->defaults('resource', 'blogs')->name('blogs');
        Route::livewire('/authors', 'admin::resource-manager.index')->defaults('resource', 'authors')->name('authors');
        Route::livewire('/gallery', 'admin::resource-manager.index')->defaults('resource', 'gallery')->name('gallery');
        Route::livewire('/hero-slides', 'admin::resource-manager.index')->defaults('resource', 'hero-slides')->name('hero-slides');
        Route::livewire('/quick-actions', 'admin::resource-manager.index')->defaults('resource', 'quick-actions')->name('quick-actions');
        Route::livewire('/stats', 'admin::resource-manager.index')->defaults('resource', 'stats')->name('stats');
        Route::livewire('/testimonials', 'admin::resource-manager.index')->defaults('resource', 'testimonials')->name('testimonials');
        Route::livewire('/stories', 'admin::resource-manager.index')->defaults('resource', 'stories')->name('stories');
        Route::livewire('/treatments', 'admin::resource-manager.index')->defaults('resource', 'treatments')->name('treatments');
        Route::livewire('/technologies', 'admin::resource-manager.index')->defaults('resource', 'technologies')->name('technologies');
        Route::livewire('/awards', 'admin::resource-manager.index')->defaults('resource', 'awards')->name('awards');
        Route::livewire('/insurance', 'admin::resource-manager.index')->defaults('resource', 'insurance')->name('insurance');
        Route::livewire('/faqs', 'admin::resource-manager.index')->defaults('resource', 'faqs')->name('faqs');
        Route::livewire('/job-openings', 'admin::resource-manager.index')->defaults('resource', 'job-openings')->name('job-openings');
        Route::livewire('/job-applications', 'admin::resource-manager.index')->defaults('resource', 'job-applications')->name('job-applications');
        Route::livewire('/menus', 'admin::menus.index')->name('menus');
        Route::livewire('/pages', 'admin::resource-manager.index')->defaults('resource', 'pages')->name('pages');
        Route::livewire('/settings', 'admin::resource-manager.index')->defaults('resource', 'settings')->name('settings');
        Route::livewire('/admin-users', 'admin::resource-manager.index')->defaults('resource', 'admin-users')->name('admin-users');
    });

    Route::post('/logout', function () {
        auth('admin')->logout();
        return redirect('/admin/login');
    })->name('logout');
});
