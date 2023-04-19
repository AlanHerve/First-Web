<?php
/* */
function ConnectDatabase(){
    // Create connection
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "projetessai";
    global $conn;
    
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }
}

function DisconnectDatabase(){
	global $conn;
	$conn->close();
}

function CheckConnected(){

    $connected = false;

    if( isset( $_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"])){
        $connected = true;
    }

    return $connected;
}

function CheckLogin(){
    global $conn, $username, $userID;

    $ID = NULL;

    $error = NULL; 
    $loginSuccessful = false;
    $row = "wrong";
   
    
    $check =true;
    //Données reçues via formulaire?
	if(isset($_POST["mail"]) && isset($_POST["password"])){
		$mail = SecurizeString_ForSQL($_POST["mail"]);
		$password = md5($_POST["password"]);
		$loginAttempted = true;
        
	}
    elseif ( isset( $_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"]) ) {
		$mail = $_COOKIE["mail"];
		$password = $_COOKIE["password"];
        $ID = $_COOKIE["ID"];
		$loginAttempted = true;
        
	}
    else {
		$loginAttempted = false;
	}

    //Si un login a été tenté, on interroge la BDD
    if ($loginAttempted){
        
        $query = "SELECT * FROM personne WHERE EMAIL = '".$mail."' AND PASSWORD ='".$password."'";
        //echo '<p>'.$query.'</p>';
        $result = $conn->query($query);
        $tap = $result->fetch_assoc();

        if ( $tap ){
            
            $ID = $tap["ID"];
            CreateLoginCookie($mail, $password, $ID);
           
            $loginSuccessful = true;
        }
        else {
            $error = "Ce couple login/mot de passe n'existe pas. Créez un Compte ou reesayez";
        }
    }

    

    return array($loginSuccessful, $loginAttempted, $error, $ID);
}

function suppressPost($mode){

    global $conn;

    $error = NULL;
/*this function can either delete a hobby from the user's account or delete a post */
    switch ($mode) {
        case 1:$query = "DELETE FROM hobby_post
        WHERE ID=".$_GET["ID"];

        $result = $conn->query($query);

        break;
        case 2: $query = "DELETE FROM regular_post
        WHERE ID=".$_GET["ID"];

        $result = $conn->query($query);

        $query = "DELETE FROM comments WHERE POST=".$_GET["ID"];

        break;
    }

    if(!$result){
        $error = "NO FILE TO DELETE OR COULDN'T DELETE FILE";
    }elseif($mode==2){
        $result = $conn->query($query);

        if(!$result) $error = "ERROR IN DELETING COMMENTS";
    }

    return $error;

    

}


/*This function checks if the post exists as well as if the current user as the right to edit it */
function validate($mode){

    global $conn;

    $validated = false;

    if($mode == 1){
        $query = "SELECT * FROM hobby_post WHERE ID=".$_GET["ID"]." AND OWNER=".$_COOKIE["ID"];
    }else{
        $query = "SELECT * FROM regular_post WHERE ID=".$_GET["ID"]." AND OWNER=".$_COOKIE["ID"];
    }

    $result = $conn->query($query);
    
    if($result){
        

        if($row = $result->fetch_assoc()){
            /*If query returns a line, then the post exist and its owner is the current user */
            $validated=true;
        }
    }

    return array($validated, $row);

    
}



function newPost($mode){

    global $conn;

    /*availability of user is stored in the slq database as a binary value */
    $present = 0;

    $error = NULL;


             $values = $_POST["Nom"];

            /*separates the name of the hobby from its value */
            $values_explode = explode('|', $values);

        if($mode==1){
            /*if mode == 1, uploads a hobby into the user's account*/
            //check if the image user wants to upload is valid
            $good = checkFile("fileToUpload1");

            if($_POST["available"]!="No"){
                $present = 1;
            } 
            $content = NULL;
            if($_POST["content"]!=""){
                $content=$_POST["content"];
            }

            
            /*if user is uploading an image */
            
            if(isset($_FILES["fileToUpload1"]["name"]) && $good!=NULL){
                $query = "INSERT INTO hobby_post (ID, NOM, EXPERIENCE, FREQUENCY, AVAILABLE, IMAGE1, OWNER, DESCRIPTION, TYPEID) VALUES
                (NULL, \"".$values_explode[1]."\", \"".$_POST["experience"]."\", \"".$_POST["frequence"]."\", ".$present.", '".$_FILES["fileToUpload1"]["name"]."', ".$_POST["owner"].", \"".SecurizeString_ForSQL($content)."\", ".$values_explode[0].")";
            }elseif(isset($_FILES["fileToUpload1"]["name"]) && $_FILES["fileToUpload1"]["name"]!="" && $good == NULL){
                $error = "FILE TYPE IS NOT OF ACCEPTABLE TYPE";
            }else{
                
                $query = "INSERT INTO hobby_post (ID, NOM, EXPERIENCE, FREQUENCY, AVAILABLE, IMAGE1, OWNER, DESCRIPTION, TYPEID) VALUES
                (NULL, \"".$values_explode[1]."\", \"".$_POST["experience"]."\", \"".$_POST["frequence"]."\", ".$present.", NULL, ".$_POST["owner"].", \"".SecurizeString_ForSQL($content)."\", ".$values_explode[0].")";
            }

            if($error == NULL){
                $result = $conn->query($query);

                if(!$result){
                    $error = "FAILED UPLOADING THE POST, PLEASE TRY AGAIN";
                }
            }
            
        }else{

            /*Upload a regular post */
            $present = 0;
            
        
            $content = NULL;
            if($_POST["content"]!=""){
                $content=$_POST["content"];
            }

            

            

            $query = "INSERT INTO regular_post (ID, NOM, IMAGE1, IMAGE2, IMAGE3, IMAGE4, OWNER, DESCRIPTION, TYPEID) VALUES
            (NULL, \"".$values_explode[1]."\",";
        
        for ($i=1; $i<=4; $i++){
            $good = checkFile("fileToUpload".$i);

            if(isset($_FILES["fileToUpload".$i]["name"]) && $good!=NULL){
                $query = $query."'".SecurizeString_ForSQL($_FILES["fileToUpload".$i]["name"])."', ";
                $redirect = true;
            }elseif($good == NULL && $_FILES["fileToUpload".$i]["name"]!=""){
                $error = "FILE TYPE IS NOT OF ACCEPTABLE TYPE";
                $redirect = false;
            }else{
                $query = $query."NULL, ";
                $redirect = true;
            }
           
        }

            $query = $query." ".$_COOKIE["ID"].", \"".$content."\", ".$values_explode[0].")";

            $result = $conn->query($query);

        }

        return $error;
 
}



function editPost($mode){
    
    global $conn;

    $error = NULL;
    $redirect = false;

    $row = NULL;

    if(!isset($_POST["newPost"])){
        /* */
        if($mode == 1){
            $query = "SELECT * FROM hobby_post WHERE OWNER=".$_COOKIE["ID"]." AND ID=".$_GET["ID"];
        } else{
            $query = "SELECT * FROM regular_post WHERE OWNER=".$_COOKIE["ID"]." AND ID=".$_GET["ID"];
        }

        
        $result=$conn->query($query);
        $row = $result->fetch_assoc();
        if(!$row){
            $error = "COULDNT FIND ANY HOBY POST WITH TARGET OWNER";
        }


    }else{

        if($mode == 1){
             $query = "SELECT * FROM hobby_post WHERE OWNER=".$_COOKIE["ID"]." AND ID=".$_GET["ID"];
            $result=$conn->query($query);

            $row = $result->fetch_assoc();

            $query = "UPDATE hobby_post
            SET ";
    
            if(isset($_POST["experience"])){
                if($_POST["experience"]!=$row["EXPERIENCE"]){
                    $query = $query."EXPERIENCE='".$_POST["experience"]."',";
                    $redirect = true;
                }
                
            
            }
    
            if(isset($_POST["available"])){
                if($_POST["available"]=="Yes" && $row["AVAILABLE"]==0){
                    $query = $query."AVAILABLE='1',";
                    $redirect = true;
                }elseif($_POST["available"]=="No" && $row["AVAILABLE"]==1){
                    $query = $query."AVAILABLE='0',";
                    $redirect = true;
                }
            
            }
    
            if(isset($_POST["frequence"])){
                if($_POST["frequence"]!=$row["FREQUENCY"]){
                    $query = $query."FREQUENCY='".$_POST["frequence"]."',";
                    $redirect = true;
                }
            
            }
    
            if(isset($_POST["content"])){
                if($_POST["content"]!=$row["DESCRIPTION"]){
                    $query = $query."DESCRIPTION='".SecurizeString_ForSQL($_POST["content"])."'";
                    $redirect = true;
                }
            
            }


            $good = checkFile("fileToUpload1");

            if(isset($_FILES["fileToUpload1"]["name"]) && $good!=NULL){
                $query = $query."IMAGE='".SecurizeString_ForSQL($_FILES["fileToUpload1"]["name"])."'";
                $redirect = true;
            }elseif($good == NULL && $_FILES["fileToUpload1"]["name"]!=""){
                $error = "FILE TYPE IS NOT OF ACCEPTABLE TYPE";
                $redirect = false;
            }elseif($_POST["deleteImage1"]==0){
                $query = $query."IMAGE=NULL ";
                $redirect = true;
            }


            $length = strlen($query) - 1;

            if($query[$length]==","){
                $query[$length]=" ";
            }

            if($redirect){
                $query = $query."WHERE ID=".$_GET["ID"];
                $result = $conn->query($query);
                if(!$result){
                    $error = "COULD NOT UPDATE";
                }
            }

            

        }else{
            
        $query = "SELECT * FROM regular_post WHERE OWNER=".$_COOKIE["ID"]." AND ID=".$_GET["ID"];
        $result=$conn->query($query);
        $row = $result->fetch_assoc();

        $query = "UPDATE regular_post
        SET ";
    
        
        /*MODIFIER LES IMAGES*/
    
        if(isset($_POST["content"])){
            if($_POST["content"]!=$row["DESCRIPTION"]){
                $query = $query."DESCRIPTION='".SecurizeString_ForSQL($_POST["content"])."'";
                $redirect = true;
            }
            
        }

        for ($i=1; $i<=$_POST["numberOfImage"]; $i++){
            $good = checkFile("fileToUpload".$i);

            if(isset($_FILES["fileToUpload".$i]["name"]) && $good!=NULL){
                $query = $query."IMAGE".$i."='".SecurizeString_ForSQL($_FILES["fileToUpload".$i]["name"])."'";
                $redirect = true;
            }elseif($good == NULL && $_FILES["fileToUpload".$i]["name"]!=""){
                $error = "FILE TYPE IS NOT OF ACCEPTABLE TYPE";
                $redirect = false;
            }elseif($_POST["deleteImage".$i]==0){
                $query = $query."IMAGE".$i."=NULL,";
                $redirect = true;
            }
           
        }
            
        $length = strlen($query) - 1;
        if($query[$length]==",") $query[$length]=" ";

        $query = $query."WHERE ID=".$_GET["ID"];

        if($redirect){
            $result = $conn->query($query);
        }
        
    }

    }

    return array($error, $row);

}

function editPost2(){
    
    global $conn;
    $redirect = false;

    if(!isset($_POST["newPost"])){
        $query = "SELECT * FROM regular_post WHERE OWNER=".$_COOKIE["ID"]." AND ID=".$_GET["ID"];
        $result=$conn->query($query);
        $row = $result->fetch_assoc();
        if(!$row){
            $error = "COULDNT FIND ANY HOBY POST WITH TARGET OWNER";
        }
    }else{

        $query = "SELECT * FROM regular_post WHERE OWNER=".$_COOKIE["ID"]." AND ID=".$_GET["ID"];
        $result=$conn->query($query);
        $row = $result->fetch_assoc();

        $query = "UPDATE regular_post
        SET ";
    
        $files = array("fileToUpload1", "fileToUpload2", "fileToUpload3", "fileToUpload4");
        $good = array();
        foreach ($files as &$file){
            array_push($good, checkFile($file));
        }
        /*MODIFIER LES IMAGES*/
    
        if(isset($_POST["content"])){
            if($_POST["content"]!=$row["DESCRIPTION"]){
                $query = $query."DESCRIPTION='".SecurizeString_ForSQL($_POST["content"])."'";
                $redirect = true;
            }
            echo '<p><br> row: '.$row["DESCRIPTION"].'<br>4<br>'.$_POST["content"].'</p>';
            
        }
        if($redirect){
            echo '<p>papaya<br>ture</p>';
        }else{
            echo '<p>papaya<br>false</p>';
        }
        
    
        $query = $query."WHERE ID=".$_GET["ID"];
        
    }

    $result = $conn->query($query);

    return array($result, $redirect, $query, $row);

}

function test(){

    global $conn;

    $error = NULL;

    $count=0;
    $max= 10;

    $query = "SELECT TYPEID FROM hobby_post WHERE OWNER=".$_GET["ID"];
    $result = $conn->query($query);
    if($result){
        $query = "SELECT NOM, ID FROM hobby_list WHERE ID NOT IN (";
        
        while($row = $result->fetch_assoc()){
            $count += 1;
            $to_add = "'".$row["TYPEID"]."'";
            $query = $query.$to_add.",";
        }
        /*echo '<p><br><br><br><br><br>'.$query.'<br><br><br><br></p>';*/
        $length = strlen($query) - 1;
        if($query[$length]=="("){

            /*????????????? */
            $query ="SELECT NOM, ID FROM hobby_list";
        }else{
            if($count<$max){
                $query[$length] = ")";
            }else{
                $error = "YOU'VE POSTED IN EACH HOBBY CATEGORY, THERE AREN'T ANYMORE HOBBIES TO PUT ON YOUR PAGE";
            }
            
        }
        
    }else{
        $query ="SELECT NOM, ID FROM hobby_list";
    }
        

       
    
    /*echo '<p><br><br><br><br><br>'.$query.'<br><br><br><br></p>';*/
    if($error==NULL){
        $result = $conn->query($query);

        if(!$result){
            $error = "COULD NOT RETRIEVE HOBBY LIST"; 
        }
    }
    

    return array($result, $error, $query);

}

function getAvailableTags($mode){

    /*VOIR SI CETTE FONCTION EST SI UTILE QUE CA*/

    global $conn;
    $error = NULL;
    $found = 0;


    if($mode==1){
        echo 'mode 1 <br>';
        $query = "SELECT * FROM hobby_post WHERE OWNER=".$_GET["ID"];
    }else{
        $query = "SELECT * FROM regular_post WHERE OWNER=".$_GET["ID"];
    }

    

    $tags = array();
    $IDs = array();

    $result = $conn->query($query);
            
    while($row = $result->fetch_assoc()){     
        if(!in_array($row["TYPEID"], $IDs)){
            array_push($tags, $row["NOM"]);
            array_push($IDs, $row["TYPEID"]);
            $found += 1;  
        }
           
    }  
    
 

    return array($tags, $IDs, $found, $error);

}

function SecurizeString_ForSQL($string) {
    $string = trim($string);
    $string = stripcslashes($string);
    $string = addslashes($string);
    $string = htmlspecialchars($string);
    return $string;
}

function editProfile(){

    global $conn;

    global $conn;

    $success = false;
    $error = NULL;
    $row = NULL;

    if(isset($_POST["new"])){
        $tryEdit = true; 
        $query = "SELECT * FROM personne WHERE ID=".$_GET["ID"];
        $result = $conn->query($query);

        
        $row = $result->fetch_assoc();
        if($row){
            

            $query = "UPDATE personne
                      SET ";

        if(isset($_POST["name"])){
            if($_POST["name"]!=$row["NOM"] && $_POST["name"]!=""){
                $query = $query."NOM='".$_POST["name"]."',";
                $success = true;
            }else{

            }
    
        }
            

            if(isset($_POST["nickName"])){
                if($_POST["nickName"]!=$row["NICKNAME"] && $_POST["nickName"]!=""){
                    $query = $query."NICKNAME='".$_POST["nickName"]."',";
                    $success = true;
                }
                
                
            }

            if($_POST["current_password"]!=""){
                if(md5($_POST["current_password"]) == $row["PASSWORD"]){
                    if($_POST["password"]=="" ){
                        $error = "PLEASE ENTER NEW PASSWORD";
                        $success = false;
                        return array($row, $success, $error, $query);
                    }

                    if($_POST["confirm"]==""){
                        $error = "PLEASE CONFIRM PASSWORD";
                        $success = false;
                        return array($row, $success, $error, $query);
                    }

                    if($_POST["password"]!=$_POST["confirm"]){
                        $error = "PASSWORD AND CONFIRMATION ARE DIFFERENT";
                        $success = false;
                        return array($row, $success, $error, $query);
                    }else{
                        $query = $query."PASSWORD='".md5($_POST["password"])."',";
                        $success = true;
                    }
                }else{
                    $error = "CURRENT PASSWORD IS WRONG";
                    $success = false;
                }
                
            }
                if($success){
                    $length = strlen($query) - 1;
                    $query[$length] = " ";
                    
                    $query = $query."WHERE ID=".$_GET["ID"];
                    $result = $conn->query($query);
                }
        }
    }else{
        
        $query = "SELECT * FROM personne WHERE ID=".$_GET["ID"];
        $result = $conn->query($query);
    
        $row = $result->fetch_assoc();
    }

    return array($row, $success, $error, $query);

}


function getProfile($addToConversationButton){


    global $conn;

    $found = 0;    

    $error = NULL;

    
    $query = 'SELECT * FROM personne WHERE ID='.$_GET["ID"];

    /*checks database if user with ID mentionned in webpage url exists */
    if ( $guy = $conn->query($query) ){

        if($personne = $guy->fetch_assoc()){

        $found = 1;
        
        /*TODO : MAKE A BETTER SQL REQUEST */

        /*Selects all the hobbies of the current page owner */
        $query = 'SELECT * FROM hobby_post WHERE OWNER='.$_GET["ID"];
        $result = $conn->query($query);
        
        /*Beginning of request to get the name of the hobbies */
        $query = "SELECT NOM, ID FROM hobby_list WHERE ID IN (";

        /*For each hobby, add its ID to the request */
        while($row = $result->fetch_assoc()){
            $to_add = "'".$row["TYPEID"]."'";
            $query = $query.$to_add.",";
        }

        $length = strlen($query) - 1;
        $query[$length] = ")";

        $result = $conn->query($query);
        
        /*Container of the user's profile */
        echo ' <div id="ProfileContainer">';

        /*If user is the owner of the page, they get the option to edit their profile */
        if(isset($_COOKIE["ID"]) && isset($_COOKIE["ID"])){
            if($_COOKIE["ID"]==$_GET["ID"]){
                echo '<a title="Edit Profile" href="./EditProfile.php?ID='.$_GET["ID"].'"><img src="./Images/Edit.png" class="circleButton"></a>';
            }
        }

        echo '
        <div class="con2">
        ';
        if($personne["avatar"]!=NULL){
            echo '
        <img class="pic" src="./Images/'.$personne["avatar"].'">';
        }else {
            /*If person does not have an avatar, display the default image */
            echo '
        <img class="pic" src="./Images/img_avatar.png">';
        }
/*TODO : make $var a span */
        echo '
        <div class="con">
            <div class="name">
                '.$personne["NOM"].' '.$addToConversationButton.'
            </div>
            <div class="nickname">
                '.$personne["NICKNAME"].'
            </div>
            <div class="tagArea">';
/*If query returned something, the user has already published hobbies on the website
Each hobby tag is a link to the Tag.php page */
        if($result){
            while($row = $result->fetch_assoc()){
            echo'<div class="tag">
                <a class="tag" href="Tag.php?TAG='.$row["ID"].'">'.$row["NOM"].'</a>
            </div>';
            }   
        }else{
            echo'<p><i>This person does not seem to have any hobby yet...</i></p>';
        }
/*close the divs of tagArea, con and con2 */
        echo '
            </div>

        </div>
        </div>
        
        <div class="sideSelector">';
/*Allows user to choose between displaying a user's hobbies or their posts */
        if(isset($_GET["SIDE"])){
            if($_GET["SIDE"]==1){
                echo '<a class="active" href="./Profile.php?ID='.$_GET["ID"].'&SIDE=1">Liste Hobbys</a>
                <a href="./Profile.php?ID='.$_GET["ID"].'&SIDE=2">Posts</a>';
            }else{
                echo '<a  href="./Profile.php?ID='.$_GET["ID"].'&SIDE=1">Liste Hobbys</a>
                <a class="active" href="./Profile.php?ID='.$_GET["ID"].'&SIDE=2">Posts</a>';
            }
        }
            
        echo '</div>';

        echo '</div>';
        //TODO : changer les echos pour gerer les différents cas
        }else{
         
        $error = "COULD NOT FIND ANYONE WITH THIS ID";
        }
 
    }else{

        $error = "COULD NOT FIND ANYONE WITH THIS ID";

    }

    return $error;

}


function getPosts($mode){
        
        global $conn;
        $error = NULL;

        switch($mode){
            /*selects the post about hobbies of the user */
            case 1:$query = 'SELECT * FROM hobby_post WHERE OWNER='.$_GET["ID"];
            $result = $conn->query($query);
            
            break;

            case 2: 
                
                /* Selects the regulat post of an user
                 * $_GET["TAG"] allows the user to filter posts based on the hobby they reference
                 */
                if(isset($_GET["TAG"])){
                    if($_GET["TAG"]=="none"){
                        $query = 'SELECT * FROM regular_post WHERE OWNER='.$_GET["ID"].' ORDER BY ID DESC';
                        $result = $conn->query($query);
                    }else{
                    $query = 'SELECT * FROM regular_post WHERE OWNER='.$_GET["ID"].' AND TYPEID='.$_GET["TAG"]." ORDER BY ID DESC";
                    $result = $conn->query($query);
                    
                    }
                }else{
                    $query = 'SELECT * FROM regular_post WHERE OWNER='.$_GET["ID"]." ORDER BY ID DESC";
                    $result = $conn->query($query);
                }
                break;
        }
        if(!$result){
            $error = "COULD NOT RETRIEVE POSTS";
        }
    
    return array($error, $result);
        
}

/*Get all the hobbies stored in the SQL database */
function getAllTags(){
    global $conn;

    $query = "SELECT * FROM hobby_list";
    $result = $conn->query($query);

    return $result;
}

function getLine($owner){

    global $conn;

    $query2 = "SELECT * FROM personne WHERE ID=".$owner;
    $result2 = $conn->query($query2);
    $result2 = $result2->fetch_assoc();

    return $result2;
}

function tagPost(){
    global $conn;
    $name = NULL;
    $result = NULL;

    if(isset($_GET["TAG"])){

        $query="SELECT * FROM hobby_post WHERE TYPEID=".$_GET["TAG"];
        $result = $conn->query($query);

        $row = $result->fetch_assoc();

        $name = $row["NOM"];
       
        $query="SELECT * FROM hobby_post WHERE TYPEID=".$_GET["TAG"];
        $result = $conn->query($query);
   
 
    }else{
        $query = "SELECT * FROM hobby_post";
        $result = $conn->query($query);

        $value = array();

        while($row = $result->fetch_assoc()){
            if(!in_array($row["TYPEID"], $value)){
                array_push($value, $row["TYPEID"]);
            }
        }

        $size = sizeof($value);

        if($size > 0){
            $random = rand(0, $size -1);
            $redirect = "Location:Tag.php?TAG=".$value[$random];
            header($redirect);
        }else{
            $result = NULL;
        }
        
        

    }
    return array($result, $name);
     
}

function CreateLoginCookie($username, $encryptedPasswd, $ID){
    setcookie("mail", $username, time() + 24*3600 );
    setcookie("password", $encryptedPasswd, time() + 24*3600);
    setcookie("ID", $ID, time() + 24*3600);
}

function CreateInterlocutorCookie($interlocutorID, $interlocutorName){
    setcookie("interlocutorID", $interlocutorID, time() + 24*3600);
    setcookie("interlocutorName", $interlocutorName, time() + 24*3600);
    
}

//Méthode pour détruire les cookies de Login
//--------------------------------------------------------------------------------
function DestroyLoginCookie(){
    setcookie("mail", NULL, -1 );
    setcookie("password", NULL, -1);
    setcookie("ID", NULL, -1);
    setcookie("interlocutorID", NULL, -1);
    setcookie("interlocutorName", NULL, -1);
    
}

function CheckNewAccountForm(){
    global $conn;

    $creationAttempted = false;
    $creationSuccessful = false;
    $error = NULL;

    $ID = NULL;

    //Données reçues via formulaire?
    if(isset($_POST["name"]) && isset($_POST["password"]) && isset($_POST["confirm"])){

        $creationAttempted = true;
        //CHANGER ORDRE DE VERIFICATION
        //Form is only valid if password == confirm, and username is at least 4 char long
        if ( strlen($_POST["name"]) < 4 ){
            $error = "Your UserName must be at least 4 characters long";
        }
        elseif ( $_POST["password"] != $_POST["confirm"] ){
            $error = "Password and confirmation password are different";
        }
        elseif($_POST["mail"]==" "){ /*TODO */
            $error = "Le mail est invalide";
        }
        else {
            $query="SELECT email
            FROM personne
            WHERE email LIKE '".$_POST["mail"]."'";
            $checkmail=$conn->query($query);
            $ID = "1";
            $error="no error";
            $count = 0;

            if($checkmail->num_rows !=0){
                $error = "This email address already belongs to somebody";
                return array($creationAttempted, $creationSuccessful, $error);
            }
            
            $query="SELECT nom
            FROM personne
            WHERE nom LIKE '".$_POST["name"]."'";
            $checkmail=$conn->query($query);

            if($checkmail->num_rows !=0){
                $error = "This name address already belongs to somebody";
                return array($creationAttempted, $creationSuccessful, $error);
            }



            /*$query="SELECT nickname
            FROM personne
            WHERE nickname LIKE '".$_POST["nickName"]."'";
            $checkmail=$conn->query($query);
            
            if($checkmail->num_rows !=0){
                $error = "Le surnom est déja prix";
                return array($creationAttempted, $creationSuccessful, $error);
            }*/

                $username = SecurizeString_ForSQL($_POST["name"]);
                $nickname = SecurizeString_ForSQL($_POST["nickName"]);

                $mail = $_POST["mail"];
		        $password = md5($_POST["password"]);

                $query = "INSERT INTO `personne` VALUES (NULL, '$username', '$nickname', '$password', '$mail', NULL )";/*IMAGE */

                $result = $conn->query($query);

                

                if( mysqli_affected_rows($conn) == 0 )
                {
                    $error = "Error during SQL insertion, try entering names, nicknames and password without any special character";
                }
                else{

                    /*SINCE ID is an autoincrement, we need to ask the SQL database which ID the new user got */
                    $query= "SELECT * FROM personne WHERE EMAIL='".$mail."'";
                    $result = $conn->query($query);
                    
                    $row = $result->fetch_assoc();
                    $ID = $row["ID"];

                    CreateLoginCookie($mail, $password, $ID);
                    $creationSuccessful = true;

                }
            

        
		    
        }

	}

    return array($creationAttempted, $creationSuccessful, $error, $ID);
}
?>