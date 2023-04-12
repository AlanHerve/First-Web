<?php 
include("databaseFunctions.php");
ConnectDatabase();

/*REMPLACER CA PAR UN SWITCH */
if(isset($_REQUEST["action"])){
    if ($_REQUEST["action"]=="display"){
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
    }elseif($_REQUEST["action"]=="destroyComment"){
        destroy(1);
    }elseif($_REQUEST["action"]=="destroyMessage"){
        destroy(2);
    }elseif($_REQUEST["action"]=="comment"){
        sendComment();
    }
}else{

}

DisconnectDatabase();



function destroy($mode){
    global $conn;
    $error = NULL;

    $query = "DELETE FROM ";

    if($mode==1){
        $query = $query."comments";
        
    }else{
        $query = $query."messages";
        
    }

    $query = $query." WHERE ID=".$_REQUEST["ID"];

    $result = $conn->query($query);

    if(!$result){
        if($mode==1){
            $error = "COULD NOT DELETE COMMENT";
        }else{
            $error = "COULD NOT DELETE MESSAGE";
        } 
    }
    echo $error;

}

function displayComments(){
    global $conn;
    $error = NULL;

    $query = "SELECT * FROM comments WHERE POST=".$_REQUEST["ID"];
    $result = $conn->query($query);
    

    if($result){
        
        while($row = $result->fetch_assoc()){
            
            if(isset( $_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"])){
                if($row["OWNERID"]!=$_COOKIE["ID"]){
                    echo '<div class="messageContainer1">
                    <p>'.$row["CONTENT"].'</p>
                  </div>';
                }else{
                    echo '<div class="messageContainer2" id="comment'.$row["ID"].'" name="comment'.$row["ID"].'">
                    <p>'.$row["CONTENT"].'</p><span class="destroyButton" onclick="destroyComment('.$row["ID"].')">&#215;</span>
                  </div>';
                }
            }else{
                echo '<div class="messageContainer1">
                    <p>'.$row["CONTENT"].'</p>
                  </div>';
            }
            
        }


    }
}

function like($case){
    global $conn;
    $error = NULL;

    $query = "UPDATE regular_post SET LIKES=";
    if($case==1){
        $query = $query."LIKES+1";
    }else{
        $query = $query."LIKES-1";
    }
    $query= $query." WHERE ID=".$_REQUEST["ID"];
    echo '<script>console.log('.$query.');</script>';
    $result = $conn->query($query);

    if(!$result){
        $error = "COULD NOT UPDATE TABLE";
        echo $query;
        echo $error;
    }
}

function getName(){
    global $conn;
    $error = NULL;

    $query = "SELECT NOM FROM personne WHERE ID=".$_REQUEST["ID"];
    $result = $conn->query($query);

    if($result){
        if($row = $result->fetch_assoc()){
            echo $row["NOM"];
        }
    }
}

function sendComment(){
    global $conn;
    $error = NULL;

    if(isset($_REQUEST["msg"])){
        $query = "INSERT INTO comments (ID, CONTENT, POST, OWNERID) VALUES (NULL, '".SecurizeString_ForSQL($_REQUEST["msg"])."', ".$_REQUEST["ID"].", ".$_COOKIE["ID"].")";
        $result = $conn->query($query);
        

    if(!$result){
        $error = "COULDN'T SEND COMMENT";
        echo $error;
    }else{
        echo '<div class="messageContainer2" id="comment'.$_REQUEST["ID"].'" name="comment'.$_REQUEST["ID"].'">
                <p>'.$_REQUEST["msg"].'</p><span class="destroyButton" onclick="destroyComment('.$_REQUEST["ID"].')">&#215;</span>
              </div>';
    }
    }
}

function sendMessage($ID){
    
    global $conn;
    $error = NULL;

    if(isset($_REQUEST["msg"])){
        $query = "INSERT INTO messages (ID, OWNER1, OWNER2, CONTENT) VALUES (NULL, ".$_COOKIE["ID"].", ".$ID.", '".SecurizeString_ForSQL($_REQUEST["msg"])."')";
        $result = $conn->query($query);

    if(!$result){
        $error = "COULDN'T SEND MESSAGE";
    }else{

        $query = "SELECT * FROM messages WHERE CONTENT='".$_REQUEST["msg"]."'";
        $result = $conn->query($query);

        if($result){
            if($row = $result->fetch_assoc()){
                if($row["OWNER1"]==$_COOKIE["ID"]){
                    echo '<div class="messageContainer2" id="message'.$row["ID"].'" name="message'.$row["ID"].'">
                            <p>'.$row["CONTENT"].'</p><span class="destroyButton" onclick="destroyMessage('.$row["ID"].')">&#215;</span>
                          </div>';
                }else{
                    echo '<div class="messageContainer1">
                            <p>'.$row["CONTENT"].'</p>
                          </div>';
                }
            }
        }

        

    }
    }else{
        echo'<script>console.log("erreur");</script>';
    }

    return $error;
}

function sendTest(){
    global $conn;

    $query = "INSERT INTO messages (ID, OWNER1, OWNER2, CONTENT) VALUES (NULL, ".$_COOKIE["ID"].", 2, 'TEST 1 2')";
    
    $result = $conn->query($query);
}

function getMessages($ID){
    global $conn;

    $error = NULL;

    $query = "SELECT * FROM messages WHERE (OWNER1=".$_COOKIE["ID"]." AND OWNER2=".$ID.") OR (OWNER1=".$ID." AND OWNER2=".$_COOKIE["ID"].")";
    
    $result = $conn->query($query);

    if(!$result){
        $error = "COULDN'T RETRIEVE MESSAGE";
    }

  
    

    return array($error, $result);
}

function displayMessages($ID){
    
    $result = getMessages($ID);
    $message = $result[1];
        $error = NULL;
    if($result[0]!=NULL){
        echo $error;
    }else{
        while(  $row = $message->fetch_assoc()){
            if($row["OWNER1"]==$_COOKIE["ID"]){
                echo '<div class="messageContainer2" id="message'.$row["ID"].'" name="message'.$row["ID"].'">
                        <p>'.$row["CONTENT"].'</p><span class="destroyButton" onclick="destroyMessage('.$row["ID"].')">&#215;</span>
                      </div>';
            }else{
                echo '<div class="messageContainer1">
                        <p>'.$row["CONTENT"].'</p>
                      </div>';
            }
            
        }
    }
    
}

function addInterlocutor(){
 
    global $conn;

    $query = "SELECT * FROM personne WHERE ID=".$_REQUEST["toadd"];

    echo $query;

    $result = $conn->query($query);

    if($result){
        if($row = $result->fetch_assoc()){
            
            echo '<button value='.$row["ID"].' id="'.$row["NOM"].'" name="'.$row["NOM"].'" onclick="changeInterlocutor(this)">'.$row["NOM"].'</button>';
        }
    }
}

function checkInterlocutor(){
    global $conn;

    $error = NULL;

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

function getInterlocutors(){

    global $conn;

    $error = NULL;

    $query = "SELECT * FROM messages WHERE OWNER1=".$_COOKIE["ID"]." OR OWNER2=".$_COOKIE["ID"];

    $result = $conn->query($query);

    $interlocutors = array();

    $query = "SELECT * FROM personne WHERE ID IN (";

    if($result){
        while($row = $result->fetch_assoc()){
            if($row["OWNER1"]!=$_COOKIE["ID"] && !in_array($row["OWNER1"] , $interlocutors)){
                array_push($interlocutors, $row["OWNER1"]);
                $query = $query.$row["OWNER1"].",";
            }elseif($row["OWNER2"]!=$_COOKIE["ID"] && !in_array($row["OWNER2"] ,$interlocutors)){
                array_push($interlocutors, $row["OWNER2"]);
                $query = $query.$row["OWNER2"].",";
            }
        }
        if(isset($_REQUEST["receiver"])){
            if(!in_array($_REQUEST["receiver"], $interlocutors)){
           
                $query = $query.$_REQUEST["receiver"].",";
            } 
        }
        

        $length = strlen($query) - 1;

        if($query[$length]==","){
            $query[$length]=")";
        }else{
            $error = "COULDNT FIND ANYTHING";
        }

        $result = $conn->query($query);
        if($result){
            while($row = $result->fetch_assoc()){
                if(isset($_REQUEST["receiver"])){
                    if($row["ID"]!=$_REQUEST["receiver"]){
                    
                        echo '<button value='.$row["ID"].' id="'.$row["NOM"].'" name="'.$row["NOM"].'" onclick="changeInterlocutor(this)">'.$row["NOM"].'</button>';
                        
                    }
                }else{
                    echo '<button value='.$row["ID"].' id="'.$row["NOM"].'" name="'.$row["NOM"].'" onclick="changeInterlocutor(this)">'.$row["NOM"].'</button>';
                }
                
            }
        }
        
    }
}



?>