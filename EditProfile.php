<link rel="stylesheet" href="./Css/Post.css">
<link rel="stylesheet" href="./Css/Profile.css">
<script type="text/javascript" src="./profile.js"></script>

<?php
include("databaseFunctions.php");
include("fileFunctions.php");
ConnectDatabase();
$active = 0;
include("Topnav.php");

$redirect = "Location:Index.php";
if(!isset($_GET["ID"]) || !isset($_COOKIE["ID"]) && !isset($_POST["new"])){
    DisconnectDatabase();
    header($redirect);
}elseif(isset($_GET["ID"]) && isset($_COOKIE["ID"])){
    if($_GET["ID"]!=$_COOKIE["ID"]){
        DisconnectDatabase();
        header($redirect);
    }
}
$status = editProfile();

$row = $status[0];




if($status[1]){
    DisconnectDatabase();
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

<div id="MainContainerF">
    Welcome to the Profile Edition page !
</div>


<div id=confirmPrompt>
<?php
echo ' 
<div id=MainContainerF>
    <form action="./EditProfile.php?ID='.$_GET["ID"].'" class="formF" method="POST" id="myForm" name="myForm" enctype="multipart/form-data">
        <input type="hidden" id="idOfPost" name="idOfPost" value='.$row["ID"].'>
        <input type="hidden" value="1" name = "processForm" id="processForm">
        <input class="line" type="hidden" name="new" value="present">
        <label for="name">Change name :</label>
        <input class="line" type="text" name="name" placeholder="'.$row["NAME"].'" maxlength="30">
        <label for="name">Change nickname :</label>
        <input class="line" type="text" name="nickName" placeholder="'.$row["NICKNAME"].'" maxlength="30">
        <label for="name">Current password (necessary to change password):</label>
        <input class="line" type="password" name="current_password" maxlength="30">
        <label for="name">Change password :</label>
        <input class="line" type="password" name="password" maxlength="30">
        <label for="name">Confirm new password :</label>
        <input class="line" type="password" name="confirm">
        <label for="imagePost'.$row["ID"].'&1">Click on Image to change it :</label>
        ';
        
        if($row["AVATAR"]!=NULL){
            echo '      <img id="imagePost'.$row["ID"].'&1" class="pic zoomable" src="./uploads/'.$row["AVATAR"].'" onclick="zoomImage(this)">
                <input type="hidden" id="default1" name="default1" value="false">
                <input type="hidden" value="1" name="deleteImage1" id="deleteImage1" form="myForm">';
                    }else {
                        /*If person does not have an avatar, display the default image */
            echo '      <img id="imagePost'.$row["ID"].'&1" class="pic zoomable" src="./Images/img_avatar.png" onclick="zoomImage(this)">
            <input type="hidden" id="default1" name="default1" value="true">
                <input type="hidden" value="1" name="deleteImage1" id="deleteImage1" form="myForm">';
                    }

            
  echo' <input type="submit" value="Submit">
        
    </form>
</div>';

?>


<div id="Modal" class="imageModal">
    
        <!-- The Close Button -->
        <span id="closeModal" class="closeModal">&times;</span>

        <!-- Modal Content (The Image) -->
        <img class="imageOfModal" id="ModalImage">

        <!-- Modal Caption (Image Text) -->
        <div id="caption"></div>

        <input type="hidden" id="current" name="current" value="null">

        <div class="changePrompt">
        <label for="img">Change image :</label>
            <input type="file" name="fileToUpload1" id="fileToUpload1" class="uploadImagePrompt" form="myForm">
            
           
            
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