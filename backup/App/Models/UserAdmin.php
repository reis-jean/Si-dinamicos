<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class UserAdmin extends Model
{
    protected $table = 'user_admins';
    protected $fillable = [
        'name', 'email', 'password', 'updated_at', 'created_at'
    ];
   
}