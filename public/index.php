<?php
// Set the app path
define('APPPATH', __DIR__."/../app/");

// Include slim
require '../submodules/slim/Slim/Slim.php';

// Start the app
$app = new Slim();

// Set the 404 location
$app->notFound(function() {
  require_once APPPATH.'controllers/error.php';
  $app_error= new app_error();
  if(method_exists($app_error, 'init'))
    $app_error->init();
  $args = func_get_args();
  call_user_func_array(array($app_error, "slim_404"), $args);
});

// Grab the called uri
$called_uri= $app->request()->getResourceUri();

// Grab the routes
$routes= require_once __DIR__."/../app/routes.php";
// Grab the config file
$config= require_once __DIR__."/../app/config.php";
// Set the configurations
$app->config($config);
$app->config('templates.path', APPPATH.'views');

// By default we haven't found the uri yet.
$found_uri= false;

// Check to see if there's a route that matches the called uri
foreach(array_reverse($routes) AS $route_uri => $file)
  if(strstr($called_uri, $route_uri))
  {
    $found_uri= true;
    break;
  }

// If we find a matching uri then we lift off!
if($found_uri)
{
  // include the file
  require_once APPPATH."controllers/$file.php";
  // Set the class to load
  $load_class= "app_$file";
  // Initiate a new instance of the class
  $class= new $load_class();
  // Set slim var so we can access the app inside the class
  $class->slim= $app;
  // Grab all the methods
  $methods= get_class_methods($class);
  // Loop through and add all the methods
  foreach($methods AS $method)
  {
    // If we have an init method run it
    if($method == 'init')
      $class->$method();

    // Any function starting with slim_ is a function that can be
    // accessed by the URL (browser)
    if(strstr($method, 'slim_'))
    {
      // Set default rest method
      $rest_methods= array('GET');
      // Set the function
      $func= str_replace('slim_', '', $method);
      // Set uri
      $uri= '';
      // Set uri params
      $uri_params     = array();
      // Set uri conditions
      $uri_conditions = array();
      // Check to see if the function has any settings
      if(isset($class->settings[$func]))
      {
        // Load the function settings
        $settings= $class->settings[$func];
        // If it has params load them
        if(isset($settings['params']))
        {
          foreach($settings['params'] AS $param => $condition)
          {
            // If it has any conditions add them
            if($condition)
              $uri_conditions[$param]= $condition;
            $uri_params[]= $param;
          }
          // Build the uri with the uri params
          $uri.= "(/:".implode('(/:', $uri_params).str_repeat(")", count($uri_params));
        }

        // Override the methods with the one in settings if it exists
        if(isset($settings['methods']))
          $rest_methods= $settings['methods'];
      }

      // If the function isn't index
      if($func != 'index')
      // Create the full uri to call
        $uri= (strlen($route_uri) > 1?'/':'')."$func$uri";

      // Add the route
      $route= $app->map("$route_uri$uri", function() use ($app, $class, $func){
        $args = func_get_args();
        // Call the function with all the args passed back from slim
        call_user_func_array(array($class, "slim_$func"), $args);
      });

      // Add any conditions to the route
      if(count($uri_conditions))
        $route->conditions($uri_conditions);

      // Add the rest methods for the routes
      call_user_func_array(array($route, 'via'), $rest_methods);
    }
  }
}

// Run the application
$app->run();