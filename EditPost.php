<?php
echo '<script type="text/javascript" src="./profile.js"></script>';
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
        
    //header($redirect);
        
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
    
    


    <form id="myForm" action="./EditPost.php?ID='.$_GET["ID"].'&SIDE=1" method="POST" enctype="multipart/form-data">
<input type="hidden" id="idOfPost" name="idOfPost" value='.$row["ID"].'>
<input type="hidden" value="1" name = "newPost" id="newPost">
<input type="hidden" name="owner" id="owner" value="'.$_GET["ID"].'">';  
echo ' <div class="conhobby" style="width:100%">

            <div class="titlehobby">
                <h1>'.$row["NOM"].'</h1>
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
                if($row["AVAILABLE"]==1){
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

            <textarea maxlength="100" class="post" name="content" placeholder="Ecrivez ici une petite description si vous le souhaitez">'.$row["DESCRIPTION"].'</textarea>

        </div>
        
        
    </div>' ;  
        
  echo '
       
        </form>
        </div>
        

        ';

        if(!$row["IMAGE"]){

            /*gets default image corresponding to the hobby, see fileFunctions.php */
            $default = getDefault($row["TYPEID"], 1);

            $error = $default[0];
            $image = $default[1];

            if($error == NULL){
                echo '<img id="imagePost'.$row["ID"].'&1" name="imagePost'.$row["ID"].'&1" class="hobby2" src="./Images/'.$image.'" onclick="zoomImage(this)">
                <input type="hidden" id="default1" name="default1" value="true">
                <input type="hidden" value="1" name="deleteImage1" id="deleteImage1" form="myForm">';
               
            
            }else{
                /*TODO */
            }
            
        } else{
            echo' <img id="imagePost'.$row["ID"].'&1" name="imagePost'.$row["ID"].'&1" class="hobby2" src="./uploads/'.$row["IMAGE"].'" onclick="zoomImage(this)">
            <input type="hidden" id="default1" name="default1" value="false">
            <input type="hidden" value="1" name="deleteImage1" id="deleteImage1" form="myForm">';
        }

       echo'</div>';

    }else{

        echo '<form id="myForm" action="./EditPost.php?ID='.$_GET["ID"].'&SIDE=2" method="POST" enctype="multipart/form-data">
        <input type="hidden" id="idOfPost" name="idOfPost" value='.$row["ID"].'>
        <input type="hidden" name="newPost" value="2">
        <div id="MainContainerP2" >';
                echo '
                    <div class="divP" style="border:solid">
                        <div class="conhobby">
                            <div class="titlehobby" >
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
                                    <textarea maxlength="100" class="post" name="content" placeholder="Ecrivez ici une petite description si vous le souhaitez">'.$row["DESCRIPTION"].'</textarea>';
                                }else{
                                    echo '<textarea maxlength="100" class="post" name="content" placeholder="Ecrivez ici une petite description si vous le souhaitez">'.$row["DESCRIPTION"].'</textarea>';
                    
                                }
             echo ' 
                            </div>
                        </div>
                
                
                     <div id="potentialGrid'.$row["ID"].'" class="potentialGrid">' ;  
                    $images = array("IMAGE1", "IMAGE2", "IMAGE3", "IMAGE4");  
                    $countImage = 0;
                    foreach($images as &$image){
                        
                        if($row[$image]!=NULL){
                            $countImage++;
                            echo '<img id="imagePost'.$row["ID"].'&'.$countImage.'" class="regularImage" src="./uploads/'.$row[$image].'" onclick="zoomImage(this)">
                            <input type="hidden" value="1" name="deleteImage'.$countImage.'" id="deleteImage'.$countImage.'" form="myForm">
                            <input type="hidden" id="default'.$countImage.'" name="default'.$countImage.'" value="false">';
                        }
                    }
                    echo '<input type="hidden" value="'.$countImage.'" id="numberOfImage" name="numberOfImage">';
                    if($countImage > 1){
                        echo '<script>resizeImages('.$row["ID"].', '.$countImage.');</script>';
                    }elseif($countImage == 0){
                        $default = getDefault($row["TYPEID"], 1);

                        $error = $default[0];
                        $image = $default[1];
    
                        if($error == NULL){
                            echo '<img id="imagePost'.$row["ID"].'&1" class="regularImage" src="./Images/'.$image.'" onclick="zoomImage(this)">
                            <input type="hidden" id="default1" name="default1" value="true">';
                           
                        
                        }else{
                            /*TODO */
                        }
                    }
                    echo '</div>
                    
            
                    </div><p></p>
                    
                    
            </div></form>';

    /*DISPLAY IMAGES, MAKE BUTTONS A NICE LITTLE CIRCLE IN THE CORNER */
        /*echo' <div id=MainContainerE>
            <h1>'.$row["NOM"].'</h1>
            <form id="myForm" action="./EditPost.php?ID='.$_GET["ID"].'&SIDE=2" method="POST" enctype="multipart/form-data">

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


            
            </form>

            
            
            ';*/
    }
    echo '<input type="hidden" id="typeOfPost" name="typeOfPost" value="'.$row["TYPEID"].'" form="myForm">'


?></div>
<input type="submit" value="Edit" form="myForm">

<div id="Modal" class="imageModal">

<!-- The Close Button -->
<span class="close">&times;</span>

<!-- Modal Content (The Image) -->
<img class="imageOfModal" id="ModalImage">

<!-- Modal Caption (Image Text) -->
<div id="caption"></div>

<input type="hidden" id="current" name="current" value="null">
<label for="img">Changer d'image :</label>
        <input type="file" name="fileToUpload1" id="fileToUpload1" class="uploadImagePrompt" form="myForm">
        <?php
            if($_GET["SIDE"]==2){
                echo '<input type="file" name="fileToUpload2" id="fileToUpload2" class="uploadImagePrompt" form="myForm">
                <input type="file" name="fileToUpload3" id="fileToUpload3" class="uploadImagePrompt" form="myForm">
                <input type="file" name="fileToUpload4" id="fileToUpload4" class="uploadImagePrompt" form="myForm">
                ';
            }
        
        ?>
    <div id="defaultPrompt">
        <label for="delete">Delete Image ?</label>
        <input type="submit" name="deleteImage" id="deleteImage" onclick="deleteImage()" value="Edit">
    </div>
</div>

<?php
include("Footer.php");
DisconnectDatabase();
?>