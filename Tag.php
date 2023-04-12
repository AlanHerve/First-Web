<?php
/*This page allows the user to search users based on the hobbies they 
partake in */

/*WARNING LINES SOMETIMES NOT ALIGNED */

include("databaseFunctions.php");
$active = 2;
ConnectDatabase();
include("Topnav.php");
include("fileFunctions.php");

if(isset( $_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"]) && !isset($_GET["ID"])){
    
}
/*takes the list of tags from the SQL database */
$list = getAllTags();
?>
<p><br></p>
    <div id="MainContainerPC" style="flex-direction: column;">
        <h1>Find people by using tags</h1>
    </div>

    <div class="headline">
        <div class="void"></div>
        <div class="full">
        <form name="value" action="./Tag.php" method="get">
            <!--Select tag you want to research-->
            <select class="tagselect" id="TAG" name="TAG">
                <?php
                    while($row = $list->fetch_assoc()){
                        /*if a tag has been researched, the select's initial value will be the researched tag */
                        if(isset($_GET["TAG"])){
                            if($_GET["TAG"]==$row["ID"]){
                                /*ID of tag is equal to the tag researched */
                                echo '<option value="'.$row["ID"].'" selected>'.$row["NOM"].'</option>';
                            }else{
                                echo '<option value="'.$row["ID"].'">'.$row["NOM"].'</option>';
                            }
                        }else{
                            echo '<option value="'.$row["ID"].'">'.$row["NOM"].'</option>';
                        }
                    }
                ?>
            </select>
            <button class="tagbutton" type="submit">Research</button>
            
        </form>
        </div>
    </div>

<?php
/*Asks the database for a list of hobby posts with the same tag*/
$queryHobbyPosts = tagPost();

/*Name of the hobby */
$name = $queryHobbyPosts[1];

/*list of hobby posts */
$result = $queryHobbyPosts[0];



if($result!=NULL){
   
    echo '<div id="MainContainerT">';
    if($name!=""){
        echo ' <h1>Hobby : '.$name.'</h1>';
    }
    echo '
    <div class="line" style="width:80%">
        <div class="fieldpic" style="vertical-align: middle; margin: auto">Avatar</div>
        <div class="fieldtext">Nom</div>
        <div class="fieldtext">Experience</div>
        <div class="fieldtext">Frequence</div>
        <div class="fieldtext">Disponible</div>
        <div class="fieldots" style="font-size:2vw">Options</div>
    
    </div>
    <div class="con">';
    $found = false;    

    $count = 0;
    while($row = $result->fetch_assoc()){

        $count +=1;

        $found=true;
    
        $result2 = getLine($row["OWNER"]);
    
        echo '<div class="line">
        <div class="fieldpic">';
        if($result2["avatar"]){
            echo '<img class="profilepic" src="./Images/'.$result2["avatar"].'">';
        }else{
            echo '<img class="profilepic" src="./Images/img_avatar.png">';
        }
            
        echo '
        </div>
        <div class="fieldtext">
           <a href="./Profile.php?ID='.$row["OWNER"].'">'.$result2["NOM"].'</a>
        </div>
        <div class="fieldtext">
        <p class="tagExperience"  style="width:85%">'.$row["EXPERIENCE"].'</p>
        </div>
        <div class="fieldtext">
        <p class="tagFrequence" style="width:85%">'.$row["FREQUENCY"].'</p>
        </div>
        
        <div class="available">
            <div class="tag" style="width:100%">';
            if($row["AVAILABLE"]==1){
                echo '<p class="tagAvailable"  style="width:85%">Available</p>';
            }else{
                echo '<p class="tagUnAvailable"  style="width:85%">Not Available</p>';
            }
                //TODO : fieldots
                /*Modal giving additionnal options to the user */
        echo '        
            </div>
        </div>
        <div class="fieldots">
            <span class="dots" onclick="displayOptions('.$row["OWNER"].')" id="dots'.$row["OWNER"].'">&#xFE19;</span>';
        if(isset($_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"]))
           echo' <div class="optionModal" id="options'.$row["OWNER"].'" >
           <p onclick="checkInterlocutor('.$row["OWNER"].')">
           Contact</p></div>';
    echo '
        </div>
    </div>';
    }

    if(!$found){
        /*If we haven't found anyone with this hobby */
        echo '<p>NO ONE SEEMS TO PARTAKE IN THAT HOBBY AT THE MOMENT</p>';
    }elseif($count<4){
        /*If the number of person found is > 0 but < 4, the page will display the default image for the hobby researched to occupy the empty space */
        $default = getDefault($row, 2);

                    $error = $default[0];
                    $image = $default[1];
        if($error==NULL)
        echo '<img class="hobby" src="./Images/'.$image.'">';
    }
    
}else{
    /*Just no one has shared a hobby, which is unlikely */
    echo '<div id="MainContainerT">
    <div class="con">
    <p><i>No one has shared a hobby yet</i></p>';
}


echo '   </div>
</div>' ;  

include("Footer.php");
DisconnectDatabase();

if(isset( $_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"]) && !isset($_GET["ID"])){
    include("Popup.php");
    displayPopUp(NULL);
}
?>    

<script>

function displayOptions(number){
    document.getElementById("dots"+number).style.color = "lightgray";
    document.getElementById("options"+number).style.display = "block";
    document.getElementById("dots"+number).onclick = function(){closeOptions(number);};
    window.onclick = function(event){
        /*When window registers a click, if target of click is not our modal, close options */
        if(event.target != document.getElementById("dots"+number)){
            closeOptions(number);
        }
    }
}

function closeOptions(number){
    document.getElementById("dots"+number).style.color = null;
    document.getElementById("options"+number).style.display = null;
    document.getElementById("dots"+number).onclick = function(){displayOptions(number);};
}



</script>
    
