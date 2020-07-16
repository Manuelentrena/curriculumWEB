<?php
  namespace App\Controllers;
  use App\Models\Skill;

  class SkillController extends BaseController{

    protected $skill=[];
    protected $num;

    public function getAddSkillAction($request){

      $this->getSkill();
      $this->getSkillNum();
      $postData = $request->getParsedBody();
      $this->cleanMessage();

      if($request->getMethod()=='GET'){

        if(empty($this->setSkillNum())){
          
          $this->getMessage('No tiene ningúna habilidad. Inserte una.');
        }else{
          $this->getMessage('Tienes <strong>'.$this->setSkillNum().'</strong> habilidades');
          
        }
        $this->getClassMessage("dark");
      }

      if($request->getMethod()=='POST'){

        if($postData['mode']=="save"){

          $skillValidator = new ValidatorController("skill",$postData);
          $this->getMistakes($skillValidator->error);

          if(empty($this->setMistakes())){
            if($this->skillRepeat($this->setSkill(),mb_strtoupper($postData['nombre']))==true){
              $skill = Skill::where('id_persona', $_SESSION['id'])->where('nombre', mb_strtoupper($postData['nombre']));
              $skill->update(['tipo' => $postData['tipo'][0]]);
              $this->getMessage('Habilidad <strong>'.mb_strtoupper($postData['nombre']).'</strong> actualizada');
            }else{
              $skill = new Skill();
              $skill->nombre = mb_strtoupper($postData['nombre']);
              $skill->tipo = $postData['tipo'][0];
              $skill->nivel = $postData['nivel'];
              $skill->id_persona = $_SESSION['id'];
              $skill->save();
              $this->getMessage('Habilidad <strong>'.mb_strtoupper($postData['nombre']).'</strong> registrada');
            }
            $this->getClassMessage("success");
            $this->getSkill();
          }else{
            foreach ($this->setMistakes() as $mistake) {
              $this->getMessage($mistake);
            }
            $this->getClassMessage("danger");
          }

        }

        if($postData['mode']=="delete"){
          $skill = Skill::where('id_persona', $_SESSION['id'])->where('nombre', mb_strtoupper($postData['nombre']));
          $skill->delete();
          $this->getMessage('Habilidad <strong>'.mb_strtoupper($postData['nombre']).'</strong> borrada');
          $this->getClassMessage("success");
          $this->getSkill();
        }

      }

      return $this->renderHTML('addSkill.twig',[
        'mensaje' => $this->setMessage(),
        'classMensaje' => $this->setClassMessage(),
        'listSkill'  => $this->setSkill(),
        'user' => $_SESSION['userId'],
        'admin' => $this->isAdmin($_SESSION['userId'])
      ]);
    } 
    /* Funciones GET */
    public function getSkill(){
      $this->skill  = Skill::where('id_persona', $_SESSION['id'])->get();
    }

    public function getSkillNum(){
      $this->num  = Skill::where('id_persona', $_SESSION['id'])->count();
    }

    /* Funciones SET */
    public function setSkill(){
      return $this->skill->toarray();
    }

    public function setSkillNum(){
      return $this->num;
    }

    public function skillRepeat($listSkill,$skill){
    //Comprobamos que email no sea repetido
    foreach( $listSkill as $skill2 ){
      if($skill == $skill2['nombre']){
        return true;
      }
    }
    return false;
  }

  }

?> 