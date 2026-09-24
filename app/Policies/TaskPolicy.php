<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * مشاهده تسک: مالک پروژه، ایجادکننده تسک، یا مسئول انجام آن.
     */
    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->project->owner_id
            || $user->id === $task->created_by
            || $user->id === $task->assigned_to;
    }

    /**
     * ایجاد تسک جدید.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * ویرایش کامل تسک (تغییر عنوان، توضیحات، مسئول و ...): مالک پروژه یا ایجادکننده تسک.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->project->owner_id
            || $user->id === $task->created_by;
    }

    /**
     * تغییر وضعیت سریع تسک (مانند درگ‌وانددراپ در بورد کانبان): افرادی که دسترسی مشاهده دارند.
     */
    public function updateStatus(User $user, Task $task): bool
    {
        return $this->view($user, $task);
    }

    /**
     * حذف تسک: مالک پروژه یا ایجادکننده تسک.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->project->owner_id
            || $user->id === $task->created_by;
    }
}
