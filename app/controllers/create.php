<?php

class Create extends Controller {

    public function index($data = []) {	
      $this->view('create/index', $data);
    }

    public function verify(){
    $username = $_REQUEST['username'];
    $password = $_REQUEST['password'];
    $confirm_password = $_REQUEST['confirm_password'];
  
    $user = $this->model('User');
    $result = $user->check($username, $password, $confirm_password); 

    
    $this->index(['message' => $result]);
    }
}
