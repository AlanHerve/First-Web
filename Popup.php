
<link rel="stylesheet" href="./Css/Popup.css">
<?php

function addButton($var){
  $button = '<button value="contact" class="buttonContact" onclick="checkInterlocutor('.$var.')">Contact</button>';
    return $button;
}

?>


<?php
function displayPopUp($interlocutor){
  
if(isset($_COOKIE["interlocutorName"])){
  echo '<button class="open-button" id="open-button" onclick="openForm()">Chat with : '.$_COOKIE["interlocutorName"].'</button>';
}else{
  echo '<button class="open-button" id="open-button" onclick="openForm()">Chat</button>';
}

echo '
  <div class="chat-popup" id="myForm">
    <h1 id="titleChat">Chat</h1>
    <div class="history" name="people" id="people">
    </div>
    
    <div class="history" name="historyMessages" id="historyMessages">
    </div> 
      <form class="form-container" id="Post">

        <label for="msg"><b>Message</b></label>
        <textarea maxlength="50" placeholder="Type message.." id="msg" name="msg" required rows="1"></textarea>
        <input type="hidden" name="closeConditionPopup" id="closeConditionPopup" value="">
        <input type="hidden" name="runningPopup" id="runningPopup" value="">
        <input type="hidden" name="closeIntervalPopup" id="closeIntervalPopup" value="">
        <input type="hidden" name="loadIntervalPopup" id="loadIntervalPopup" value="">
        <input type="hidden" name="kind" id="kind" value="1">
        <input type="hidden" name="interlocutor" id="interlocutor" value="0">
        <input type="hidden" name="interlocutorName" id="interlocutorName" value="0">
      
        <button type="button" class="btn" onclick="uploadMessages()">Send</button>
        <button type="button" class="btn cancel" onclick="closeForm()">Close</button>
      </form>
  </div>
  
  <script>initInterlocutor();
  document.getElementById("runningPopup").value = "false";
  document.getElementById("closeConditionPopup").value = "false";</script>';
  
  
}

?> 

