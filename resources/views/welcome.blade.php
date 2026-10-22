<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>سیستم مدیریت تسک B2B</title>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;600;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50 dark:bg-gray-900 dark:text-gray-100 flex items-center justify-center min-h-screen">

<div class="max-w-md w-full px-6 py-12 bg-white dark:bg-gray-800 shadow-xl rounded-2xl text-center border border-gray-100 dark:border-gray-700 transition-colors duration-300">

    <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
    </div>

    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2">مدیریت تسک سازمانی</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">ساخته شده با Laravel 13 و Livewire Volt.</p>

    <div class="space-y-4">
        @auth
            <a href="{{ url('/dashboard') }}" class="block w-full py-3 px-4 font-semibold text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 rounded-xl transition">
                ورود به داشبورد
            </a>
        @else
            <a href="{{ route('login') }}" class="block w-full py-3 px-4 font-semibold text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 rounded-xl transition shadow-md">
                ورود به سیستم
            </a>
            <a href="{{ route('register') }}" class="block w-full py-3 px-4 font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 dark:text-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-xl transition">
                ثبت‌نام کاربر جدید
            </a>
        @endauth
    </div>

</div>

</body>
</html>
