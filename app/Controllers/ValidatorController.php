<?php

namespace App\Controllers;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException as exception;

class ValidatorController {

  public $error = NULL;
  public $validator;

  /* Inicializamos validador */
  public function __construct($type,$array){
    $this->validator = $this->createValidator($type); /* Creamos el validador del form */
    $this->validate($array);
  }

  public function validate($array){
    /* Camturamos cualquier posible error */
    try {
      $this->validator->assert($array);
    } catch(exception $exception) {
      $this->getError($exception->getMessages());
    }
    return $this->setError();
  }

  public function getError($error){
    $this->error = $error;
  }

  public function setError(){
    return $this->error;
  }

  public function createValidator($type){
    if($type=="person"){
      return v::key('nombre', v::stringType()->notEmpty()->length(1, 50))
            ->key('apellido', v::stringType()->notEmpty()->length(1, 50))
            ->key('especialidad', v::stringType()->notEmpty()->length(1, 50))
            ->key('telefono', v::Phone())
            ->key('descripcion', v::notEmpty()->length(1, 2000))
            ->key('visible', v::boolVal());
    }
    if($type=="user"){
      return v::key('email', v::email()->notEmpty()->length(1, 200))
            ->key('password', v::notEmpty()->length(1, 255));
    }
    if($type=="social"){
      return v::key('link', v::notEmpty());
    }
    if($type=="language"){
      return v::key('nivel', v::notEmpty());
    }
    if($type=="skill"){
      return v::key('nombre', v::notEmpty())
            ->key('nivel', v::digit()->between(0, 100));
    }
    if($type=="project"){
      return v::key('nombre', v::notEmpty()->length(1, 200))
            ->key('description', v::notEmpty()->length(1, 2000));
    }
    if($type=="work"){
      return v::key('empresa', v::notEmpty()->length(1, 100))
            ->key('puesto', v::notEmpty()->length(1, 100))
            ->key('inicio', v::date())
            ->key('fin', v::date());
    }
    if($type=="task"){
      return v::key('description', v::notEmpty()->length(1, 500));
    }
    if($type=="join"){
      return v::key('email', v::email()->notEmpty()->length(1, 200))
            ->key('emailRepeat', v::email()->notEmpty()->length(1, 200))
            ->key('password', v::notEmpty()->length(1, 255))
            ->key('passwordRepeat', v::notEmpty()->length(1, 255));
    }
  }
}

?>