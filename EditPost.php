<?php

include("databaseFunctions.php");
ConnectDatabase();
$active = 0;

include("Topnav.php");
include("fileFunctions.php");

/*MUST CHECK IF :
-cookie ID = owner
-Post belong to you
-Post ID exists*/
echo '<p><br><br><br></p>';


$redirect = "Location:Index.php";

if(!isset($_GET["ID"]) || !isset($_COOKIE["ID"]) || !isset($_GET["SIDE"])){
    DisconnectDatabase();
    header($redirect);
}elseif(!validate($_GET["SIDE"])){
    DisconnectDatabase();
   header($redirect);
}else if(isset($_POST["erase"])){
    suppressPost($_GET["SIDE"]);
    DisconnectDatabase();
    $redirect = "Location:Profile.php?ID=".$_COOKIE["ID"]."&SIDE=".$_GET["SIDE"];
    header($redirect);
}
/*REGARDER SI ENLEVER REDIRECT POSE PROBLEME */

 
    $status = editPost($_GET["SIDE"]);

    $error = $status[0];
    $row = $status[1];


    if($error!=NULL){
        echo'<div id=ErrorContainer>
                <p>'.$error.'</p>
            </div>';
    }elseif(isset($_POST["newPost"])){
        /*TRY TO MAKE IT A NOTIFICATION */
    $redirect = "Location:Profile.php?ID=".$row["OWNER"]."&SIDE=".$_GET["SIDE"];
        
    header($redirect);
        
    }


    $test = validate($_GET["SIDE"]);
    $valid = $test[0];
    $row = $test[1];

    if(!validate($_GET["SIDE"])){
    DisconnectDatabase();
    header($redirect);
    }

    if($_GET["SIDE"]==1){

        

        



        echo '<div id="MainContainerP">
                <div>
    
    


    <form action="./EditPost.php?ID='.$_GET["ID"].'&SIDE=1" method="POST" enctype="multipart/form-data">
<input type="hidden" value="1" name = "newPost" id="newPost">
<input type="hidden" name="owner" id="owner" value="'.$_GET["ID"].'">';  
echo ' <div class="conhobby" style="width:100%">

            <div class="titlehobby">
                <h1>Edit</h1>
                <p class="tagExperience">
                <select class="post"  name="experience">';
                $text = array("Débutant", "Avancé", "Intermédiaire", "Expert", "Occasionnel");
                $value = array("Debutant", "Avance", "Intermediaire","Expert", "Occasionnel");
                $index =0;
                foreach ($value as &$value_i) {
                    if($row["EXPERIENCE"]==$value_i){
                        echo '<option value='.$value_i.' selected>'.$text[$index].'</option>';
                    }else{
                        echo '<option value='.$value_i.'>'.$text[$index].'</option>';
                    }
        
                    $index++;
                }
                echo '
                </select></p>
                <p class="tagFrequence">
                <select class="post"  name="frequence">';
                $text = array("Quotidien", "3 à 4 fois par semaine", "2 à 3 fois par semaine", "Hebdomadaire", "Mensuel", "Rarement");
                $value = array("Quotidien", "3-4/semaine", "2-3/Semaine", "Hebdomadaire", "Mensuel", "Rarement");
                $index =0;
                foreach ($value as &$value_i) {
                    if($row["FREQUENCY"]==$value_i){
                        echo '<option value='.$value_i.' selected>'.$text[$index].'</option>';
                    }else{
                        echo '<option value='.$value_i.'>'.$text[$index].'</option>';
                    }
        
                    $index++;
                }
                echo '
                </select></p>';
            
          echo '<p class="tagAvailable">
                <select class="post" name="available">';
                if($row["AVAILABLE"==1]){
                    echo '<option value="Yes" selected>Available</option>
                    <option value="No">Not Available</option>';
                }else {
                    echo '<option value="Yes">Available</option>
                    <option value="No" selected>Not Available</option>';
                }
                    
                echo '
                 </select></p>
           
            </div>
        <div class="charahobby">

            <textarea class="post" name="content" placeholder="Ecrivez ici une petite description si vous le souhaitez">'.$row["DESCRIPTION"].'</textarea>

        </div>
        <label for="img">Une image pour illustrer :</label>
        <input type="file" name="fileToUpload" id="fileToUpload">
        
    </div>' ;  
        
    


        

        
  echo '
        <input type="submit" value="Edit">
        </form>
        </div>
        

        <img class="hobby2" src="./Images/Filler3.png">

        </div>';

    }else{

        

    /*DISPLAY IMAGES, MAKE BUTTONS A NICE LITTLE CIRCLE IN THE CORNER */
        echo' <div id=MainContainerE>
            <h1>'.$row["NOM"].'</h1>
            <form action="./EditPost.php?ID='.$_GET["ID"].'&SIDE=2" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="newPost" value="2">
            <input type="file" name="fileToUpload" id="fileToUpload">
            <p></p>
            <input type="file" name="fileToUpload2" id="fileToUpload2">
            <p></p>
            <input type="file" name="fileToUpload3" id="fileToUpload3">
            <p></p>
            <input type="file" name="fileToUpload4" id="fileToUpload4">
            <p></p>
    

            <textarea style="resize:none" name="content" placeholder="Ecrivez ici une petite description si vous le souhaitez">'.$row["DESCRIPTION"].'</textarea>


            <button type="submit">Edit Post</button>
            </form>
            ';
    }


?>

</div>


<?php
include("Footer.php");
DisconnectDatabase();
?>