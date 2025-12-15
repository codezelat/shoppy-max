<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'branch',
        'return_fee',
        'courier_id',
        'parent_id',
        'user_type',
        'phone', // Assuming phone was added or existed, checking migration, users table usually doesn't have phone by default in Laravel unless added. 
                 // Wait, the prompt asked for "Phone Number" in the form. Standard users table doesn't have it.
                 // I missed adding 'phone' to the users table migration!
                 // I should check if 'phone' exists. The migration I just ran didn't add it.
                 // I will add it to fillable, and creating a new migration for phone number is needed if not present.
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'return_fee' => 'decimal:2',
        ];
    }

    public function subResellers()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function targets()
    {
        return $this->hasMany(ResellerTarget::class);
    }

    public function payments()
    {
        return $this->hasMany(ResellerPayment::class);
    }
}
