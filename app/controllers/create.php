<?php

class Create extends Controller {

    public function index() {	
	    $this->view('create/index');
    }

    public function verify(){
      $username = $_REQUEST['username'];
      $password = $_REQUEST['password'];
      $confirm_password = $_REQUEST['conifrm_password'];
  
      $user = $this->model('User');
      $result = $user->check($username, $password, $confirm_password); 
      echo $result;
      $this->index();
    }
}
