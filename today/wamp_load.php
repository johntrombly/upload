<?php
$target_dir = $_SERVER['DOCUMENT_ROOT'];
$target_dir = 'pics/';
$target_file = $target_dir . strtolower(basename($_FILES["uploadedfile"]["name"]));
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["uploadedfile"]["tmp_name"]);

    if($check !== false) {
        //echo "File is an image - " . $check["mime"] . ".<br>";
        $uploadOk = 1;
    } else {
        echo "File is not an image.<br>";
        $uploadOk = 0;
    }
} else {
	echo "<br>No post value of submit!<br>";
}	
// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.<br>";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["uploadedfile"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded because it was disqualified!.<br>";
// if everything is ok, try to upload file
} else {

    if (move_uploaded_file($_FILES["uploadedfile"]["tmp_name"], $target_file)) {
        echo "The file " . strtolower(basename($_FILES["uploadedfile"]["name"])) . " has been uploaded.";
    } else {
        echo "<br><br>Sorry, there was an error uploading your file.<br>";
		echo 'Here is some more debugging info:<br>';
		echo '<pre>';
		echo "Other stuff $myfilename<br>";
		echo "Target_file = " . $target_file . "<br>";
		print_r($_FILES);
		echo '</pre>';
    }
}
?> 