<?php
include("databaseFunctions.php");
ConnectDatabase();
$active = 0;
include("Topnav.php");

$redirect = "Location:Index.php";
if(!isset($_GET["ID"]) || !isset($_COOKIE["ID"]) && !isset($_POST["new"])){
    DisconnectDatabase();
    header($redirect);
}

if(isset($_GET["ID"]) && isset($_COOKIE["ID"])){
    if($_GET["ID"]!=$_COOKIE["ID"]){
        DisconnectDatabase();
        header($redirect);
    }
}

$status = editProfile();

$row = $status[0];




if($status[1]){
   
    $redirect = "Location:Profile.php?ID=".$_GET["ID"];
    header($redirect);
}elseif($status[2]!=NULL){
    echo'<div id=ErrorContainer>
	<p>'.$status[2].'</p>
</div>';
}
if(!$row){
    DisconnectDatabase();
    header($redirect);
}



?>




<div id=confirmPrompt>
<?php
echo '
<form action="./EditProfile.php?ID='.$_GET["ID"].'" method="POST">
<input type="hidden" name="new" value="present">
<input type="text" name="name" placeholder="'.$row["NOM"].'">
<input type="text" name="nickName" placeholder="'.$row["NICKNAME"].'">
<input type="password" name="current_password">
<input type="password" name="password">
<input type="password" name="confirm">
<button type="submit">Submit</button>
<p>CHANGEMNT D IMAGE</p>
</form>';


include("Footer.php");
DisconnectDatabase();
?>