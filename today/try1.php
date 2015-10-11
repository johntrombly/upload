<?php
//properties of the uploaded file
$name= $_FILES["myfile"]["name"];
$type= $_FILES["myfile"]["type"];
$size= $_FILES["myfile"]["size"];
$temp= $_FILES["myfile"]["temp_name"];
$error= $_FILES["myfile"]["error"];

if ($error > 0)
    die("Error uploading file! code $error.");
else
   {
    if($type=="image/png" || $size > 2000000)//condition for the file
    {
    die("Format  not allowed or file size too big!");
    }
    else
    {
     move_uploaded_file($temp, "uploaded/" .$name);
     echo "Upload complete!";
     }
}

?>