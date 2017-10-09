<?php
class User extends STable
{
  static $_table="users";
  static $_key="id";

  public   $id;
  public   $username;
  public   $name;
  public   $surname;
  public   $email;
  public   $password;
  public   $registerDate;
  public   $image;
  public   $token;

  function __construct() {

  }

  function guest()
  {
          if($this->id > 0)
                  return false;
          return true;
  }

  function getFullName()
  {
          return $this->name." ".$this->surname;
  }

}
?>