<?php
include("databaseFunctions.php");
ConnectDatabase();
$active = 0;
echo '<p><br><br></p>';
$newAccount = CheckNewAccountForm();
include("Topnav.php");


if($newAccount[1]){
    $redirect = "Location:Profile.php?ID=".$newAccount[3];
    session_start();
    $_SESSION["inscription"]=1;
    header($redirect);
   
    
}

if($newAccount[2]!=NULL){
    echo'<div id=ErrorContainer>
			<p>'.$newAccount[2].'</p>
		</div>';
}
?>




<div id="MainContainerF">

<form action="./newAccount.php" class="formF" method="POST">
    <input class="line" type="hidden" name="new" value="new">
    <label for="name">Username</label>
    <input class="line" type="text" name="name" placeholder="Name" maxlength="30" required>
    <label for="nickName">Nickname</label>
    <input class="line" type="text" name="nickName" placeholder="NickName" maxlength="30">
    <label for="mail">Email</label>
    <input class="line" name="mail" placeholder="email" type="email" maxlength="60" required />
    <label for="password">Password</label>
    <input class="line" type="password" name="password" placeholder="Password" maxlength="20" required>
    <label for="confirm">Confirm password</label>
    <input class="line" type="password" name="confirm" placeholder="Confirm Password" maxlength="30" required>
    <input class="line" type="submit" value="Submit">
</form>

</div>

</body>





</html>

<?php
include("Footer.php");
DisconnectDatabase();
?>