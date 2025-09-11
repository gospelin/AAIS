<?php
// app/Models/UserSessionPreference.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSessionPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'current_term'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(AcademicSession::class);
    }
}
