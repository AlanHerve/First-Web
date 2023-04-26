<?php

$active = 1;

include("Topnav.php");



?>
<p><br></p>

    <div>
        <h1>Hobby<br>Share</h1>
    </div>

    <div id="MainContainerI">
        <h2>Hobby-Share : The best way to share your hobbies</h2>
    </div>

    <div id="MainContainerF">
     <p>Hobby-Shares allow you to share your hobbies on your profile for the entire website to see</p>
     <p>Users will be able to see wether or not you are available for a group and may contact you</p>

     <p>You can also post about something related to one of your hobbies !</p>
    </div>


    


<?php
if(isset( $_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"]) && !isset($_GET["ID"])){
    include("Popup.php");
    displayPopUp(NULL);
}

include("Footer.php");
?>