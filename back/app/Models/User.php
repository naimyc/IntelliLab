<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name','email','password','rolle','avatar_url'];
    protected $hidden   = ['password','remember_token'];

    protected function casts(): array {
        return ['email_verified_at'=>'datetime','password'=>'hashed'];
    }

    public function projects() {
        return $this->hasMany(Project::class);
    }
    public function analyseErgebnis() {
        return $this->hasMany(AnalyseErgebnis::class);
    }
}
