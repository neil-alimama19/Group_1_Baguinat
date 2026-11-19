<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'first_name', 'middle_name', 'last_name', 'role_id', 'is_active', 'is_admin', 'photo'])]
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
    public function hasRole(string ...$names): bool
    {
        return in_array($this->role?->name, $names, true);
    }

    public function dashboardRoute(): string
    {
        return match ($this->role?->name) {
            'Admin' => 'admin.dashboard',
            'Officer' => 'officer.dashboard',
            'Adviser' => 'adviser.dashboard',
            default => 'student.dashboard',
        };
    }

    public function role()
    {
        // Explicitly tell Laravel to use 'role_id' as the foreign key
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
