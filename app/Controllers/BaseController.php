<?php

namespace App\Controllers;
use App\Models\User;
/* Usamos libreria para que twig nos devuelve HTML */
use Laminas\Diactoros\Response\HtmlResponse;

class BaseController{

  /* Variables */
  protected $templateEngine;
  protected $message = [];
  protected $classMessage;
  protected $mistakes;

  /* Inicializamos twig template */
  public function __construct(){
    $loader = new \Twig\Loader\FilesystemLoader('../views');
    $this->templateEngine = new \Twig\Environment($loader,[
      'debug' => true,
      'cache' => false
    ]);
  }

  public function renderHTML($fileName, $data = []){
    return new HtmlResponse($this->templateEngine->render($fileName, $data));
  }

  /* Funciones GET */
  public function getMessage($message){
    $this->message[] = $message;
  }
  public function getclassMessage($classMessage){
    $this->classMessage = $classMessage;
  }
  public function getMistakes($mistakes){
    $this->mistakes = $mistakes;
  }

  /* Funciones SET */
  public function setMessage(){
    return $this->message;
  }
  public function setClassMessage(){
    return $this->classMessage;
  }
  public function setMistakes(){
    return $this->mistakes;
  }

  /* Otras */
  public function cleanMessage(){
    unset($this->message);
  }
  public function IsAdmin($email){
    $admin = User::where('email', '=', $email)->get();
    if($admin[0]['email']=="super@gmail.com"){
      return true;
    }else{
      return false;
    }
  }
}



?>