<?php

namespace App\Controllers;
use App\Models\{User,Person};
use Laminas\Diactoros\Response\RedirectResponse;

class JoinController extends BaseController{
  
  protected $user ;
  
  public function getjoinAction($request){

    return $this->renderHTML('join.twig');
  }

  public function getcreateAccountAction($request){
    $this->cleanMessage();
    $postData = $request->getParsedBody();

    $joinValidator = new ValidatorController("join",$postData);
    $this->getMistakes($joinValidator->error);

    if(empty($this->setMistakes())){
      if($this->isSameEmail($postData["email"],$postData["emailRepeat"])!=true || $this->isSamePassword($postData["password"],$postData["passwordRepeat"])!=true){
        if($this->isSameEmail($postData["email"],$postData["emailRepeat"])!=true){
          $this->getMessage("Email no coincide");
        }
        if($this->isSamePassword($postData["password"],$postData["passwordRepeat"])!=true){
          $this->getMessage("Contraseña no coincide");
        }
        $this->getClassMessage("danger");
      }else{

        $this->getUser($postData["email"]);
        if(empty($this->setUser()->toarray()[0])){
          /* Creamos usuario */
          $user = new User();
          $user->email = $postData['email'];
          $user->password = password_hash($postData['password'], PASSWORD_DEFAULT);
          $user->save();
          $person = new Person();
          $person->email = $postData['email'];
          $person->nombre = $postData['name'];
          $person->save();

          /* Abrimos perfil */
          $_SESSION['userId'] = $user->email;
          $person  = Person::where('email', $postData['email'])->first();
          $_SESSION['id'] = $person->id;
          return new RedirectResponse('add/person');
        }else{
         $this->getClassMessage("danger");
         $this->getMessage("Ya existe una cuenta con <strong>".$postData['email']."</strong>");
        }
        
      }

    }else{
      foreach ($this->setMistakes() as $mistake) {
        $this->getMessage($mistake);
      }
      $this->getClassMessage("danger");
    }

    return $this->renderHTML('join.twig',[
        'mensaje' => $this->setMessage(),
        'classMensaje' => $this->setClassMessage(),
        'listForm' => $postData
    ]);
    
  }

  public function isSameEmail($email1,$email2){
    if($email1==$email2){
      return true;
    }else{
      return false;
    }
  }

  public function isSamePassword($password1,$password2){
    if($password1==$password2){
      return true;
    }else{
      return false;
    }
  }

  /* Funciones GET */
  public function getUser($email){
    $this->user = User::where('email', '=', $email)->select('email')->get();
  }

  /* Funciones SET */
  public function setUser(){
    return $this->user;
  }
  

}



?>