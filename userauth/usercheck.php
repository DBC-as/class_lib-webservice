<html>
<head>
<title>usercheck</title>

<style type="text/css">
<!--
 label
{
width: 4em;
float: left;
text-align: right;
margin-right: 4.5em;
display: block
}

.submit input
{
margin-left: 2.5em;
}

-->
</style>

</head>
<body>

<?php

require("wwz/std_func.phpi");
require("wwz/userauth_class.phpi");

if(isset($_POST['username']) || isset($_GET["username"])) {
	$username=$_REQUEST['username'];
}

if(isset($_POST['password'])) {
	$password=$_POST["password"];
}

show_form();

$ul=new userauth("oracle");
$ul->set_oci_login("vip_kig/gakpiv@dora1");
if(isset($username) && $ul->user_exists($username)) {

		$ul->set_current_user($username);

		if(isset($_GET["confirm"])) {
			$ul->confirm_user($username, $ul->create_user_confirm_key($username));
			echo "Bruger: $username er nu godkendt.<P>";
		}


	if($_POST["submit"]=="Check bruger") {

		if($ul->get_user_field("confirmed")==1) {
			$confirm="ja";
		} else {
			$confirm="nej"."(<a href='?username=$username&confirm=1'>Godkend bruger</a>)";
		}

		echo "Brugernavn: ".$ul->get_user_field("username");
		echo "<br>";
		echo "Godkendt: ".$confirm;
		echo "<br>";
		echo "Oprettet dato: ".$ul->get_user_field("creation_date");
	}

	if($_POST["submit"]=="Skift password") {
		if(isset($password)) {
			$ul->set_current_user($username);
			$ul->update_user_password($password);

			echo "Password for $username ændret til $password";

		} else {
			echo "Password ikke udfyldt";

		}

	}
	
	$ul->logout();
} else {
	if(isset($username)) {
	echo "Brugeren findes ikke.";
	}
}


function show_form() {
	echo '<form method="POST" action="http://vision/~mkr/usercheck.php">';
	echo '<p><label for="name">Brugernavn:</label> <input type="text" name="username" id="name" value="'.$username.'"></p>';
	echo '<p><input type="submit" name="submit" value="Check bruger"></p>';
	echo '</form>';
	echo '<hr>';
  echo '<form method="POST" action="http://vision/~mkr/usercheck.php">';
  echo '<p><label for="name">Brugernavn:</label> <input type="text" id="name" name="username" value="'.$username.'"></p>';
  echo '<p><label for="password">Password:</label> <input type="text" id="password" name="password"></p>';
  echo '<p><input type="submit" name="submit" value="Skift password"></p>';
  echo '</form>';
	echo '<hr>';

}

?>
</body>
</html>
