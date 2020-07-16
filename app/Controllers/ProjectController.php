<?php
  namespace App\Controllers;
  use App\Models\{Project,Skill,Skill_Project};

  class ProjectController extends BaseController{

    protected $project=[];
    protected $projectSelected=[];
    protected $skills=[];
    protected $skillsIdSelected=[];
    protected $skillsSelected=[]; 
    protected $skillsNoSelected=[];
    protected $num;

    public function getAddProjectAction($request){
      
      $this->getProject();
      $this->getProjectNum(); 
      $this->cleanMessage();
      $this->getSkills();

      if($request->getMethod()=='GET'){

        if(empty($this->setProjectNum())){
          $this->getMessage('No tiene ningún proyecto. Inserte uno.');
        }else{
          if(empty($_GET['pro'])){
            $this->getMessage('Tienes <strong>'.$this->setProjectNum().'</strong> proyectos');
          }elseif($_GET['pro']=="new"){
            $this->getMessage('Agrega un proyecto nuevo');
          }else{
            $this->getMessage('Tienes <strong>'.$this->setProjectNum().'</strong> proyectos');
          }  
        }
        $this->getProjectSelected($_GET['pro'] ?? null);
        $this->getSkillsIdSelected($_GET['pro'] ?? null);
        $this->setSkillsGroup();
        $this->getClassMessage("dark");
      }

      if($request->getMethod()=='POST'){

        $postData = $request->getParsedBody();

        if($postData['mode']=="save"){

          $skillValidator = new ValidatorController("project",$postData);
          $this->getMistakes($skillValidator->error);

          if(empty($this->setMistakes())){

            if($postData['id']=="new"){ /* Nuevo */

              $project = new Project();
              $project->nombre = mb_strtoupper($postData['nombre']);
              $project->description = $postData['description'];
              $project->id_persona = $_SESSION['id'];
              $project->url_img = $postData['url_img'];
              $project->save();
            
              $this->getMessage('Proyecto <strong>'.mb_strtoupper($postData['nombre']).'</strong> registrado');
              
            }else{ /* Editar */

              $project = Project::find($postData['id']);
              $project->update(['nombre' => mb_strtoupper($postData['nombre'])]);
              $project->update(['description' => $postData['description']]);
              $project->update(['url_img' => $postData['url_img']]);

              $this->getMessage('Proyecto <strong>'.mb_strtoupper($postData['nombre']).'</strong> modificado');

            }

              /* Guardar las habilidades */
              if($postData['id']!="new"){
                $Skill_Project = Skill_Project::where('id_pro', '=', $postData['id'])->delete();
              }
              if(!empty($postData['checkbox'])){
                foreach ($postData['checkbox'] as $skill) {
                  $skill_project = new Skill_Project();
                  $skill_project->id_hab = (int)$skill;
                  $skill_project->id_pro = (int)$project->id;
                  $skill_project->save();
                }
              }

              $this->getClassMessage("success");
              $this->getProject();
              $this->getProjectSelected((int)$project->id);
              $this->getSkillsIdSelected($project->id);
          }else{
            foreach ($this->setMistakes() as $mistake) {
              $this->getMessage($mistake);
            }
            $this->getClassMessage("danger");
            $this->getProjectSelected($postData['id']);
            $this->getSkillsIdSelected($postData['id']);
          }

        }

        if($postData['mode']=="delete"){
          /* borrar las habilidades */
          $Skill_Project = Skill_Project::where('id_pro', '=', $postData['id'])->delete();
          $Project = Project::where('id', '=', $postData['id'])->get();
          $this->getMessage('Proyecto <strong>'.mb_strtoupper($Project->toarray()[0]['nombre']).'</strong> borrado');
          /* borrar El proyecto */
          $Project = Project::where('id', '=', $postData['id'])->delete();

          $this->getClassMessage("success");
          $this->getProject();
          $this->getProjectSelected(null);
          $this->getSkillsIdSelected(null);
        }
        $this->setSkillsGroup();
      }

      return $this->renderHTML('addProject.twig',[
        'mensaje' => $this->setMessage(),
        'classMensaje' => $this->setClassMessage(),
        'listSkills' => $this->setSkills(),
        'listSkillsNoSelected'  => $this->setSkillsNoSelected(),
        'listSkillsSelected'  => $this->setSkillsSelected(),
        'listProjects'  => $this->setProject(),
        'projectSelected'  => $this->setProjectSelected(),
        'user' => $_SESSION['userId'],
        'admin' => $this->isAdmin($_SESSION['userId'])
      ]);

    } 
    /* Funciones GET */

    public function getSkills(){
      $this->skills = Skill::where('id_persona', $_SESSION['id'])->get();
    }

    public function getSkillsIdSelected($idProject){
      if(empty($idProject)){
        if(empty($this->setProjectSelected())){
          $Project = null;
        }else{
          $Project = $this->setProjectSelected()->toarray()['id'];
        }
        
        $this->skillsIdSelected = Skill_Project::where('id_pro', $Project)->get();
      }elseif($idProject=="new"){
        $this->skillsIdSelected = [];
      }else{
        $this->skillsIdSelected = Skill_Project::where('id_pro', $idProject)->get();
      }
    }

    public function getProject(){
      $this->project  = Project::where('id_persona', $_SESSION['id'])->get();
    }
    
    public function getProjectSelected($idProject){
      if(empty($idProject)){
        $this->projectSelected  = Project::where('id_persona', $_SESSION['id'])->orderBy('created_at', 'asc')->first();
      }elseif($idProject=="new"){
        $this->projectSelected = [];
      }else{
        $this->projectSelected  = Project::find($idProject);
      }
    } 

    public function getProjectNum(){
      $this->num  = Project::where('id_persona', $_SESSION['id'])->count();
    }

    public function getSkillsNoSelected($array){
      $this->skillsNoSelected  = $array;
    }

    public function getskillsSelected($array){
      $this->skillsSelected  = $array;
    }

    /* Funciones SET */

    public function setSkills(){
      return $this->skills;
    }
    public function setProject(){
      return $this->project->toarray();
    }

    public function setProjectNum(){
      return $this->num;
    }

    public function setSkillsIdSelected(){
      return $this->skillsIdSelected;
    }

    public function setSkillsNoSelected(){
      return $this->skillsNoSelected;
    }

    public function setSkillsSelected(){
      return $this->skillsSelected;
    }

    public function setSkillsGroup(){

      $pila_selected = array();
      $pila_noSelected = array();
      $flag = false;

      foreach ($this->setSkills() as $skill) {
        
        foreach ($this->setSkillsIdSelected() as $skillselected) {
          if($skillselected['id_hab']==$skill['id']){
            $flag = true;
            array_push($pila_selected, $skill);
          }
        }
        if($flag == false){
          array_push($pila_noSelected, $skill);
        }
        $flag = false;
      }
      $this->getSkillsNoSelected($pila_noSelected);
      $this->getskillsSelected($pila_selected);
    }

    public function setProjectSelected(){
      return $this->projectSelected;
    }

    public function projectRepeat($listProject,$project){
      //Comprobamos que email no sea repetido
      foreach( $listProject as $project2 ){
        if($project == $project2['nombre']){
          return true;
        }
      }
      return false;
    }
  }

?> 