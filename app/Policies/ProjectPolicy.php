<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    // 👈 این متد به Admin اجازه دسترسی به همه چیز را می‌دهد
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'admin') {
            return true;
        }
        return null; // برای بقیه کاربران، برو سراغ شرط‌های عادی
    }
    /**
     * مشاهده لیست پروژه‌ها یا یک پروژه خاص.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        return $user->id === $project->owner_id;
    }

    /**
     * ایجاد پروژه جدید.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * ویرایش پروژه.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->id === $project->owner_id;
    }

    /**
     * حذف پروژه.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->id === $project->owner_id;
    }
}
