<?php

require_once("../oci_class.php");

/** \brief Handles user login and user creation.
*
* examples:
* $ul=new userauth("oracle");
* $ul->set_oci_login("mkr/mkr@tora1");
*
*
* if(!$ul->create_user("martin.krois@gmail.com","123","")) {
*   echo $ul->get_error();
* }
*
*
*$ul->confirm_user("martin.krois@gmail.com", $ul->create_user_confirm_key("martin.krois@gmail.com"));
*
*
* if($ul->login("martin.krois@gmail.com","123"))
* {
*   print_r($ul->get_user_field("settings"));
* } else {
*   echo $ul->get_error();
* }
*
*
* $ul->set_user_field("settings","111111");
* $ul->delete_user("martin.krois@gmail.com");
*
*/

class userauth {

	 /// Current error code <int>
	var $error_code;

	 /// OCI object <object>
	var $oci;

	 /// Connection type (for future, not used yet) <string>
	var $mode;

	 /// Name of current user logged in <string>
	var $current_user;

	/// Name of session id <string>
	var $session_id="USERAUTH";

	/// Number of seconds before automatic logout
	var $timeout_seconds=3600;

	/// Encryption key value <string>
  //WARNING!!! DO NOT UNDER ANY CIRCUMSTANCES MODIFY THE VALUE BELOW
	var $crypt_key="fh@jd!jE3211395b26c729e570e70572a7495f2eIHFDwj:#ij92ZUM71918(#a!";

 /** \brief Constructor
  *
  * @param mode (string)
  *
  */

	function userauth($mode) {
		$this->mode=$mode;
		@session_start(); 
		$this->current_user=$_SESSION[$this->session_id]["username"];
	}

 /** \brief Get current error
  *
  * @return error (string)
  *
  */

	function get_error() {
		if(empty($this->error_code)) return FALSE;

		$error[1]="User already exists";	
		$error[2]="Wrong username or password";	
		$error[3]="User not confirmed";	
		$error[4]="Session timeout";	
		return "Error in userauth: ".$error[$this->error_code]."\n";
	}

	function set_session_id($id) {
		$this->session_id=$id;
	}

 /** \brief Checks if timeout
  * 
  * @return (bool)
  *
  */

  function check_timeout()
  {
		if((time()-$_SESSION[$this->session_id]["last_move"]) >= $this->timeout_seconds) {
			$this->error_code=4;
			return TRUE;
		}
		return FALSE;
  }


 /** \brief Set timeout for session
  *
  * @param seconds (int)
  *
  */

  function set_timeout($seconds) {
    $this->timeout_seconds=$seconds;
  }


 /** \brief Set's OCI login parameters
  *
  * @param oci_login (string)
  * @return status (bool)
  *
  */

	function set_oci_login($oci_login) {
		$this->oci = new Oci($oci_login);
		$this->oci->connect();
		$this->oci->commit_enable(TRUE);
	}

 /** \brief Login user
  *
  * @param username (string)
  * @param password (string)
  * @return status (bool)
  *
  */

	function login($username,$password) {

   if(!$this->is_confirmed($username)) {
      return FALSE;
    }

		if($this->is_logged_in()) {
			$this->current_user=$username;
			return TRUE;
		}

		if(!$this->user_exists($username)) {
			return FALSE;
		} 

		if($this->verify_password($username, $password)) {
			$this->set_current_user($username);
			$this->store_user_session();
			$_SESSION[$this->session_id]["last_move"]=time();
			#$this->oci->bind("username",$username, 128);
			#$this->oci->set_query("update userauth SET lastlogin=SYSDATE,lastmod=SYSDATE WHERE username= :username");
			$this->oci->set_query("update userauth SET lastlogin=SYSDATE,lastmod=SYSDATE WHERE username='$username'");
			return TRUE;
		} 

		unset($this->crypt);
		$this->error_code=2;

		return FALSE;
	
	}

 /** \brief Set's current user to username
  * 
  * @param username (string)
  *
  */

	function set_current_user($username)
	{
		$this->current_user=$username;
	}


 /** \brief Verifies password
  *
  * @param username (string)
  * @param password (string)
  * @return status (bool)
  *
  */

	function verify_password($username, $password) {

		$stored_password_encrypted=$this->get_stored_password($username);
		$password_encrypted=$this->encrypt_password($password);

		if($stored_password_encrypted==$password_encrypted) {
			return TRUE;
		}

		return FALSE;
	}

 /** \brief Checks if user is logged in via authorization key.
  *
  * @return status (bool)
  *
  */

	function is_logged_in() {
		if(isset($_SESSION[$this->session_id]["authorization_key"]) && $_SESSION[$this->session_id]["authorization_key"]==$this->make_authorization_key()) {
			return TRUE;
		}
		return FALSE;
	}

	 /** \brief Create authorization key in session
  *
  */

	function make_authorization_key($salt_value="") {
		if(empty($this->current_user)) {
			$this->current_user=$_SESSION[$this->session_id]["username"];
		}
		$salt_string=$this->current_user.$this->crypt_key.$_SERVER["REMOTE_ADDR"].$salt_value;
		return md5($salt_string);
	}


 /** \brief Stores data in $_SESSION[$this->session_id], if given no params, it stores authorization_key and username.
  *
  * @param field, optional (string)
  * @param data, optional (string)
  *
  */

	function store_user_session($field="", $data="") {
		if($field!="" && $data!="") {
			$_SESSION[$this->session_id][$field]=$data;
		} else {
			$_SESSION[$this->session_id]["authorization_key"]=$this->make_authorization_key();
			$_SESSION[$this->session_id]["username"]=$this->current_user;
		}
	}

