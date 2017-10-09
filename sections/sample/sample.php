<?php
class SController_Sample extends SController
{

  function defaultTask()
  {
    $this->define();

  }

  function define() {
    $v = new SView("sample", $this);
    $v->test = "Sample Page";
    $v->display();
  }
}
?>