<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
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
     * مشاهده دسته‌بندی: فقط مالک پروژه‌ای که دسته‌بندی متعلق به آن است.
     */
    public function view(User $user, Category $category): bool
    {
        return $user->id === $category->project->owner_id;
    }

    /**
     * ایجاد دسته‌بندی: کنترل دسترسی در فرم ساخت براساس مالکیت project_id انجام می‌شود.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * ویرایش دسته‌بندی.
     */
    public function update(User $user, Category $category): bool
    {
        return $user->id === $category->project->owner_id;
    }

    /**
     * حذف دسته‌بندی.
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->id === $category->project->owner_id;
    }
}
