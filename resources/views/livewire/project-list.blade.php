<?php

use function Livewire\Volt\{state, with, rules};
use App\Models\Project;

// وضعیت‌های محلی کامپوننت (State)
state([
    'title' => '',
    'description' => '',
    'showCreateModal' => false,
]);

// قواعد اعتبارسنجی فرم
rules([
    'title' => 'required|string|max:255',
    'description' => 'nullable|string|max:1000',
]);

// دریافت لیست پروژه‌های کاربر جاری به همراه تعداد تسک‌ها
with(function () {
    return [
        'projects' => auth()->user()->projects()
            ->withCount('tasks')
            ->latest()
            ->get(),
    ];
});

// اکشن ثبت پروژه جدید
$createProject = function () {
    $this->validate();

    auth()->user()->projects()->create([
        'title' => $this->title,
        'description' => $this->description,
    ]);

    $this->reset(['title', 'description', 'showCreateModal']);
};

?>

<div class="p-6">
    {{-- هدر صفحه داشبورد --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">پروژه‌های من</h1>
            <p class="mt-1 text-sm text-gray-500">پروژه مورد نظر خود را انتخاب کنید یا یک پروژه جدید بسازید.</p>
        </div>

        <button
            wire:click="$set('showCreateModal', true)"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
        >
            + پروژه جدید
        </button>
    </div>

    {{-- گرید لیست پروژه‌ها --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($projects as $project)
            <div class="flex flex-col justify-between p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition">
                <div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-800">
                        {{ $project->title }}
                    </h3>
                    <p class="mb-4 text-sm text-gray-600 line-clamp-2">
                        {{ $project->description ?? 'بدون توضیحات' }}
                    </p>
                </div>

                <div class="flex items-center justify-between pt-4 mt-auto border-t border-gray-100">
                    <span class="text-xs font-medium text-gray-500">
                        {{ $project->tasks_count }} تسک
                    </span>

                    {{-- لینک هدایت به صفحه تسک‌های پروژه --}}
                    <a
                        href="{{ route('projects.show', $project->id) }}"
                        wire:navigate
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition"
                    >
                        ورود به پروژه ←
                    </a>
                </div>
            </div>
        @empty
            <div class="py-12 text-center bg-white border border-dashed border-gray-300 rounded-xl col-span-full">
                <p class="mb-4 text-gray-500">هنوز هیچ پروژه‌ای ایجاد نکرده‌اید.</p>
                <button
                    wire:click="$set('showCreateModal', true)"
                    class="text-sm font-medium text-indigo-600 hover:underline"
                >
                    همین حالا اولین پروژه را بسازید
                </button>
            </div>
        @endforelse
    </div>

    {{-- مدال ساخت پروژه جدید --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="w-full max-w-md p-6 bg-white rounded-xl shadow-xl">
                <h2 class="mb-4 text-xl font-bold text-gray-800">ایجاد پروژه جدید</h2>

                <form wire:submit="createProject" class="space-y-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">عنوان پروژه</label>
                        <input
                            type="text"
                            wire:model="title"
                            class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="مثلا: کمپین اینستاگرام شب عید"
                        >
                        @error('title') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">توضیحات (اختیاری)</label>
                        <textarea
                            wire:model="description"
                            rows="3"
                            class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="توضیحات مختصری درباره این پروژه بنویسید..."
                        ></textarea>
                        @error('description') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end space-x-2 space-x-reverse pt-2">
                        <button
                            type="button"
                            wire:click="$set('showCreateModal', false)"
                            class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition"
                        >
                            انصراف
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition"
                        >
                            ذخیره پروژه
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
