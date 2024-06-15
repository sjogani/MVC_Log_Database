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
          return 'good'; // Log successful attempt
          header('Location: /home');
          exit();
      } 
      else {
          if (isset($_SESSION['failedAuth'])) {
              $_SESSION['failedAuth']++;
          } else {
              $_SESSION['failedAuth'] = 1;
          }
          return 'bad'; // Log failed attempt
          header('Location: /login');
			    exit;
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
          return "Account created";
        }
        else {
          return "Passwords do not match";
        }

      }
    }

    public function logAttempt($username, $attempt) {
        
        $db = db_connect();

        $currentdate = date("Y-m-d H:i:s");
        $statement = $db->prepare("INSERT INTO logs (username, attempt, time) VALUES (:username, :attempt, :time);");
        $statement->bindValue(':username', $username);
        $statement->bindValue(':attempt', $attempt);
        $statement->bindValue(':time', $currentdate);
        $statement->execute();
        if ($attempt == 'good') {
            header('Location: /home');
            exit();
            
        } else
        {
            header('Location: /login');
            exit();
        }
    }
}