	 /** \brief Encrypts password
  *
  * @param input_password (string)
  * @return encrypted password (string)
  *
  */

	function encrypt_password($input_password) {
		return md5($input_password.$this->crypt_key);	
	}

  /** \brief Retrieves stored password
  *
  * @param username (string)
  * @return password (string or FALSE)
  *
  */

	function get_stored_password($username) {
    if(!$this->user_exists($username)) {
     	return FALSE;
   	} else {
			return $this->get_user_field("password");
   }
	}

  /** \brief Checks if user is confirmed.
  *
  * @param username (string)
  * @return status (bool)
  *
  */

	 function is_confirmed($username) {
    if(!$this->user_exists($username)) {
      return FALSE;
    } else {
			$this->current_user=$username;
			$ret=$this->get_user_field("confirmed");
			if($ret==1) {
				return TRUE; 
			} 
      $this->error_code=3;
			return FALSE;
   }
  }

  /** \brief Checks if user exists
  *
  * @param username (string)
  * @return status (bool)
  *
  */

	function user_exists($username) {
			#$this->oci->bind("username",$username, 128);
     	#$this->oci->set_query("select username from userauth where username= :username");
     	$this->oci->set_query("select username from userauth where username='$username'");
			$a= $this->oci->fetch_into_assoc();
			if(!empty($a)) {
				$this->error_code=1;
				return TRUE;
			}
		return FALSE;
	}

  /** \brief Creates a user
  *
  * @param username (string)
  * @param password (string)
  * @param data, optional (string)
  * @return status (bool)
  *
  */

	function create_user($username, $password, $data="") {
		if($this->user_exists($username)) {
			return FALSE;
		} else {
			$password_encrypted=$this->encrypt_password($password);
			#$this->oci->bind("username",$username, 128);
			#$this->oci->bind("password",$password, 32);
			#$this->oci->bind("data",$data, 1);
 			#$this->oci->set_query("insert into userauth(username,password,settings,creation_date,lastmod) VALUES (:username,:password_encrypted,:data,SYSDATE,SYSDATE)");
 			$this->oci->set_query("insert into userauth(username,password,settings,creation_date,lastmod) VALUES ('$username','$password_encrypted','$data',SYSDATE,SYSDATE)");
			return TRUE;
		}
	}

  /** \brief Sets a choosen userfield to a value
  *
  * @param fieldname (string)
  * @param value (string)
  * @return status (bool)
  *
  */

	function set_user_field($fieldname, $value) {
	 if(!$this->user_exists($this->current_user)) {
      return FALSE;
    } else {
			$username=$this->current_user;
			
			#$this->oci->bind("username",$username, 128);
			if(is_int($value)) {
 				$this->oci->set_query("update userauth SET $fieldname=$value,lastmod=SYSDATE WHERE username='$username'");
			} else {
 				$this->oci->set_query("update userauth SET $fieldname='$value',lastmod=SYSDATE WHERE username='$username'");
			}
			$this->store_user_session($fieldname, $value);
			
			return TRUE;
		}
	}


  /** \brief Updates a users password
  * 
  * @param password (string)
  * @return status (bool)
  *
  */

	function update_user_password($password) {
    if(!$this->user_exists($this->current_user)) {
      return FALSE;
    } else {
			$this->set_user_field("password", $this->encrypt_password($password));
			return TRUE;
		}
	}

  /** \brief Removes a user
  *
  * @param username (string)
  * @return status (bool)
  *
  */


	function delete_user($username) {
		if(!$this->user_exists($username)) {
      return FALSE;
    }
		#$this->oci->bind("username",$username, 128);
    #$this->oci->set_query("delete from userauth WHERE username= :username");
    $this->oci->set_query("delete from userauth WHERE username='$username'");
		return TRUE;
	}

  /** \brief Logs out current user
  *
  * @return status (bool)
  *
  */
	function logout() {
		if(session_destroy()) {
			return TRUE;
		}
		return FALSE;
	}

  /** \brief Creates a user confirm key
  *
  * @param username (string)
  * @return confirm_key (string)
  *
  */

	function create_user_confirm_key($username) {
		return md5($username.$this->crypt_key);
	}

  /** \brief Confirm user (make user active)
  *
  * @param username (string)
  * @param username (string)
  * @return status (bool)
  *
  */

	function confirm_user($username, $user_confirm_key) {
    if(!$this->user_exists($username)) {
      return FALSE;
    } else {
			$this->current_user=$username;
			if($this->get_user_field("confirmed")==1) {
				return FALSE;
			}
			if($user_confirm_key==$this->create_user_confirm_key($username)) {
				$this->set_user_field("confirmed", 1);
      	return TRUE;
			}
    }
		return FALSE;
	}


  /** \brief Returns specified field value
  *
  * @param fieldname (string)
  * @return value (string or FALSE)
  *
  */

	function get_user_field($fieldname) {
		$username=$this->current_user;
		if(isset($_SESSION[$this->session_id][$fieldname])) {
			return $_SESSION[$this->session_id][$fieldname];
		} else {
			#$this->oci->bind("username",$username, 128);
     	#$this->oci->set_query("select $fieldname from userauth where username= :username");
     	$this->oci->set_query("select $fieldname from userauth where username='$username'");

			if($a= $this->oci->fetch_into_assoc()) {

				if($fieldname=="settings" && is_object($a["SETTINGS"])) {
					$value=$a["SETTINGS"]->load();
				} else {
					$value=$a[strtoupper($fieldname)];
				}
				$_SESSION[$this->session_id][$fieldname]=$value;

			}
			return $value;
		}
		return FALSE;
	}

}

?>
