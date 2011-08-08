<?php

Class app_error {

  public $levels = array(
    0                  => 'Error',
    E_ERROR            => 'Error',
    E_WARNING          => 'Warning',
    E_PARSE            => 'Parsing Error',
    E_NOTICE           => 'Notice',
    E_CORE_ERROR       => 'Core Error',
    E_CORE_WARNING     => 'Core Warning',
    E_COMPILE_ERROR    => 'Compile Error',
    E_COMPILE_WARNING  => 'Compile Warning',
    E_USER_ERROR       => 'User Error',
    E_USER_WARNING     => 'User Warning',
    E_USER_NOTICE      => 'User Notice',
    E_STRICT           => 'Runtime Notice'
  );

  public $fatal_levels = array(E_PARSE, E_ERROR, E_USER_ERROR, E_COMPILE_ERROR);

  function slim_404() {
    $this->slim->render('error/404.php');
  }

  function slim_error($e= false)
  {
    $error      = error_get_last();
    $error_type = $error['type'];
    if($e)
      $this->exception_handler($e);
    elseif(isset($this->levels[$error_type]))
      $this->exception_handler(new ErrorException($error['message'], $error_type, 0, $error['file'], $error['line']), $error_type);
  }

  function exception_handler($e, $type = '')
  {
    $file = $e->getFile();
    $line = $e->getLine();
    $lines= false;

    if ( file_exists( $file ) )
    {
        $lines = file( $file );
    }

    $this->slim->render('error/exception.php', array(
        'e'    => $e
      , 'file' => $file
      , 'line' => $line
      , 'lines'=> $lines
    ));

    exit;
  }
}