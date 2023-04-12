<?php
include("databaseFunctions.php");
include("fileFunctions.php");

echo '<p><br><br><br><br><br><br><br><br></p>';
ConnectDatabase();

if(!isset($_COOKIE["ID"]) || !isset($_GET["ID"]) || !isset($_GET["SIDE"])){
    $redirect = "Location:Index.php";
    header($redirect);
}elseif(isset($_POST["newPost"])){

    $error = newPost($_GET["SIDE"]);


    /*MAKE SURE YOU CANT GO BACK TO NEW POST OR ERASE POST */
    if($error == NULL){
        $redirect = "Location:Profile.php?ID=".$_COOKIE["ID"]."&SIDE=".$_GET["SIDE"];
       header($redirect);
       echo 'Success';
    }else{
        echo'<div id=ErrorContainer>
			<p>'.$loginStatus[2].'</p>
		</div>';
    }

}


$status = test();

if($status[1]!=NULL){
    echo '<div id=ErrorContainer>
	<p>'.$status[1].'</p>
</div>';
}
$active = 0;
include("Topnav.php");

if($_GET["SIDE"]==1){

    
   


    echo '<div id="MainContainerP">
    <div>
    <form style="border:solid" action="./newPost.php?ID='.$_GET["ID"].'&SIDE=1" method="POST" enctype="multipart/form-data">
    <input type="hidden" value="1" name = "newPost" id="newPost">
    <input type="hidden" name="owner" id="owner" value="'.$_GET["ID"].'">';
    


    
echo ' <div class="conhobby" style="width:100%">

            <div class="titlehobby">
                <h1><select class="title" name="Nom">';
                while($row = $status[0]->fetch_assoc()){
                    $s = (string) $row["ID"];
                    $p = (string) $row["NOM"];
                    echo '<option value="'.$s.'|'.$p.'">'.$row["NOM"].'</option>';
                
                }
        echo '
                   </select></h1>
                <p class="tagExperience">
                <select class="post"  name="experience">
                    <option value="Debutant">Débutant</option>
                    <option value="Avance">Avancé</option>
                    <option value="Intermediaire">Intermédiaire</option>
                    <option value="Expert">Expert</option>
                    <option value="Occasionnel">Occasionnel</option>
                </select></p>
                <p class="tagFrequence">
                <select class="post"  name="frequence">
                    <option value="Quotidien">Quotidien</option>
                    <option value="3-4/semaine">3-4/semaine</option>
                    <option value="2-3/semaine">2-3/semaine</option>
                    <option value="Hebdomadaire">Hebdomadaire</option>
                    <option value="Mensuel">Mensuel</option>
                    <option value="Rarement">Rarement</option>
                </select></p>';
            
          echo '<p class="tagAvailable">
                <select class="post" name="available">
                    <option value="Yes">Available</option>
                    <option value="No">Not Available</option>
                 </select></p>';
            echo '
            </div>
        <div class="charahobby">

            <textarea class="post" name="content" placeholder="Ecrivez ici une petite description si vous le souhaitez"></textarea>

        </div>
        <label for="img">Une image pour illustrer :</label>
        <input type="file" name="fileToUpload" id="fileToUpload">
        
    </div>' ;  
        
    


        

        
  echo '
        <input type="submit" value="Post">
        </form>
        </div>
        

        <img class="hobby2" src="./Images/Filler3.png">

        </div>';
        

   



}else{
    echo '<div id=MainContainer>';


    $status = getAvailableTags(1);
    $tag_array = $status[0];
    $ID_array = $status[1];
    $error = $status[3];


    if($error==NULL){

        $index = 0;

        echo '<form action="./newPost.php?ID='.$_GET["ID"].'&SIDE=2" method="POST" enctype="multipart/form-data">
        <div class="linePost">
        <label for="Nom">Hobby : </label>
        <select name="Nom">';
        
        foreach($tag_array as &$tag){
            $s = (string) $ID_array[$index];
            $p = (string) $tag;
            echo '<option value="'.$s.'|'.$p.'">'.$p.'</option>';
        
        }
    
        echo ' 
        </select>
        </div>

        <input type="hidden" name="owner" value="'.$_GET["ID"].'">
        <div class="linePost">
        <label for="content">Description</label>
        <textarea style="resize:none" name="content" placeholder="Ecrivez ici une petite description si vous le souhaitez"></textarea>
        </div>
        <input type="hidden" name="newPost" value="1">

        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="file" name="fileToUpload2" id="fileToUpload2">
        <input type="file" name="fileToUpload3" id="fileToUpload3">
        <input type="file" name="fileToUpload4" id="fileToUpload4">


        <button type="submit">test</button>
        </form>
        ';









    }else{
        echo'<div id=ErrorContainer>
	    <p>'.$error.'</p>
        </div>';
    }


        echo '
        </div>';
}


?>






<?php
include("Footer.php");
DisconnectDatabase();
?>