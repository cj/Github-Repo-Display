<?php

class app_main {

  public $settings= array(
    'test' => array(
        'params' => array(
            'id'   => ''
          , 'year' => '(19|20)\d\d'
        )
      , 'methods' => array(
        'GET', 'POST'
      )
    )
  );

  function init()
  {

  }

  public function slim_index()
  {
    echo 'Hello, World!';
  }

  public function slim_test($id= false, $year= false)
  {
    if($id) echo "id: $id<br />";
    echo $this->hello('Test');
  }

  function hello($name= 'World')
  {
    return "Hello, $name!";
  }

}