<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['list_id', 'title', 'description', 'deadline', 'priority', 'is_completed'])]
class Task extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'is_completed' => 'boolean',
        ];
    }

    // Alias: dipakai Programmer 3 (collaboration view)
    public function taskList()
    {
        return $this->belongsTo(TaskList::class, 'list_id');
    }

    // Alias: dipakai Programmer 2 (dashboard upcoming tasks)
    public function list()
    {
        return $this->belongsTo(TaskList::class, 'list_id');
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }
}
