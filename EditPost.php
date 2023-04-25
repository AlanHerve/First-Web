<link rel="stylesheet" href="./Css/Post.css">
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
/*If user isn't a connected user, redirects them */
if(!isset($_GET["ID"]) || !isset($_COOKIE["ID"]) || !isset($_GET["SIDE"])){
    DisconnectDatabase();
    header($redirect);

/*If user isn't the owner of the post, redirects thel */
}elseif(!($test = validate($_GET["SIDE"]))){
   DisconnectDatabase();
   header($redirect);

/*If form has been filled */
}else if(isset($_POST["erase"])){
    suppressPost($_GET["SIDE"]);
    DisconnectDatabase();
    $redirect = "Location:Profile.php?ID=".$_COOKIE["ID"]."&SIDE=".$_GET["SIDE"];
    header($redirect);
}

    $status = editPost($_GET["SIDE"]);

    $error = $status[0];
    $row = $status[1];


    if($error!=NULL){
        echo'<div id=ErrorContainer>
                <p>'.$error.'</p>
            </div>';
    }elseif(isset($_POST["processForm"])){
       
        $redirect = "Location:Profile.php?ID=".$row["OWNER"]."&SIDE=".$_GET["SIDE"];
        
        header($redirect);
        
    }


    
    $valid = $test[0];
    $row = $test[1];

  
    /*If editing a post of type "hobby" */
    if($_GET["SIDE"]==1){


        echo '<div id="MainContainerProfileSide1">
                <div>

                    <form id="myForm" action="./EditPost.php?ID='.$_GET["ID"].'&SIDE=1" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="idOfPost" name="idOfPost" value='.$row["ID"].'>
                        <input type="hidden" value="1" name = "processForm" id="processForm">
                        <input type="hidden" name="owner" id="owner" value="'.$_GET["ID"].'">';  
                 echo ' <div class="conhobby" style="width:100%">';
                        /*display the time of post and if post was modified */
                        if($row["MODIFIED"]==1){
                        echo '<p style="color:gray"><i>Modified '.formatDate($row["TIME"]).'</i></p>';
                        }else{
                        echo '<p style="color:gray"><i>Posted '.formatDate($row["TIME"]).'</i></p>';
                        }
                        /*echoes the name of the hobby and selectors allowing user to choose
                        * their experience, frequency, and availability
                        */
                      echo '<div class="titlehobby">
                                <h1>'.$row["HOBBY_NAME"].'</h1>
                                <div class="tagPost">
                                    <p class="tagLightColor">
                                        <select class="post"  name="experience">';
                                        $value = array("Beginner", "Intermediate", "Advanced", "Expert", "Casual");
         
                                        /*Selects the right level of experience when opening the page*/
                                        foreach ($value as &$value_i) {
                                            if($row["EXPERIENCE"]==$value_i){
                                                echo '<option value='.$value_i.' selected>'.$value_i.'</option>';
                                            }else{
                                                echo '<option value='.$value_i.'>'.$value_i.'</option>';
                                        }
        

                    }   
                                  echo '</select>
                                    </p>
                                    <p class="tagDarkColor">
                                        <select class="post"  name="frequence">';
                                        $value = array("Daily", "3-4/week", "2-3/week", "Weekly", "Montly", "Rarely");

                                        /*Selects the right Frequency when opening the page*/
                                        foreach ($value as &$value_i) {
                                            if($row["FREQUENCY"]==$value_i){
                                                echo '<option value='.$value_i.' selected>'.$value_i.'</option>';
                                            }else{
                                                echo '<option value='.$value_i.'>'.$value_i.'</option>';
                                            }
        
                                        }
                                  echo '</select>
                                    </p>
                                    <p class="tagLightColor">
                                    <select class="post" name="available">';
                                    /*availability is stored as a binary value in sql database */
                                    if($row["AVAILABLE"]==1){
                                        echo '<option value="Yes" selected>Available</option>
                                              <option value="No">Not Available</option>';
                                    }else {
                                        echo '<option value="Yes">Available</option>
                                                <option value="No" selected>Not Available</option>';
                                    }
                    
                                echo '
                                    </select>
                                    </p>
                                </div>
                            </div>
                            <div class="charahobby">
                                <textarea maxlength="100" class="post" name="content" placeholder="Ecrivez ici une petite description si vous le souhaitez">'.$row["DESCRIPTION"].'</textarea>
                            </div>
                        </div>' ;  
        
  echo '
                    </form>
                </div>';

        /*if post does not have an image */
        if(!$row["IMAGE"]){

            /*gets default image corresponding to the hobby, see fileFunctions.php */
            $default = getDefault($row["TYPEID"], 1);

            $error = $default[0];
            $image = $default[1];

            if($error == NULL){
                echo '<img id="imagePost'.$row["ID"].'&1" name="imagePost'.$row["ID"].'&1" class="uniqueImageHobby" src="./Images/'.$image.'" onclick="zoomImage(this)">
                <input type="hidden" id="default1" name="default1" value="true">
                <input type="hidden" value="1" name="deleteImage1" id="deleteImage1" form="myForm">';
               
            
            }else{
                /*TODO */
            }
            
        } else{
            echo' <img id="imagePost'.$row["ID"].'&1" name="imagePost'.$row["ID"].'&1" class="uniqueImageHobby" src="./uploads/'.$row["IMAGE"].'" onclick="zoomImage(this)">
            <input type="hidden" id="default1" name="default1" value="false">
            <input type="hidden" value="1" name="deleteImage1" id="deleteImage1" form="myForm">';
        }

       echo'</div>';

    /*if editing a post of type "regular" */
    }else{

        echo '<form id="myForm" action="./EditPost.php?ID='.$_GET["ID"].'&SIDE=2" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="idOfPost" name="idOfPost" value='.$row["ID"].'>
                <input type="hidden" name="processForm" value="2">
                <div id="MainContainerProfileSide2" >';
                echo '
                    <div class="divFlexRow" style="border:solid">
                        <div class="conhobby">';
                        if($row["MODIFIED"]==1){
                            echo '<p style="color:gray"><i>Modified '.formatDate($row["TIME"]).'</i></p>';
                         }else{
                            echo '<p style="color:gray"><i>Posted '.formatDate($row["TIME"]).'</i></p>';
                         }
                              echo' <div class="titlehobby" >
                                        <h1>'.$row["HOBBY_NAME"].'</h1>';
                                        if($row["MODIFIED"]==1){
                                            echo '<h2>(Modifié le AJOUTER DATE)</h2>';
                                        }
                    echo '
                                    </div>
                                    <div class="charahobby">';
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
                                    /*if we have more than one image, resizes and rearrange them */
                                    echo '<script>resizeImages('.$row["ID"].', '.$countImage.');</script>';
                                }elseif($countImage == 0){
                                    $default = getDefault($row["TYPEID"], 1);

                                    $error = $default[0];
                                    $image = $default[1];
    
                                    if($error == NULL){
                                        /*allows user to zoom on image */
                                        echo '<img id="imagePost'.$row["ID"].'&1" class="regularImage" src="./Images/'.$image.'" onclick="zoomImage(this)">
                                             <input type="hidden" id="default1" name="default1" value="true">';
                                    }else{
                                        /*TODO */
                                    }
                                }
                          echo '</div>
                            </div>
                            <p></p>
                        </div>
                    </form>';

    }

    
    echo '<input type="hidden" id="typeOfPost" name="typeOfPost" value="'.$row["TYPEID"].'" form="myForm">'


