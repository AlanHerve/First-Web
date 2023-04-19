
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
        <input type="hidden" name="kind" id="kind" value="1">
        <input type="hidden" name="interlocutor" id="interlocutor" value="0">
        <input type="hidden" name="interlocutorName" id="interlocutorName" value="0">
      
        <button type="button" class="btn" onclick="uploadMessages()">Send</button>
        <button type="button" class="btn cancel" onclick="closeForm()">Close</button>
      </form>
  </div>
  
  <script>initInterlocutor();</script>';
  
  
}

?> 

<script>
  function destroyMessage(number){
   
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function(){
      
        if(this.responseText != null)
        document.getElementById("message"+number).remove();  
        else alert(this.responseText);    
      
    }
    xmlhttp.open("post", "messageFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
   
    var parameters = "action=destroyMessage&ID="+number;
   
   
    xmlhttp.send(parameters);
  }

  function checkInterlocutor(interlocutor){

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function(){
      
        
        if(this.responseText=="false"){
          addInterlocutor(interlocutor);
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
    xmlhttp.open("post", "messageFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    xmlhttp.send("action=check&tocheck="+interlocutor);
    
  }
  
  
function addInterlocutor(interlocutor){


    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function(){
      
        document.getElementById("people").innerHTML += this.responseText;
        var person = {name: null, value: interlocutor};
        changeInterlocutor(person);
      
    }
    xmlhttp.open("post", "messageFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    xmlhttp.send("action=add&toadd="+interlocutor);
    openForm();
  }
  
  function getInterlocutor(value){

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function(){
      
        document.getElementById("people").innerHTML = this.responseText;
      
    }
    xmlhttp.open("post", "messageFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    

    if(value != null){
      xmlhttp.send("action=fetch&interlocutor="+document.getElementById("interlocutor").value);
    }else{
       xmlhttp.send("action=fetch");
    }
    
  }

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
      
      xmlhttp.open("post", "messageFunctions.php", true);
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


    xmlhttp.open("post", "messageFunctions.php", true);
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
    xmlhttp.open("post", "messageFunctions.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    xmlhttp.send("action=display&ID="+document.getElementById("interlocutor").value);
    }
    
  
  
   
  
  }
  
  function openForm() {
    document.getElementById("myForm").style.display = "block";
    /*Returns element with id = myFrom*/
    var Pop = document.querySelector("#myForm");

    /*If clicks outside of popup or open button, close popup (have to put openbutton in the allowed element, otherwise the popup won't open)*/
    window.onclick = function(event){
        /*When window registers a click, if target of click is not our modal, close options */
       if(event.target != document.getElementById("open-button") && !Pop.contains(event.target)){
          closeForm();
        }
    }
  }
  
  function closeForm() {
    document.getElementById("myForm").style.display = "none";
  }
  
  
</script>
  






