<?php

use Livewire\Volt\Component;
use App\Models\Project;
use App\Models\Task;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $taskFile = null; // متغیر برای نگهداری فایل آپلود شده
    public Project $project;
    // متغیرهای فیلتر و جستجو
    public string $search = '';
    public string $filterPriority = '';
    public string $filterStatus = '';
    // وضعیت‌های فرم ساخت و ویرایش تسک
    public ?int $editTaskId = null;
    public string $title = '';
    public string $priority = 'medium';
    public bool $showCreateModal = false;
    public ?int $assigned_to = null;// شناسه کاربر مسئول تسک (اختیاری)
    public ?int $category_id = null; // شناسه دسته‌بندی تسک (اختیاری)
    public array $selected_tags = []; // آرایه شناسه‌های تگ‌های انتخاب‌شده (چندتایی)
    // قواعد اعتبارسنجی
    protected array $rules = [
        'title' => 'required|string|max:255',
        'priority' => 'required|in:low,medium,high',
        'assigned_to' => 'nullable|exists:users,id',
        'category_id' => 'nullable|exists:categories,id',
        'selected_tags' => 'nullable|array',
        'selected_tags.*' => 'exists:tags,id',
        'taskFile' => 'nullable|file|max:2048',
    ];

    public function mount(Project $project): void
    {
        $this->project = $project;
        $this->authorize('view', $this->project);
    }

// 👈 متد جدید برای باز کردن مدال در حالت ویرایش و پر کردن فرم
    public function editTask(Task $task): void
{
        $this->authorize('update', $this->project);

    // نگهداری آی‌دی تسک در حال ویرایش
        $this->editTaskId = $task->id;
    // پر کردن متغیرهای فرم با اطلاعات موجود تسک
        $this->title = $task->title;
        $this->priority = $task->priority;
        $this->category_id = $task->category_id;
        $this->assigned_to = $task->assigned_to;

    // گرفتن تگ‌های متصل به این تسک و ریختن در آرایه
        $this->selected_tags = $task->tags->pluck('id')->toArray();

    // باز کردن مدال
        $this->showCreateModal = true;
}

// متد واحد برای ساخت یا ویرایش تسک
//   public function saveTask(): void
//{
//    $this->authorize('update', $this->project);
//    $this->validate();
//
//    if ($this->editTaskId) {
//        // --- حالت ویرایش (Update) ---
//        $task = Task::findOrFail($this->editTaskId);
//        $this->authorize('update', $task);
//
//        $task->update([
//            'title'       => $this->title,
//            'priority'    => $this->priority,
//            'category_id' => $this->category_id ?: null,
//            'assigned_to' => $this->assigned_to ?: null,
//        ]);
//
//        // همگام‌سازی تگ‌ها (حذف تگ‌های قبلی و اتصال جدیدها)
//        $task->tags()->sync($this->selected_tags);
//
//    } else {
//        // --- حالت ساخت جدید (Create) ---
//        $task = $this->project->tasks()->create([
//            'title'       => $this->title,
//            'priority'    => $this->priority,
//            'status'      => 'todo',
//            'created_by'  => auth()->id(),
//            'category_id' => $this->category_id ?: null,
//            'assigned_to' => $this->assigned_to ?: null,
//        ]);
//
//        if (!empty($this->selected_tags)) {
//            $task->tags()->attach($this->selected_tags);
//        }
//    }
//
//    // ریست کردن فرم و بستن مدال
//    $this->resetForm();
//}

    public function saveTask(): void
    {
        $this->authorize('update', $this->project);
        $this->validate();

        // بررسی و آپلود فایل (اگر فایلی انتخاب شده باشد)
        $filePath = null;
        if ($this->taskFile) {
            // فایل در مسیر storage/app/public/tasks ذخیره می‌شود
            $filePath = $this->taskFile->store('tasks', 'public');
        }

        if ($this->editTaskId) {
            // --- حالت ویرایش ---
            $task = Task::findOrFail($this->editTaskId);
            $this->authorize('update', $task);

            $updateData = [
                'title'       => $this->title,
                'priority'    => $this->priority,
                'category_id' => $this->category_id ?: null,
                'assigned_to' => $this->assigned_to ?: null,
            ];

            // فقط اگر فایل جدیدی آپلود شده بود، مسیر آن را آپدیت کن
            if ($filePath) {
                $updateData['file_path'] = $filePath;
            }

            $task->update($updateData);
            $task->tags()->sync($this->selected_tags);

        } else {
            // --- حالت ساخت جدید ---
            $task = $this->project->tasks()->create([
                'title'       => $this->title,
                'priority'    => $this->priority,
                'status'      => 'todo',
                'created_by'  => auth()->id(),
                'category_id' => $this->category_id ?: null,
                'assigned_to' => $this->assigned_to ?: null,
                'file_path'   => $filePath, // 👈 مسیر فایل در دیتابیس ذخیره می‌شود
            ]);

            if (!empty($this->selected_tags)) {
                $task->tags()->attach($this->selected_tags);
            }
        }

        $this->resetForm();
    }

    public function resetForm(): void
    {
        // taskFile به لیست پاکسازی اضافه شد
        $this->reset(['editTaskId', 'title', 'priority', 'category_id', 'assigned_to', 'selected_tags', 'showCreateModal', 'taskFile']);
    }

    // متد برای آماده‌سازی فرمِ ساخت تسک جدید
    public function openCreateForm(): void
    {
        $this->reset(['editTaskId', 'title', 'priority', 'category_id', 'assigned_to', 'selected_tags']);
        $this->showCreateModal = true;
    }

