<!-- This is the navigation bar fixed at the top of each webpage
visited by the suer -->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Hobby-Share</title>
    <link rel="stylesheet" href="./Css/Formatting.css">
    <link rel="stylesheet" href="./Css/Topnav.css">
    
</head>

<body>

<div class="topnav">
        
        <?php

        /*Active is the number of the page that is currently being visited
        If active==0 thenthe page being visited is not present on the navigation bar */
        if($active == 1){
            /*if active, the link gets the class active*/
            echo '<a class="active" href="./Index.php">Home</a>';
        }else{
            /*if not, the display does not change*/
            echo'<a href="./Index.php">Home</a>';
        }

        if($active == 2){
            echo '<a class="active" href="./Tag.php">Tags</a>'; 
        }else{
            echo '<a href="./Tag.php">Tags</a>';
        }

        if($active == 3){
            echo '<a class="active" href="">Discover</a>';
        }else{
            echo '<a href="">Discover</a>';
        }
        
        /*If login cookies are set, the navigation bar gets the option to logout */
        if( isset( $_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"])){
            if($active == 4){
                echo '<a class = "active" href="./Profile.php?ID='.$_COOKIE["ID"].'">My Profile</a>
            <a class="" href="./LogIn.php?connect=false.php">Log Out</a>';
            }else{
                echo '<a href="./Profile.php?ID='.$_COOKIE["ID"].'">My Profile</a>
            <a class="" href="./LogIn.php?connect=false.php">Log Out</a>';
            }
            
        }else{
            /*if login cookies are not set the navigation bar gets the option to login */
            if($active == 5){
                echo '<a class="active" href="./LogIn.php">Log In</a>';
            }else{
                echo '<a class="" href="./LogIn.php">Log In</a>';
            }
            
        }

        
        
        
        ?>
</div>

<?php
/*if(isset( $_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"])){
    
    include("Popup.php");
}*/

?>

<p></p>
    