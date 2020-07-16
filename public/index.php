<?php
/* TODAS LAS LIBRERIAS CUMPLEN CON EL ESTANDAR PSR-7*/
/*  Muestra errores de inicio, inicia variables de php
Esto habilita los errores en los servidores para poder verlos, 
xampp suele tenerlos activos por defectos,

Se recomienda usar esto solo en desarrollo */
  ini_set('display_errors',1); 
  ini_set('display_starup_error',1);
  error_reporting(E_ALL);

/* Dependencias */
  require_once '../vendor/autoload.php';

/* Iniciamos la sesion de usuario */
session_start();

/* Cargamos archivo de variables de entorno */
if(file_exists("../.env")){
  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
  $dotenv->load();
}

  /* Aura Router, libreria para gestinonar las rutas*/
  use Aura\Router\RouterContainer;

/* Usamos la libreria Eloquent,conecta a la BD */
  use Illuminate\Database\Capsule\Manager as Database;
  $database = new Database;
  $database->addConnection([
      'driver'    => $_ENV['DB_DRIVE'],
      'host'      => $_ENV['DB_HOST'],
      'database'  => $_ENV['DB_NAME'],
      'username'  => $_ENV['DB_USER'],
      'password'  => $_ENV['DB_PASS'],
      'charset'   => 'utf8',
      'collation' => 'utf8_unicode_ci',
      'prefix'    => '',
  ]);
// Make this BD instance available globally via static methods... (optional)
$database->setAsGlobal();
// Para iniciar Eloquent ORM... (optional; unless you've used setEventDispatcher())
$database->bootEloquent();
// create a server request object
$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

/* Creamos un contenedor de rutas */
$routerContainer = new RouterContainer();
/* Hacemos un mapeado*/
$map = $routerContainer->getMap();
/* Nombre de la carpeta de mi proyecto */
$baseRoute = '/plantilla-vitae-php';
/* Generamos nuestras rutas personalizadas */
/*1 $route (name), 2 (path), 3 (handler), var_dump($route) para verlo*/
$map->get('index',$baseRoute.'/',[
  'controller' => 'App\Controllers\IndexController',
  'action' => 'IndexAction',
  'auth' => true
]);
$map->get('addPerson',$baseRoute.'/add/person',[
  'controller' => 'App\Controllers\PersonController',
  'action' => 'getAddPersonAction',
  'auth' => true
]);
$map->post('savePerson',$baseRoute.'/add/person',[
  'controller' => 'App\Controllers\PersonController',
  'action' => 'getAddPersonAction',
  'auth' => true
]);
$map->get('addSocial',$baseRoute.'/add/social',[
  'controller' => 'App\Controllers\SocialController',
  'action' => 'getAddSocialAction',
  'auth' => true
]);
$map->post('saveSocial',$baseRoute.'/add/social',[
  'controller' => 'App\Controllers\SocialController',
  'action' => 'getAddSocialAction',
  'auth' => true
]);
$map->get('addLanguage',$baseRoute.'/add/language',[
  'controller' => 'App\Controllers\LanguageController',
  'action' => 'getAddLanguageAction',
  'auth' => true
]);
$map->post('saveLanguage',$baseRoute.'/add/language',[
  'controller' => 'App\Controllers\LanguageController',
  'action' => 'getAddLanguageAction',
  'auth' => true
]);
$map->get('addSkill',$baseRoute.'/add/skill',[
  'controller' => 'App\Controllers\SkillController',
  'action' => 'getAddSkillAction',
  'auth' => true
]);
$map->post('saveSkill',$baseRoute.'/add/skill',[
  'controller' => 'App\Controllers\SkillController',
  'action' => 'getAddSkillAction',
  'auth' => true
]);
$map->get('addProject',$baseRoute.'/add/project',[
  'controller' => 'App\Controllers\ProjectController',
  'action' => 'getAddProjectAction',
  'auth' => true
]);
$map->get('addWork',$baseRoute.'/add/work',[
  'controller' => 'App\Controllers\WorkController',
  'action' => 'getAddWorkAction',
  'auth' => true
]);
$map->post('saveWork',$baseRoute.'/add/work',[
  'controller' => 'App\Controllers\WorkController',
  'action' => 'getAddWorkAction',
  'auth' => true
]);
$map->post('saveProject',$baseRoute.'/add/project',[
  'controller' => 'App\Controllers\ProjectController',
  'action' => 'getAddProjectAction',
  'auth' => true
]);
$map->get('addUser',$baseRoute.'/add/user',[
  'controller' => 'App\Controllers\UserController',
  'action' => 'getAddUserAction',
  'auth' => true
]);
$map->post('saveUser',$baseRoute.'/add/user',[
  'controller' => 'App\Controllers\UserController',
  'action' => 'getAddUserAction',
  'auth' => true
]);
$map->get('loginForm',$baseRoute.'/login',[
  'controller' => 'App\Controllers\AuthController',
  'action' => 'getLoginAction'
]);
$map->get('logoutForm',$baseRoute.'/logout',[
  'controller' => 'App\Controllers\AuthController',
  'action' => 'getLogoutAction'
]);
$map->post('auth',$baseRoute.'/auth',[
  'controller' => 'App\Controllers\AuthController',
  'action' => 'postLoginAction',
  'auth' => true
]);
$map->get('protected',$baseRoute.'/protected',[
  'controller' => 'App\Controllers\AuthController',
  'action' => 'getProtected'
]);
$map->get('join',$baseRoute.'/join',[
  'controller' => 'App\Controllers\JoinController',
  'action' => 'getjoinAction'
]);
$map->post('createAccount',$baseRoute.'/join',[
  'controller' => 'App\Controllers\JoinController',
  'action' => 'getcreateAccountAction'
]);
/* Creamos una instancia de nuestro objeto request */
$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);
/* var_dump($routerContainer); */
/* var_dump($route); */
/* echo $request->getUri()->getPath(); */
if(!$route){
  echo " Ruta no encontrada";
}else{
  $handlerData = $route->handler;  /* Cogemos el array de handler*/ 
  $actionName = $handlerData['action']; /* Cogemos el nombre de la funcion action */
  $controller =  new $handlerData['controller']; /* Creamos la clase directamente */
  $response = $controller->$actionName($request); /* LLamamos a la funcion de la clase controlador y le pasamos resquest */

  $needsAuth = $handlerData['auth'] ?? false; /* Miramos si la ruta devuelve seguridad o no, sino por defecto false */
  $sessionUserId = $_SESSION['userId'] ?? null;
  if($needsAuth && !$sessionUserId && $actionName!=="postLoginAction"){
    header("Location: ".$baseRoute."/protected");
    exit; 
  }

  
  foreach($response->getHeaders() as $name => $values){
    foreach($values as $value){
      header(sprintf('%s: %s', $name, $value),false);
    }
  }
  http_response_code($response->getStatusCode());
  echo $response->getBody(); /* Mostramos al cliente la pagina */
}
/* echo $request->getUri()->getPath(); */
?>