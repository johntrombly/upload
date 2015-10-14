<!doctype html public "-//w3c//dtd html 3.2//en">

<html>

<head>
<title>(Type a title for your page here)</title>
<meta name="GENERATOR" content="Arachnophilia 4.0">
<meta name="FORMATTER" content="Arachnophilia 4.0">
</head>

<body bgcolor="#ffffff" text="#000000" link="#0000ff" vlink="#800080" alink="#ff0000">
<center><b>All varibles passed to this routine are below</b></center>
<hr width="40%" align=center><br>

<?PHP
$lookie = array();
  if (IsSet($HTTP_GET_VARS)) {
    $lookie = $HTTP_GET_VARS;
    if (count($lookie) > 0) {
      echo "<center><table  width='50%' border='1' cellspacing='0' cellpadding='4'>";
      echo "<tr><td colspan='2'><center><b>Get Vars</b></center></td></tr>";
      while ($array_cell = each($lookie)) {
         $current_key = $array_cell['key'];
         $current_value = $array_cell['value'];
         echo "<tr><td>$current_key</td><td>$lookie[$current_key]</td></tr>";
      }
      echo "</table></center>";
    }
  }

  if (IsSet($HTTP_POST_VARS)) {
    $lookie = $HTTP_POST_VARS;
    if (count($lookie) > 0) {
      echo "<center><table  width='50%' border='1' cellspacing='0' cellpadding='4'>";
      echo "<tr><td colspan='2'><center><b>Post Vars</b></center></td></tr>";
      while ($array_cell = each($lookie)) {
         $current_key = $array_cell['key'];
	 $current_value = $array_cell['value'];
         echo "<tr><td>$current_key</td><td>$lookie[$current_key]</td></tr>";
	 if ( is_array($array_cell['value'])) {
		 while ($in_array = each($lookie[$current_key])){
		 echo "<tr><td align='right'>$current_key Array($in_array[0]) -></td>";
		 echo "<td>$in_array[1]</td</tr>";

		 }

//		 echo "<tr><td>We made it $$current_value[0]</td>";
//		 echo "<td>We made it$$current_value[1]</td</tr>";
	   //}
	 }	 
      }
      echo "</table></center>";
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
    }
  } else {
    echo "<center><b>No Cookie Vars</b></center>";
  }






?>


</body>

</html>
