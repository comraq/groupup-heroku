<?php

require_once(__DIR__.'/database.php');

class Login extends Database {

	function __construct(){
		parent::__construct();

        session_start();
	}

	private function dologinWithPostData($data) {

		$pre_email = $data["email"];
		$pre_password = $data["password"];

		$this->connect();
        
        $email = $this->conn->real_escape_string($pre_email);
        $password = $this->conn->real_escape_string($pre_password);

        
        $sql_User = "SELECT firstName, lastName, email, password
                FROM User
                WHERE email = '" . $email . "';";
        $result_of_user_login_check = $this->conn->query($sql_User);

        if ($result_of_user_login_check->num_rows == 1) {

            $result_row = $result_of_user_login_check->fetch_object();
            $hashPass = $result_row->password;

            
            if (password_verify($password, $hashPass)) {
                $_SESSION['email'] = $result_row->email;
                $_SESSION['login_status'] = 1;
                $_SESSION['account_type'] = 0;
                return $arr = array('email' => $_SESSION['email'], 'accountType' => $_SESSION['account_type']);
            } else {
                $this->response("Wrong password. Try again.", 401);
            }
            return;
        }

        $sql_EventProvider = "SELECT firstName, lastName, email, password
                FROM EventProvider
                WHERE email = '" . $email . "';";
        $result_of_eventprovider_login_check = $this->conn->query($sql_EventProvider);

        if ($result_of_eventprovider_login_check->num_rows == 1) {

            $result_row = $result_of_eventprovider_login_check->fetch_object();
            $hashPass = $result_row->password;

            if (password_verify($password, $hashPass)) {
                $_SESSION['email'] = $result_row->email;
                $_SESSION['login_status'] = 1;
                $_SESSION['account_type'] = 1;
                return $arr = array('email' => $_SESSION['email'], 'accountType' => $_SESSION['account_type']);
            } else {
                $this->response("Wrong password. Try again.", 401);
            }
            return;
        }

        $sql_Admin = "SELECT firstName, lastName, email, password
                FROM Admin
                WHERE email = '" . $email . "';";
        $result_of_admin_login_check = $this->conn->query($sql_Admin);

        if ($result_of_admin_login_check->num_rows == 1) {

            $result_row = $result_of_admin_login_check->fetch_object();
            $hashPass = $result_row->password;

            if (password_verify($password, $hashPass)) {
                $_SESSION['email'] = $result_row->email;
                $_SESSION['login_status'] = 1;
                $_SESSION['account_type'] = 2;
                return $arr = array('email' => $_SESSION['email'], 'accountType' => $_SESSION['account_type']);
            } else {
                $this->response("Wrong password. Try again.", 401);
            }
            return;
        }
        else {
            $this->response("This account does not exist.", 401);
        }
    }

    public function doLogout() {
        $_SESSION = array();
        session_destroy();
        $this->response("You have been logged out.", 200);
    }

    public function getSessionInfo() {
        if ($_SESSION != ""
            AND isset($_SESSION['login_status'])
            AND $_SESSION['login_status'] == 1) {
            $arr = array('email' => $_SESSION['email'], 'accountType' => $_SESSION['account_type']);
            $this->response($arr, 200);
            // return $arr; // user = 0, eventprovider = 1, admin = 2
        }
        $this->response(null, 200);
    }

    public function login() {
		$method = $_SERVER['REQUEST_METHOD'];
		if ($method == 'POST') {
			$json = file_get_contents("php://input");
			$data = json_decode($json,TRUE);
			$result = $this->dologinWithPostData($data);
            if ($result == null) {
                $this->response("Not logged in", 401);
            }
            else {
                $this->response($result, 200);
            }
		}else{
			$this->response("Method Not Allowed", 405);
		}
    }
}


?>