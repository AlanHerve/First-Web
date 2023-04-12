<?php

$active = 1;

include("Topnav.php");



?>
<p><br></p>

    <div>
        <h1>Hobby<br>Share</h1>
    </div>

    <div id="MainContainerI">
        <h2>Hobby-Share : le meilleur moyen de trouver un groupe pour vivre son hobby</h2>
        
        

    </div>


    


<?php
if(isset( $_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"]) && !isset($_GET["ID"])){
    include("Popup.php");
    displayPopUp(NULL);
}

include("Footer.php");
?>