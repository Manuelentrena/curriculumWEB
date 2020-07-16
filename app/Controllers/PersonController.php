<?php

namespace App\Controllers;
use App\Models\Person;

class PersonController extends BaseController{

  protected $dataPerson;
  protected $dataForm = [];

  public function getAddPersonAction($request){
    
    $this->getPerson();

    if($request->getMethod()=='GET'){
      if(!empty($this->setPersonEmail())){
        $this->getDataForm($this->setPerson());
      }else{
        echo "No exite la persona";
      }
      $this->cleanMessage();
      $this->getMessage("Introduce datos de un usuario nuevo");
      $this->getClassMessage("dark");
    }

    if($request->getMethod()=='POST'){

      $personValidator = new ValidatorController("person",$request->getParsedBody());
      $this->getMistakes($personValidator->error);

      if(empty($this->setMistakes())){ 
        $postData = $request->getParsedBody(); 

        $person = Person::find($this->setPersonID());
        $person->nombre = $postData['nombre'];
        $person->apellido = $postData['apellido'];
        $person->especialidad = $postData['especialidad'];
        $person->telefono = $postData['telefono'];
        $person->descripcion = $postData['descripcion'];
        $person->visible = $postData['visible'];
        $person->avatar = $postData['avatar'];
        $person->save();
        $this->cleanMessage();
        $this->getMessage("Nuevos Datos Añadidos");
        $this->getClassMessage("success");
        $this->getDataForm($postData);
      }else{ 
        $this->cleanMessage();
        foreach ($this->setMistakes() as $mistake) {
          $this->getMessage($mistake);
        }
        $this->getClassMessage("danger");
        $this->getDataForm($request->getParsedBody());
      }
    }

    return $this->renderHTML('addPerson.twig',[
      'mensaje' => $this->setMessage(),
      'classMensaje' => $this->setClassMessage(),
      'dataForm' => $this->setDataForm(),
      'admin' => $this->isAdmin($_SESSION['userId']),
      'user' => $_SESSION['userId'],
      'admin' => $this->isAdmin($_SESSION['userId'])
    ]);
  }

  /* Funciones GET */
  public function getPerson(){
    /* $this->dataPerson = Person::get(); */
    $this->dataPerson = Person::where('email', '=',$_SESSION['userId'])->get();
  }

  public function getDataForm($data){
    $this->dataForm = $data;
  }

  /* Funciones SET */
  public function setPerson(){
    return $this->dataPerson[0];
  }
  public function setPersonID(){
    return $this->dataPerson[0]['id'];
  }
  public function setPersonEmail(){
    return $this->dataPerson[0]['email'];
    
  }

  public function setDataForm(){
    return $this->dataForm;
  }

}

?>