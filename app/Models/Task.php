<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['list_id', 'title', 'description', 'deadline', 'priority', 'is_completed'];

    public function taskList()
    {
        return $this->belongsTo(TaskList::class, 'list_id');
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }
}
