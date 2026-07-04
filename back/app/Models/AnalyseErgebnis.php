<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyseErgebnis extends Model {
    protected $table    = 'analyse_ergebnis';
    protected $fillable = ['user_id','language','gelernt','verbessern','noch_lernen'];

    public function user() { return $this->belongsTo(User::class); }
}
