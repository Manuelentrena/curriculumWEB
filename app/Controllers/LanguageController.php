<?php
  namespace App\Controllers;
  use App\Models\Language;

  class LanguageController extends BaseController{

    protected $language=[];
    protected $num;

    public function getAddLanguageAction($request){

      $this->getLanguage();
      $this->getLanguageNum();
      $postData = $request->getParsedBody();
      $this->cleanMessage();

      if($request->getMethod()=='GET'){

        if(empty($this->setLanguageNum())){
          
          $this->getMessage('No tiene ningún idioma. Inserte una.');
        }else{
          $this->getMessage('Tienes <strong>'.$this->setLanguageNum().'</strong> lenguages');
          
        }
        $this->getClassMessage("dark");
      }

      if($request->getMethod()=='POST'){

        if($postData['mode']=="save"){

          $userValidator = new ValidatorController("language",$postData);
          $this->getMistakes($userValidator->error);

          if(empty($this->setMistakes())){
            if($this->languageRepeat($this->setLanguage(),$postData['tipo'][0])==true){
              $language = Language::where('id_persona', $_SESSION['id'])->where('nombre', $postData['tipo'][0]);
              $language->update(['nivel' => $postData['nivel']]);
              $this->getMessage('Idioma <strong>'.$postData['tipo'][0].'</strong> actualizado');
            }else{
              $language = new Language();
              $language->nombre = $postData['tipo'][0];
              $language->nivel = $postData['nivel'];
              $language->id_persona = $_SESSION['id'];
              $language->save();
              $this->getMessage('Idioma <strong>'.$postData['tipo'][0].'</strong> registrado');
            }
            $this->getClassMessage("success");
            $this->getLanguage();
          }else{
            foreach ($this->setMistakes() as $mistake) {
              $this->getMessage($mistake);
            }
            $this->getClassMessage("danger");
          }

        }

        if($postData['mode']=="delete"){
          $language = Language::where('id_persona', $_SESSION['id'])->where('nombre', $postData['nombre']);
          $language->delete();
          $this->getMessage('Idioma <strong>'.$postData['nombre'].'</strong> borrado');
          $this->getClassMessage("success");
          $this->getLanguage();
        }

      }

      return $this->renderHTML('addLanguage.twig',[
        'mensaje' => $this->setMessage(),
        'classMensaje' => $this->setClassMessage(),
        'listLanguage'  => $this->setLanguage(),
        'user' => $_SESSION['userId'],
        'admin' => $this->isAdmin($_SESSION['userId'])
      ]);
    } 
    /* Funciones GET */
    public function getLanguage(){
      $this->language  = Language::where('id_persona', $_SESSION['id'])->get();
    }

    public function getLanguageNum(){
      $this->num  = Language::where('id_persona', $_SESSION['id'])->count();
    }

    /* Funciones SET */
    public function setLanguage(){
      return $this->language->toarray();
    }

    public function setLanguageNum(){
      return $this->num;
    }

    public function languageRepeat($listLanguage,$language){
    //Comprobamos que email no sea repetido
    foreach( $listLanguage as $language2 ){
      if($language == $language2['nombre']){
        return true;
      }
    }
    return false;
  }

  }

?> 