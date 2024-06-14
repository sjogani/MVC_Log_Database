<?php

class User {

    public $username;
    public $password;
    public $auth = false;

    public function __construct() {
        
    }

    public function test () {
      $db = db_connect();
      $statement = $db->prepare("select * from users;");
      $statement->execute();
      $rows = $statement->fetch(PDO::FETCH_ASSOC);
      return $rows;
    }

    public function authenticate($username, $password) {
        /*
         * if username and password good then
         * $this->auth = true;
         */
		$username = strtolower($username);
		$db = db_connect();
        $statement = $db->prepare("select * from users WHERE username = :name;");
        $statement->bindValue(':name', $username);
        $statement->execute();
        $rows = $statement->fetch(PDO::FETCH_ASSOC);
		
		if (password_verify($password, $rows['password'])) {
			$_SESSION['auth'] = 1;
			$_SESSION['username'] = ucwords($username);
			unset($_SESSION['failedAuth']);
			header('Location: /home');
			die;
		} else {
			if(isset($_SESSION['failedAuth'])) {
				$_SESSION['failedAuth'] ++; //increment
			} else {
				$_SESSION['failedAuth'] = 1;
			}
			header('Location: /login');
			die;
		}
    }
    public function check($username, $password, $confirm_password){
      $db = db_connect();
      $statement = $db->prepare("select * from users WHERE username = :name;");
      $statement->bindValue(':name', $username);
      $statement->execute();
      $rows = $statement->fetch(PDO::FETCH_ASSOC);
      if($rows) {
        return "Username already exists";
      }
      else {
        if($password == $confirm_password) {
          $password = password_hash($password, PASSWORD_DEFAULT);
          $statement = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password);");
          $statement->bindValue(':username', $username);
          $statement->bindValue(':password', $password);
          $statement->execute();
          $this->authenticate($username, $password);
        }
        else {
          return "Passwords do not match";
        }

      }
    }
}
