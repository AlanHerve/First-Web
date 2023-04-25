<?php 
include("databaseFunctions.php");

ConnectDatabase();


/*All functions in this page are only called in the context of an AJAX request
 * The value $_REQUEST["action"] allows the page to know which function to call
 */

if(isset($_REQUEST["action"])){

    switch($_REQUEST["action"]){
        case "display" : displayMessages();
            break;
        case "post" : sendMessage();
            break;
        case "comment" : sendComment();
            break;
        case "fetch" : getInterlocutors();
            break;
        case "add" : addInterlocutor();
            break;
        case "check" : checkInterlocutor();
            break;
        case "identify" : getName();
            break;
        case "like" : like(1);
            break;
        case "dislike" : like(2);
            break;
        case "displayComments" : displayComments();
            break;
        case "deleteComment" : deleteMessageOrComment(1);
            break;
        case "deleteMessage" : deleteMessageOrComment(2);
            break;
        case "default" : 
            include("fileFunctions.php");
            echoDefault();
            break;
        case "initLike" :
            initLike();
            break;
    }


   /* if ($_REQUEST["action"]=="display"){
        displayMessages($_REQUEST["ID"]);
    }elseif($_REQUEST["action"]=="post"){
        sendMessage($_REQUEST["ID"]);
    }elseif($_REQUEST["action"]=="fetch"){
        getInterlocutors();
    }elseif($_REQUEST["action"]=="add"){
        addInterlocutor();
    }elseif($_REQUEST["action"]=="check"){
        checkInterlocutor();
    }elseif($_REQUEST["action"]=="identify"){
        getName();
    }elseif($_REQUEST["action"]=="like"){
        like(1);
    }elseif($_REQUEST["action"]=="displayComments"){
        displayComments();
       
    }elseif($_REQUEST["action"]=="dislike"){
        like(2);
    }elseif($_REQUEST["action"]=="deleteComment"){
        deleteMessageOrComment(1);
    }elseif($_REQUEST["action"]=="deleteMessage"){
        deleteMessageOrComment(2);
    }elseif($_REQUEST["action"]=="comment"){
        sendComment();
    }*/
}else{

}

DisconnectDatabase();

/*echo the default image of a hobby */
function echoDefault(){

    $status = getDefault($_REQUEST["type"], 1);
    $error = $status[0];
    $img = $status[1];

    if($error==NULL){
        echo "./Images/".$img;
    }else{
        echo 'error';
    }

}

/*delete message or comment */
function deleteMessageOrComment($mode){
    global $conn;
    $error = NULL;

    $query = "DELETE FROM ";

    /*selects which table to delete from */
    if($mode==1){
        $query = $query."comments";
        
    }else{
        $query = $query."messages";
        
    }

    /*Add the Id of the comment or the message */
    $query = $query." WHERE ID=".$_REQUEST["ID"];

    $result = $conn->query($query);

    /*if error */
    if(!$result){
        if($mode==1){
            $error = "COULD NOT DELETE COMMENT";
        }else{
            $error = "COULD NOT DELETE MESSAGE";
        } 
    }
    /*echo error to be used in responseText */
    echo $error;

}