?></div>

<!-- Submit button -->
<input type="submit" value="Edit" form="myForm">

<!-- Modal displaying images user wants to see zoomed
 * also allows user to change images -->
 
<div id="Modal" class="imageModal">
    
        <!-- The Close Button -->
        <span id="closeModal" class="closeModal">&times;</span>

        <!-- Modal Content (The Image) -->
        <img class="imageOfModal" id="ModalImage">

        <!-- Modal Caption (Image Text) -->
        <div id="caption"></div>

        <!--stores id of the current image -->
        <input type="hidden" id="current" name="current" value="null">
        <div class="changePrompt">
            <label for="img">Changer d'image :</label>
            <input type="file" name="fileToUpload1" id="fileToUpload1" class="uploadImagePrompt" form="myForm">
            <?php
                /*Regular post are allowed to have more than one image*/
                if($_GET["SIDE"]==2){
              echo '<input type="file" name="fileToUpload2" id="fileToUpload2" class="uploadImagePrompt" form="myForm">
                    <input type="file" name="fileToUpload3" id="fileToUpload3" class="uploadImagePrompt" form="myForm">
                    <input type="file" name="fileToUpload4" id="fileToUpload4" class="uploadImagePrompt" form="myForm">
                    ';
                }
        
        ?>
        </div>
        <div id="defaultPrompt">
        <label for="delete">Delete Image ?</label>
        <input type="submit" name="deleteImage" id="deleteImage" onclick="deleteImage()" value="Delete Image">
        </div>
    
</div>

<?php
include("Footer.php");
DisconnectDatabase();
?>