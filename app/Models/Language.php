<?php
/* Usamos el espacio de nombres Models */
namespace App\Models;
use Illuminate\Database\Eloquent\Model; /* Importamos Eloquent ORM */

class Language extends Model { /* Extendemos el modelo en nuestra clase */

  protected $primaryKey = 'id';
  /* public $incrementing = false; */
  protected $table = "idiomas"; 
  protected $fillable = [
    'nombre',
    'nivel',
  ];
  public $timestamps = false; /* TODO: Para no necesitar las columnas created_at updated_at  */
}

?>