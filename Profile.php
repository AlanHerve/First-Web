<link rel="stylesheet" href="./Css/Post.css">
<link rel="stylesheet" href="./Css/Profile.css">
<link rel="stylesheet" href="./Css/Popup.css">

<script type="text/javascript" src="./profile.js"></script>
<?php

include("databaseFunctions.php");
include("fileFunctions.php");

ConnectDatabase();

/*Allows topnav to know which link to highlight */
$active = 0;

if(isset($_COOKIE["inscription"])){
    echo '<div id=ErrorContainer style="background-color:lightblue;border-color:white">
    <p> Congrats on creating you new Account ! </p>
</div>';
setcookie("inscription", NULL, -1);
}

/*If a user is connected but no ID is specified in the URL, redirect to user's own page */
if(isset( $_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"]) && !isset($_GET["ID"])){

    $redirect="Location:Profile.php?ID=".$_COOKIE["ID"];
   
    header($redirect);

/*If no user is connected an no ID is specified, redirects to Index */
}elseif((!isset( $_COOKIE["mail"] ) || !isset( $_COOKIE["password"] ) || !isset($_COOKIE["ID"])) && !isset($_GET["ID"])){

    $redirect="Location:Index.php";
    
    header($redirect);

/*If no side is specified, redirect to the "Hobbies" side */
}elseif(!isset($_GET["SIDE"])){
    $redirect = "Location:Profile.php?ID=".$_GET["ID"]."&SIDE=1";
   
    header($redirect);



/*If we are on the "Posts" side and post are not filtered by their hobby sets TAG to none */
}elseif(!isset($_GET["TAG"]) && $_GET["SIDE"]==2){
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
                /*Beginning of post container */
                echo '<div id="MainContainerProfileSide1" >';
                /*  If user is the owner of the page, they get the option to edit or delete their posts */
                if(isset($_COOKIE["ID"])){
                    if($_COOKIE["ID"]==$_GET["ID"]){
                        echo '<a title="Edit Post" href="./EditPost.php?ID='.$row["ID"].'&SIDE=1"><img src="./Images/Edit.png" class="circleButton"></a>';
                        echo '<a title="Edit Post" href="./Confirm.php?ID='.$row["ID"].'&SIDE=1"><img src="./Images/Delete.png" class="circleButton" style="left:81%"></a>';
                    }
                }

                /*Container containing : name of hobby, exeperience of user, the frequency at which the hobby is being done, as well as if the user wishes to do this hobby
                 *with a group/partner 
                 */

         echo ' <div class="conhobby">';
         if($row["MODIFIED"]==1){
            echo '<p style="color:gray"><i>Modified '.formatDate($row["TIME"]).'</i></p>';
         }else{
            echo '<p style="color:gray"><i>Posted '.formatDate($row["TIME"]).'</i></p>';
         }
               
                  echo '<div class="titlehobby">
                        <h1>'.$row["HOBBY_NAME"].'</h1>
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
                    }elseif(isset( $_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"])){
                            /*if user has decided to not add a description display message : */
                            if($_GET["ID"]!=$_COOKIE["ID"]){
                                
                        echo '<p class="descriptionText" style="color:gray"><i>This user does not seem to have any description for this hobby</i></p>';
                            }else{
                                echo '<p class="descriptionText" style="color:gray"><i>You do not seem to have any description for this hobby</i></p>';
                            }
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
                        echo '<img class="uniqueImageHobby zoomable" src="./Images/'.$image.'" onclick="zoomImage(this)">
                       
                    </div>';
                    }
                    
                } else{
                    echo' <img class="uniqueImageHobby zoomable" src="./uploads/'.$row["IMAGE"].'" onclick="zoomImage(this)">
                       
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
                $tags = $status[0];
                $IDs = $status[1];
                $index = 0;
                echo '
                <div class="headline">
                <div class="void"></div>
                <div class="full">
                    <form name="value" action="./Profile.php" method="get">
                    <input type="hidden" name="ID" value="'.$_GET["ID"].'">
                    <input type="hidden" name="SIDE" value="'.$_GET["SIDE"].'">
                    <select class="tagselect" id="TAG" name="TAG">';

               
                
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
                        
              echo '</select>
                    <button class="tagbutton" type="submit">Filter</button>
                    </form>
                    </div>
                </div>';
            }
            
            $count = 0;

            if( isset( $_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"])){
                echo '<input type="hidden" id="idUser" name="idUser" value="'.$_COOKIE["ID"].'">';
            }

            if(mysqli_num_rows($result)>0){


                while($row = $result->fetch_assoc()){
                
                    echo '<div id="MainContainerProfileSide2" >';
                          if(isset($_COOKIE["ID"]) && isset($_COOKIE["ID"])){
                              if($_COOKIE["ID"]==$_GET["ID"]){
                                  echo '<a title="Edit Post" href="./EditPost.php?ID='.$row["ID"].'&SIDE=2"><img src="./Images/Edit.png" class="circleButton"></a>';
                                  echo '<a title="Edit Post" href="./Confirm.php?ID='.$row["ID"].'&SIDE=2"><img src="./Images/Delete.png" class="circleButton" style="left:81%"></a>';
                              }
                          }
                          echo '
                              <div class="divFlexRow">
                                  <div class="conhobby">';
                                  if($row["MODIFIED"]==1){
                                      echo '<p style="color:gray"><i>Modified '.formatDate($row["TIME"]).'</i></p>';
                                   }else{
                                      echo '<p style="color:gray"><i>Posted '.formatDate($row["TIME"]).'</i></p>';
                                   }
                                     echo' <div class="titlehobby" >
                                          <h1>'.$row["HOBBY_NAME"].'</h1>
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
                                      echo '<img id="imagePost'.$row["ID"].'&'.$countImage.'" class="regularImage zoomable" src="./uploads/'.$row[$image].'" onclick="zoomImage(this)">';
                                  }
                              }
                              
                              if($countImage > 1){
                                  echo '<script>resizeImages('.$row["ID"].', '.$countImage.');</script>';
                              }elseif($countImage==0){
                                  $default = getDefault($row["TYPEID"], 1);
          
                                  $error = $default[0];
                                  $image = $default[1];
              
                                  if($error == NULL){
                                      echo '<img id="imagePost'.$row["ID"].'&1" class="regularImage zoomable" src="./Images/'.$image.'" onclick="zoomImage(this)">
                                     
                                  ';
                                  }
                              }
                              echo '</div>
                              </div>';

                          echo '
                          <div class="commentOrLikeButtonDiv" ">
                          <input type="hidden" name="running'.$row["ID"].'" id="running'.$row["ID"].'" value="false">
                          <input type="hidden" name="closeConditionInterval'.$row["ID"].'" id="closeCondition'.$row["ID"].'" value="false">
                          <input type="hidden" name="IdInterval'.$row["ID"].'" id="IdInterval'.$row["ID"].'" value="">
                          <input type="hidden" name="IdIntervalClose'.$row["ID"].'" id="IdIntervalClose'.$row["ID"].'" value="">
                          <input type="hidden" name="post'.$row["ID"].'" id="post'.$row["ID"].'" value="'.$row["LIKES"].'">
                          <button class="commentOrLikeButton" style="border-right:solid" id="button'.$row["ID"].'" onclick="like('.$row["ID"].')">Like this post : '.$row["LIKES"].'</button>
                          <button id="buttonComments'.$row["ID"].'" class="commentOrLikeButton" onclick="openComments('.$row["ID"].')">Show Comments</button>
                          </div>
                          <div  class="historyComments" name="historyComments'.$row["ID"].'" id="historyComments'.$row["ID"].'"></div>';
                          /*these values must be set manually to false or they won't be resseted if cache is erased/page reloaded */
                          echo '<script>document.getElementById("closeCondition'.$row["ID"].'").value = "false";
                          document.getElementById("running'.$row["ID"].'").value = false</script>';

                          if(isset($_COOKIE["ID"])){
                              echo '<div class="commentUserZone" id="input'.$row["ID"].'" style="display:none" >
                              <textarea maxlength="50" placeholder="Type message.." id="commentZone'.$row["ID"].'" name="commentZone'.$row["ID"].'" required rows="1"></textarea>
                              <button  type="button" class="btn" onclick="uploadComment('.$row["ID"].')">Send</button> 
                              </div>';

                              echo '<script>initLikes('.$row["ID"].');</script>';
                          }
                          echo '
                          
                          </div>';
                      }

            }else{
                /*If user has not shared any posts, display message */
                echo '<div id="MainContainerProfileSide1">
                    <p><i>This user do not seem to have any posts as of yet</i></p>
                </div>';
            }
            

            

        break;
}



/*Checks if user is connected*/ 
if(isset( $_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"])){
    /*if user is the owner of the page, gives the option to add a hobby or post something */
    if ($_GET["ID"]==$_COOKIE["ID"]){
        echo'<div class="addPostContainer"><div class="tag"><a class="tagEdit" style="right:43%" href="./newPost.php?ID='.$_GET["ID"].'&SIDE=1"">Add a Hobby</a></div>
        <div class="tag" ><a class="tagEdit" style="right:33.5%;background-color:#5c7999" href="./newPost.php?ID='.$_GET["ID"].'&SIDE=2">New Post</a></div></div>';
        echo '';
    

        echo '';
    
    
    
    }
    
}
?>


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




<?php
include("Footer.php");
DisconnectDatabase();
?>