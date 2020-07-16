<?php
/* Usamos el espacio de nombres Models */
namespace App\Models;
use Illuminate\Database\Eloquent\Model; /* Importamos Eloquent ORM */

class Skill_Project extends Model { /* Extendemos el modelo en nuestra clase */

  protected $primaryKey = ['id_hab', 'id_pro'];
  public $incrementing = false;
  
  protected $table = "hab_pro"; 
  protected $fillable = [
    'id_hab',
    'id_pro',
  ];
  public $timestamps = false;
}

?>