<script>
  function deleteMessage(number){
   
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function(){
      
        if(this.responseText != null){
          /*remove a message and the time of message */
          document.getElementById("message"+number).remove();  
          document.getElementById("time"+number).remove();

          /*if responseText!=null, error is returned */
        }
        else alert(this.responseText);    
      
    }
    xmlhttp.open("post", "XMLFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
   
    var parameters = "action=deleteMessage&ID="+number;
   
   
    xmlhttp.send(parameters);
  }

  /*check if interlocutor is already in user's list of interlocutors */
  function checkInterlocutor(interlocutor){

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function(){
      
        /*if not already in, adds it */
        if(this.responseText=="false"){
          var history = document.getElementById("historyMessages");
          history.innerHTML = this.responseText;
          addInterlocutor(interlocutor);
          
        /*if already in, changes urrent interlocutor to it and open popup */
        }else if(this.responseText=="true"){
          var person = {name:null, value:interlocutor};
          changeInterlocutor(person, null);
          openForm();

         
        }else if(this.responseText=="identity"){
          alert("You can't contact yourself");
        }else{
          alert("error");
        }

      
    }
    xmlhttp.open("post", "XMLFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    xmlhttp.send("action=check&tocheck="+interlocutor);
    
  }
  
/*add interlocutor to the popup */
function addInterlocutor(interlocutor){


    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function(){
       /*change the value of people (list of interlocutors) */
        document.getElementById("people").innerHTML += this.responseText;
        var person = {name: null, value: interlocutor};
        /*change current interlocutor to make the newly added one the current one */
        changeInterlocutor(person);
      
    }
    xmlhttp.open("post", "XMLFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    xmlhttp.send("action=add&toadd="+interlocutor);
    openForm();
  }
  
  function getInterlocutor(value){

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function(){
      
        document.getElementById("people").innerHTML = this.responseText;
      
    }
    xmlhttp.open("post", "XMLFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    

    if(value != null){
      xmlhttp.send("action=fetch&interlocutor="+document.getElementById("interlocutor").value);
    }else{
       xmlhttp.send("action=fetch");
    }
    
  }

  /*if the ID of the last interlocutor a user has messaged is still present in the cookies */
  function initInterlocutor(){
   
<?php
/*InterLocutorID is the cookie containing the ID of the last user you've sent a message to 
  changeInterlocutor will make this user the current interlocutor
  getInterlocutor will refresh the list of available interlocutors
*/
    if(isset($_COOKIE["interLocutorName"]) && isset($_COOKIE["interLocutorID"])){
      echo '
      var person = {value:"'.$_COOKIE["interLocutorID"].'", name:"'.$_COOKIE["interLocutorName"].'"};
      changeInterlocutor(person);
      getInterlocutor(person);';
    }else{
      echo 'displayMessages(null);
      getInterlocutor(null);';
    } 
?>   
      
  }
  
  /*Changes the user you're currently speaking with*/
  function changeInterlocutor(obj){

    var history = document.getElementById("historyMessages");
        history.innerHTML = this.responseText;

    if(obj.name == null){
        /*If parameter does not specify the name of the user*/
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onload = function(){
        
          obj.name = this.responseText;

          /*store the ID and name of the interlocutor in the html of the page */
          document.getElementById("interlocutor").value = obj.value;
          document.getElementById("interlocutorName").value = obj.name;

          /*Change the title of the popup */
          document.getElementById("titleChat").innerHTML = "Chat with : "+obj.name;
          
          /*Display messages  */
          displayMessages(obj.value);
          /*Referesh the list of possible interlocutors */
          getInterlocutor(obj.value);
        
      }
      
      xmlhttp.open("post", "XMLFunctions.php", true);
      xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      
      /*Identify the name of the interlocutor */
      xmlhttp.send("action=identify&ID="+obj.value);

    }else{

      /*store the ID and name of the interlocutor in the html of the page */
      document.getElementById("interlocutor").value = obj.value;
      document.getElementById("interlocutorName").value = obj.name;

      /*Change the title of the popup */
      document.getElementById("titleChat").innerHTML = "Chat with : "+obj.name;
  
      /*Display messages  */
      displayMessages(obj.value);
      /*Referesh the list of possible interlocutors */
      getInterlocutor(obj.value);
    }
    
  }

 
  function uploadMessages(){
    /*if the user has clicked on the send button without typing anything, this function does nothing */
    if(document.getElementById("msg").value == ""){
      return;
    }else if(document.getElementById("interlocutor").value==0){
      alert("Please choose an interlocutor before sending anything");
      document.getElementById("msg").value = "";
      return;
    }
    

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function(){
      
        
        /*var divElement = document.createElement("div");
        divElement.setAttribute('id',);
        divElement.setAttribute('class',);
        var parElement = document.createElement("par");
        const points = document.createTextNode("");
        document.getElementById("history").appendChild(divElement);*/
        
        var history = document.getElementById("historyMessages");
        history.innerHTML += this.responseText;
        history.scrollTop = history.scrollHeight;
        
        document.getElementById("msg").value = "";
        

        /*Store the name and ID of the last person you've sent a message to in the cookies, allow user to go back to that conversation after closing the website */
        document.cookie="interLocutorID="+document.getElementById("interlocutor").value;
        document.cookie="interLocutorName="+document.getElementById("interlocutorName").value;
      
    }


    xmlhttp.open("post", "XMLFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    /*parameters are the ID of the interlocutor as well as the content of the message you're sending */
    var parameters = "action=post&ID="+document.getElementById("interlocutor").value+"&msg="+document.getElementById("msg").value
    xmlhttp.send(parameters);

  }
  
  function displayMessages(value){
  
    if(value!=null){
      var xmlhttp = new XMLHttpRequest();
      xmlhttp.onload = function(){
      
        var history = document.getElementById("historyMessages");
        history.innerHTML = this.responseText;
        
      
    }

    xmlhttp.open("post", "XMLFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    xmlhttp.send("action=display&ID="+document.getElementById("interlocutor").value);
    }
    
  
  
   
  
  }
  
  /*open popup */
  function openForm() {
    document.getElementById("myForm").style.display = "block";
    /*Returns element with id = myFrom*/
    var Pop = document.querySelector("#myForm");


    
   
    /*if the loadingMessages loop is not set up */
    if(document.getElementById("runningPopup").value == "false"){

      loadMessages();
    }

    /*If clicks outside of popup or open button, close popup (have to put openbutton in the allowed element, otherwise the popup won't open)*/
    window.onclick = function(event){
        /*When window registers a click, if target of click is not our modal, close options */
       if(event.target != document.getElementById("open-button") && !Pop.contains(event.target)){
          closeForm();
        }
    }


    /*Indicates the reloading loop still needs to be active */
    document.getElementById("closeConditionPopup").value = "false";
    /*terminates the interval aiming to terminate the reloading loop*/
    clearInterval(document.getElementById("closeIntervalPopup").value);
  }
  
  /*close popup */
  function closeForm() {
    document.getElementById("myForm").style.display = "none";

    document.getElementById("closeIntervalPopup").value = setInterval(function(){
      document.getElementById("closeConditionPopup").value = "true";
    } , 10000);
  }

  function loadMessages(){
    /*Sets content of comment container */
          
            /*If the popupn has been closed for a while, stop reloading process */
            if(document.getElementById("closeConditionPopup").value=="true"){
              
              /*stops the reloading condition*/
              clearInterval(document.getElementById("loadIntervalPopup").value);
              /*sets the running state and closing condition back to original */
              document.getElementById("runningPopup").value="false";
              document.getElementById("closeConditionPopup").value = "false";

              /*If the function is called while the relaoding loop is not set: */
            }else if(document.getElementById("runningPopup").value=="false"){

           
              /*Mark reloading loop for post n° "number" as active */
              document.getElementById("runningPopup").value="true";
              displayMessages(document.getElementById("interlocutor"));
              /*Sets the reloading loop and store it id in HTML element*/
              document.getElementById("loadIntervalPopup").value =   setInterval(function(){
               loadMessages();
              }, 7000);

                
            }else{
              /*if the popup is open, and the loop is running, display messages */
              displayMessages(document.getElementById("interlocutor"));
            }
  }



  
  
  
</script>
  






