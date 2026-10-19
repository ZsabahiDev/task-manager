<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;

class TagPolicy
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
     * مشاهده تگ: فقط مالک پروژه‌ای که تگ متعلق به آن است.
     */
    public function view(User $user, Tag $tag): bool
    {
        return $user->id === $tag->project->owner_id;
    }

    /**
     * ایجاد تگ.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * ویرایش تگ.
     */
    public function update(User $user, Tag $tag): bool
    {
        return $user->id === $tag->project->owner_id;
    }

    /**
     * حذف تگ.
     */
    public function delete(User $user, Tag $tag): bool
    {
        return $user->id === $tag->project->owner_id;
    }
}
