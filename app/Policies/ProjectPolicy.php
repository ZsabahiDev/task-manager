<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
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
