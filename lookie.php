<!doctype html public "-//w3c//dtd html 3.2//en">

<html>

<head>
<title>(Return Vars from page)</title>
<meta name="GENERATOR" content="Arachnophilia 4.0">
<meta name="FORMATTER" content="Arachnophilia 4.0">
</head>

<body bgcolor="#ffffff" text="#000000" link="#0000ff" vlink="#800080" alink="#ff0000">
<?php
function multi_post_item($repeatedString) {
   // Gets the specified array of multiple selects and/or 
   // checkboxes from the Query String
   $ArrayOfItems = array();
   $raw_input_items = preg_split("/&/", $_SERVER["QUERY_STRING"]);
//   $raw_input_items = split("&", $_SERVER["argv"]);
   foreach ($raw_input_items as $input_item) {
       $itemPair = preg_split("/=/", $input_item);
       if ($itemPair[0] == $repeatedString) {
           $ArrayOfItems[] = $itemPair[1];
       }
   }
   return $ArrayOfItems;
} 


$QUERY_STRING = $_SERVER["QUERY_STRING"]; 
//echo "<center>Query string is</center>\n";
echo "<hr><center>Here is the string $QUERY_STRING<hr></center>\n";
/*
$q11 = array();
$q11 = multi_post_item("Q11");
foreach ($Q11 as $v) {
	echo "Current value of \$q11: $v<br>\n";
}
*/	
//$mystr = $QUERY_STRING;
//$tok = strtok($mystr, "=");
//while ($tok) {
//  echo "<center>Q11 = $tok</center>";
//  $tok = strtok("=");
//}	
?>
<center><b>All varibles passed to this routine are below</b></center>
<hr width="40%" align=center><br>

<?PHP
$lookie = array();

// $HTTP_GET_VARS
  if (IsSet($_GET)) {
//			$lookie = $HTTP_GET_VARS;
			$lookie = $GLOBALS['HTTP_GET_VARS'];
    if (count($lookie) > 0) {
      echo "<center><table  width='50%' border='1' cellspacing='0' cellpadding='4'>";
      echo "<tr><td colspan='2'><center><b>Get Vars</b></center></td></tr>";
      while ($array_cell = each($lookie)) {
         $current_key = $array_cell['key'];
         $current_value = $array_cell['value'];
         echo "<tr><td>$current_key</td><td>$lookie[$current_key]</td></tr>";
      }
      echo "</table></center>";
			} else {
				echo "No Get Vars<br>";	
    }
  }
// $
  if (IsSet($HTTP_POST_VARS)) {
    $lookie = $HTTP_POST_VARS;
    if (count($lookie) > 0) {
      echo "<center><table  width='50%' border='1' cellspacing='0' cellpadding='4'>";
      echo "<tr><td colspan='2'><center><b>Post Vars</b></center></td></tr>";
      while ($array_cell = each($lookie)) {
         $current_key = $array_cell['key'];
         $current_value = $array_cell['value'];
         echo "<tr><td>$current_key</td><td>$lookie[$current_key]</td></tr>";
      }
      echo "</table></center>";
    } else {
				echo "No Posted Vars<br>";
			}
  } else {
    echo "<center><b>No Posted Vars</b></center>";
  }

  if (IsSet($HTTP_COOKIE_VARS)) {
    $lookie = $HTTP_COOKIE_VARS;
    if (count($lookie) > 0) {
      echo "<center><table  width='50%' border='1' cellspacing='0' cellpadding='4'>";
      echo "<tr><td colspan='2'><center><b>Cookie Vars</b></center></td></tr>";
      while ($array_cell = each($lookie)) {
         $current_key = $array_cell['key'];
         $current_value = $array_cell['value'];
         echo "<tr><td>$current_key</td><td>$lookie[$current_key]</td></tr>";
      }
      echo "</table></center>";
    } else {
				echo "No Cookie Vars<br>";
			}
  } else {
    echo "<center><b>No Cookie Vars</b></center>";
  }

// $_SERVER, $_ENV
  if (IsSet($_SERVER)) {
    $lookie = $_SERVER;
    if (count($lookie) > 0) {
      echo "<center><table  width='50%' border='1' cellspacing='0' cellpadding='4'>";
      echo "<tr><td colspan='2'><center><b>Envroment Vars</b></center></td></tr>";
      while ($array_cell = each($lookie)) {
         $current_key = $array_cell['key'];
         $current_value = $array_cell['value'];
         echo "<tr><td>$current_key</td><td>$lookie[$current_key]</td></tr>";
      }
      echo "</table></center>";
    } else {
				echo "No Envroment Vars<br>";
			}
  } else {
    echo "<center><b>No Server Vars</b></center>";
  }





?>


</body>

</html>
