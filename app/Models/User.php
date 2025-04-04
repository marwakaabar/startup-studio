<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', 
        'approved'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'approved' => 'boolean',
        ];
    }
    public function startup(): HasOne
    {
        return $this->hasOne(Startup::class);
    }

    public function coach(): HasOne
    {
        return $this->hasOne(Coach::class);
    }

    public function investisseur(): HasOne
    {
        return $this->hasOne(Investisseur::class);
    }

    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }
    // Méthode pour savoir si l'utilisateur est un Coach
    public function isCoach(): bool
    {
        return $this->role === 'coach';
    }

    // Méthode pour savoir si l'utilisateur est une Startup
    public function isStartup(): bool
    {
        return $this->role === 'startup';
    }

    // Méthode pour savoir si l'utilisateur est un Investisseur
    public function isInvestisseur(): bool
    {
        return $this->role === 'investisseur';
    }

    // Méthode pour savoir si l'utilisateur est un Admin
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }


    public function forums()
    {
        return $this->hasMany(Forum::class);
    }
    
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
