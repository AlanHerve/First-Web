<?php
/*checks if file submitted is of valid format and upload it 
 * if function succeeds or if file already exists, return the name of the file
 */
function checkFile($file_name){

    /*directory where the image will be uploaded */
    $target_dir = "uploads/";
    /*builds path the file will take during its upload */
    $target_file = $target_dir . basename($_FILES[$file_name]["name"]);
    $uploadOk = 1;
    $already = 0;

    /*fetches type of image */
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    


    $return_value = NULL;



    // Check if image file is a actual image or fake image
    if(isset($_POST["newPost"]) && $_FILES[$file_name]["tmp_name"]!=NULL) {
      $check = getimagesize($_FILES[$file_name]["tmp_name"]);
      if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
      } else {
        echo "File is not an image. / IS EMPTY";
        $uploadOk = 0;
      }
    
    
      // Check if file already exists
      if (file_exists($target_file)) {
        echo "<p>Sorry, file already exists.</p>";
        $already = 1;
      }
    
      // Check file size
      if ($_FILES[$file_name]["size"] > 500000) {
        echo "<p>Sorry, your file is too large.</p";
        $uploadOk = 0;
      }
    
      // Allow certain file formats
      if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" && $imageFileType != "PNG") {
        echo "<p>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</p>";
        $uploadOk = 0;
      }
    
      // Check if $uploadOk is set to 0 by an error
      if ($uploadOk == 0) {
        echo "<p>Sorry, your file was not uploaded.</p>";
        // if everything is ok, try to upload file
      } else {
        if (move_uploaded_file($_FILES[$file_name]["tmp_name"], $target_file) && $already==0) {
          echo "<p>The file ". htmlspecialchars( basename( $_FILES[$file_name]["name"])). " has been uploaded.</p>";
          /*return the name of the file */
          $return_value = htmlspecialchars( basename( $_FILES[$file_name]["name"]));
        }elseif($already == 1){
          echo 'already';
          
          $return_value = $_FILES[$file_name]["tmp_name"];
        } else {
          echo "<p>Sorry, there was an error uploading your file.</p>";
        
        }
      } 

    }
  
    return $return_value;
    
}

/*Get default picture of a hobby
$mode : gets hobby ID either from type of post or from the url
 */
function getDefault($type, $mode){

  global $conn;

  $error = NULL;

  $image = NULL;

  /*if mode!=1 we are in the page Tag.php */
  if($mode==1){
    $query = "SELECT IMAGE FROM hobby_list WHERE ID=".$type;
  }else{
    $query = "SELECT IMAGE FROM hobby_list WHERE ID=".$_GET["TAG"];
  }
  
  $result = $conn->query($query);

  if($result){
    $value = $result->fetch_assoc();
    $image = $value["IMAGE"];

    /*If hobby has no default picture, or if the default picture mentionned in the SQL database does not exists, display Fille Picture */
    if($image == NULL || !file_exists("./Images/".$image)){
      $image = "Filler3.png";
    }
  }else{
    $error = "COULD NOT LOAD DATABASE"; 
  }



  return array($error, $image);

}

?>