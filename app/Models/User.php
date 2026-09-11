<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // List yang dimiliki user (sebagai pemilik)
    public function lists()
    {
        return $this->hasMany(TaskList::class, 'user_id');
    }

    // List yang diikuti sebagai kolaborator dengan pivot role (Programmer 3 - SRS-7)
    public function sharedLists()
    {
        return $this->belongsToMany(TaskList::class, 'list_user', 'user_id', 'list_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    // Alias tanpa pivot (Programmer 2)
    public function joinedLists()
    {
        return $this->belongsToMany(TaskList::class, 'list_user', 'user_id', 'list_id');
    }

    /**
     * Cek apakah user memiliki peran admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user memiliki peran user biasa.
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}
