
/*This function is used to delete an image in the DeletePost section */
function deleteImage(){

  var current = document.getElementById("current").value;
  /*stores in the form that image n° current will need to be deleted
   * in the database
   */
  document.getElementById("deleteImage"+current).value = "0";
  

  /*Aks the database for the default image for the hobby */
  var type = document.getElementById("typeOfPost");
  if(type){


    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onload = function(){
    var result = this.responseText;
    if(result == "error") alert("An error occured");
    else {
      const idOfPost = document.getElementById("idOfPost").value;
      /*changes image n° current of post n° idOfPost */
      document.getElementById("imagePost"+idOfPost+"&"+current).src = result;
    }
    
    
    /*hides Modal and notifies html that image n° current is a default image */
    document.getElementById("Modal").style.display = "none";
    document.getElementById("default"+current).value = "true";
  }
    xmlhttp.open("post", "./XMLFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    var parameters = "action=default&type="+type.value;
    /*Start the request*/
    xmlhttp.send(parameters);


    /*if image does not come from a post, then it comes from a profile pic */
  } else{
    const idOfPost = document.getElementById("idOfPost").value;
    /*changes image n° current of post n° idOfPost */
    document.getElementById("imagePost"+idOfPost+"&"+current).src = "./Images/img_avatar.png";
  
  
  
    /*hides Modal and notifies html that image n° current is a default image */
    document.getElementById("Modal").style.display = "none";
    document.getElementById("default"+current).value = "true";
  }
   
  

}

/*Function allowing user to click on an image to display it in better proportions*/
function zoomImage(obj){

  /*plane of z-index > to that of the rest of the website */
  var modal = document.getElementById("Modal");
  modal.style.display = "block";

  /*changes content of img element of the Modal */
  var image = document.getElementById("ModalImage");
  image.src = obj.src;

  /*remove  useless string to keep only the ID of the Image inside the post
   * (value between 1 and 4)
   */
  const regex = /^.+&/ ;
  var ID = obj.id;
  ID = ID.replace(regex, "");

  /*HTML element telling us if this image is the default image of the hobby or not */
  var defaultID = document.getElementById("default"+ID);
  if(defaultID){
    /*if image is default, display text explaining it is default image */
    if (defaultID.value == "true")  document.getElementById("defaultPrompt").style.display = "none";
    
  }
  
  /*the current HTML element helps us identify the current image in the editPost page */
  if(document.getElementById("current")){
   
    document.getElementById("current").value = ID;
    document.getElementById("fileToUpload"+ID).style.display="block";
    
  }



  
  window.onclick = function(event){
    
    /*When window registers a click, if target of click is not our modal, close options */
   if(event.target == document.getElementById("Modal") && event.target != document.getElementsByClassName("gridImageComponent") || event.target == document.getElementById("closeModal")){
      deZoomImage(defaultID);
    }
}

}

function deZoomImage(defaultID){
  
  var modal = document.getElementById("Modal");
 
  if(defaultID) document.getElementById("defaultPrompt").style.display = "block";
  var current = document.getElementById("current");
  /*checks if we have a current element, if yes, hides it */
  if(current) document.getElementById("fileToUpload" + current.value).style.display="none";
  modal.style.display = "none";
  /*remove the window onclik listener */
  window.onclick = null;
}


/*if more than one image per post, resizes it */
function resizeImages(IdOfImages, numberOfImages){
    

  /*original container for img is not fit for multiple elements */
    document.getElementById("potentialGrid"+IdOfImages).className = "gridImage zoomable";
    /*original image class is not fit for the presence of otehr images alongside it */
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
      xmlhttp.onload = function(){
        
        /*Empties textbox */
          document.getElementById("commentZone"+number).value = "";
          /*Add new comment to comments already being displayed 
           *The request will generate a php echo, this php echo will be accessible through this.responseText (meaning we display the php echo)
           */
          document.getElementById("historyComments"+number).innerHTML += this.responseText;
          
          
        
      }
      /*Sends the request via post to avoid cluttering url with useless information */
      xmlhttp.open("post", "./XMLFunctions.php", true);
      xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

      /*Parameters allows us to pass information to a "post" request
       *action: behavior the XMLFunctions.php page will take
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
function deleteComment(number){

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onload = function(){
      
        /*this.responseText can only be an error, if is null, there is no error
         * To avoid reloading all the comments, just the deleted comment is being removes via remove()
         */
        if(this.responseText != null)
        document.getElementById("comment"+number).remove();  
        else alert(this.responseText);    
      
    }

    xmlhttp.open("post", "./XMLFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
   
    var parameters = "action=deleteComment&ID="+number;
    xmlhttp.send(parameters);

}

/*checks if user has already liked a post */
function initLikes(number){

  /*disable button */
  document.getElementById("button"+number).disabled = true;

  /*get the ID of the user */
  const userId = document.getElementById("idUser");

  if(userId){

  var xmlhttp = new XMLHttpRequest();

  xmlhttp.onload = function(){
    /*if request return liked, the user has already liked the post
     * thus, the button behaviour is changed
     */
    if(this.responseText=="liked"){
      document.getElementById("button"+number).innerHTML ="Dislike this post : "+  document.getElementById("post"+number).value;
      
      document.getElementById("button"+number).onclick = function () {dislike(number);}
    }

    document.getElementById("button"+number).disabled = false;
  }

  
/* Could also parse the cookies to access user ID :
  let x = document.cookie; 

  const regexFirst = /^.+ ID=/ ;
  const regexLast =/;.+/;
  
  var ID = x.replace(regexFirst, "");
  var ID = ID.replace(regexLast, "");
*/
 
  xmlhttp.open("post", "./XMLFunctions.php", true);
  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
 
  var parameters = "action=initLike&USER_ID="+userId.value+"&POST_ID="+number;
  xmlhttp.send(parameters);
  
  }

}

/* Allow user to like a post*/
function like(number){
  document.getElementById("button"+number).disabled = true;
  const userId = document.getElementById("idUser");

  if(userId){
    var xmlhttp = new XMLHttpRequest();
    
    xmlhttp.onload = function(){
      
      
        
        /*calculate new value of number of likes */
        var add = parseInt(document.getElementById("post"+number).value) + 1;
        
        /*change displayed value of numbers of like */
        document.getElementById("post"+number).value = add;
        document.getElementById("button"+number).innerHTML ="Dislike this post : "+  document.getElementById("post"+number).value;

        /*change behavior of like button 
         * IMPORTANT : it is necessary to encapsulate the new behavior of the button in a function (){}, other wise the page will just execute the function
         */
        document.getElementById("button"+number).disabled = false;
        document.getElementById("button"+number).onclick = function () {dislike(number);}
      
    }

    xmlhttp.open("post", "./XMLFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    var parameters = "action=like&ID="+number+"&USER_ID="+userId.value;
    xmlhttp.send(parameters);
  }else{
    alert("Please log in to like a post");
    
  }
    
 

    


}

/* Allow user to dislike a post*/
function dislike(number){

    /*Disable button to prevent bug where user can like multiple times by clicking the button
     * multiple times in very short intervals
     */
    document.getElementById("button"+number).disabled = true;
    const userId = document.getElementById("idUser");

    if(userId){
      var xmlhttp = new XMLHttpRequest();
      

    

          xmlhttp.onload = function(){
          
          /*parses the number of likes of current post stored in HTML*/
          var substract = parseInt(document.getElementById("post"+number).value) - 1;
          /*Reduces the number of likes displayed */
          document.getElementById("post"+number).value = substract;

          /*Changes text an behavior of button */
          document.getElementById("button"+number).innerHTML ="Like this post : "+  document.getElementById("post"+number).value;
          document.getElementById("button"+number).disabled = false;
          document.getElementById("button"+number).onclick = function () {like(number);}
        
    }

    xmlhttp.open("post", "./XMLFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    var parameters = "action=dislike&ID="+number+"&USER_ID="+userId.value;
    xmlhttp.send(parameters);
  }

}


/*Hid comments of post of ID=number */
function closeComments(number){
    
    /*hides comment */
    document.getElementById("historyComments"+number).style.display="none";
    /*change text and behavior and text of button */
    document.getElementById("buttonComments"+number).innerHTML = "Show Comments";
    if(document.getElementById("input"+number)){
      document.getElementById("input"+number).style.display = "none";
    }
    /*changes behavior of button */
    document.getElementById("buttonComments"+number).onclick = function() {
        openComments(number);
    };

    /*Sets a timer to stop the reloading comments process after a certain time */
    document.getElementById("IdIntervalClose"+number).value = setInterval(function(){
      document.getElementById("closeCondition"+number).value = "true";
    } , 10000);
        
}
/*Function loading the comments */
function loadComments(number){
  var xmlhttp = new XMLHttpRequest();

  

  xmlhttp.onload = function(){
            
            
            
            /*Sets content of comment container */
            document.getElementById("historyComments"+number).innerHTML = this.responseText;
            
            /*If the current post's comment section has been closed for a while, stop reloading process */
            if(document.getElementById("closeCondition"+number).value=="true"){
              /*stops the reloading condition*/
              clearInterval(document.getElementById("IdInterval"+number).value);
              /*sets the running state and closing condition back to original */
              document.getElementById("running"+number).value="false";
              document.getElementById("closeCondition"+number).value = "false";

              /*If the function is called while the relaoding loop is not set: */
            }else if(document.getElementById("running"+number).value=="false"){
              /*Mark reloading loop for post n° "number" as active */
              document.getElementById("running"+number).value="true";
              
              /*Sets the reloading loop and store it id in HTML element*/
              document.getElementById("IdInterval"+number).value =   setInterval(function(){
               loadComments(number);
              }, 7000);

                
            }
        
    }

    xmlhttp.open("post", "./XMLFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    var parameters = "action=displayComments&ID="+number;
    xmlhttp.send(parameters);

}

/*Open comments for the first time, loading them from the SQL database */
function openComments(number){
 
  /*If reloading loop not setup, calls function loadCOmments */
  if(document.getElementById("running"+number).value=="false") loadComments(number);

  /*displays message */
  document.getElementById("historyComments"+number).style.display = "block";

  /*If the textarea element to write comments exists (if user is conencted) display it */
  if(document.getElementById("input"+number)) document.getElementById("input"+number).style.display = "flex";
  
  
  /*Change text and behavior of the open comment button */
  document.getElementById("buttonComments"+number).onclick = function() {closeComments(number)};
  document.getElementById("buttonComments"+number).innerHTML = "Hide Comments";
    
    /*Indicates the reloading loop still needs to be active */
    document.getElementById("closeCondition"+number).value = "false";
    /*terminates the interval aiming to terminate the reloading loop*/
    clearInterval(document.getElementById("IdIntervalClose"+number).value);
  
}

