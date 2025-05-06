<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;


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
    
    /**
     * Accesseur pour récupérer l'image de profil de l'utilisateur selon son rôle
     */
    public function getProfileImageAttribute()
    {
        $imagePath = null;

        if ($this->isCoach() && $this->coach && $this->coach->profile_image) {
            $imagePath = 'images/' . $this->coach->profile_image;
        } elseif ($this->isStartup() && $this->startup && $this->startup->logo_startup) {
            $imagePath = 'images/' . $this->startup->logo_startup;
        } elseif ($this->isInvestisseur() && $this->investisseur && $this->investisseur->profile_image) {
            $imagePath = 'images/' . $this->investisseur->profile_image;
        } elseif ($this->isAdmin() && $this->admin && $this->admin->profile_image) {
            $imagePath = $this->admin->profile_image;
        }

        if ($imagePath) {
            try {
                return Storage::disk('s3')->temporaryUrl($imagePath, now()->addMinutes(30));
            } catch (\Exception $e) {
                \Log::error('S3 temporary URL error: ' . $e->getMessage());
            }
        }

        // Fallback to default image if no image exists or error occurs
        return "https://eu.ui-avatars.com/api/?background=D43347&color=fff&bold=true&name=" . urlencode($this->name);
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

    public function forumParticipations()
    {
        return $this->hasMany(ForumParticipation::class);
    }

    public function participatedForums()
    {
        return $this->belongsToMany(Forum::class, 'forum_participations')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    /**
     * Get the channels that model events should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel|\Illuminate\Database\Eloquent\Model>
     */
    public function receivesBroadcastNotificationsOn(): string
    {
        return 'App.Models.User.' . $this->id;
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }
}
