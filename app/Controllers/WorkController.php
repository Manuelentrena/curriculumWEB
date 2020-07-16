<?php
  namespace App\Controllers;
  use App\Models\{Work,Task};

  class WorkController extends BaseController{
      
    protected $works=[];
    protected $work=[];
    protected $numWorks;
    protected $tasks=[];

    public function getAddWorkAction($request){

      $this->cleanMessage();
      $this->getWorks();
      $this->getNumWorks();

      if($request->getMethod()=='GET'){

        if (!isset($_GET['id'])) {
          $_GET['id'] = null;
        }

        if($_GET['id']=="new"){ /* Si el una laboral nueva */
          $this->getClassMessage("dark");
          $this->getMessage('Agrega una Experiencia Laboral nueva');  
        }else{
          if(empty($this->setNumWorks())){ /* Si no es nueva y no tiene laboral ninguna */
          $this->getClassMessage("dark");
          $this->getMessage('No tiene ningúna Experiencia Laboral. Inserte uno.');
          }else{
            if(empty($_GET['id'])){ /* tiene laboral y no tiene id */
              $this->getOneWork($this->getIdFirstWork());
              $this->getTasks($this->getIdFirstWork());
              $this->getClassMessage("dark");
              $this->getMessage('Tienes <strong>'.$this->setNumWorks().'</strong> Experiencia Laboral');
            }else{ /* tiene laboral y tiene id */
              $this->getOneWork($_GET['id']);
              $this->getTasks($_GET['id']);
              $this->getClassMessage("dark");
              $this->getMessage('Tienes <strong>'.$this->setNumWorks().'</strong> Experiencia Laboral');
            }
          }
        }
      }
 
      if($request->getMethod()=='POST'){

        $postData = $request->getParsedBody();
        if($postData['mode']=="save"){

          $skillValidator = new ValidatorController("work",$postData);
          $this->getMistakes($skillValidator->error);

          if(empty($this->setMistakes())){

            if($postData['id']=="new"){
              $work = new Work();
              $work->empresa = $postData['empresa'];
              $work->puesto = $postData['puesto'];
              $work->inicio = $postData['inicio'];
              $work->fin = $postData['fin'];
              $work->id_persona = $_SESSION['id'];
              $work->save();
              $this->getWorks();
              $this->getOneWork($work->id);
              $this->getTasks($work->id);
              $this->getClassMessage("success");
              $this->getMessage('Experiencia Laboral en <strong>'.$postData['empresa'].'</strong> registrada');
            }else{
              $work = Work::find($postData['id']);
              $work->update(['empresa' => $postData['empresa']]);
              $work->update(['puesto' => $postData['puesto']]);
              $work->update(['inicio' => $postData['inicio']]);
              $work->update(['fin' => $postData['fin']]);
              $this->getWorks();
              $this->getOneWork($work->id);
              $this->getTasks($work->id);
              $this->getClassMessage("success");
              $this->getMessage('Experiencia Laboral <strong>'.$postData['empresa'].'</strong> modificada');
              
            }

          }else{
            $this->getWorks();
            $this->getOneWork($postData['id']);
            $this->getTasks($postData['id']);
            foreach ($this->setMistakes() as $mistake) {
                $this->getMessage($mistake);
              }
            $this->getClassMessage("danger");
          }

        }

        if($postData['mode']=="delete"){
          $task = Task::where('id_laboral', '=', $postData['id'])->delete();
          $work = Work::where('id', '=', $postData['id'])->delete();
          $this->getWorks();
          $this->getOneWork($this->getIdFirstWork());
          $this->getTasks($this->getIdFirstWork());
          $this->getClassMessage("success");
          $this->getMessage('Experiencia Laboral borrada');
        }

        if($postData['mode']=="task"){

          $skillValidator = new ValidatorController("task",$postData);
          $this->getMistakes($skillValidator->error);

          if(empty($this->setMistakes())){

            if($postData['id']=="new"){
              $this->getMessage('Primero agrega una Experiencia Laboral para poder agregar una tarea.');
              $this->getClassMessage("danger");
            }else{
              $task = new Task();
              $task->description = $postData['description'];
              $task->id_laboral = $postData['id'];
              $task->save();
              $this->getOneWork($postData['id']);
              $this->getTasks($postData['id']);
              $this->getClassMessage("success");
              $this->getMessage('Tarea agregada.');
            }
          }else{
            $this->getWorks();
            $this->getOneWork($postData['id']);
            foreach ($this->setMistakes() as $mistake) {
                $this->getMessage($mistake);
              }
            $this->getClassMessage("danger");
            $this->getOneWork($postData['id']);
            $this->getTasks($postData['id']);
          }
        }


        if($postData['mode']=="deleteTask"){
          $task = Task::where('id', '=', $postData['id'])->delete();
          $this->getWorks();
          $this->getOneWork($postData['idWork']);
          $this->getTasks($postData['idWork']);
          $this->getClassMessage("success");
          $this->getMessage('Tarea borrada');
        }

      }

      return $this->renderHTML('addWork.twig',[
        'mensaje' => $this->setMessage(),
        'classMensaje' => $this->setClassMessage(),
        'workSelected' => $this->setOneWork(),
        'listWorks' => $this->setListWorks(),
        'listTasks' => $this->setTasks(),
        'user' => $_SESSION['userId'],
        'admin' => $this->isAdmin($_SESSION['userId'])
      ]);

  }

  /* Funciones GET */

  public function getWorks(){
    $this->works  = Work::where('id_persona', $_SESSION['id'])->get();
  }

  public function getOneWork($id){

    foreach ($this->setWorks() as $work) {
      if($work->toarray()['id']==$id){
        $this->work  = $work->toarray();
      }
    }
  }

  public function getIdFirstWork(){
  
    $res = Work::select('id')->where('id_persona', $_SESSION['id'])->first();
    if(empty($res)){
      return null;
    }else{
      return $res->toarray()['id'];
    }
  }

  public function getNumWorks(){
    $this->numWorks  = Work::where('id_persona', $_SESSION['id'])->count();
  }

  public function getTasks($id){
    $this->tasks  = Task::where('id_laboral', $id)->get();
  }

  /* Funciones SET */
  public function setWorks(){
    return $this->works;
  }

  public function setOneWork(){
    return $this->work;
  }

  public function setNumWorks(){
    return $this->numWorks;
  }

  public function setListWorks(){
    $pila = array();
    foreach ($this->setWorks() as $work) {
      array_push($pila, $work->toarray()['id']);
    }
    return $pila;
  }

  public function setTasks(){
    return $this->tasks;
  }

}
    


  ?>