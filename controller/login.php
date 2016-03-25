<?php

require_once(__DIR__.'/database.php');

class Login extends Database {

	public $errors = array();
    
    public $messages = array();

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

        // database query, getting all the info of the selected user (allows login via email address in the
        // username field)
        $sql_User = "SELECT firstName, lastName, email, password
                FROM User
                WHERE email = '" . $email . "';";
        $result_of_user_login_check = $this->conn->query($sql_User);

        // if this user exists
        if ($result_of_user_login_check->num_rows == 1) {

            // get result row (as an object)
            $result_row = $result_of_user_login_check->fetch_object();
            $hashPass = $result_row->password;

            
            if (password_verify($password, $hashPass)) {
                // write user data into PHP SESSION (a file on your server)
                $_SESSION['email'] = $result_row->email;
                $_SESSION['login_status'] = 1;
                $_SESSION['account_type'] = 0;
            } else {
                $this->errors[] = "Wrong password. Try again.";
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
            } else {
                $this->errors[] = "Wrong password. Try again.";
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
            } else {
                $this->errors[] = "Wrong password. Try again.";
            }
            return;
        }
        else {
            $this->errors[] = "This account does not exist.";
        }
    }

    /**
     * perform the logout
     */
    public function doLogout() {
        // delete the session of the user
        $_SESSION = array();
        session_destroy();
        // return a little feeedback message
        $this->messages[] = "You have been logged out.";

    }

    /**
     * simply return the current state of the user's login
     * @return boolean user's login status
     */
    public function isUserLoggedIn() {
        if (isset($_SESSION['login_status']) AND $_SESSION['login_status'] == 1) {
            return true;
        }
        // default return
        return false;
    }

    public function getAccountType() {
        if (isset($_SESSION['login_status']) AND $_SESSION['login_status'] == 1) {
            return $_SESSION['account_type']; // user = 0, eventprovider = 1, admin = 2
        }
        // default return
        return -1;
    }

    public function getEmail() {
        if (isset($_SESSION['login_status']) AND $_SESSION['login_status'] == 1) {
            return $_SESSION['email'];
        }
        // default return
        return -1;
    }

    public function account() {
		$method = $_SERVER['REQUEST_METHOD'];
		if ($method == 'POST') {
			$json = file_get_contents("php://input");
			$data = json_decode($json,TRUE);
			$result = $this->dologinWithPostData($data);
			$this->response($result, 200);
		}else{
			$this->response("Method Not Allowed", 405);
		}
    }
}


?>