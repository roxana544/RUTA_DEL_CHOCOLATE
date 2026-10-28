<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_admin', 'role', 'phone', 'origin_city', 'marketing_consent'])]
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
            'is_admin' => 'boolean',
        ];
    }

    public const ROLE_TOURIST = 'tourist';
    public const ROLE_GUIDE = 'guide';
    public const ROLE_ADMIN = 'admin';
    public const ROLES = [self::ROLE_TOURIST => 'Turista', self::ROLE_GUIDE => 'Guía / Agencia', self::ROLE_ADMIN => 'Administrador'];

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN || $this->is_admin;
    }

    /** Guías y administradores gestionan eventos (el admin hereda al guía). */
    public function canManageEvents(): bool
    {
        return $this->role === self::ROLE_GUIDE || $this->isAdmin();
    }

    public function memories(): HasMany
    {
        return $this->hasMany(Memory::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function guidedEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'guide_id');
    }

    public function checkpoints(): HasMany
    {
        return $this->hasMany(Checkpoint::class);
    }

    /** Puntos acumulados: checkpoints + bonus por completar toda la ruta. */
    public function routePoints(int $totalFactories): int
    {
        $points = (int) $this->checkpoints()->sum('points');

        if ($totalFactories > 0 && $this->checkpoints()->count() >= $totalFactories) {
            $points += config('checkpoints.completion_bonus');
        }

        return $points;
    }
}
