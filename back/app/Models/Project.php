<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model {
    protected $fillable = ['user_id','name','language','topic','abgeschlossen','total_tasks','completed_tasks','exercise_data','ki_feedback'];
    protected $casts    = ['abgeschlossen'=>'boolean','exercise_data'=>'array'];

    public function user() { return $this->belongsTo(User::class); }
}
