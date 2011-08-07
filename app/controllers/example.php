<?php

class app_example {

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
    echo $this->hello();
  }

  public function slim_test($id= false, $year= false)
  {
    $this->slim->render('example/test.php', array(
        'id' => $id
      , 'message' => $this->hello('Test')
    ));
  }

  function hello($name= 'World')
  {
    return "Hello, $name!";
  }

}