<?php
class SController_Home extends SController
{

  function defaultTask()
  {
    $this->define();

  }

  function define() {
    $v = new SView("home", $this);
    $v->test = "Home Page";
    $v->display();
  }
}
?>