/*display comments */
function displayComments(){
    global $conn;
    $error = NULL;

    /*set timezone, to be able to calculate if a comment was posted today */
    date_default_timezone_set('Europe/Paris');

    /*Selects all comments who belong to the post */
    $query = "SELECT * FROM comments WHERE POST=".$_REQUEST["ID"];
    $result = $conn->query($query);
    
    /*count the number of comments */
    $count = 0;

    if($result){
        
        while($row = $result->fetch_assoc()){
            $count++;
            
            if(isset( $_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"])){
                if($row["OWNERID"]!=$_COOKIE["ID"]){
                    echo '<p class="timeContainer1">'.formatDate($row["TIME"]).'</p>';
                    echo '<div class="messageContainer1">
                    <p class="paragraph">'.$row["CONTENT"].'</p>
                  </div>';
                }else{
                    echo '<p class="timeContainer2">'.formatDate($row["TIME"]).'</p>';
                    echo '<div class="messageContainer2" id="comment'.$row["ID"].'" name="comment'.$row["ID"].'">
                    <p class="paragraph">'.$row["CONTENT"].'</p><span class="destroyButton" onclick="deleteComment('.$row["ID"].')">&#215;</span>
                  </div>';
                }
            }else{
                echo '<p class="timeContainer1">'.formatDate($row["TIME"]).'</p>';
                echo '<div class="messageContainer1">
                    <p class="paragraph">'.$row["CONTENT"].'</p>
                  </div>';
            }
            
        }

        /*if no comments, add little message so user knows the abscence of comments isn't due to a bug */
        if($count==0){
            /*If user is not connected, offers them to log in */
            if(isset( $_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"])) echo '<p class="descriptionText"><i>This post does not seem to have any comments yet</i></p>';
            else echo '<p class="descriptionText"><i>This post does not seem to have any comments yet, <a href="./Login.php">log in</a> to add your own</i></p>';
        }


    }
}

function initLike(){

    global $conn;
    $error = NULL;

    $query = "SELECT ID FROM likes WHERE USER_ID=".$_REQUEST["USER_ID"]." AND POST_ID=".$_REQUEST["POST_ID"];
    $result = $conn->query($query);

    if(mysqli_num_rows($result)>0){
        echo 'liked';
    }else{
        echo $query;
    }
}

/*Like of dislike a post */
function like($case){
    global $conn;
    $error = NULL;


    /*Beginning of query */
    $query = "UPDATE regular_post SET LIKES=";

    /*Sets new value of the like column */
    if($case==1){
        $query = $query."LIKES+1";
    }else{
        $query = $query."LIKES-1";
    }
    /*Add ID of the post */
    $query= $query." WHERE ID=".$_REQUEST["ID"];
    
    $result = $conn->query($query);

    if(!$result){
        $error = "COULD NOT UPDATE TABLE";
        echo $error;
    }else{
        if($case==1) $query = "INSERT likes (USER_ID, POST_ID) VALUES (".$_REQUEST["USER_ID"].",".$_REQUEST["ID"].")";
        else $query = "DELETE FROM likes WHERE USER_ID=".$_REQUEST["USER_ID"]." AND POST_ID=".$_REQUEST["ID"];
        $result = $conn->query($query);
        echo $query;
    }
}



/*getName of current Interlocutor in the popup */
function getName(){
    global $conn;
    $error = NULL;

    $query = "SELECT NAME FROM users WHERE ID=".$_REQUEST["ID"];
    $result = $conn->query($query);

    if($result){
        if($row = $result->fetch_assoc()){
            echo $row["NAME"];
        }
    }
}

function sendComment(){

    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "hobbysharedatabase";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $error = NULL;

    if(isset($_REQUEST["msg"])){
        $query = "INSERT INTO comments (ID, CONTENT, POST, OWNERID) VALUES (NULL, '".SecurizeString_ForSQL($_REQUEST["msg"])."', ".$_REQUEST["ID"].", ".$_COOKIE["ID"].")";

        $result = $conn->query($query);
        
    if(!$result){
        $error = "COULDN'T SEND COMMENT";
        echo $error;
    }else{
        /*echoes the comment posted to avoid reloading each time a comment is posted */
        date_default_timezone_set('Europe/Paris');
        echo '<p class="timeContainer2">'.date("h:i", time()).'</p>';
        echo '<div class="messageContainer2" id="comment'.$_REQUEST["ID"].'" name="comment'.$_REQUEST["ID"].'">
                <p class="paragraph">'.$_REQUEST["msg"].'</p><span class="destroyButton" onclick="deleteComment('.$conn->insert_id.')">&#215;</span>
              </div>';
    }
    }
}

function sendMessage(){

    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "hobbysharedatabase";
    
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $error = NULL;

    if(isset($_REQUEST["msg"])){
        $query = "INSERT INTO messages (ID, OWNER1, OWNER2, CONTENT) VALUES (NULL, ".$_COOKIE["ID"].", ".$_REQUEST["ID"].", '".SecurizeString_ForSQL($_REQUEST["msg"])."')";
        $result = $conn->query($query);

        if(!$result){
            $error = "COULDN'T SEND MESSAGE";
        }else{
            /*Since ID of message is autoincrement, get the ID this way
             * We need the ID in case user wishes to delete the message
             */
            $idOfMessage = $conn->insert_id;
                  echo '<p class="timeContainer2"><i>now</i></p>';
                  echo '<div class="messageContainer2" id="message'.$idOfMessage.'" name="message'.$idOfMessage.'">
                            <p class="paragraph">'.$_REQUEST["msg"].'</p><span class="destroyButton" onclick="deleteMessage('.$idOfMessage.')">&#215;</span>
                        </div>';
               
            
       

        

        }
    }

    if($error!=NULL) echo $error;
    
    
}

/*Selects message with current interlocutor */
function getMessages(){

    date_default_timezone_set('Europe/Paris');

    global $conn;

    $error = NULL;

    /*case 1, you were the one sending the message, case 2 you were the one receiving the message
     * $_COOKIE["ID"] is the user's ID, $_REQUEST["ID"] is the interlocutor's ID
     */
    $query = "SELECT * FROM messages WHERE (OWNER1=".$_COOKIE["ID"]." AND OWNER2=".$_REQUEST["ID"].") OR (OWNER1=".$_REQUEST["ID"]." AND OWNER2=".$_COOKIE["ID"].")";
    
    $result = $conn->query($query);

    if(!$result){
        $error = "COULDN'T RETRIEVE MESSAGE";
    }

  
    

    return array($error, $result);
}

function displayMessages(){

    
    $result = getMessages($_REQUEST["ID"]);
    $message = $result[1];

    $error = $result[0];

    if($error!=NULL){
        echo $error;
    }else{
        while(  $row = $message->fetch_assoc()){
            /*If you were the one to send the message */
            if($row["OWNER1"]==$_COOKIE["ID"]){
                echo '<p class="timeContainer2" id="time'.$row["ID"].'">'.formatDate($row["TIME"]).'</p>';
                echo '<div class="messageContainer2" id="message'.$row["ID"].'" name="message'.$row["ID"].'">
                        <p class="paragraph">'.$row["CONTENT"].'</p><span class="destroyButton" onclick="deleteMessage('.$row["ID"].')">&#215;</span>
                      </div>';
            }else{
                /*if you were the one to receive the message */
                echo '<p class="timeContainer1" id="time'.$row["ID"].'">'.formatDate($row["TIME"]).'</p>';
                echo '<div class="messageContainer1">
                        <p class="paragraph">'.$row["CONTENT"].'</p>
                      </div>';
            }
            
        }

        
    }
    
}

/*Add an interlocutor to the list of people you can message on your popup */
function addInterlocutor(){
 
    global $conn;

    $query = "SELECT * FROM users WHERE ID=".$_REQUEST["toadd"];

    echo $query;

    $result = $conn->query($query);

    if($result){
        if($row = $result->fetch_assoc()){
            /*button to make interlocutor the current interlocutor */
            echo '<button value='.$row["ID"].' id="'.$row["NAME"].'" name="'.$row["NAME"].'" onclick="changeInterlocutor(this)">'.$row["NAME"].'</button>';
        }
    }
}

function checkInterlocutor(){
    global $conn;

    $error = NULL;

    if($_REQUEST["tocheck"] == $_COOKIE["ID"]){
        echo 'identity';
    }else{

    $query = "SELECT * FROM messages WHERE OWNER1=".$_REQUEST["tocheck"]." OR OWNER2=".$_REQUEST["tocheck"];
    $result = $conn->query($query);

    if($result){
        if($row = $result->fetch_assoc()){
            echo 'true';
        }else{
            echo 'false';
        }
    }else{
        echo 'error';
    }

    }
}

function getInterlocutors(){

    global $conn;

    $error = NULL;

    /*Selects all message where user was one of the interlocutors */
    $query = "SELECT * FROM messages WHERE OWNER1=".$_COOKIE["ID"]." OR OWNER2=".$_COOKIE["ID"];

    $result = $conn->query($query);

    $interlocutors = array();

    /*get Info about user's interlocutors */
    $query = "SELECT * FROM users WHERE ID IN (";

    if($result){
        while($row = $result->fetch_assoc()){

            /*Add to query everyone who isn't the user */

            if($row["OWNER1"]!=$_COOKIE["ID"] && !in_array($row["OWNER1"] , $interlocutors)){
                array_push($interlocutors, $row["OWNER1"]);
                $query = $query.$row["OWNER1"].",";
            }elseif($row["OWNER2"]!=$_COOKIE["ID"] && !in_array($row["OWNER2"] ,$interlocutors)){
                array_push($interlocutors, $row["OWNER2"]);
                $query = $query.$row["OWNER2"].",";
            }
        }
        if(isset($_REQUEST["interlocutor"])){
            if(!in_array($_REQUEST["interlocutor"], $interlocutors)){
           
                $query = $query.$_REQUEST["interlocutor"].",";
            } 
        }
        

        $length = strlen($query) - 1;

        /*close query */
        if($query[$length]==","){
            $query[$length]=")";
        }else{
            $error = "COULDNT FIND ANYTHING";
        }

        $result = $conn->query($query);

        if($result){
            /*for each user found, add a button allowing user to make them the current
             * interlocutor
             */
            while($row = $result->fetch_assoc()){
                if(isset($_REQUEST["interlocutor"])){
                    if($row["ID"]!=$_REQUEST["interlocutor"]){
                    
                        echo '<button value='.$row["ID"].' id="'.$row["NAME"].'" name="'.$row["NAME"].'" onclick="changeInterlocutor(this)">'.$row["NAME"].'</button>';
                        
                    }
                }else{
                    echo '<button value='.$row["ID"].' id="'.$row["NAME"].'" name="'.$row["NAME"].'" onclick="changeInterlocutor(this)">'.$row["NAME"].'</button>';
                }
                
            }
        }
        
    }
}



?>