<?php
require '../submodules/slim/Slim/Slim.php';

$app = new Slim();
$called_uri= $app->request()->getResourceUri();

$routes= require_once __DIR__."/../app/routes.php";

foreach(array_reverse($routes) AS $route => $file)
  if(strstr($called_uri, $route))
    continue;

require_once __DIR__."/../app/controllers/$file.php";

$load_class= "app_$file";
$class= new $load_class();
$class->slim= $app;
$methods= get_class_methods($class);

foreach($methods AS $method)
{
  if($method == 'init')
    $class->$method();

  if(strstr($method, 'slim_'))
  {
    $rest_methods= array('GET');
    $func= str_replace('slim_', '', $method);
    $uri= '';
    $uri_params     = array();
    $uri_conditions = array();

    if(isset($class->settings[$func]))
    {
      $settings= $class->settings[$func];
      if(isset($settings['params']))
      {
        foreach($settings['params'] AS $param => $condition)
        {
          if($condition)
            $uri_conditions[$param]= $condition;
          $uri_params[]= $param;
        }
        $uri.= "(/:".implode('(/:', $uri_params).str_repeat(")", count($uri_params));
      }

      if(isset($settings['methods']))
        $rest_methods= $settings['methods'];
    }

    if($func != 'index')
      $uri= "$func$uri";

    $route= $app->map("/$uri", function() use ($app, $class, $func){
      $args = func_get_args();
      call_user_func_array(array($class, "slim_$func"), $args);
    });

    if(count($uri_conditions))
      $route->conditions($uri_conditions);
    call_user_func_array(array($route, 'via'), $rest_methods);
  }
}

$app->run();