<?php

use App\Livewire\Pages;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| The public website. Admin routes live in routes/admin.php (loaded from
| bootstrap/app.php under the `admin` prefix).
|--------------------------------------------------------------------------
*/

Route::livewire('/', Pages\HomepageIndex::class)->name('home');

// Services
Route::livewire('/services', Pages\ServicesIndex::class)->name('services.index');
Route::livewire('/services/{slug}', Pages\ServicesShow::class)->name('services.show');

// Doctors
Route::livewire('/doctors', Pages\DoctorsIndex::class)->name('doctors.index');
Route::livewire('/doctors/{slug}', Pages\DoctorsShow::class)->name('doctors.show');

// Departments
Route::livewire('/departments', Pages\DepartmentsIndex::class)->name('departments.index');
Route::livewire('/departments/{slug}', Pages\DepartmentsShow::class)->name('departments.show');

// Blogs
Route::livewire('/blogs', Pages\BlogsIndex::class)->name('blogs.index');
Route::livewire('/blogs/{slug}', Pages\BlogsShow::class)->name('blogs.show');

// Static content pages
Route::livewire('/health-packages', 'pages::health-packages.index')->name('health-packages');
Route::livewire('/design-guide', 'pages::design-guide.index')->name('design-guide');
Route::livewire('/gallery', Pages\GalleryIndex::class)->name('gallery');
Route::livewire('/careers', Pages\CareersIndex::class)->name('careers');
Route::livewire('/careers/{slug}', Pages\CareersShow::class)->name('careers.show');
Route::livewire('/contact', Pages\ContactIndex::class)->name('contact');
Route::livewire('/appointment', Pages\AppointmentIndex::class)->name('appointment');
Route::livewire('/pages/{slug}', 'pages::page.show')->name('page');