// متد کمکی برای پاکسازی و بستن فرم
//    public function resetForm(): void
//    {
//        $this->reset(['editTaskId', 'title', 'priority', 'category_id', 'assigned_to', 'selected_tags', 'showCreateModal']);
//    }

    // اکشن تغییر وضعیت تسک (تکمیل / در حال انجام)
    public function toggleTask(Task $task): void
    {
        $this->authorize('update', $this->project);

        // 👈 حل باگ اول: استفاده از مقادیر مجاز enum دیتابیس ('done' و 'todo')
        $task->update([
            'status' => $task->status === 'done' ? 'todo' : 'done',
        ]);
    }

    // اکشن حذف تسک
    public function deleteTask(Task $task): void
    {
        $this->authorize('update', $this->project);

        $task->delete();
    }

    // پاس دادن تسک‌ها به ویو
    public function with(): array
    {
        return [
            //           اینجا به لاراول میگیم لطفاً دسته‌بندی، همکار و تگ‌ها را همون اول با متد with بیار
            'tasks' => $this->project->tasks()
                ->with(['category', 'assignee', 'tags'])
                // جستجو در عنوان تسک‌ها
                ->when($this->search, function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%');
                })

                // فیلتر بر اساس اولویت
                ->when($this->filterPriority, function ($query) {
                    $query->where('priority', $this->filterPriority);
                })

                // فیلتر بر اساس وضعیت
                ->when($this->filterStatus, function ($query) {
                    $query->where('status', $this->filterStatus);
                })
                ->latest()
                ->get(),
            'categories' =>$this->project->categories()->latest()->get(),
            'tags' =>$this->project->tags()->latest()->get(),
            'users' => \App\Models\User::query()->select('id', 'name')->get(),
        ];
    }
}; ?>

