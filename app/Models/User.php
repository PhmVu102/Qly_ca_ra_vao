<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Department;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

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
            'status' => 'boolean',
            'is_locked' => 'boolean',
        ];
    }

    protected $fillable = [
        'name', 'email', 'password',
        'role_id', 'department_id',
        'employee_code', 'phone', 'avatar',
        'gender', 'birth_date', 'hire_date',
        'status', 'is_locked', 'last_login_at',
    ];

    // Relationship
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Helper methods kiểm tra role
    public function hasRole(string $slug): bool
    {
        return $this->role?->slug === $slug;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function workSchedules()
    {
        return $this->hasMany(WorkSchedule::class);
    }
}
