<link rel="stylesheet" href="./Css/Profile.css">
<link rel="stylesheet" href="./Css/Post.css">
<script type="text/javascript" src="./profile.js"></script>
<?php
include("./databaseFunctions.php");
include("./fileFunctions.php");
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
echo '<div id="MainContainerProfileSide1" >';
/* TODO : MAKE LINKS SPANS ? 
 * If user is the owner of the page, they get the option to edit or delete their posts
 */
if(isset($_COOKIE["ID"])){
    if($_COOKIE["ID"]==$_GET["ID"]){
        echo '<a title="Edit Post" href="./EditPost.php?ID='.$row["ID"].'&SIDE=1"><img src="./Images/Edit.png" class="circleButton"></a>';
        echo '<a title="Edit Post" href="./Confirm.php?ID='.$row["ID"].'&SIDE=1"><img src="./Images/Delete.png" class="circleButton" style="left:81%"></a>';
    }
}

/*Container containing : name of hobby, exeperience of user, the frequency at which the hobby is being done, as well as if the user wishes to do this hobby
 *with a group/partner 
 */

echo ' <div class="conhobby">

    <div class="titlehobby">
        <h1>'.$row["NOM"].'</h1>
        <div class="tagPost"><p class="tagLightColor">'.$row["EXPERIENCE"].'</p><p class="tagDarkColor">'.$row["FREQUENCY"].'</p>';

        /*Availability is stored in the SQL database as a binary value */
        if($row["AVAILABLE"]==1){
            echo '<p class="tagLightColor">Available</p>';
        }else{
            echo '<p class="tagLightColor">Not Available</p>';
        }
    echo '
    </div>
    </div>
    <div  class="description">';

    /*Checks if user decided to add a description to their hobby */
    if($row["DESCRIPTION"]){
        echo'
        <h4>Descritpion</h4>
        <p >'.$row["DESCRIPTION"].'</p>';
    }else{
        /*if user has decided to not add a description display message : */
        echo '<p class="descriptionText" style="color:gray"><i>This user does not seem to have any description for this hobby</i></p>';
    }
     echo '</div>
     </div>' ;  
    /*If post does not have an image : */
if(!$row["IMAGE"]){

    /*gets default image corresponding to the hobby, see fileFunctions.php */
    $default = getDefault($row["TYPEID"], 1);

    $error = $default[0];
    $image = $default[1];

    if($error == NULL){
        echo '<img class="uniqueImageHobby" src="./Images/'.$image.'" onclick="zoomImage(this)">
       
    </div>';
    }else{
        /*TODO */
    }
    
} else{
    echo' <img class="uniqueImageHobby" src="./uploads/'.$row["IMAGE"].'" onclick="zoomImage(this)">
       
    </div>';
}
    


break;
case 2:
/*Confirmation for deleting a post */

echo '<div id="MainContainerProfileSide2" >';
                if(isset($_COOKIE["ID"]) && isset($_COOKIE["ID"])){
                    if($_COOKIE["ID"]==$_GET["ID"]){
                        echo '<a title="Edit Post" href="./EditPost.php?ID='.$row["ID"].'&SIDE=2"><img src="./Images/Edit.png" class="circleButton"></a>';
                        echo '<a title="Edit Post" href="./Confirm.php?ID='.$row["ID"].'&SIDE=2"><img src="./Images/Delete.png" class="circleButton" style="left:81%"></a>';
                    }
                }
                echo '
                    <div class="divFlexRow">
                        <div class="conhobby">
                            <div class="titlehobby" >
                                <h1>'.$row["NOM"].'</h1>';
                                if($row["MODIFIED"]==1){
                                    echo '<h2>(Modifié le AJOUTER DATE)</h2>';
                                }
                    echo '
                            </div>
                            <div  class="description">
                        ';
                                if($row["DESCRIPTION"]){
                                    echo'<h4>Descritpion</h4>
                                         <p class="descriptionText">'.$row["DESCRIPTION"].'</p>';
                                }else{
                                    echo '<p class="descriptionText" style="color:gray"><i>This user does not seem to have any description for this post</i></p>';
                    
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
                            echo '<img id="imagePost'.$row["ID"].'&'.$countImage.'" class="regularImage" src="./uploads/'.$row[$image].'" onclick="zoomImage(this)">';
                        }
                    }
                    
                    if($countImage > 1){
                        echo '<script>resizeImages('.$row["ID"].', '.$countImage.');</script>';
                    }elseif($countImage==0){
                        $default = getDefault($row["TYPEID"], 1);

                        $error = $default[0];
                        $image = $default[1];
    
                        if($error == NULL){
                            echo '<img id="imagePost'.$row["ID"].'&1" class="regularImage" src="./Images/'.$image.'" onclick="zoomImage(this)">
                           
                        ';
                        }else{
                            /*TODO */
                        }
                    }
                    echo '</div>
                    </div>
                
                </div>';




break;
}
    




DisconnectDatabase();

?>

<!--The form asking the user for confirmation-->

<div class="confirmPrompt">
    <form method="post">
        <h1>Do you really want to delete this post ?</h1>
        <input  type="submit" class="confirm" name="confirm" value="Yes">
        <input  type="submit" class="confirm" name="confirm" value="No">
    </form>
</div>

<div id="Modal" class="imageModal">
    
        <!-- The Close Button -->
        <span id="closeModal" class="closeModal">&times;</span>

        <!-- Modal Content (The Image) -->
        <img class="imageOfModal" id="ModalImage">

        <!-- Modal Caption (Image Text) -->
        <div id="caption">
        </div>
    </div>
</div>

<?php>
include("Footer.php");
?>