<div class="p-6">
    {{-- هدر پروژه --}}
    <div class="flex items-center justify-between pb-6 mb-6 border-b border-gray-200">
        <div>
            <a href="{{ route('dashboard') }}" wire:navigate class="text-sm font-medium text-gray-500 hover:text-gray-700 transition">
                ← بازگشت به پروژه‌ها
            </a>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">{{ $project->title }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $project->description ?? 'بدون توضیحات' }}</p>
        </div>

        <button
            wire:click="openCreateForm"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition"
        >
            + تسک جدید
        </button>
    </div>
    {{-- نوار ابزار فیلتر و جستجو (جادوی Real-time) --}}
    <div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center">
        {{-- باکس جستجو --}}
        <div class="flex-1">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="🔍 جستجو در عنوان تسک‌ها..."
            >
        </div>

        {{-- فیلتر وضعیت --}}
        <div class="w-full md:w-48">
            <select wire:model.live="filterStatus" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">همه وضعیت‌ها</option>
                <option value="todo">برای انجام</option>
                <option value="done">انجام شده</option>
            </select>
        </div>

        {{-- فیلتر اولویت --}}
        <div class="w-full md:w-48">
            <select wire:model.live="filterPriority" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">همه اولویت‌ها</option>
                <option value="high">فوری / بالا</option>
                <option value="medium">متوسط</option>
                <option value="low">عادی</option>
            </select>
        </div>

    </div>
    {{-- لیست تسک‌ها --}}
    <div class="space-y-3">
        @forelse($tasks as $task)
            <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:border-gray-300 transition">
                <div class="flex items-center gap-3">
                    {{-- چک‌باکس تغییر وضعیت --}}
                    <input
                        type="checkbox"
                        wire:click="toggleTask({{ $task->id }})"
                        {{ $task->status === 'done' ? 'checked' : '' }}
                        class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer"
                    >

                    <span class="text-sm font-medium {{ $task->status === 'done' ? 'line-through text-gray-400' : 'text-gray-800' }}">
                        {{ $task->title }}
                    </span>
                    {{-- نمایش اطلاعات تکمیلی زیر عنوان تسک --}}
                    <div class="flex items-center gap-2 mt-2">
                        @if($task->category)
                            <span class="text-xs font-medium text-gray-500 border border-gray-200 rounded px-1.5 py-0.5">
                                📁 {{ $task->category->name }}
                            </span>
                        @endif

                        @if($task->assignee)
                            <span class="text-xs font-medium text-indigo-600 bg-indigo-50 rounded px-1.5 py-0.5">
                                👤 {{ $task->assignee->name }}
                            </span>
                        @endif

                        @foreach($task->tags as $tag)
                            <span class="text-xs font-medium rounded-full px-2 py-0.5" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                #{{ $tag->name }}
                            </span>
                        @endforeach
                            {{-- لینک فایل ضمیمه --}}
                            @if($task->file_path)
                                <a href="{{ Storage::url($task->file_path) }}" target="_blank" class="text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded px-1.5 py-0.5 hover:underline">
                                    📎 مشاهده فایل
                                </a>
                            @endif
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    {{-- نشانگر اولویت تسک --}}
                    @if($task->priority === 'high')
                        <span class="px-2.5 py-0.5 text-xs font-semibold text-red-700 bg-red-100 rounded-full">فوری</span>
                    @elseif($task->priority === 'medium')
                        <span class="px-2.5 py-0.5 text-xs font-semibold text-amber-700 bg-amber-100 rounded-full">متوسط</span>
                    @else
                        <span class="px-2.5 py-0.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-full">عادی</span>
                    @endif
                    {{-- دکمه ویرایش تسک --}}
                    <button
                        wire:click="editTask({{ $task->id }})"
                        class="p-1 text-gray-400 hover:text-indigo-600 transition"
                        title="ویرایش تسک"
                    >✎
                    </button>

                    {{-- دکمه حذف تسک --}}
                    <button
                        wire:click="deleteTask({{ $task->id }})"
                        wire:confirm="آیا از حذف این تسک اطمینان دارید؟"
                        class="p-1 text-gray-400 hover:text-red-600 transition"
                        title="حذف تسک"
                    >
                        ✕
                    </button>
                </div>
            </div>
        @empty
            <div class="py-12 text-center bg-white border border-dashed border-gray-300 rounded-xl">
                <p class="mb-2 text-gray-500">هنوز هیچ تسکی برای این پروژه ثبت نشده است.</p>
                <button
                    wire:click="$set('showCreateModal', true)"
                    class="text-sm font-medium text-indigo-600 hover:underline"
                >
                    اولین تسک را اضافه کنید
                </button>
            </div>
        @endforelse
    </div>

    {{-- مدال ساخت تسک جدید --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="w-full max-w-md p-6 bg-white rounded-xl shadow-xl">
                <h2 class="mb-4 text-xl font-bold text-gray-800">{{ $editTaskId ? 'ویرایش تسک' : 'افزودن تسک جدید' }}</h2>
                <form wire:submit="saveTask" class="space-y-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">عنوان تسک</label>
                        <input
                            type="text"
                            wire:model="title"
                            class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="مثلا: طراحی لاگوی کمپین"
                        >
                        @error('title') <span class="block mt-1 text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">اولویت</label>
                        <select
                            wire:model="priority"
                            class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="low">عادی</option>
                            <option value="medium">متوسط</option>
                            <option value="high">فوری / بالا</option>
                        </select>
                    </div>
                    {{-- انتخاب دسته‌بندی و همکار (در یک ردیف) --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">دسته‌بندی (اختیاری)</label>
                            <select wire:model="category_id" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">بدون دسته‌بندی</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">ارجاع به (اختیاری)</label>
                            <select wire:model="assigned_to" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">هیچ‌کس</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- انتخاب برچسب‌ها (تگ‌ها) --}}
                    @if($tags->count() > 0)
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">برچسب‌ها</label>
                            <div class="flex flex-wrap gap-3">
                                @foreach($tags as $tag)
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:model="selected_tags" value="{{ $tag->id }}" class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="mr-2 text-sm text-gray-700">{{ $tag->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    {{-- آپلود فایل --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">فایل ضمیمه (اختیاری)</label>
                        <input
                            type="file"
                            wire:model="taskFile"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        >
                        <div wire:loading wire:target="taskFile" class="text-xs text-blue-600 mt-1">در حال آپلود...</div>
                        @error('taskFile') <span class="block mt-1 text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            wire:click.prevent="resetForm(false)"
                            class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition"
                        >
                            انصراف
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition"
                        >
                            {{ $editTaskId ? 'ذخیره تغییرات' : 'ذخیره تسک' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
