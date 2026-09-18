<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'user_id'])]
class TaskList extends Model
{
    use HasFactory;

    protected $table = 'lists';

    // Relasi: pemilik list
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Alias owner (untuk kompatibilitas Programmer 2)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi: kolaborator dengan pivot role (Programmer 3 - SRS-7)
    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'list_user', 'list_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    // Alias: dipakai Programmer 2 (tanpa pivot role)
    public function members()
    {
        return $this->belongsToMany(User::class, 'list_user', 'list_id', 'user_id');
    }

    // Relasi: tasks dalam list
    public function tasks()
    {
        return $this->hasMany(Task::class, 'list_id');
    }

    // Progress percentage (Programmer 3 - SRS-8)
    public function progressPercentage()
    {
        $total = $this->tasks()->count();
        if ($total === 0) return 0;
        $completed = $this->tasks()->where('is_completed', true)->count();
        return round(($completed / $total) * 100);
    }

    protected static function boot()
    {
        // apparently it's needed for something
        parent::boot();

        static::deleting(function (TaskList $list) {
            $list->tasks()->delete();
            $list->collaborators()->detach();
        });
    }
}