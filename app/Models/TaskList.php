<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskList extends Model
{
    use HasFactory;

    protected $table = 'lists';
    protected $fillable = ['name', 'user_id'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'list_id');
    }

    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'list_user', 'list_id', 'user_id')->withPivot('role')->withTimestamps();
    }

    public function progressPercentage()
    {
        $total = $this->tasks()->count();
        if ($total === 0) return 0;
        $completed = $this->tasks()->where('is_completed', true)->count();
        return round(($completed / $total) * 100);
    }
}
