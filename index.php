<?php
	{ // local vs production settings
		$num_col = 3;  // Number of items per row
		$sandbox = "no";
		$sandbox = "yes";
//		$sandbox = "dad";
		// settings for production
		$server = "localhost";
		$sql_user = "ccaac";
		$pass_wd = "caplin";
		$dbf = "ccaac";
		$table1 = "inventory";
		//$dblink = new mysqli($server,$user_id,$pass_wd,$dbf);
		// Beware of line 78 in function berrer_odbc_num_rows
		//	while($temp = odbc_fetch_into($result, &$counter)){
		date_default_timezone_set("America/Chicago");
		if ($sandbox == "yes") { // settings for Sandbox
			$server = "localhost";
			$sql_user = "root";
			$pass_wd = "";
			$dbf = "ccaac";
			$table1 = "inventory";
			$title = "John Inventory";
			$dbf_prefix = "cap";
			$copyrite = "VTN";
		}
		if ($sandbox == "dad") { // settings for Sandbox
			$server = "localhost";
			$sql_user = "reneeteague";
			$pass_wd = "TromblyTeague1100";
			$dbf = "ccaac";
			$table1 = "my_inventory";
			$title = "Renees Check List";
			$dbf_prefix = "cap";
			$copyrite = "VTN";
		}
		if ($sandbox == "no") { // settings for CCAAC
			$server = "localhost";
			$sql_user = "ccaac";
			$pass_wd = "caplin";
			$dbf = "ccaac";
			$table1 = "inventory";
			$title = "Dr. Caplin Inventory";
			$dbf_prefix = "cap";
			$copyrite = "CCAAC";
		}
		
	} // END Local vs production settings
	
	{ // Session Stuff
	session_start();
	$session_lifetime = 1800; // 30 min or 1800 sec
//	$session_lifetime = 10; // 30 min or 1800 sec
	if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_lifetime)) {
		// last request was more than 30 minutes ago
		session_unset();     // unset $_SESSION variable for the run-time 
		session_destroy();   // destroy session data in storage
	}
	$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
	if (!isset($_SESSION['CREATED'])) {
		$_SESSION['CREATED'] = time();
	} else if (time() - $_SESSION['CREATED'] > $session_lifetime) {
		// session started more than 30 minutes ago
		session_regenerate_id(true);    // change session ID for the current session an invalidate old session ID
		$_SESSION['CREATED'] = time();  // update creation time
	}
	foreach ($_SESSION as $key=>$value)     {
		${$key} = $value;	
	}
	$dblink = new mysqli($server,$sql_user,$pass_wd,$dbf);
	if ($dblink->connect_errno)  {
		echo "Failed to connect to MySQL: " . $dblink->connect_error;
	}
	} // End Session Stuff

	{ //System Vars
		// Reveal all get and post variables
		// Should be replaced later by calls to the $_GET or $_POST arrays
		foreach($_GET as $variable => $value) {
			$$variable = $value; // copy all get vars into globals
		}  
		foreach($_POST as $variable => $value) {
			$$variable = $value; // copy all post vars into globals
		}
//		foreach($_SESSION as $variable => $value) {
//			$$variable = $value; // copy all Session vars into globals
//		}
		//Trun off session error that is triggered when
		//you have a local variable with the same name as a session Var
			ini_set('session.bug_compat_warn', 0);
			ini_set('session.bug_compat_42', 0);
		//Trun off session error reporting
		$PHP_SELF = $_SERVER['PHP_SELF'];
		$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
		// end of reveal all get and post variables
		$css_include_file = "/css/jbt12.css";
		$cgi = $PHP_SELF;
		$d_quote = chr(34);
		$s_quote = "'";
		$lf = chr(10);
		$cr = chr(13);
		$tab = chr(9);
		$comma = ",";
		$mydbf = $table1;
		$dblink->select_db("ccaac") or die("Error contacting ccaac DBF " . $dblink->error);

		{ // Set defaults for date, CPT, Patient id for the selection grids
			$mynow = getdate(time());
			$mydate = $mynow["mon"] . "/" . $mynow["mday"] . "/" .$mynow["year"];
//			echo "<p>Date = $mydate</p>";
			$newdate = new DateTime($mydate);
			$interval = new DateInterval('P1M');
			$newdate->sub($interval);
			$past_date = $newdate->format('m/d/Y');
		}


	} //End of System Vars
	
	{ //- Start of Functions
	function better_odbc_num_rows($con,$sql){
 //echo "<p>$sql</p>";	
	   $result = odbc_exec($con,$sql);
	   $count=0;
		while($temp = odbc_fetch_into($result, $dummy)){
		   $count++;
		}
	   return $count;
	} // end of better_odbc_num_rows

	function DosDate($date) 	{
		// takes a date string in YYYY-MM-DD format
		// and returns it in MM/DD/YYYY format (for Regular People)
		if (preg_match ("/([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})/", $date, $regs)) {
		  $retval = "$regs[2]/$regs[3]/$regs[1]";
		}
		return $retval;
	} //end of LinuxDate function
	
	function FixMyString($mystring) {
		// *** ATTENTION ***
		// Special Modified version allows single quotes to pass!
		// Be sure that when this text is sent, double quites surround it in the SQL statement!
		// *** ATTENTION ***
		// This will remove the dangerous characters quotes and question mark
		$temp = $mystring;
		$temp = str_replace("'", "",  $mystring); // byby single quote
		$temp = str_replace('"', "", $temp); // byby double quotes
		$temp = str_replace("?", "", $temp); // byby question mark
		$temp = str_replace("$", "", $temp); // byby Dollar sign (just for good measure
		$temp = str_replace("&", "", $temp); // byby AND sign (makes html crankey
		$temp = str_replace(",", "", $temp); // byby comma sign (makes html crankey as well
		// no HTML tags should pass so no < or >
		$temp = str_replace("<", "", $temp); // byby start of HTML Tags
		$temp = str_replace(">", "", $temp); // byby end of HTML Tags
		$mylen_now = strlen($temp);
		$mylen_prior = $mylen_now + 1;
		while ($mylen_now <> $mylen_prior) {
		  $mylen_prior = strlen($temp);
		  $temp = str_replace("  ", " ", $temp); // Get rid of ** ALL ** the white space!!
		  $mylen_now = strlen($temp);
		}
		$bkslach = chr(92);
		$temp = str_replace($bkslach, "", $temp); // byby Backslash because this is used to signal special characters)
		return $temp;
	} // end of FixMyString

	function form_button($form1) {
		$start = "<table class='menu'>
			<tr><td class='blank'><div id='CssMenu'>
			<ul><li class='firstmain'>\n";
		$middle = "</li></ul></div></td>
			<td class='blank'><div id='CssMenu'>
			<ul><li class='firstmain'>";
		$end = "</li></ul></div></td>
			</tr></table>\n";
			
		$start = "";
		$middle = "";
		$end = "";

		$match_str = "type='submit'";
		$repl_str = "type='submit' class='sbutton'";
		$form1 = str_replace($match_str, $repl_str, $form1);
		echo "$start$form1$end";
	}

	function getname($target_dir) {
		$path_parts = pathinfo($_SERVER['SCRIPT_FILENAME']);
		$mydir = $path_parts['dirname'];
		$mydir .= "/" . $target_dir;
		return $mydir . strtolower(basename($_FILES["uploadedfile"]["name"]));
	}

	function LinuxDate($date) 	{
		// takes a date string in MM/DD/YYYY format
		// and returns it in YYYY-MM-DD format (for Mysql)
		if (preg_match ("#([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})#", $date, $regs)) {
		  $retval = "$regs[3]-$regs[1]-$regs[2]";
		}
		return $retval;
	} //end of LinuxDate function

	function MeColorTR($count, $numCols) {
			// Puts in the color tag for a row (TR>
			// numCols is the # of Colums desired
			$setColor = $count % $numCols;
			if ($setColor == 0 ) {
				// Here is where we plan the color for the new Row
				$setColor = intval($count / $numCols);
				$setColor = $setColor%3;
				  switch ($setColor) {
					case "0":
					  echo "\n<tr bgcolor=#DDDDDD>";
					  break;
					case "1":
					  echo "\n<tr bgcolor=#CCCCCC>";
					  break;
					case "2": 	
					  echo "\n<tr bgcolor=#C0C0C0>";
					  break;
					case "3":
					  echo "\n<tr>";
					  break;
				  } // end switch
			}
			  // increment the counter
			  $retval = $count + 1;
			  return $retval;
		} // end of MeColorTR

	function mybanner($string, $dblink, $css_include_file, $title) 	{

		// default banner for this program
		$bk = "background='/backgrounds/pa-sw-602.jpg'";
		//  public_html
		//  pa-sw-602.jpg
		$myheadder = "== $title ==<br />";
		$mydate = getdate(time());
		echo '<!DOCTYPE HTML>' . "\n";
		echo "<html>\n";
		echo "<head>\n";
		echo "<link rel='stylesheet' type='text/css' href='$css_include_file'>\n";
		echo "<link rel='stylesheet' type='text/css' href='/css/1.8.16.css' media='all'>\n";
		echo "<link rel='shortcut icon' href='https://www.vtracnow.com/favicon.ico?v=2' />\n";
		echo "<script type='text/javascript' src='/css/1.7.1.js'></script>\n";
		echo "<script type='text/javascript' src='/css/1.8.16.js'></script>\n";
		
		echo "<script>\n";
		echo "$(function() {\n";
		echo "    $('#datepick').datepicker();\n";
		echo "});\n";
		echo "$(function() {\n";
		echo "    $('#datepick2').datepicker();\n";
		echo "});\n";
		echo "$(function() {\n";
		echo "    $('#datepick3').datepicker();\n";
		echo "});\n";
		echo "$(function() {\n";
		echo "    $('#datepick4').datepicker();\n";
		echo "});\n";
		echo "</script>\n";
		echo "</head>\n";
		echo "<body>";

	//	echo "<script>\n";
	//	echo "history.forward();\n";
	//	echo "</script>\n";
		$user_name = $_SESSION['user_name'];
		$usr_access = $_SESSION['usr_access'];
		$string1 = "<p class='trademark'>$myheadder Administration</p>\n";
		$string2 ="<p class='h3'>User: $user_name<br>Access Level: $usr_access<br>$string<br>";

		echo "$string1$string2 ===================<br>" . $mydate["month"] . " " . $mydate["mday"] . ", " .$mydate["year"] . "</p>\n";
		// No Back Button!!!!!
	} // end of mybanner

	function NavBar($cgi, $act, $usr_access, $copyrite) 	{
		// Displays links for navigation in this program
		$d_quote = chr(34);
		echo "<br>";
		echo "<table class='menu'>\n";
		//	if ($usr_access > 50) {
		//		echo "<tr><td colspan='5'>== Main Navigation Controls $usr_access ==</td></tr>\n";
		//	} else {
		//		echo "<tr><td colspan='4'>== Main Navigation Controls $usr_access ==</td></tr>\n";
		//	}
		$start = "<div id='CssMenu'>\n<ul><li class='firstmain'>";
		$end = "</li></ul></div>";
		switch ($act) {
			case "login": {
				echo"<tr>\n";
				echo "<td class='blank'>$start" . "Please Login" . "$end</td>\n";
				echo "</tr>\n";
			}
			break;
			case "check_login": {
				echo"<tr>\n";
				echo "<td class='blank'>$start<a href='$cgi' >Continue</a>$end</td>\n";
				echo "</tr>\n";
			}
			break;
			
			
			case "logout": {
				echo"<tr>\n";
				echo "<td class='blank'>$start<a href='$cgi' >Login?</a>$end</td>\n";
				echo "</tr>\n";
			}
			break;
			 default: {
				echo"<tr>\n";
//				if (($_SESSION['my_cat_id'] == '') || (!isset($_SESSION['my_cat_id']))) {
				if (!isset($_SESSION['my_cat_id'])) {
				} else {
					echo "<td class='blank'>$start<a href='$cgi?act=list'>List</a>$end</td>\n";
					echo "<td class='blank'>$start<a href='$cgi?act=new' >Create New</a>$end</td>\n";
				}
				echo "<td class='blank'>$start<a href='$cgi?act=logout' >Log Off</a>$end</td>\n";
				echo "<td class='blank'>$start<a href='$cgi?act=make_cat' >Category</a>$end</td>\n";
				echo "</tr>\n";
				echo "</table>";
				
			 }
		} // end act switch
		echo "</table>";
		echo "<p class='h3smallred'> &#169; 2015 $copyrite </p>";
	} // end of NavBar

	function shortbanner($string, $dblink, $css_include_file, $title) 	{
		$myheadder = "== $title ==<br />";
		$mydate = getdate(time());
		echo '<!DOCTYPE HTML>' . "\n";
		echo "<html>\n";
		echo "<head>\n";
		echo "<link rel='stylesheet' type='text/css' href='$css_include_file'>\n";
		echo "<link rel='stylesheet' type='text/css' href='/css/1.8.16.css' media='all'>\n";
		echo "<link rel='shortcut icon' href='https://www.vtracnow.com/favicon.ico?v=2' />\n";
		echo "<script type='text/javascript' src='/css/1.7.1.js'></script>\n";
		echo "<script type='text/javascript' src='/css/1.8.16.js'></script>\n";
		
		echo "<script>\n";
		echo "$(function() {\n";
		echo "    $('#datepick').datepicker();\n";
		echo "});\n";
		echo "$(function() {\n";
		echo "    $('#datepick2').datepicker();\n";
		echo "});\n";
		echo "</script>\n";
		echo "</head>\n";
		echo "<body>";

	//	echo "<script>\n";
	//	echo "history.forward();\n";
	//	echo "</script>\n";
		
		$string1 = "<p class='trademark'>$myheadder Administration</p>\n";
		$string2 ="<p class='h3'>$string<br>";
		$string2 = "";	

		echo "$string1$string2 ===================<br>" . $mydate["month"] . " " . $mydate["mday"] . ", " .$mydate["year"] . "</p>\n";
		// No Back Button!!!!!
		echo "</html>";
		
	} // end of shortbanner

	function ValadateInput($txt, $type, $comment) 	{
		// send text and a type and get back an error comment
		// if the error comment is empty, then it is ok
		$retval = "";
		switch ($type) {
			case "char"; {
				$retval = "";
				if (strlen($txt) < 1) {
					$retval = "$comment '$txt' is too short<BR>\nMust be at least 1 characters long<br>\n";
				} 
				break;
			}
			
			case "short"; {
				$retval = "";
				if (strlen($txt) < 1) {
					$retval = "$comment '$txt' is too short<BR>\nMust be at least 1 characters long<br>\n";
				} 
				break;
			}

			case "sname"; {
				$retval = "";
				if (strlen($txt) < 2) {
					$retval = "$comment '$txt' is too short<BR>\nMust be at least 2 characters long<br>\n";
				} 
				break;
			}
			
			case "name"; {
				$retval = "";
				if (strlen($txt) < 3) {
					$retval = "$comment '$txt' is too short<BR>\nMust be at least 3 characters long<br>\n";
				} 
				break;
			}
			
			case "date": {
				$err_date = "Invalid date format for $comment: <BR>Cannot determine '$txt' as being a valid date<BR>";
				$retval = "";
				if (preg_match ("#([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})#", $txt, $regs)) {
					$mymonth = $regs[1];
					$mydate = $regs[2];
					$myyear = $regs[3];
					settype($mymonth, "integer");
					settype($mydate, "integer");
					settype($myyear, "integer");
					if ($myyear < 1 or $myyear > 2071) {
						$retval .= "<p class='h3red'>$err_date The year is in question<br></p>";
					} else {
						if (($mymonth < 1) or ($mymonth > 12)) $retval .= "<p class='h3red'>$err_date Month should be between 1 and 12</p>";
						if ($mydate < 1 or $mydate > 31) {
							$retval .= "<p class='h3red'>$err_date No month has more than 31 days or less than 1 day</p>";
						} else {
							if ($mymonth == 4 or $mymonth == 6 or $mymonth == 9 or $mymonth == 11) {
								if ($mydate == 31) $retval .= "<p class='h3red'>$err_date Only 30 days in this month (month #$mymonth)</p>";
							} 
							if ($mymonth == 2) {
								if ($mydate > 29) $retval .= "<p class='h3red'>$err_date There can never be more than 29 days in ANY Febuary</p>";
								if ($mydate == 29 and $myyear % 4 > 0) $retval .= "<p class='h3red'>$err_date Only 28 days in THIS Febuary</p>";
							}
						} 
					} 
				} else {
					$retval .= "<p class='h3red'>$err_date Try using the MM/DD/YYYY format</p>";
				} 
				break;
			}
			
			case "email": {
				$at_loc = strpos($txt, "@");
				$email_exp = "^[a-z0-9\._-]+@[a-z0-9\._-]+\.+[a-z]{2,3}$";
					if ($at_loc < 2) {
				$retval .= "Please check the prefix and @ sign<br>\n";
				} 
				$firstdot = strpos($txt, ".");
				$afterdot = substr($txt, $firstdot + 1);
				if ($firstdot < 1) {
					$retval .= "Please check the suffix for accuracy.<br>\n";
					$retval .= "(It should include a .com,.net,.org,.gov,.mil, or other)<br>\n";
					// 3/26/2003 jbt
					// Found email address with a dot prior to the @ ie john.trombly@goofy.org
					// making sure that there is a . after the @ and not allowing more than 2 dots prior to the @
				} else {
					if ($at_loc > $firstdot + strpos($afterdot, ".")) {
					$retval .= "Max of one dot prior to the @ sign<br>and<br>must have a dot after the @ sign<br>\n";
					} 
				} 
				if (!preg_match("#" . $email_exp . "#i", $txt)) {
					$retval .= "Check Email address again for valid form";
				}
				if (strlen($retval) > 0) {
					$retval = "<p class='h3red'>Sorry. This $comment '$txt' seems wrong.<br>\n" . $retval . "</p>\n";
				} 
				break;
			}
			
			case "num": {
				if (strlen($txt) < 1) {
					$retval = "$comment is too short<BR>\nPut $comment in number format ie 999<br>\n";
				} else {
					if (!preg_match("/(^[0-9]{1,6})$/", $txt)) {
						$retval = "$comment must be in this format<BR>9999<br>\n";
					} 
				} 
				if (strlen($retval) > 0) {
					$retval = "<p class='h3red'>Error in $comment ($txt).<br>\n" . $retval . "</p>\n";
				} 
				break;
			}
			
			case "no_zero_num": {
				if (strlen($txt) < 1) {
					$retval = "$comment is too short<BR>\nPut $comment in number format ie 999<br>\n";
				} else {
					if (!preg_match("/(^[0-9]{1,6})$/", $txt)) {
						$retval = "$comment must be in this format<BR>9999<br>\n";
					} else {
						if (($txt * 1) == 0) {
							$retval = "$comment can not be ZERO<br>\n";
						}
					}
				} 
				if (strlen($retval) > 0) {
					$retval = "<p class='h3red'>Error in $comment ($txt).<br>\n" . $retval . "</p>\n";
				} 
				break;
			}

			case "num62": {
				if (strlen($txt) < 1) {
					$retval = "$comment is too short<BR>\nPut $comment in number format ie 999.99<br>\n";
				} else {
					if (!preg_match("/(^[0-9]{1,3}.[0-9]{1,2})$/", $txt)) {
					$retval = "$comment must be in this format<BR>999.99<br>\n";
					} 
				} 
				if (strlen($retval) > 0) {
					$retval = "<p class='h3red'>Error in $comment ($txt).<br>\n" . $retval . "</p>\n";
				} 
				break;
			}
			
			case "phone": {
				if (strlen($txt) < 12) {
					$retval = "$comment is too short<BR>\nPut phone number in 999-999-9999<br>\n";
				} else {
					if (!preg_match("/([0-9]{3,3})-([0-9]{3,3})-([0-9]{3,3})/", $txt)) {
					$retval = "Phone number must be in this format<BR>999-999-9999<br>\n";
					} 
				} 
				if (strlen($retval) > 0) {
					$retval = "<p class='h3red'>Error in $comment ($txt).<br>\n" . $retval . "</p>\n";
				} 
				break;
			}
			
			case "time": {
				if (strlen($txt) != 5) {
					$retval = "<br>$comment is not 5 characters long<br>Put time string like 99:99";
				} else {
				if (!preg_match("/([0-2]{1,1})([0-9]{1,1}):([0-5]{1,1})([0-9]{1,1})/", $txt)) {
					$retval = "<br><b>$comment</b> is time and must be in this format<BR>hh:mm<br>hh must be less than 30<br>mm must be less than 60<br>\n";                                                                                                                }
				}
				break;
			}
			
			case "YN": {
				if (strlen($txt) != 1) {
					$retval = "<br>$comment is not 1 characters long<br>Only put Y or N";
				} else {
					if ($txt != 'Y' and $txt != 'N') {
					$retval = "<br>$comment can only be <b>Y</b> or <b>N</b><br>\n";
					}
				}
				break;
			}	

			case "hml": {
				if (strlen($txt) != 1) {
					$retval = "<br>$comment is not 1 characters long<br>Only put H, M, or L";
				} else {
					if ($txt != '1' and $txt != '2' and $txt != '3') {
						$retval = "<br>$comment can only be <b>1</b> or <b>2</b> or <b>3</b><br>\n";
					}
				}
				break;
			}
				
			default: {
				$retval = "<p class='h3red'>Error<BR>Unable to data<BR>Data Type unknown</p>\n";
				break;
			}

		}  // end switch
		return $retval;
	} // end ValadateInput

	}
	// ---------------- End of Functions

	// ---------------- Start of Code
	{ // Session Redirect
		if (!isset($_SESSION['user_id'])) {
			echo "<p>You have not logged in</p>";
			if (!isset($act)) {
				$act = "login";
			} else {
				// set act to login unless it is set to login or check_login
				if ($act == "login" or $act == "check_login") {
					// Do nothing
				} else {
					$act = "login";
					// Need to get session vars set (ie user_id)
				}
			}
		} else {
			if (!isset($act)) {
				$act = "menu";
			}
			// Here is where we check to see if the password/access has excluded person from executing!
			$qpass_ok = "select id
				from $dbf.s_usr 
				where name = $d_quote$user_name$d_quote and 
					pass = $d_quote$password$d_quote  and
					id = $d_quote$user_id$d_quote";
			$rpass_ok = $dblink->query($qpass_ok) or die("Error line 234" . $dblink->error);
			$found = $rpass_ok->num_rows;
			if ($found > 0) { // user/pass is ok
				$qaccess_ok = "select usr_access 
					from $dbf.s_usr_prog_x 
					where s_usr_id = $d_quote$user_id$d_quote and 
						s_prog_id = $d_quote$prog_id$d_quote";
				$raccess_ok = $dblink->query($qaccess_ok) or die("Error line 241" . $dblink->error);
				$found = $raccess_ok->num_rows;
				if ($found > 0) { // we have a match, just set access (in case it changed)
					//$act = "menu";
					$row = $raccess_ok->fetch_array(MYSQLI_ASSOC);
					$new_usr_access = $row['usr_access'];
					// now make sure that the session knows about it.
					if ($new_usr_access <> $usr_access) {
						$_SESSION['usr_access'] = $new_usr_access;
						$usr_access = $new_usr_access;
					}
$dblink->select_db($mydbf) or die("Error contacting inventory DBF " . $dblink->error);
					
				} else { // not allowed!
					$act = "login";
				}
			} else { // need to login cause usr/pass no longer valid!
				$act = "login";
			}
		}
	} // End of Session Redirect

		
		switch ($act) {

			case "activity": {
				mybanner("Invntory Activity", $dblink, $css_include_file, $title);
				if (($_SESSION['my_cat_id'] == '') || (!isset($_SESSION['my_cat_id']))) {
					echo "<p>No Catigory Set!</p>";
				} else {
					$q = "select * from $dbf_prefix" . "_item where id = $d_quote$my_id$d_quote and cat_id = $d_quote$my_cat_id$d_quote";
					$r = $dblink->query($q) or die ("Error Getting info from $dbf_prefix" . "_item in act in " . $dblink->error);
					if ($r->num_rows > 0) {
						$row = $r->fetch_array(MYSQLI_ASSOC);
						$s_desc = $row["s_desc"];
						$l_desc = $row["l_desc"];
						$on_hand = $row["my_count"];
						echo "<p>$s_desc</p>";
					}
					$q = "select * from $dbf_prefix" . "_trans where prod_id = $d_quote$my_id$d_quote order by my_date, my_time";
					$r = $dblink->query($q) or die ("Error Getting history from $dbf_prefix" . "_trans in act activity " . $dblink->error);
					$color = 1;
					echo "<td><center><IMG title='$l_desc' src='./pics/" . $my_cat_id . "_$my_id" . ".jpg'></center>";
					if ($r->num_rows > 0) {
						echo "<center><table border='1' cellspacing='0' cellpadding='4'>\n";
						while ($row = $r->fetch_array(MYSQLI_ASSOC)) {
							$color = MeColorTR($color, 1);
							$my_date = DosDate($row["my_date"]);
							$my_time = $row["my_time"];
							$qty = $row["qty"];
							$my_usr = $row["my_usr"];
							$my_note = $row["my_note"];
							if ($qty > 0) {
								echo "<td><IMG src='add.png'>";
							} else {
								echo "<td><IMG src='minus.png'>"; 
							}
							echo "$my_date</td>";
							echo "<td>$my_time</td>";
							echo "<td>$qty</td>";
							echo "<td>$my_usr</td>";
							echo "<td>$my_note</td>";
							echo "</tr>\n";
						}
					} else {
						echo "<p><h1>No history Found!</h1></p>";
					}
					echo "</table></center><br><br>";
				}
				NavBar($cgi, $act, $usr_access, $copyrite);
			break;
			}
			
			case "check_login": {
			
				shortbanner("Verifying Login", $dblink, $css_include_file, $title);
				$err_str_start = "This is a major security flaw<br>Please contact support<br>Fatal Error<br>";
				$err_str = "";
				$user_name = $_POST['user_name'];
				$password = $_POST['password'];	
				// first let's do some self enrollment
				// Is the Program enrolled in the Database?
				$q = "select id from $dbf.s_prog where name = $d_quote$cgi$d_quote";
				$r = $dblink->query($q) or die("Error line 229" . $dblink->error);
				$found = $r->num_rows;
				if ($found < 1) { // time to enroll!
					$comment = "Name:" . chr(13) . "General:" . chr(13) . "Features:" . chr(13) . "Model:";
					$q = "insert into $dbf.s_prog set name = $d_quote$cgi$d_quote, comment = $d_quote$comment$d_quote"; 
					$r = $dblink->query($q) or die("Error line 234" . $dblink->error);
					echo "<p>Enrolled $cgi</p>";
					// it is now enrolled!
				}
				// Is the User in the Database with matching password?
				$q = "select a.id
						from $dbf.s_usr a, $dbf.s_prog b, $dbf.s_usr_prog_x c
						where a.name = $d_quote$user_name$d_quote and a.pass = $d_quote$password$d_quote and 
						b.id = c.s_prog_id and a.id = s_usr_id";
	//echo "<br><br>$q<br><br>";	
				$r = $dblink->query($q) or die("Error line 342" . $dblink->error);
				$found = $r->num_rows;
				if ($found < 1) { // User is not in database
					echo "<p class='trademark'>Access Forbidden!</p>";
					echo "<p>User $user_name not on File<br>or password does not match<br><br></p><p>Access to Caplin Inventory Denied!</p>";
					//exit;
				} else { // is this person registered with permission to this program?
					$row = $r->fetch_array(MYSQLI_ASSOC);
					$user_id = $row["id"]; // this is the user id
					// what is the program id??
					$q = "select id from $dbf.s_prog where name = $d_quote$cgi$d_quote";
					$r = $dblink->query($q) or die("Error line 259" . $dblink->error);
	//echo "<br><br>$q<br><br>";	
					$row = $r->fetch_array(MYSQLI_ASSOC);
					$prog_id = $row['id'];
					// prepair the SQL to check user_id allows access to program in s_usr_prog table
					$q = "select id, usr_access 
						from $dbf.s_usr_prog_x 
						where s_usr_id = $d_quote$user_id$d_quote and s_prog_id = $d_quote$prog_id$d_quote";
					$r = $dblink->query($q) or die("Error line 266" . $dblink->error);
	//echo "<br><br>$q<br><br>";	
					$found = $r->num_rows;
					if ($found == 0) { //Houston, we have a problem
						echo "<p class='trademark'>Access Forbidden!</p>";
						echo "<p>User $user_name does not have access to $cgi</p>";
						//exit;
					} else {
						$row = $r->fetch_array(MYSQLI_ASSOC);
						$usr_access = $row["usr_access"];
						$_SESSION['user_name'] = $user_name;
						$_SESSION['password'] = $password;
						$_SESSION['user_id'] = $user_id;
						$_SESSION['usr_access'] = $usr_access;
						$_SESSION['prog_id'] = $prog_id;
						echo "<p>$user_name is set to access level $usr_access</p>";
					}
				}
				NavBar($cgi, $act, 0, $copyrite);
			break;
			}	

			case "del": {
				if (($_SESSION['my_cat_id'] == '') || (!isset($_SESSION['my_cat_id']))) {
					mybanner("Delete Item and History", $dblink, $css_include_file, $title);
					echo "<p>No Catigory Set!</p>";
				} else {
					$my_cat_id = $_SESSION['my_cat_id'];
					if (isset($cargo)) {
						mybanner("Confirmation of Deleting item and item history", $dblink, $css_include_file, $title);
						// Remove Transactions
						$q = "delete from $dbf_prefix" . "_trans where prod_id = $d_quote$my_id$d_quote and cat_id = $d_quote$my_cat_id$d_quote";
						$r = $dblink->query($q) or die ("Error removing history from $dbf_prefix" . "_trans in act del " . $dblink->error);
						// Remove Item
						$q = "delete from $dbf_prefix" . "_item where id = $d_quote$my_id$d_quote and cat_id = $d_quote$my_cat_id$d_quote";
						$r = $dblink->query($q) or die ("Error removing item from $dbf_prefix" . "_item in act del " . $dblink->error);
						// Remove Picture
						// Remember to chmod 0777 for that dir or it will not delete
						$delete_file = $_SERVER['DOCUMENT_ROOT'] . "/pics/" . "$my_id" . ".jpg";
						$delete_file = "pics/" . $my_cat_id . "_" . $my_id . ".jpg";
						if (file_exists($delete_file)) {
							unlink($delete_file);
						}
						echo "<p><h1>Item has been scrubbed from inventory</h1></p>";
					} else {
						$my_id = $_GET["my_id"];
						$q = "select * from $dbf_prefix" . "_item where id = $d_quote$my_id$d_quote and cat_id = $d_quote$my_cat_id$d_quote";
						$r = $dblink->query($q) or die ("Error Getting info from $dbf_prefix" . "_item in act in " . $dblink->error);
						if ($r->num_rows > 0) {
							mybanner("Delete Item and History", $dblink, $css_include_file, $title);
							$row = $r->fetch_array(MYSQLI_ASSOC);
							$s_desc = $row["s_desc"];
							$l_desc = $row["l_desc"];
							$on_hand = $row["my_count"];
							echo "<p>$s_desc</p>";
							echo "<p><IMG title = '$l_desc' src='./pics/" . $my_cat_id . "_$my_id" . ".jpg'></p>";
							echo "<p><h1>You are about to Remove the above item from Inventory<br>This includes all History</h1></p>";

							echo "<form class='new-single-red' action='$cgi' method='post'>\n";
							echo "<input type='hidden' name='act' value='del'>\n";
							echo "<input type='hidden' name='cargo' value='comment'>\n";
							echo "<input type='hidden' name='my_id' value='$my_id'>\n";
							echo "<input type='submit'  class='hulk-button' value='Remove'>\n";
							echo "</form>\n";
						} else {
							mybanner("Delete Item and History", $dblink, $css_include_file, $title);
							echo "<p>NO ITEM FOUND!</p>\n";
						}
					}
				}
				NavBar($cgi, $act, $usr_access, $copyrite);
			break;
			}
			
			case "make_cat": {
				if (isset($cargo)) {
					$desc = FixMyString($desc);
					$err_str = ValadateInput($desc, "name", "Name");
					// next need to make sure that the start date is less than the beginning date		  
					if (strlen($err_str) > 5) {
						mybanner("ERROR", $dblink, $css_include_file, $title);
						echo $err_str;
						echo "<br><center><font size=+2>Try Again</font></center><br>";
					} else {
						mybanner("New Item Creation", $dblink, $css_include_file, $title);
						$q = "insert into $dbf_prefix" . "_cat set my_desc = $d_quote$desc$d_quote";
						$r = $dblink->query($q) or die("Error creating new item in act $act" . $dblink->error);
						echo "<br><center><font size=+2>Category $desc was Created</font></center><br>";
					}
				} else {
					mybanner("Select/Create Category", $dblink, $css_include_file, $title);
					// Show the Categories
					$q = "select * from $dbf_prefix" . "_cat order by my_desc";
					$r = $dblink->query($q) or die("Error listing categories act $act" . $dblink->error);
					if ($r->num_rows > 0) {
						echo "<p><h1>Select</h1></p>";
						echo "<center><table border='0' cellspacing='0' cellpadding='4'>\n";
						echo "<form action = '$cgi'>";
						echo "<input type='hidden' name='cat' value='comment'>\n";
						echo "<input type='hidden' name='act' value='list'>\n";
						echo "<tr><td><center><select name='my_cat_id'>";
						while ($row = $r->fetch_array(MYSQLI_ASSOC)) {
							$my_desc = $row["my_desc"];
							$my_id = $row["id"];
							echo "<option value='$my_id'>$my_desc</option>";
						}
						echo "</center></td></tr><tr><td><input type='submit'  class='hulk-button' value='Select Category'>\n";
						echo "</form>\n";
						echo "</td></tr></table><br><br>";
					} else {
						echo "<p>No Categories Found</p>";
					}
					echo "<p><h1>Create New</h1></p>";
					echo "<form class='new-single' action='$cgi' method='post'>\n";
					echo "<input type='hidden' name='act' value='$act'>\n";
					echo "<input type='hidden' name='cargo' value='comment'>\n";
					echo "<p class='center'>Name: <input type='text' name='desc' maxlength='40' size='20' ></p>\n";
					echo "<input type='submit'  class='hulk-button' value='Create Category'>\n";
					echo "</form>\n";
					echo "<p></p>";
					// Need to have a delete function here using new_single_red
/*					
						// Remove Transactions
						$q = "delete from $dbf_prefix" . "_trans where prod_id = $d_quote$my_id$d_quote and cat_id = $d_quote$my_cat_id$d_quote";
						// Remove Item
						$q = "delete from $dbf_prefix" . "_item where id = $d_quote$my_id$d_quote and cat_id = $d_quote$my_cat_id$d_quote";
						// Remove Picture
						// Remember to chmod 0777 for that dir or it will not delete
						$mask = "pics/" . $my_cat_id . "_*.jpg";
						array_map( "unlink", glob( $mask ) );
*/						
					
					
				}
				NavBar($cgi, $act, $usr_access, $copyrite);
			break;
			}
			
			case "modify": {
				if (isset($cargo)) {
					$s_desc = FixMyString($s_desc);
					$l_desc = FixMyString($l_desc);
					$err_str = ValadateInput($s_desc, "name", "Name");
					$err_str = $err_str . ValadateInput($l_desc, "name", "Ordering Information");
					if (strlen($err_str) > 5) {
						mybanner("ERROR", $dblink, $css_include_file, $title);
						echo $err_str;
						echo "<br><center><font size=+2>Try Again</font></center><br>";
					} else {
						mybanner("Confirm Item Modification", $dblink, $css_include_file, $title);
						$q = "update $dbf_prefix" . "_item set s_desc = $d_quote$s_desc$d_quote, l_desc = $d_quote$l_desc$d_quote where id = $d_quote$my_id$d_quote";
						$r = $dblink->query($q) or die ("Error Updating Descriptions from $dbf_prefix" . "_item in act modify " . $dblink->error);
						echo "<p><h1>Item has been modified</h1></p>";
					}
				} else {
					mybanner("Modify Item", $dblink, $css_include_file, $title);
					$q = "select * from $dbf_prefix" . "_item where id = $d_quote$my_id$d_quote";
					$r = $dblink->query($q) or die ("Error Getting Description from $dbf_prefix" . "_item in act modify " . $dblink->error);
					if ($r->num_rows > 0) {
						$row = $r->fetch_array(MYSQLI_ASSOC);
						$s_desc = $row["s_desc"];
						$l_desc = $row["l_desc"];
						echo "<p>$s_desc</p>";
						
						echo "<td><center><IMG title='$l_desc' src='./pics/" . $my_cat_id . "_$my_id" . ".jpg'></center>";
						echo "<form class='new-single' action='$cgi' method='post'>\n";
						echo "<input type='hidden' name='act' value='modify'>\n";
						echo "<input type='hidden' name='cargo' value='comment'>\n";
						echo "<input type='hidden' name='my_id' value='$my_id'>\n";
						echo "<p class='center'>Name: <input type='text' name='s_desc' maxlength='40' size='20' value='$s_desc'></p>\n";
						echo "Ordering Information:<br><textarea name='l_desc' rows='8' cols='45' >$l_desc
							</textarea><br><br><input type='submit'  class='hulk-button' value='Update Item'>\n";
						echo "</form>\n";
					} else {
						echo "<p><h1>Item not found!</h1></p>";
					}
					echo "</table></center><br><br>";
				}
				NavBar($cgi, $act, $usr_access, $copyrite);
			break;
			}
			
			case "trans": {
				if (($_SESSION['my_cat_id'] == '') || (!isset($_SESSION['my_cat_id']))) {
					mybanner("Transaction", $dblink, $css_include_file, $title);
					echo "<p>No Catigory Set!</p>";
				} else {
					$my_cat_id = $_SESSION['my_cat_id'];
					if (isset($cargo)) {
						$in = FixMyString($in);
						$error = "none";
						$my_note = FixMyString($my_note);
						$err_str = ValadateInput($in, "no_zero_num", "Items to add/remove");
						$err_str = $err_str . ValadateInput($my_note, "name", "Note");
						if (strlen($err_str) > 5) {
							mybanner("ERROR", $dblink, $css_include_file, $title);
							echo $err_str;
							echo "<br><center><font size=+2>Try Again</font></center><br>";
						} else {
							if ($add == "in") {
								mybanner("Record Received", $dblink, $css_include_file, $title);
								echo "<p>You have selected to add $in items</p>\n";
							} else {
								// Make sure that you have at least these many items to remove
								mybanner("Record Outgoing", $dblink, $css_include_file, $title);
								echo "<p>You have selected to Remove $in items</p>\n";
								$q = "select my_count from $dbf_prefix" . "_item where id = $d_quote$my_id$d_quote and cat_id = $d_quote$my_cat_id$d_quote";
								$r = $dblink->query($q) or die ("Error counting items in $dbf_prefix" . "_trans in act in " . $dblink->error);
								$row = $r->fetch_array(MYSQLI_ASSOC);
								$in_stock = $row["my_count"];
								if ($in_stock < $in) { // Stop and show error
									echo "<p class='h3red'>You have selected to Remove more items than you have in stock<br>DENIED!</p>\n";
									$error = "error";
								} else { // Continue
									$in = $in * -1;
								}
							}
						}
						if ($error == "none") {
							$my_id = $_POST["my_id"];
							$add = $_POST["add"];
							$q = "insert into $dbf_prefix" . "_trans set prod_id = $d_quote$my_id$d_quote
								, cat_id = $d_quote$my_cat_id$d_quote 
								, qty = $d_quote$in$d_quote
								, my_usr = 	$d_quote" . $_SESSION['user_name'] . "$d_quote
								, my_date = $d_quote" . LinuxDate($mydate) . "$d_quote
								, my_time = $d_quote" . date('H:i', time()) . "$d_quote
								, my_ip = $d_quote" . $REMOTE_ADDR . "$d_quote
								, my_note = $d_quote$my_note$d_quote
								";
							// Create the entry and update the item count
							$r = $dblink->query($q) or die ("Error entering info into $dbf_prefix" . "_trans in act in " . $dblink->error);
							$q = "select sum(qty) as mynum from $dbf_prefix" . "_trans where prod_id = $d_quote$my_id$d_quote";
							$r = $dblink->query($q) or die ("Error counting items in $dbf_prefix" . "_trans in act in " . $dblink->error);
							$row = $r->fetch_array(MYSQLI_ASSOC);
							$now_count = $row["mynum"];
							$q = "update $dbf_prefix" . "_item set my_count = $d_quote$now_count$d_quote where id = $d_quote$my_id$d_quote";
							$r = $dblink->query($q) or die ("Error updating count items in $dbf_prefix" . "_item in act in " . $dblink->error);
						}	
					} else {
						if ($add == "in") {
							mybanner("Record Received", $dblink, $css_include_file, $title);
						} else  {
							mybanner("Record Outgoing", $dblink, $css_include_file, $title);
						}
						$my_id = $_GET["my_id"];
						$q = "select * from $dbf_prefix" . "_item where id = $d_quote$my_id$d_quote";
						$r = $dblink->query($q) or die ("Error Getting info from $dbf_prefix" . "_item in act in " . $dblink->error);
						if ($r->num_rows > 0) {
							$row = $r->fetch_array(MYSQLI_ASSOC);
							$s_desc = $row["s_desc"];
							$l_desc = $row["l_desc"];
							$on_hand = $row["my_count"];
							echo "<p>$s_desc</p>";
							echo "<p><IMG title='$l_desc' src='./pics/" . $my_cat_id . "_$my_id" . ".jpg'></p>";
							if ($add == "in") {
								echo "<form class='new-single' action='$cgi' method='post'>\n";
							} else {
								echo "<form class='new-single-red' action='$cgi' method='post'>\n";
							}
							echo "<input type='hidden' name='act' value='trans'>\n";
							echo "<input type='hidden' name='cargo' value='comment'>\n";
							echo "<input type='hidden' name='add' value='$add'>\n";
							echo "<input type='hidden' name='my_id' value='$my_id'>\n";
							echo "<p class='center'># of $s_desc to ";
							if ($add == "in") {
								echo "add:";
							} else {
								echo "Subtract:";
							}
							echo " <input type='text' name='in' maxlength='4' size='5' ></p>\n";
							echo "<p class='center'>Note: <input type='text' name='my_note' maxlength='132' size='40' ></p>\n";
							if ($add == "in") {
								echo "<input type='submit'  class='hulk-button' value='Add'>\n";
							} else {
								echo "<input type='submit'  class='hulk-button' value='Remove'>\n";
							}
							echo "</form>\n";
						} else {
							mybanner("Record Received", $dblink, $css_include_file, $title);
							echo "<p>NO ITEM FOUND!</p>\n";
						}
					}
				}
				NavBar($cgi, $act, $usr_access, $copyrite);
			break;
			}
			
			case "list": {
				mybanner("Invntory Listing", $dblink, $css_include_file, $title);
				if (isset($cat)) {
//	echo "<p>Cat is set to $my_cat_id</p>";
// set session var for my_cat_id
					$_SESSION['my_cat_id'] = $my_cat_id;  // update Cat ID
				}
				if (($_SESSION['my_cat_id'] == '') || (!isset($_SESSION['my_cat_id']))) {
					echo "<p>No Catigory Set!</p>";
				} else {
//	echo "<p>my_cat = " . $_SESSION['my_cat_id'] . "</p>";
					// Show the Cat name
					$my_cat_id = $_SESSION['my_cat_id'];
					$q = "select my_desc from $dbf_prefix" . "_cat where id = $d_quote$my_cat_id$d_quote";
					$r = $dblink->query($q) or die ("Error Getting Catagory info from $dbf_prefix" . "_cat in act $act " . $dblink->error);
					$row = $r->fetch_array(MYSQLI_ASSOC);
					$my_desc = $row["my_desc"];
					echo "<p><h1>$my_desc</h1></p>";
					$q = "select * from $dbf_prefix" . "_item where cat_id = $d_quote" . $_SESSION['my_cat_id'] . "$d_quote order by s_desc";
					$r = $dblink->query($q) or die ("Error Getting info from $dbf_prefix" . "_item in act list " . $dblink->error);
					echo "<center><table border='1' cellspacing='0' cellpadding='4'>\n";
					$color = $num_col;
					if ($r->num_rows > 0) {
						while ($row = $r->fetch_array(MYSQLI_ASSOC)) {
							if (gmp_div_r($color,$num_col) == 0) {
								echo "<tr>";
							}
							$s_desc = $row["s_desc"];
							$l_desc = $row["l_desc"];
							$my_id = $row["id"];
							$my_count = $row["my_count"];
							echo "<td><center>( $my_count )<br>$s_desc<br><IMG title='$l_desc' src='./pics/" . $my_cat_id . "_$my_id" . ".jpg'><br></center>";
							echo "<center><a href='$cgi?act=trans&my_id=$my_id&add=in'><IMG title='Add' src='add.png'></a>";
							if ($my_count > 0) {
								echo "<a href='$cgi?act=trans&my_id=$my_id&add=out'><IMG title='Remove' src='minus.png'></a>";
							} else {
//								echo "<IMG title='No inventory to check out' src='nono.png'>";
							}
							echo "<a href='$cgi?act=del&my_id=$my_id&add=in'><IMG title='Delete from Inventory' src='delete.png'></a>";
							echo "<br>";
							echo "<a href='$cgi?act=modify&my_id=$my_id'><IMG title='Edit' src='edit.png'></a>";
							echo "<a href='$cgi?act=upload&my_id=$my_id'><IMG title='Change Photo' src='picture.png'></a>";
							echo "<a href='$cgi?act=activity&my_id=$my_id'><IMG title='History' src='list_edit.png'></center></a></td>\n\n\n";
							if (gmp_div_r($color,$num_col) ==  $num_col - 1) {
								echo "</tr>\n";
							}
							$color = $color + 1;
						}
					} else {
						echo "<p><h1>No items</h1></p>";
					}
				}
				echo "</table></center><br><br>";
				NavBar($cgi, $act, $usr_access, $copyrite);
			break;
			}
			
			case "login": {
				shortbanner("Login", $dblink, $css_include_file, $title);
					if (!isset($_SESSION['user_id'])) {
						// do nothing
					} else {
						echo "<br>User_id is: " . $_SESSION['user_id'] . "<br>";
					}
					echo "<form id='form1' name='form1' method='post' action='$cgi?act=check_login'>
					<table width='510' border='0' align='center'>
					<tr>
					<td colspan='2'><h1>Login Form</h1></td>
					</tr>
					<tr>
					<td>Username:</td>
					<td><input type='text' name='user_name' id='user_name'></td>
					</tr>
					<tr>
					<td>Password</td>
					<td><input type='password' name='password' id='password'></td>
					</tr>
					<tr>
					<td>&nbsp;</td>
					<td><input type='submit' name='button' id='button' value='Submit'></td>
					</tr>
					</table>
					</form>";
				NavBar($cgi, $act, 0, $copyrite);
			break;
			}	
			
			case "logout": {
				shortbanner("Logout", $dblink, $css_include_file, $title);
					echo "<p>Session has been closed</p>";
					session_destroy();
				NavBar($cgi, $act, $usr_access, $copyrite);
			break;
			}

			case "menu": {
				mybanner("Main Menu", $dblink, $css_include_file, $title);
				NavBar($cgi, $act, $usr_access, $copyrite);
			break;
			}
			
			case "new": {
				if (($_SESSION['my_cat_id'] == '') || (!isset($_SESSION['my_cat_id']))) {
					mybanner("New Item Creation", $dblink, $css_include_file, $title);
					echo "<p>No Catigory Set!</p>";
				} else {
					$my_cat_id = $_SESSION['my_cat_id'];
					$q = "select my_desc from $dbf_prefix" . "_cat where id = $d_quote$my_cat_id$d_quote";
					$r = $dblink->query($q) or die ("Error Getting Catagory info from $dbf_prefix" . "_cat in act $act " . $dblink->error);
					$row = $r->fetch_array(MYSQLI_ASSOC);
					$my_desc = $row["my_desc"];
//					echo "<p>$my_desc</p>";
					if (isset($cargo)) {
						$s_desc = FixMyString($s_desc);
						$l_desc = FixMyString($l_desc);
						$err_str = ValadateInput($s_desc, "name", "Name");
						$err_str = $err_str . ValadateInput($l_desc, "name", "Ordering Information");
						// next need to make sure that the start date is less than the beginning date		  
						if (strlen($err_str) > 5) {
							mybanner("ERROR", $dblink, $css_include_file, $title);
							echo $err_str;
							echo "<br><center><font size=+2>Try Again</font></center><br>";
						} else {
							mybanner("New Item Creation", $dblink, $css_include_file, $title);
							echo "<p><h1>$my_desc</h1></p>";
//	echo "<p>Stuff was sent</p>";
							$q = "insert into $dbf_prefix" . "_item set s_desc = $d_quote$s_desc$d_quote, l_desc = $d_quote$l_desc$d_quote,
							cat_id = $d_quote" . $_SESSION['my_cat_id'] . "$d_quote";
							
							$r = $dblink->query($q) or die("Error creating new item in act new" . $dblink->error);
							echo "<br><center><font size=+2>Item $s_desc was Created</font></center><br>";
						}
					} else {
						mybanner("New Item Creation", $dblink, $css_include_file, $title);
						echo "<p><h1>$my_desc</h1></p>";
						echo "<form class='new-single' action='$cgi' method='post'>\n";
						echo "<input type='hidden' name='act' value='new'>\n";
						echo "<input type='hidden' name='cargo' value='comment'>\n";
						echo "<p class='center'>Name: <input type='text' name='s_desc' maxlength='40' size='20' ></p>\n";
						echo "Ordering Information:<br><textarea name='l_desc' rows='8' cols='45'></textarea><br><br><input type='submit'  class='hulk-button' value='Create Item'>\n";
						echo "</form>\n";
					}
				}
				NavBar($cgi, $act, $usr_access, $copyrite);
			break;
			}

			case "upload": {
				if (($_SESSION['my_cat_id'] == '') || (!isset($_SESSION['my_cat_id']))) {
						mybanner("Upload Photo", $dblink, $css_include_file, $title);
					echo "<p>No Catigory Set!</p>";
				} else {
					$my_cat_id = $_SESSION['my_cat_id'];
					if (isset($cargo)) {
						mybanner("Upload Photo Confirmation", $dblink, $css_include_file, $title);
						// check to see if anything was selected
						if (strlen($_FILES["uploadedfile"]["tmp_name"]) > 3 ) {
							$check = getimagesize($_FILES["uploadedfile"]["tmp_name"]);
							$target_file = "pics/" . $my_cat_id . "_" . $my_id . ".jpg";
							$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
							if($check !== false) {
								$uploadOk = 1;
							} else {
								echo "<p><h1>File is not an image</h1></p>";
								$uploadOk = 0;
							}
							if ($_FILES["uploadedfile"]["size"] > 2500000) {
								echo "<p><h1>Sorry, your file is too large</h1></p>";
								$uploadOk = 0;
							} else {
								if($imageFileType != "jpg" && $imageFileType != "jpeg") {
									echo "<p>Sorry, only JPG and JPEG files are allowed.</p>";
									$uploadOk = 0;
								}
							}
							if (move_uploaded_file($_FILES["uploadedfile"]["tmp_name"], $target_file)) {
								// Now to resize the file in place
								$uploadedfile = $target_file; 
								if ($imageFileType == 'jpg' or  $imageFileType == 'jpeg') {
									$src = imagecreatefromjpeg($uploadedfile);
								}
								list($width, $height) = getimagesize($uploadedfile); 
								$newWidth = 190;
								$newHeight = ($height / $width) * $newWidth;
								if ($newHeight > 144) {
									$newHeight = 143;
									$newWidth = ($width / $height) * $newHeight;
								}	
								$tmp = imagecreatetruecolor($newWidth, $newHeight); 
								$filename = $target_file;
								imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
								imagejpeg($tmp, $filename, 100);
								// End of Now to resize the file in place
							} else {
								echo "<p><h1>Sorry, there was an error uploading your file</h1></p>";
								echo '<p>Here is some more debugging info:</p>';
								echo '<pre>';
								print_r($_FILES);
								echo '</pre>';
							}
						} else {
							echo "<p><h1>File Not Selected</h1></p>";
						}
					} else {
						mybanner("Upload Photo", $dblink, $css_include_file, $title);
						echo "<p><h1>Old Photo Below</h1></p>";
						$q = "select * from $dbf_prefix" . "_item where id = $d_quote$my_id$d_quote and cat_id = $d_quote$my_cat_id$d_quote";
						$r = $dblink->query($q) or die ("Error Getting info from $dbf_prefix" . "_item in act in " . $dblink->error);
						if ($r->num_rows > 0) {
							$row = $r->fetch_array(MYSQLI_ASSOC);
							$s_desc = $row["s_desc"];
							$l_desc = $row["l_desc"];
							$on_hand = $row["my_count"];
							echo "<p>$s_desc</p>";
						}
						echo "<p><IMG title='$l_desc' src='./pics/" . $my_cat_id . "_$my_id" . ".jpg'></p>";
						echo "<form method='POST' action='$cgi' enctype='multipart/form-data'>";
						echo "<input type='hidden' name='act' value='upload'>\n";
						echo "<input type='hidden' name='my_id' value='$my_id'>\n";
						echo "<input type='hidden' name='cargo' value='comment'>\n";
						echo "<p>File to upload : <input type ='file' name = 'uploadedfile'></p><br />";
						echo "<input type='submit'  class='hulk-button' name = 'submit' value='Change Photo'>\n";
						echo "</form>";
					}
				}
				NavBar($cgi, $act, $usr_access, $copyrite);
			break;
			}
			
			
		} // end of switch
		
		{ // Cleanup
		$dblink->close();
		}

		{ // notes
			/*
	
				CREATE TABLE IF NOT EXISTS `$dbf_prefix" . "_trans` (
				  `id` float NOT NULL AUTO_INCREMENT,
				  `cat_id` float NOT NULL,
				  `prod_id` float NOT NULL,
				  `qty` float NOT NULL,
				  `my_usr` varchar(30) NOT NULL DEFAULT '',
				  `my_date` date NOT NULL DEFAULT '0000-00-00',
				  `my_time` varchar(5) NOT NULL DEFAULT '',
				  `my_ip` varchar(15) NOT NULL DEFAULT '',
				  `my_note` varchar(132) NOT NULL DEFAULT '',
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

				CREATE TABLE IF NOT EXISTS `$dbf_prefix" . "_item` (
				  `id` float NOT NULL AUTO_INCREMENT,
				  `cat_id` float NOT NULL,
				  `s_desc` varchar(40) NOT NULL,
				  `l_desc` text NOT NULL,
				  `my_count` float NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

				CREATE TABLE IF NOT EXISTS `$dbf_prefix" . "_cat` (
				  `id` float NOT NULL AUTO_INCREMENT,
				  `my_desc` varchar(40) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
				

			add the collowing to the css file:
			.new-single-red {
				background: -webkit-gradient(linear, bottom, left 175px, from(#ff4000), to(#ff9100));
				background: -moz-linear-gradient(bottom, #ff4000, #ff9100 175px); 
				background: linear-gradient(to top, #ff4000, #ff9100 175px);
				background: -webkit-linear-gradient(#ff9100, #ff4000 );  
				margin:auto;
				position:relative;
				width:390px;
				font-family: Tahoma, Geneva, sans-serif;
				font-size: 14px;
				font-style: italic;
				line-height: 24px;
				font-weight: bold;
				color: #09C;
				text-decoration: none;
				padding:12px;
				-webkit-border-radius: 10px;
				-moz-border-radius: 10px;
				border-radius: 10px;
				border: 1px solid #999;
				-webkit-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);
				-moz-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);
				box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);
			}

				
			*/
		
		}
?>
