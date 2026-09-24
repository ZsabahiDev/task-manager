<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Category;
use App\Models\Task;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ۱. ساخت کاربر اصلی
        $testUser = User::factory()->create([
            'name' => 'Ali User',
            'email' => 'ali@example.com',
            'password' => bcrypt('password'),
        ]);

        // ۲. ساخت همکاران
        $otherUsers = User::factory(5)->create();
        $allUsers = $otherUsers->concat([$testUser]);

        // ۳. خلق پروژه‌ها و وابستگی‌ها
        Project::factory(3)->create([
            'owner_id' => $testUser->id,
        ])->each(function ($project) use ($allUsers, $testUser) {

            $categories = Category::factory(4)->create([
                'project_id' => $project->id,
            ]);

            $tags = Tag::factory(4)->create([
                'project_id' => $project->id,
            ]);

            Task::factory(10)->create([
                'project_id' => $project->id,
                'category_id' => fn() => $categories->random()->id,
                'created_by' => $testUser->id,
                'assigned_to' => fn() => $allUsers->random()->id,
            ])->each(function ($task) use ($tags) {
                $task->tags()->attach(
                    $tags->random(rand(1, 3))->pluck('id')
                );
            });
        });
    }
}
