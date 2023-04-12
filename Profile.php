<?php

include("databaseFunctions.php");
include("fileFunctions.php");
ConnectDatabase();

/*Allows topnav to know which link to highlight */
$active = 0;

/*If a user is connected but no ID is specified in the URL, redirect to user's own page */
if(isset( $_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"]) && !isset($_GET["ID"])){

    $redirect="Location:Profile.php?ID=".$_COOKIE["ID"];
    
    header($redirect);

/*If no user is connected an no ID is specified, redirects to Index */
}elseif((!isset( $_COOKIE["mail"] ) || !isset( $_COOKIE["password"] ) || !isset($_COOKIE["ID"])) && !isset($_GET["ID"])){

    $redirect="Location:Index.php";

    header($redirect);
}
/*If no side is specified, redirect to the "Hobbies" side */
if(!isset($_GET["SIDE"])){
    $redirect = "Location:Profile.php?ID=".$_GET["ID"]."&SIDE=1";
    header($redirect);
}


/*If we are on the "Posts" side and post are not filtered by their hobby sets TAG to none */
if(!isset($_GET["TAG"]) && $_GET["SIDE"]==2){
    $redirect = "Location:Profile.php?ID=".$_GET["ID"]."&SIDE=2&TAG=none";
    header($redirect);
}

$to_add = NULL;

/*If user is connected, displays message popup and check if we are on their profile */
if(isset( $_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"])){
    include("Popup.php");

    /*We are on the user's profile */
    if($_COOKIE["ID"]==$_GET["ID"]){
        $active = 4;
        displayPopUp(NULL);

    /*We are not on the user's profile, we add the option to contact the user who owns the page through Private Messages */
    }else{
        
        $to_add = addButton($_GET["ID"]);
        displayPopUp($_GET["ID"]);
    }
   
}

include("Topnav.php");


/*Get and display the profile of the owner of the page */
$error = getProfile($to_add);

if($error!=NULL){
    /*If error occured, stops rest of php from executing and display error message */
            exit('<div id=ErrorContainer>
            <p>'.$error.'. PLEASE GO BACK MAIN MENU </p>
        </div>');
}

/*displays page differently depending on if we are on the Hobby side or the Regular side */
switch($_GET["SIDE"]){
    case 1: $status = getPosts(1);
    $error = $status[0];

    /*The array returned by the query */
    $result = $status[1];

    if($error!=NULL){
        exit('<div id=ErrorContainer>
        <p>'.$error.'</p>
    </div>');
    }
        
            while($row = $result->fetch_assoc()){
                /*RECUP LE NOMBRE DE LIKE */
                /*Beginning of post container */
                echo '<div id="MainContainerP">';
                /* TODO : MAKE LINKS SPANS ? 
                 * If user is the owner of the page, they get the option to edit or delete their posts
                 */
                if(isset($_COOKIE["ID"])){
                    if($_COOKIE["ID"]==$_GET["ID"]){
                        echo '<a title="Edit Post" href="./EditPost.php?ID='.$row["ID"].'&SIDE=1"><img src="./Images/Edit.png" class="addPost3"></a>';
                        echo '<a title="Edit Post" href="./Confirm.php?ID='.$row["ID"].'&SIDE=1"><img src="./Images/Delete.png" class="addPost3" style="left:81%"></a>';
                    }
                }

                /*Container containing : name of hobby, exeperience of user, the frequency at which the hobby is being done, as well as if the user wishes to do this hobby
                 *with a group/partner 
                 */

         echo ' <div class="conhobby">
         
                    <div class="titlehobby">
                        <h1>'.$row["NOM"].'</h1>
                        <p class="tagExperience">'.$row["EXPERIENCE"].'</p><p class="tagFrequence">'.$row["FREQUENCY"].'</p>';

                        /*Availability is stored in the SQL database as a binary value */
                        if($row["AVAILABLE"]==1){
                            echo '<p class="tagAvailable">Available</p>';
                        }else{
                            echo '<p class="tagUnAvailable">Not Available</p>';
                        }
                    echo '
                    </div>
                    <div class="charahobby">';
        
                    /*Checks if user decided to add a description to their hobby */
                    if($row["DESCRIPTION"]){
                        echo'<h4>Descritpion</h4>
                        <p>'.$row["DESCRIPTION"].'</p>';
                    }else{
                        /*if user has decided to not add a description display message : */
                        echo '<p style="color:gray"><i>This user does not seem to have any description for this hobby</i></p>';
                    }
                     echo '</div>
                     </div>' ;  
                    /*If post does not have an image : */
                if(!$row["IMAGE"]){

                    /*gets default image corresponding to the hobby, see fileFunctions.php */
                    $default = getDefault($row, 1);

                    $error = $default[0];
                    $image = $default[1];

                    if($error == NULL){
                        echo '<img class="hobby2" src="./Images/'.$image.'">
                       
                    </div>';
                    }else{
                        /*TODO */
                    }
                    
                } else{
                    echo' <img class="hobby2" src="./uploads/'.$row["IMAGE"].'">
                       
                    </div>';
                }
            }
        
        break;

    case 2:
        $status = getPosts(2);
        $error = $status[0];
        $result = $status[1];
        if($error!=NULL){
            /*If error occured, stops rest of php from executing and display error message */
        exit('<div id=ErrorContainer>
            <p>'.$error.'</p>
            </div>');
    }
            /*Gets tags user can use to filter curent page's posts (these tags are hobbies present in the current page's owner's hobbies) */
            $status = getAvailableTags(2);

            /*If page's owner has more than two hobbies, display Select to filter post */
            if($status[2]>1){
                echo '
                <div class="headline">
                <div class="void"></div>
                <form name="value" action="./Profile.php" method="get">
                <input type="hidden" name="ID" value="'.$_GET["ID"].'">
                <input type="hidden" name="SIDE" value="'.$_GET["SIDE"].'">
                <select id="TAG" name="TAG">';

                $tags = $status[0];
                $IDs = $status[1];
                $index = 0;
                
                        foreach($IDs as &$value){
                             /* if $_GET["TAG"] is equal to the idea of the current tag, this tag will be the starter value of the Select*/
                            if($_GET["TAG"]==$value){
                               
                                echo '<option value="'.$value.'" selected>'.$tags[$index].'</otpion>';
                            }else{
                                echo '<option value="'.$value.'">'.$tags[$index].'</otpion>';
                            }
                            $index +=1;
                        }
                        /*If the posts aren't being filtered, "none" will be the starting option */
                        if($_GET["TAG"]=="none"){
                            echo '<option value="none" selected>None</option>';
                        }else{
                            echo '<option value="none">None</option>';
                        }
                        
            echo '    
            
                </select>
                <button type="submit">test</button>
                </form>
                </div>';
            }
            
            $count = 0;

            while($row = $result->fetch_assoc()){
                $count += 1;
                echo '<div id="MainContainerP">';
                echo $row["LIKES"];
                if(isset($_COOKIE["ID"]) && isset($_COOKIE["ID"])){
                    if($_COOKIE["ID"]==$_GET["ID"]){
                        echo '<a title="Edit Post" href="./EditPost.php?ID='.$row["ID"].'&SIDE=2"><img src="./Images/Edit.png" class="addPost3"></a>';
                        echo '<a title="Edit Post" href="./Confirm.php?ID='.$row["ID"].'&SIDE=2"><img src="./Images/Delete.png" class="addPost3" style="left:81%"></a>';
                    }
                }
                echo '
                <div class="divP">
                <div class="conhobby">
                    <div class="titlehobby">
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
                        <p>'.$row["DESCRIPTION"].'</p>';
                    }else{
                        echo '<p style="color:gray"><i>This user does not seem to have any description for this post</i></p>';
                    
                    }
             echo ' 
                    </div>
                </div>' ;  
                    
                /*TROUVER MEILLEUR MOYEN D'UPLOAD DES NULLS */
                if(!$row["IMAGE"] || $row["IMAGE"]=="NULL"){
                    echo '<img class="hobby2" src="./Images/Filler3.png"> 
                    </div>';
                } else{
                    echo' <img class="hobby2" src="./uploads/'.$row["IMAGE"].'">
                    </div>';
                }
                
                
 
                echo '
                <div class="buttonPDiv" ">
            
                <input type="hidden" name="post'.$row["ID"].'" id="post'.$row["ID"].'" value="'.$row["LIKES"].'">
                <button class="buttonP" style="border-right:solid" id="button'.$row["ID"].'" onclick="like('.$row["ID"].')">Likes '.$row["LIKES"].'</button>
                <button id="buttonComments'.$row["ID"].'" class="buttonP" onclick="openComments('.$row["ID"].')">Show Comments</button>
                </div>

                <div style="border:solid" class="history" name="historyComments'.$row["ID"].'" id="historyComments'.$row["ID"].'"></div>';
                if(isset($_COOKIE["ID"])){
                    echo '<div id="input'.$row["ID"].'" style="display:none">
                    <textarea placeholder="Type message.." id="commentZone'.$row["ID"].'" name="commentZone'.$row["ID"].'" required rows="1"></textarea>
                    <button type="button" class="btn" onclick="uploadComment('.$row["ID"].')">Send</button> 
                    </div>';
                }
                echo '
                
                </div>';
            }

            /*If user has not shared any posts, display message */
            if($count < 1){
                echo '<div id="MainContainerP">
                    <p><i>This user do not seem to have any posts as of yet</i></p>
                </div>';
            }

        break;
}

/*Checks if user is connected*/ 
if(isset( $_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"])){
    /*if user is the owner of the page, gives the option to add a hobby or post something */
    if ($_GET["ID"]==$_COOKIE["ID"]){
    echo '<div class="tag"><a class="tagEdit" href="./newPost.php?ID='.$_GET["ID"].'&SIDE=1"">Ajouter un Hobby</a></div>';
    echo '<div class="tag" ><a class="tagEdit" style="right:19.5%;background-color:#5c7999" href="./newPost.php?ID='.$_GET["ID"].'&SIDE=2">Ajouter un Post</a></div>';
    
    /*TODO */
    echo '<a title="Open Messages" href="./newPost.php?ID='.$_GET["ID"].'"><img style="right:36%" src="./Images/Message.png" class="addPost2"></a>';
    }else{
    
    echo '<a href="./newPost.php?ID='.$_GET["ID"].'"><img src="./Images/Message.png" class="addPost2"></a>';
    }
    
}elseif(!isset( $_COOKIE["mail"] ) || !isset( $_COOKIE["password"] ) || !isset($_COOKIE["ID"])){
    echo '<a href="./newPost.php?ID='.$_GET["ID"].'"><img src="./Images/Message.png" class="addPost"></a>';
}

?>


<script>
/*Mostly xmlhttp request allowing us to comment, like posts and fetch comments without having to reload the page */
/*Allow user to comment a post
number : ID of the post under which comment is being posted */
function uploadComment(number){

    /*If textbox is empty, does nothing */
    if(document.getElementById("commentZone"+number).value == ""){
        return;
      }
      
      var xmlhttp = new XMLHttpRequest();
      /*once request is done :  */
      xmlhttp.onreadystatechange=function(){
        if(this.readyState==4 && this.status==200){
        /*Empties textbox */
          document.getElementById("commentZone"+number).value = "";
          /*Add new comment to comments already being displayed 
           *The request will generate a php echo, this php echo will be accessible through this.responseText (meaning we display the php echo)
           */
          document.getElementById("historyComments"+number).innerHTML += this.responseText;
          
          
        }
      }
      /*Sends the request via post to avoid cluttering url with useless information */
      xmlhttp.open("post", "messageFunctions.php", true);
      xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

      /*Parameters allows us to pass information to a "post" request
       *action: behavior the messageFunctions.php page will take
       *ID : ID of the post
       *msg : text of the comment
       */
      var parameters = "action=comment&ID="+number+"&msg="+document.getElementById("commentZone"+number).value
     
      /*Start the request*/
      xmlhttp.send(parameters);
}

/*Delete comment
 * Can only be done by the owner of the comment
 * Amelioration Ideas : Add options to report comment ?
 * Create an admin role that can delete any comment ?
 */
function destroyComment(number){

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange=function(){
      if(this.readyState==4 && this.status==200){
        /*this.responseText can only be an error, if is null, there is no error
         * To avoid reloading all the comments, just the deleted comment is being removes via remove()
         */
        if(this.responseText != null)
        document.getElementById("comment"+number).remove();  
        else alert(this.responseText);    
      }
    }

    xmlhttp.open("post", "messageFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
   
    var parameters = "action=destroyComment&ID="+number;
    xmlhttp.send(parameters);

}

/* Allow user to like a post
 * TODO toggle likes button, and checks if user hasn't already liked, disable button until function is done
 */
function like(number){
    
    var xmlhttp = new XMLHttpRequest();
    document.getElementById("button"+number).disbled = true;
    xmlhttp.onreadystatechange=function(){
      if(this.readyState==4 && this.status==200){
        /*calculate new value of number of likes */
        var add = parseInt(document.getElementById("post"+number).value) + 1;
        
        /*change displayed value of numbers of like */
        document.getElementById("post"+number).value = add;
        document.getElementById("button"+number).innerHTML ="Likes "+  document.getElementById("post"+number).value;

        /*change behavior of like button 
         * IMPORTANT : it is necessary to encapsulate the new behavior of the button in a function (){}, other wise the page will just execute the function
         */
        document.getElementById("button"+number).disbled = false;
        document.getElementById("button"+number).onclick = function () {dislike(number);}
      }
    }

    xmlhttp.open("post", "messageFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    var parameters = "action=like&ID="+number;
    xmlhttp.send(parameters);


}

/* Allow user to dislike a post
 * TODO toggle likes button, and checks if user hasn't already liked, disable button until function is done
 */
function dislike(number){

    /*Disable button to prevent bug where user can like multiple times by clicking the button
     * multiple times in very short intervals
     */
    document.getElementById("button"+number).disabled = true;

    var xmlhttp = new XMLHttpRequest();

    

    xmlhttp.onreadystatechange=function(){
        if(this.readyState==4 && this.status==200){
            var substract = parseInt(document.getElementById("post"+number).value) - 1;
        
            document.getElementById("post"+number).value = substract;
            document.getElementById("button"+number).innerHTML ="Likes "+  document.getElementById("post"+number).value;
            document.getElementById("button"+number).disabled = false;
            document.getElementById("button"+number).onclick = function () {like(number);}
        }
    }

    xmlhttp.open("post", "messageFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    var parameters = "action=dislike&ID="+number;
    xmlhttp.send(parameters);

}


/*Hid comments of post of ID=number */
function closeComments(number){
    
    /*hides comment */
    document.getElementById("historyComments"+number).style.display="none";
    /*change text and behavior and text of button */
    document.getElementById("buttonComments"+number).innerHTML = "Show Comments";
    /*changes behavior of button */
    document.getElementById("buttonComments"+number).onclick = function() {
        /*displays comments but does not reload them
         * TODO : is this the better solution ?
         */
        document.getElementById("historyComments"+number).style.display = "block";
        /*changes behavior of button */
        document.getElementById("buttonComments"+number).onclick = function() {closeComments(number)};
        /*changes text of button */
        document.getElementById("buttonComments"+number).innerHTML = "Hide Comments";};
}

/*Open comments for the first time, loading them from the SQL database */
function openComments(number){
    
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function(){
        if(this.readyState==4 && this.status==200){
            /*makes comment container visible */
            document.getElementById("historyComments"+number).style.display = "block";
            /*Sets content of comment container */
            document.getElementById("historyComments"+number).innerHTML = this.responseText;
            document.getElementById("input"+number).style.display = "block";
            /*Change text and behavior of the open comment button */
            document.getElementById("buttonComments"+number).onclick = function() {closeComments(number)};
            document.getElementById("buttonComments"+number).innerHTML = "Hide Comments";
        }
    }

    xmlhttp.open("post", "messageFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    var parameters = "action=displayComments&ID="+number;
    xmlhttp.send(parameters);
}

</script>
<?php
include("Footer.php");
DisconnectDatabase();
?>