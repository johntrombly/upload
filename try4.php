<?php
$file = 'c:\temp\dog.png';

# assuming you've already taken some other
# preventive measures such as checking file
# extensions...

$result_array = getimagesize($file);

if ($result_array !== false) {
    $mime_type = $result_array['mime'];
    switch($mime_type) {
        case "image/jpeg":
            echo "file is jpeg type";
            break;
        case "image/gif":
            echo "file is gif type";
            break;
        case "image/png":
            echo "file is png type";
            break;
        default:
            echo "file is an image, but not of gif, png  or jpeg type<br>";
    }
} else {
    echo "file is not a valid image file";
}
?> 