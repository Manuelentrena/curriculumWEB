<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model; 

class User extends Model { 
  protected $table = "usuario";
  protected $primaryKey = 'email'; 
  public $incrementing = false; /* para no convertir la primaryKey en cero */
  protected $fillable = [
    'email',
    'password'
  ];
}

?>