<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'user_id'])]
class TaskList extends Model
{
    protected $table = 'lists';

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'list_user', 'list_id', 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'list_id');
    }
}