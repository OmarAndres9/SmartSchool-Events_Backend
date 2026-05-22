<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, HasRoles, Notifiable;

    protected $guard_name = 'api';

    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo_documento',
        'documento',
        'recordatorio_email',
        'recordatorio_anticipacion_minutos',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'                => 'datetime',
            'password'                         => 'hashed',
            'recordatorio_email'               => 'boolean',
            'recordatorio_anticipacion_minutos' => 'integer',
        ];
    }

    // ── Relaciones ────────────────────────────────────────────────────────────
    public function eventosInscritos()
    {
        return $this->belongsToMany(Eventos::class, 'evento_inscripciones', 'user_id', 'evento_id')
            ->withPivot('estado')
            ->withTimestamps();
    }

    public function eventosFavoritos()
    {
        return $this->belongsToMany(Eventos::class, 'evento_favoritos', 'user_id', 'evento_id')
            ->withTimestamps();
    }

    public function valoraciones()
    {
        return $this->hasMany(EventoValoracion::class, 'user_id');
    }

    public function notas()
    {
        return $this->hasMany(Nota::class, 'estudiante_id');
    }

    public function materiasDicta()
    {
        return $this->hasMany(Materia::class, 'docente_id');
    }

    public function estudiantesAsociados()
    {
        return $this->belongsToMany(User::class, 'representante_estudiantes', 'representante_id', 'estudiante_id');
    }

    public function representantes()
    {
        return $this->belongsToMany(User::class, 'representante_estudiantes', 'estudiante_id', 'representante_id');
    }

    public function citasSolicitadas()
    {
        return $this->hasMany(Cita::class, 'solicitante_id');
    }

    public function citasRecibidas()
    {
        return $this->hasMany(Cita::class, 'destinatario_id');
    }

    // ── JWT ───────────────────────────────────────────────────────────────────
    public function getJWTIdentifier()       { return $this->getKey(); }
    public function getJWTCustomClaims()     { return []; }

    // FIX: usar notificación personalizada que apunta al frontend React
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
