<?php
include("databaseFunctions.php");
include("messageFunctions.php");
ConnectDatabase();

$redirect = "Location:Index.php";
if(!isset($_POST["kind"])){
    
    header($redirect);

}

$error = sendMessage($_POST["receiver"]);

if($error!=NULL){
    echo $error;
}else{
    header($redirect);
}

 



DisconnectDatabase();



?>