<link rel="stylesheet" href="./Css/Tag.css">
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
                                echo '<option value="'.$row["ID"].'" selected>'.$row["HOBBY_NAME"].'</option>';
                            }else{
                                echo '<option value="'.$row["ID"].'">'.$row["HOBBY_NAME"].'</option>';
                            }
                        }else{
                            echo '<option value="'.$row["ID"].'">'.$row["HOBBY_NAME"].'</option>';
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
$queryHobbyPosts = fetchesPostsWithSpecifiedTag();

/*Name of the hobby */
$name = $queryHobbyPosts[1];

/*list of hobby posts */
$result = $queryHobbyPosts[0];



if($result!=NULL){
   
    echo '<div id="MainContainerT">';
    
    if($name!=""){
        echo ' <h1>Hobby : '.$name.'</h1>';
    }
    /*Line to indicate to user what kind of values are displayed in each column */
    echo '
    <div class="line" style="width:80%">
        <div class="fieldpic" style="vertical-align: middle; margin: auto">Avatar</div>
        <div class="fieldtext">Name</div>
        <div class="fieldtext">Experience</div>
        <div class="fieldtext">Frequency</div>
        <div class="fieldtext">Availability</div>
        <div class="fieldots" style="font-size:2vw">Options</div>
    
    </div>
    <div class="con">';
    /*indicates if a personn with the corresponding tag was found */
    $found = false;    

    $numberOfPersonFound = 0;

    while($row = $result->fetch_assoc()){

        $numberOfPersonFound +=1;
        /*At least one personn with the corresponding tag was found */
        $found=true;
    
        $result2 = getLine($row["OWNER"]);
    
  echo '<div class="line">
            <div class="fieldpic">';
                if($result2["AVATAR"]){
                    echo '<img class="profilepic" src="./uploads/'.$result2["AVATAR"].'">';
                }else{
                    echo '<img class="profilepic" src="./Images/img_avatar.png">';
                }
            
        echo '
            </div>
            <div class="fieldtext">
                <a href="./Profile.php?ID='.$row["OWNER"].'">'.$result2["NAME"].'</a>
            </div>
            <div class="fieldtext">
                <p class="tagLightColor"  style="width:85%">'.$row["EXPERIENCE"].'</p>
            </div>
            <div class="fieldtext">
                <p class="tagDarkColor" style="width:85%">'.$row["FREQUENCY"].'</p>
            </div>
        
            <div class="fieldtext">';
                if($row["AVAILABLE"]==1){
                    echo '<p class="tagLightColor"  style="width:85%">Available</p>';
                }else{
                    echo '<p class="tagLightColor"  style="width:85%">Not Available</p>';
                }
                //TODO : fieldots
                /*Modal giving additionnal options to the user */
        echo '        
            
        </div>
        <div class="fieldots">';
            
        if(isset($_COOKIE["mail"] ) && isset( $_COOKIE["password"] ) && isset($_COOKIE["ID"])){
            echo' <span class="dots" onclick="displayOptions('.$row["OWNER"].', true)" id="dots'.$row["OWNER"].'">&#xFE19;</span>
            <div class="optionModal" id="options'.$row["OWNER"].'" >
                <p class="optionLine" onclick="checkInterlocutor('.$row["OWNER"].')">
                    Contact
                </p>
            </div>';
        }else{
            echo '<span class="dots" onclick="displayOptions('.$row["OWNER"].', false)" id="dots'.$row["OWNER"].'">&#xFE19;</span>';
        }
           
    echo '
        </div>
    </div>';
    }

    if(!$found){
        /*If we haven't found anyone with this hobby */
        echo '<p>NO ONE SEEMS TO PARTAKE IN THAT HOBBY AT THE MOMENT</p>';
    }elseif($numberOfPersonFound<4){
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


/*Displays Option modal if connected, alert message otherwise */
function displayOptions(number, connected){
    if(connected){
        /*grays out the Options dots*/
        document.getElementById("dots"+number).style.color = "lightgray";
        /*displays options */
        document.getElementById("options"+number).style.display = "block";
        document.getElementById("dots"+number).onclick = function(){closeOptions(number, connected);};
        window.onclick = function(event){
       
        /*When window registers a click, if target of click is not our modal, close options */
            if(event.target != document.getElementById("dots"+number) ){
                closeOptions(number, connected);
            }
       
        }
    }else{
        alert("Please log in if you want to access options");
    }
   
}
/*close options modal and return dots to original state */
function closeOptions(number, connected){
    document.getElementById("dots"+number).style.color = null;
    document.getElementById("options"+number).style.display = null;
    document.getElementById("dots"+number).onclick = function(){displayOptions(number, connected);};

    
}



</script>
    
