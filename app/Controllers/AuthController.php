<?php

namespace App\Controllers;
use App\Models\User;
use App\Models\Person;
use Laminas\Diactoros\Response\RedirectResponse;

class AuthController extends BaseController{
  
  protected $dataForm = [];
  
  public function getLoginAction($request){
    
    return $this->renderHTML('login.twig');

  }

  public function getLogoutAction($request){
    
    unset($_SESSION['userId']);
    unset($_SESSION['id']);
    return new RedirectResponse('login');

  }

  public function postLoginAction($request){
    $this->cleanMessage();

    $postData = $request->getParsedBody();

    $user = User::where('email', $postData['email'])->first();
    if($user){
      if(password_verify($postData['password'],$user->password)){
        $_SESSION['userId'] = $user->email;
        $person  = Person::where('email', $postData['email'])->first();
        $_SESSION['id'] = $person->id;
        return new RedirectResponse('add/person');
      }
    }
    $this->getMessage("Usuario o contraseña no correctos");
    $this->getClassMessage("danger");
    $this->getDataForm($postData);
    return $this->renderHTML('login.twig',[
      'mensaje' => $this->setMessage(),
      'classMensaje' => $this->setClassMessage(),
      'dataForm' => $this->setDataForm(),
    ]);
    

  }

  public function getProtected(){
    return $this->renderHTML('protected.twig');
  }

  /* Funciones GET */
  public function getDataForm($data){
    $this->dataForm = $data;
  }
  /* Funciones SET */
  public function setDataForm(){
    return $this->dataForm;
  }

}

?>