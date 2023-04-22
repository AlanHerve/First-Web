<link rel="stylesheet" href="./Css/Post.css">

<?php
include("databaseFunctions.php");
include("fileFunctions.php");


ConnectDatabase();

/*if user isn't connected or one of the connection cookies is missing */
if(!isset($_COOKIE["ID"]) || !isset($_GET["ID"]) || !isset($_GET["SIDE"])){
    $redirect = "Location:Index.php";
    header($redirect);

/*If new post form has been filled */
}elseif(isset($_POST["newPost"])){

    /*newPost only returns a potential error */
    $error = newPost($_GET["SIDE"]);

    /*MAKE SURE YOU CANT GO BACK TO NEW POST OR ERASE POST */
    if($error == NULL){
        $redirect = "Location:Profile.php?ID=".$_COOKIE["ID"]."&SIDE=".$_GET["SIDE"];
       header($redirect);
      
    }else{
        echo'<div id=ErrorContainer>
			<p>'.$error.'</p>
		</div>';
    }

}

/*This page isn't shown on the top navigation bar */
$active = 0;
include("Topnav.php");

if($_GET["SIDE"]==1){

    /*User can't have the same hobby twice on his page */
    $status =hobbiesRemaining();

    if($status[1]!=NULL){
      echo '<div id=ErrorContainer>
	            <p>'.$status[1].'</p>
            </div>';
}
    

      echo '<div id="MainContainerProfileSide1">
                <div>
                    <form id="myForm" action="./newPost.php?ID='.$_GET["ID"].'&SIDE=1" method="POST" enctype="multipart/form-data">
                        <input type="hidden" value="1" name = "newPost" id="newPost">
                        <input type="hidden" name="owner" id="owner" value="'.$_GET["ID"].'">
                        <div class="conhobby" style="width:100%">
                            <label for="Nom">Hobby :</label>
                            <div class="titlehobby">
                                <h1><select class="title" name="Nom">';
                                /*Allows user to choose which hobbies they wish to add
                                 * each option contains the name and the ID of the hobby
                                 */
                                while($row = $status[0]->fetch_assoc()){
                                    $s = (string) $row["ID"];
                                    $p = (string) $row["NOM"];
                                    echo '<option value="'.$s.'|'.$p.'">'.$row["NOM"].'</option>';
                
                                }
?>
                                </select></h1>
                                <p class="tagLightColor">
                                    <select class="post"  name="experience">
                                        <option value="Debutant">Beginner</option>
                                        <option value="Avance">Intermediate</option>
                                        <option value="Intermediaire">Advanced</option>
                                        <option value="Expert">Expert</option>
                                        <option value="Occasionnel">Casual</option>
                                    </select>
                                </p>
                                <p class="tagDarkColor">
                                    <select class="post"  name="frequence">
                                        <option value="Quotidien">Daily</option>
                                        <option value="3-4/semaine">3-4/week</option>
                                        <option value="2-3/semaine">2-3/week</option>
                                        <option value="Hebdomadaire">Weekly</option>
                                        <option value="Mensuel">Monthly</option>
                                        <option value="Rarement">Rarely</option>
                                    </select>
                                </p>
                                <p class="tagLightColor">
                                    <select class="post" name="available">
                                        <option value="Yes">Available</option>
                                        <option value="No">Not Available</option>
                                    </select>
                                </p>
                            </div>
                            <div class="charahobby">
                                <textarea maxlength="100" class="post" name="content" placeholder="Write here a description if you wish"></textarea>

                            </div>
                        </div>
                    </form>
                </div>
                <label for="img">An image to illustrate :</label>
                <input type="file" name="fileToUpload1" id="fileToUpload1">
            </div>
            
        <input type="submit" value="Post" form="myForm">';
        

   


<?php
}else{
    $status = getAvailableTags(1);
    $tag_array = $status[0];
    $ID_array = $status[1];
    $error = $status[3];

    if($error==NULL){
        
  

echo '
<div id="MainContainerProfileSide2">
    <div class="divFlexRow">
        <form id="myForm" action="./newPost.php?ID='.$_GET["ID"].'&SIDE=2" method="POST" enctype="multipart/form-data">
            <input type="hidden" value="1" name = "newPost" id="newPost">
            <div class="conhobby">
                <div class="titlehobby">
                    <label for="Nom">Hobby :</label>
                    <h1><select name="Nom">';
                    $index=0;
                    foreach($tag_array as &$tag){
                        $s = (string) $ID_array[$index];
                        $p = (string) $tag;
                        echo '<option value="'.$s.'|'.$p.'">'.$p.'</option>';
                        $index++;
                    }
        
                echo ' 
                </select></h1>
            </div>
            <div class="charahobby">
                <textarea maxlength="100" class="post" name="content" placeholder="Ecrivez ici une petite description si vous le souhaitez"></textarea>
            </div>
    
</div>
<div><label for="img">Up to 4 images to illustrate :</label>
<input type="file" name="fileToUpload1" id="fileToUpload1">
<input type="file" name="fileToUpload2" id="fileToUpload2">
<input type="file" name="fileToUpload3" id="fileToUpload3">
<input type="file" name="fileToUpload4" id="fileToUpload4"></div>
</div>

</div>';


    }else{
        echo'<div id=ErrorContainer>
	    <p>'.$error.'</p>
        </div>';
    }


        echo '
        </form>
        <input type="submit" value="Post" form="myForm">
        </div>';
}


?>






<?php
include("Footer.php");
DisconnectDatabase();
?>