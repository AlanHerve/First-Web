<?php
include("./databaseFunctions.php");
ConnectDatabase();

$active = 5;

include("Topnav.php");

/*if user is not already connected */
if(!isset($_GET["connect"])){
	$loginStatus = CheckLogin();
	if ( $loginStatus[0] ) {
		
		$redirect = "Location:Profile.php?ID=".$loginStatus[3];
	
		echo '<p><br><br><br>login :'.$loginStatus[3].'<br>babababa<br><br></p>';
		header($redirect);
	}elseif($loginStatus[2]!=NULL){
   echo'<div id=ErrorContainer>
			<p>'.$loginStatus[2].'</p>
		</div>';
	}

/*if user is already connected */
}else{
	DestroyLoginCookie();
	$redirect = "Location:Index.php";
	header($redirect);
}



?>

<!--Log in form -->
<div id=MainContainerF>
	<form action="./LogIn.php" class="formF" method="POST">
		<label for="mail">Email</label>
    	<input class="line" name="mail" placeholder="Email" type="email" required>
		<label for="password">Password</label>
    	<input class="line" type="password" name="password" placeholder="Password" required>
		<input type="hidden" name="connection" value="1">
    	<input type="submit" value="Log In">
	</form>
</div>


<div id=MainContainerF>
<p>You do not already have an account ? Create one <a href="./newAccount.php">here</a></p>
</div>




<?php
include("Footer.php");
DisconnectDatabase();
?>