<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['list_id', 'title', 'description', 'deadline', 'priority', 'is_completed'])]
class Task extends Model
{
    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'is_completed' => 'boolean',
        ];
    }

    public function list()
    {
        return $this->belongsTo(TaskList::class, 'list_id');
    }
}