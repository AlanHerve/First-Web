function deleteImage(){

  var current = document.getElementById("current").value;
  document.getElementById("deleteImage"+current).value = "0";
  

  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onload = function(){
    var result = this.responseText;
    if(result == "error") alert("An error occured");
    else {
      const idOfPost = document.getElementById("idOfPost").value;
      document.getElementById("imagePost"+idOfPost+"&"+current).src = result;
    }
    
    

    document.getElementById("Modal").style.display = "none";
    document.getElementById("default"+current).value = "true";
  }
  xmlhttp.open("post", "messageFunctions.php", true);
  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  
  var parameters = "action=default&type="+document.getElementById("typeOfPost").value;
 
  /*Start the request*/
  xmlhttp.send(parameters);

}



function zoomImage(obj){
  var modal = document.getElementById("Modal");
  modal.style.display = "block";

  var image = document.getElementById("ModalImage");
  image.src = obj.src;

  const regex = /^.+&/ ;

  var ID = obj.id;
  ID = ID.replace(regex, "");

  var defaultID = document.getElementById("default"+ID);
  if(defaultID){
    if (defaultID.value == "true")  document.getElementById("defaultPrompt").style.display = "none";
    //document.getElementById("defaultExplanation").style.display = "block";
  }
  

  if(document.getElementById("current")){
   
    document.getElementById("current").value = ID;

   
    document.getElementById("fileToUpload"+ID).style.display="block";
    
  }
  

  

  window.onclick = function(event){
    
    /*When window registers a click, if target of click is not our modal, close options */
   if(event.target == document.getElementById("Modal") && event.target != document.getElementsByClassName("gridImageComponent")){
      deZoomImage(defaultID);
    }
}

}

function deZoomImage(defaultID){
  
  var modal = document.getElementById("Modal");
 
  if(defaultID) document.getElementById("defaultPrompt").style.display = "block";
  var current = document.getElementById("current");
  if(current) document.getElementById("fileToUpload" + current.value).style.display="none";
  modal.style.display = "none";
  window.onclick = null;
}


function resizeImages(IdOfImages, numberOfImages){
    
    document.getElementById("potentialGrid"+IdOfImages).className = "gridImage";
    for(let i = 1; i<= numberOfImages; i++) document.getElementById("imagePost"+IdOfImages+"&"+i).className = "gridImageComponent";


}


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
    
    document.getElementById("button"+number).disabled = true;

    var xmlhttp = new XMLHttpRequest();
    
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
        document.getElementById("button"+number).disabled = false;
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
    document.getElementById("input"+number).style.display = "none";
    /*changes behavior of button */
    document.getElementById("buttonComments"+number).onclick = function() {
        /*displays comments but does not reload them
         * TODO : is this the better solution ?
         */
        document.getElementById("input"+number).style.display = "block";
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

