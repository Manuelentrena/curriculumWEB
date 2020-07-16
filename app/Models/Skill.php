<?php
/* Usamos el espacio de nombres Models */
namespace App\Models;
use Illuminate\Database\Eloquent\Model; /* Importamos Eloquent ORM */

class Skill extends Model { /* Extendemos el modelo en nuestra clase */

  protected $primaryKey = 'id';
  /* public $incrementing = false; */
  protected $table = "habilidades"; 
  protected $fillable = [
    'nombre',
    'tipo',
    'nivel',
  ];
  public $timestamps = false; /* TODO: Para no necesitar las columnas created_at updated_at  */
}

?>