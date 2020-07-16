<?php
  namespace App\Controllers;
  use App\Models\{Person,Social,Language,Skill,Work,Project};

  class IndexController extends BaseController{
    
    protected $persona;
    protected $redes;
    protected $idiomas;
    protected $tecnicas;
    protected $lenguage;
    protected $plataforma;
    protected $frameworks;
    protected $herramienta;
    protected $experiencia;
    protected $tareas;
    protected $proyectos;
    protected $hab_pro;

    public function indexAction($request){
      
      $this->getDatosPersonales($_SESSION['id'] ?? null);
      $this->getRedes($_SESSION['id'] ?? null);
      $this->getIdiomas($_SESSION['id'] ?? null);
      $this->getTecnicas($_SESSION['id'] ?? null);
      $this->getLenguages($_SESSION['id'] ?? null);
      $this->getPlataformas($_SESSION['id'] ?? null);
      $this->getFrameworks($_SESSION['id'] ?? null);
      $this->getHerramientas($_SESSION['id'] ?? null);
      $this->getExperiencias($_SESSION['id'] ?? null);
      $this->getTareas($_SESSION['id'] ?? null);
      $this->getProyectos($_SESSION['id'] ?? null);
      $this->getHabPro($_SESSION['id'] ?? null);
      
      if(empty($this->setDatosPersonales())){
        $this->getNotPerson();
      }

      return $this->renderHTML('index.twig',[
        'datosPersonales' => $this->setDatosPersonales(),
        'datosRedes' => $this->setRedes(),
        'datosIdiomas' => $this->setIdiomas(),
        'datosTecnicas' => $this->setTecnicas(),
        'datosLenguajes' => $this->setLenguages(),
        'datosPlataformas' => $this->setPlataformas(),
        'datosFrameworks' => $this->setFrameworks(),
        'datosHerramientas' => $this->setHerramientas(),
        'datosExperiencias' => $this->setExperiencias(),
        'datosTareas' => $this->setTareas(),
        'datosProyectos' => $this->setProyectos(),
        'datosHabPro' => $this->setHabPro(),
        'user' => $_SESSION['userId']
      ]); 
    }

    /* Funciones GET */
    public function getDatosPersonales($id){
      $this->persona = Person::find($id);
    }

    public function getRedes($id){
      $this->redes = Social::where('id_persona', '=', $id)->get();
    }

    public function getIdiomas($id){
      $this->idiomas = Language::where('id_persona', '=', $id)->get();
    }

    public function getTecnicas($id){
      $this->tecnicas = Skill::where([['id_persona', '=', $id],['tipo','=','Técnica']])->get();
    }

    public function getLenguages($id){
      $this->lenguage = Skill::where([['id_persona', '=', $id],['tipo','=','Lenguaje']])->get();
    }

    public function getPlataformas($id){
      $this->plataforma = Skill::where([['id_persona', '=', $id],['tipo','=','Plataforma']])->get();
    }

    public function getFrameworks($id){
      $this->frameworks = Skill::where([['id_persona', '=', $id],['tipo','=','Frameworks']])->get();
    }

    public function getHerramientas($id){
      $this->herramienta = Skill::where([['id_persona', '=', $id],['tipo','=','Herramienta']])->get();
    }

    public function getExperiencias($id){
      $this->experiencia = Work::where('id_persona', '=', $id)->get();
    }

    public function getTareas($id){
      $this->tareas = Work::join('tareas', 'laboral.id', '=', 'tareas.id_laboral')
                            ->select('laboral.id', 'tareas.description')
                            ->where('laboral.id_persona', '=', $id)
                            ->get();
    }

    public function getProyectos($id){
      $this->proyectos = Project::where('id_persona', '=', $id)->get();
    }

    public function getHabPro($id){
      $this->hab_pro = Project::join('hab_pro', 'projectos.id', '=', 'hab_pro.id_pro')
                              ->join('habilidades','hab_pro.id_hab','=','habilidades.id')
                              ->select('projectos.id', 'habilidades.nombre')
                              ->where('projectos.id_persona', '=', $id)
                              ->get();
    }

    
     

    /* Funciones SET */
    public function setDatosPersonales(){
      return $this->persona;
    }

    public function setRedes(){
      return $this->redes;
    }

    public function setIdiomas(){
      return $this->idiomas;
    }

    public function setTecnicas(){
      return $this->tecnicas;
    }

    public function setLenguages(){
      return $this->lenguage;
    }

    public function setPlataformas(){
      return $this->plataforma;
    }

    public function setFrameworks(){
      return $this->frameworks;
    }

    public function setHerramientas(){
      return $this->herramienta;
    }

    public function setExperiencias(){
      return $this->experiencia;
    }

    public function setTareas(){
      return $this->tareas;
    }

    public function setProyectos(){
      return $this->proyectos;
    }

    public function setHabPro(){
      return $this->hab_pro;
    }

    /* Otras funciones */
    public function getNotPerson(){
      $this->persona = [
        [
          'nombre' => 'Nombre',
          'apellido' => 'Apellido',
          'especialidad' => 'Especialidad N/A',
          'email' => 'Email N/A',
          'telefono' => 'Telefono N/A',
          'descripcion' => 'Descripcion N/A',
        ]
      ];
    }
  }


?>