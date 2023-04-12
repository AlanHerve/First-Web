<?php
include("databaseFunctions.php");
ConnectDatabase();
$active = 0;
echo '<p><br><br></p>';
$newAccount = CheckNewAccountForm();
include("Topnav.php");


if($newAccount[1]){
    $redirect = "Location:Profile.php?ID=".$newAccount[3];
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
    <label for="name">Nom d'utilisateur</label>
    <input class="line" type="text" name="name" placeholder="Name" required>
    <label for="nickName">Surnom</label>
    <input class="line" type="text" name="nickName" placeholder="NickName">
    <label for="mail">Email</label>
    <input class="line" name="mail" placeholder="email" type="email" required />
    <label for="password">Mot de Passe</label>
    <input class="line" type="password" name="password" placeholder="Password" required>
    <label for="confirm">Confirmation du Mot de Passe</label>
    <input class="line" type="password" name="confirm" placeholder="Confirm Password" required>
    <input class="line" type="submit" value="Submit">
</form>

</div>

</body>





</html>

<?php
include("Footer.php");
DisconnectDatabase();
?>