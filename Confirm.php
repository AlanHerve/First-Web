<?php
include("databaseFunctions.php");
ConnectDatabase();
$active = 0;
include("./Topnav.php");

$redirect = "Location:Index.php";

if(!isset($_GET["ID"]) || !isset($_COOKIE["ID"]) || !isset($_GET["SIDE"])){
    DisconnectDatabase();
    header($redirect);
}else if(isset($_POST["confirm"])){
    if($_POST["confirm"]=="Yes") suppressPost($_GET["SIDE"]);
    DisconnectDatabase();
    $redirect = "Location:Profile.php?ID=".$_COOKIE["ID"]."&SIDE=".$_GET["SIDE"];
    header($redirect);
}

$test = validate($_GET["SIDE"]);
$valid = $test[0];
$row = $test[1];

if(!validate($_GET["SIDE"])){
    DisconnectDatabase();
    header($redirect);
}

/*Confirm.php will display the profile the user want to display
 * Thanks to this, the user will be sure to delete the right post or hobby 
 */
switch($_GET["SIDE"]){
case 1:
/*Confirmation for deleting a hobby */
    echo '<div id="MainContainerP">
         <div class="conhobby">

        <div class="titlehobby">
            <h1>'.$row["NOM"].'</h1>
            <p class="tagExperience">'.$row["EXPERIENCE"].'</p>
            <p class="tagFrequence">'.$row["FREQUENCY"].'</p>';
            if($row["AVAILABLE"]==1){
                echo '<p class="tagAvailable">Available</p>';
            }else{
                echo '<p class="tagUnAvailable">Not Available</p>';
            }

        echo '
        </div>
        <div class="charahobby">';
            
            
            
        if($row["DESCRIPTION"]){
            echo'<h4>Descritpion</h4>
            <p>'.$row["DESCRIPTION"].'</p>';
        }else{
            echo '<p style="color:gray"><i>This user does not seem to have any description for this hobby</i></p>';
        }
         echo '</div>
         </div>' ;  
        
    
    if(!$row["IMAGE"]){
        echo '<img class="hobby2" src="./Images/Filler3.png">
           
        </div>';
    } else{
        echo' <img class="hobby2" src="./uploads/'.$row["IMAGE"].'">
           
        </div>';
    }

    


break;
case 2:
/*Confirmation for deleting a post */

    echo '<div id="MainContainerP">
    <div class="conhobby">
        <div class="titlehobby">
            <h1>'.$row["NOM"].'</h1>';
            if($row["MODIFIED"]==1){
                echo '<h2>(Modifié le AJOUTER DATE)</h2>';
            }
        echo '
        </div>
        <div class="charahobby">
            ';
        if($row["DESCRIPTION"]){
            echo'<h4>Descritpion</h4>
            <p>'.$row["DESCRIPTION"].'</p>';
        }
 echo ' 
 
        </div>
    </div>' ;  
        
    /*TROUVER MEILLEUR MOYEN D'UPLOAD DES NULLS */
    if(!$row["IMAGE"] || $row["IMAGE"]=="NULL"){
        echo '<img class="hobby2" src="./Images/Filler3.png"> 
        
        </div>';
    } else{
        echo' <img class="hobby2" src="./uploads/'.$row["IMAGE"].'">
        </div>';
    }

break;
}
    




DisconnectDatabase();

?>

<!--The form asking the user for confirmation-->

<div id=MainContainer>
<form method="post">
<h1>Do you really want to delete this post ?</h1>
<input class="confirm" type="submit" name="confirm" value="Yes">
<input class="confirm" type="submit" name="confirm" value="No">
</form>
</div>


</body>
<footer>baahbahbah</footer>
</html>