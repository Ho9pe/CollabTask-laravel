<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->user_id || 
                ($task->group_id && $user->groups->contains($task->group_id));
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }
} 