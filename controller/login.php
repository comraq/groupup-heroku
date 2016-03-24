<?php

require_once(__DIR__.'/database.php');
require_once("libraries/password_compatibility_library.php");

class Login extends Database {

	public $errors = array();
    
    public $messages = array();

	function __construct(){
		parent::__construct();

		session_start();

        // check the possible login actions:
        // if user tried to log out (happen when user clicks logout button)
        if (isset($_GET["logout"])) {
            $this->doLogout();
        }
        // login via post data (if user just submitted a login form)
        else if (isset($_POST["login"])) {
            $this->dologinWithPostData();
        }
	}

	private function dologinWithPostData() {
        if (empty($_POST['email'])) {
            $this->errors[] = "email field was empty.";
        } else if (empty($_POST['password'])) {
            $this->errors[] = "Password field was empty.";
        } else if (!empty($_POST['email']) && !empty($_POST['password'])) {

            // escape the POST stuff
            $email = $this->conn->real_escape_string($_POST['email']);

            // database query, getting all the info of the selected user (allows login via email address in the
            // username field)
            $sql_User = "SELECT firstName, lastName, email, password
                    FROM User
                    WHERE email = '" . $email . "';";
            $result_of_user_login_check = $this->conn->query($sql_User);


            $sql_EventProvider = "SELECT firstName, lastName, email, password
                    FROM EventProvider
                    WHERE email = '" . $email . "';";
            $result_of_eventprovider_login_check = $this->conn->query($sql_EventProvider);

            $sql_Admin = "SELECT firstName, lastName, email, password
                    FROM Admin
                    WHERE email = '" . $email . "';";
            $result_of_admin_login_check = $this->conn->query($sql_Admin);


            // if this user exists
            if ($result_of_user_login_check->num_rows == 1) {

                // get result row (as an object)
                $result_row = $result_of_user_login_check->fetch_object();

                // using PHP 5.5's password_verify() function to check if the provided password fits
                // the hash of that user's password
                if (strcmp($_POST['password'], $result_row->password) == 0) {

                    // write user data into PHP SESSION (a file on your server)
                    $_SESSION['email'] = $result_row->email;
                    $_SESSION['login_status'] = 1;
                    $_SESSION['account_type'] = 0;

                } else {
                    $this->errors[] = "Wrong password. Try again.";
                }
            } 
            else if ($result_of_eventprovider_login_check->num_rows == 1) {
            	// get result row (as an object)
                $result_row = $result_of_eventprovider_login_check->fetch_object();

                // using PHP 5.5's password_verify() function to check if the provided password fits
                // the hash of that user's password
                if (strcmp($_POST['password'], $result_row->password) == 0) {

                    // write user data into PHP SESSION (a file on your server)
                    $_SESSION['email'] = $result_row->email;
                    $_SESSION['login_status'] = 1;
                    $_SESSION['account_type'] = 1;

                } else {
                    $this->errors[] = "Wrong password. Try again.";
                }
            }
            else if ($result_of_admin_login_check->num_rows == 1) {
            	// get result row (as an object)
                $result_row = $result_of_admin_login_check->fetch_object();

                // using PHP 5.5's password_verify() function to check if the provided password fits
                // the hash of that user's password
                if (strcmp($_POST['password'], $result_row->password) == 0) {

                    // write user data into PHP SESSION (a file on your server)
                    $_SESSION['email'] = $result_row->email;
                    $_SESSION['login_status'] = 1;
                    $_SESSION['account_type'] = 2;

                } else {
                    $this->errors[] = "Wrong password. Try again.";
                }
            }
            else {
                $this->errors[] = "This account does not exist.";
            }
        }
    }

    /**
     * perform the logout
     */
    public function doLogout()
    {
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
    public function isUserLoggedIn()
    {
        if (isset($_SESSION['login_status']) AND $_SESSION['login_status'] == 1) {
            return true;
        }
        // default return
        return false;
    }

    public function getAccountType()
    {
        if (isset($_SESSION['login_status']) AND $_SESSION['login_status'] == 1) {
            return $_SESSION['account_type']; // user = 0, eventprovider = 1, admin = 2
        }
        // default return
        return -1;
    }
}


?>