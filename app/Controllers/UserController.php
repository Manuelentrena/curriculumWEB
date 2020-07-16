<?php

namespace App\Controllers;
use App\Models\{User,Person,Social};

class UserController extends BaseController{

  protected $dataUser;
  public $userExit = false;
  protected $listUser = [];
  protected $dataForm = [];
  

  public function getAddUserAction($request){
    
    $this->getUser('all'); //Leemos Usuarios
    $postData = $request->getParsedBody();

    if($request->getMethod()=='GET'){
      if(!empty($this->setUser())){
        $this->getListUser($this->setUser());
      }
      $this->cleanMessage();
      $this->getMessage("Inserte un usuario");
      $this->getClassMessage("dark");
      
    }

    if($request->getMethod()=='POST'){

      $this->cleanMessage();

      /* BUSCAR USUARIO */
      if($postData['mode']=="search"){
        $this->getUser($postData['search']);
        $this->getListUser($this->setUser());
        if(!empty($this->setUser())){
          $this->getMessage("Resultados encontrados");
          $this->getClassMessage("success");
        }else{
          $this->getClassMessage("danger");
          $this->getMessage('No se han encontrado resultados <strong>"'.$postData['search'].'"</strong>');
        }
        $this->getDataForm($postData);
      }
      /* GUARDAR USUARIO */
      if($postData['mode']=="save"){

        $userValidator = new ValidatorController("user",$postData);
        $this->getMistakes($userValidator->error);

        if(empty($this->setMistakes())){
          if($this->userRepeat($this->setUser(),$postData['email'])==true){
            $this->getMessage("Usuario <strong>".$postData['email']."</strong> ya está registrado");
            $this->getClassMessage("danger");
          }else{
            $user = new User();
            $user->email = $postData['email'];
            $user->password = password_hash($postData['password'], PASSWORD_DEFAULT);
            $user->save();
            $person = new Person();
            $person->email = $postData['email'];
            $person->save();
            /* $social = new Social();
            $social->id_persona = $person->id;
            $social->save(); */
            $this->getMessage("Usuario <strong>".$postData['email']."</strong> registrado");
            $this->getClassMessage("success");
            $this->getUser('all');
          }
        }else{
          foreach ($this->setMistakes() as $mistake) {
            $this->getMessage($mistake);
          }
          $this->getClassMessage("danger");
          
        }
        $this->getDataForm($postData);
        $this->getListUser($this->setUser());
      }
      /* EDITAR USUARIO */
      if($postData['mode']=="edit"){

        $userValidator = new ValidatorController("user",$postData);
        $this->getMistakes($userValidator->error);

        if(empty($this->setMistakes())){
          if($postData['email']==$postData['emailorigin']){
            $user = User::find($postData['emailorigin']);
            $user->update(['password' => password_hash($postData['password'], PASSWORD_DEFAULT)]);
            $this->getMessage("Contraseña Modificada del usuario <strong>".$postData['email']."</strong>");
            $this->getClassMessage("success");
            $this->getUser('all');
          }else{
            if($this->userRepeat($this->setUser(),$postData['email'])==true){
              $this->getMessage("Usuario <strong>".$postData['email']."</strong> ya está registrado");
              $this->getClassMessage("danger");
            }else{
              $person = Person::where('email', $postData['emailorigin'])->update(['email' => $postData['email']]);

              $user = User::find($postData['emailorigin']);
              $user->update(['email' => $postData['email']]);

              /* $_SESSION['userId']=$postData['email']; */ /* TODO: cambiamos la sesion, tener cuidado */

              if ($user->password!= $postData['password']){
                $user->update(['password' => password_hash($postData['password'], PASSWORD_DEFAULT)]);
                $this->getMessage("Contraseña Modificada del usuario <strong>".$postData['email']."</strong>");
              }
              $this->getMessage("Email Modificado del usuario <strong>".$postData['emailorigin']."</strong> al nuevo email <strong>".$postData['email']."</strong>");
              $this->getClassMessage("success");
              $this->getUser('all');
            }
          }
        }else{
          foreach ($this->setMistakes() as $mistake) {
            $this->getMessage($mistake);
          }
          $this->getClassMessage("danger");
        }
        $this->getListUser($this->setUser());
      }
      /* BORRAR USUARIO */
      if($postData['mode']=="delete"){
        $userDelete = User::find($postData['email']);
        $userDelete->delete();
        $person = Person::where('email', $postData['email']);
        $person->delete();
        $avatar = "../public/uploads/".$postData['email'].".jpg";
        if(file_exists($avatar)){
          unlink($avatar);
        }
        $this->getMessage("Usuario <strong>".$postData['email']."</strong> eliminado");
        $this->getClassMessage("success");
        $this->getUser('all');
        $this->getListUser($this->setUser());
      }
    }

    return $this->renderHTML('addUser.twig',[
        'mensaje' => $this->setMessage(),
        'classMensaje' => $this->setClassMessage(),
        'listUser' => $this->setListUser(),
        'dataForm' => $this->setDataForm(),
        'admin' => $this->isAdmin($_SESSION['userId']),
        'user' => $_SESSION['userId']
    ]);

  }

  /* Funciones GET */
  public function getUser($data){
    if($data=="all"){
      $this->dataUser = User::get();
    }else{
      $this->dataUser = User::where('email', 'LIKE', '%'.$data.'%')->get();
    }
  }

  public function getListUser($data){
    $this->listUser = $data;
  }

  public function getDataForm($data){
    $this->dataForm = $data;
  }

  /* Funciones SET */
  public function setUser(){
    return $this->dataUser->toarray();
  }

  public function setListUser(){
    return $this->listUser;
  }

  public function setDataForm(){
    return $this->dataForm;
  }

  /* Otras Funciones */
  public function userRepeat($listUser,$user){
    //Comprobamos que email no sea repetido
    foreach( $listUser as $user2 ){
      if($user == $user2['email']){
        return true;
      }
    }
    return false;
  }
}

?>