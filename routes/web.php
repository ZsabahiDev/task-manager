<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


Route::redirect('/', '/dashboard');
// گروه‌بندی روت‌های نیازمند احراز هویت
Route::middleware(['auth', 'verified'])->group(function () {
    // داشبورد اصلی: لیست پروژه‌ها (کامپوننت Volt)
    Volt::route('/dashboard', 'project-list')->name('dashboard');

    // صفحه اختصاصی تسک‌های هر پروژه
    Volt::route('/projects/{project}', 'project-tasks')->name('projects.show');
    // مدیریت پروفایل
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
