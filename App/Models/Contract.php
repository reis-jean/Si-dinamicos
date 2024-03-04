<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Contract extends Model
{
  
    protected $fillable = [
        'name', 'email', 'password', 'database',  'updated_at', 'created_at'
    ];
   
}