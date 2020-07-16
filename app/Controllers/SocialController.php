<?php
  namespace App\Controllers;
  use App\Models\Social;

  class SocialController extends BaseController{

    protected $social=[];
    protected $num;

    public function getAddSocialAction($request){

      $this->getSocial();
      $this->getSocialnum();
      $postData = $request->getParsedBody();
      $this->cleanMessage();

      if($request->getMethod()=='GET'){

        if(empty($this->setSocialnum())){
          
          $this->getMessage('No tienes ninguna red social. Inserte una.');
        }else{
          $this->getMessage('Tienes <strong>'.$this->setSocialnum().'</strong> redes');
          
        }
        $this->getClassMessage("dark");
      }

      if($request->getMethod()=='POST'){

        if($postData['mode']=="save"){

          $userValidator = new ValidatorController("social",$postData);
          $this->getMistakes($userValidator->error);

          if(empty($this->setMistakes())){
            if($this->socialRepeat($this->setSocial(),$postData['tipo'][0])==true){
              $social = Social::where('id_persona', $_SESSION['id'])->where('nombre', $postData['tipo'][0]);
              $social->update(['url' => $postData['link']]);
              $this->getMessage('Red Social <strong>'.$postData['tipo'][0].'</strong> actualizada');
            }else{
              $social = new Social();
              $social->nombre = $postData['tipo'][0];
              $social->url = $postData['link'];
              $social->id_persona = $_SESSION['id'];
              $social->save();
              $this->getMessage('Red Social <strong>'.$postData['tipo'][0].'</strong> registrada');
            }
            $this->getClassMessage("success");
            $this->getSocial();
          }else{
            foreach ($this->setMistakes() as $mistake) {
              $this->getMessage($mistake);
            }
            $this->getClassMessage("danger");
          }

          

        }

        if($postData['mode']=="delete"){
          $social = Social::where('id_persona', $_SESSION['id'])->where('nombre', $postData['nombre']);
          $social->delete();
          $this->getMessage('Red Social <strong>'.$postData['nombre'].'</strong> borrada');
          $this->getClassMessage("success");
          $this->getSocial();
        }

      }

      return $this->renderHTML('addSocial.twig',[
        'mensaje' => $this->setMessage(),
        'classMensaje' => $this->setClassMessage(),
        'listSocial'  => $this->setSocial(),
        'user' => $_SESSION['userId'],
        'admin' => $this->isAdmin($_SESSION['userId'])
      ]);
    } 
    /* Funciones GET */
    public function getSocial(){
      $this->social  = Social::where('id_persona', $_SESSION['id'])->get();
    }

    public function getSocialnum(){
      $this->num  = Social::where('id_persona', $_SESSION['id'])->count();
    }

    /* Funciones SET */
    public function setSocial(){
      return $this->social;
    }

    public function setSocialnum(){
      return $this->num;
    }

    public function socialRepeat($listSocial,$social){
    //Comprobamos que email no sea repetido
    foreach( $listSocial as $social2 ){
      if($social == $social2['nombre']){
        return true;
      }
    }
    return false;
  }

  }

?> 