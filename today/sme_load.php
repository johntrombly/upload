<?php
//		echo '<pre>';
		print_r($_FILES);
//		echo '</pre>';

function resize_image_max($image,$max_width,$max_height) {
	$w = imagesx($image); //current width
	$h = imagesy($image); //current height
	if ((!$w) || (!$h)) { $GLOBALS['errors'][] = 'Image couldn\'t be resized because it wasn\'t a valid image.'; return false; }

	if (($w <= $max_width) && ($h <= $max_height)) { return $image; } //no resizing needed

	//try max width first...
	$ratio = $max_width / $w;
	$new_w = $max_width;
	$new_h = $h * $ratio;

	//if that didn't work
	if ($new_h > $max_height) {
		$ratio = $max_height / $h;
		$new_h = $max_height;
		$new_w = $w * $ratio;
	}

	$new_image = imagecreatetruecolor ($new_w, $new_h);
	imagecopyresampled($new_image,$image, 0, 0, 0, 0, $new_w, $new_h, $w, $h);
	return $new_image;
}


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
echo "The name of the file is " . $_FILES["uploadedfile"]["tmp_name"] . "<br>";	
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
//if ($_FILES["uploadedfile"]["size"] > 500000) {
if ($_FILES["uploadedfile"]["size"] > 100000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
	die();
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
		
$uploadedfile = $target_file; 
$src = imagecreatefromjpeg($uploadedfile);        
list($width, $height) = getimagesize($uploadedfile); 

echo "<br>Current width = $width<br>Current height = $height<br>";
$newWidth = 190;
$newHeight = ($height / $width) * $newWidth;
if ($newHeight > 144) {
	$newHeight = 143;
	$newWidth = ($width / $height) * $newHeight;
}	

//$tmp = imagecreatetruecolor(800, 600); 
$tmp = imagecreatetruecolor($newWidth, $newHeight); 

$filename = 'C:/wamp/www/upload/today/pics/' . $_FILES['uploadedfile']['name'];

//imagecopyresampled($tmp, $src, 0, 0, 0, 0, 800, 600, $width, $height); 
imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height); 
imagejpeg($tmp, $filename, 100);
echo "<br>New width = $newWidth<br>new height = $newHeight<br>";
		
		
//		echo "<br>Target file is $target_file<br>";
/*
		$myfile = fopen('C:\wamp\www\upload\today\pics\100goofie.jpg','r');
		if ($myfile) {
			if (resize_image_max($myfile,190,43)) {
				echo "<br>Error Resizing File<br>";
			} else {
				echo "<br>File should now be 190 x 42<br>";
			}
		} else {
		}
*/		
//		if (resize_image_max($target_file,190,43)) {
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