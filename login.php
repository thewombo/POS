<?php

/***************************************************************************
 *   copyright            : (C) 2012 PCRepairTracker.com
 *   email                : info@pcrepairtracker.com
 *   This program is a copyrighted work.
 *   Please see the license.txt file for details
 *
 ***************************************************************************/

include_once("deps.php");

function pv($value) {
$value2 = trim($value);
 if (get_magic_quotes_gpc()) {
   return addslashes($value2);
 } else {
   return mysql_real_escape_string($value2);
 }
}


function pcrtlang($string) {

require("deps.php");
$rs_connect = @mysql_connect($dbhost, $dbuname, $dbpass) or die("Couldn't connect the db");
$rs_select_db = @mysql_select_db($dbname, $rs_connect) or die("Couldn't select the db in pcrtlang");
mysql_query("SET NAMES utf8");
$safestring = pv($string);
$findbasestring = "SELECT * FROM languages WHERE basestring LIKE BINARY '$safestring'";
$findbasestringq = @mysql_query($findbasestring, $rs_connect);
if(mysql_num_rows($findbasestringq) == 0) {
$addstring = "INSERT INTO languages (language,languagestring,basestring) VALUES ('en-us','$safestring','$safestring')";
@mysql_query($addstring, $rs_connect);
}

$findstring = "SELECT languagestring FROM languages WHERE basestring LIKE BINARY '$safestring' AND language = '$mypcrtlanguage'";

$findstringq = @mysql_query($findstring, $rs_connect);
if(mysql_num_rows($findstringq) == 0) {
return "$string";
} else {
$rs_result_qs = mysql_fetch_object($findstringq);
return "$rs_result_qs->languagestring";
}
}


if (array_key_exists("RURI", $_REQUEST)) {
$ruri = $_REQUEST['RURI'];
} else {
$ruri = "../repair";
}

if (array_key_exists("METHOD", $_REQUEST)) {
$method = $_REQUEST['METHOD'];
} else {
$method = "";
}


if(isset($_POST["username"])&&isset($_POST["password"])) {
$user = $_POST["username"];
$pass = md5($_POST["password"]);
$validated = false;


if(isset($passwords[$user])) if($passwords[$user]==$pass) $validated = true;
if($validated) {


if(isset($cookiedomain)) {
setcookie("username", $user, time()+36000, "/","$cookiedomain");
setcookie("password", $pass, time()+36000, "/","$cookiedomain"); 
} else {
setcookie("username", $user, time()+36000, "/");
setcookie("password", $pass, time()+36000, "/");
}


if("$method" == "POST") {

if (preg_match("/store/i", $ruri)) {
$gotouri = urlencode("../store");
header("Location: loglogin.php?gotouri=$gotouri");
} else {
$gotouri = urlencode("../repair");
header("Location: loglogin.php?gotouri=$gotouri");
}




} else {
$gotouri = urlencode($ruri);
header("Location: loglogin.php?gotouri=$gotouri");
}


} else {
$failedlogin = "1"; 
}
//End login code
}
?>
<!DOCTYPE html>
<html>
<head>
<?php
if(!isset($pcrt_stylesheet)) {
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"../repair/style.css\">";
} else {
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"../repair/$pcrt_stylesheet\">";
}
?>
<link rel="stylesheet" type="text/css" href="ani.css">
<title><?php echo pcrtlang("Login"); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script>
<!--
function sf(){document.loginbox.username.focus();}
// -->
</script>


</head>
<body onLoad=sf()>
<center><br><br><img src="<?php echo "$logo"; ?>" class="animated bounceIn">
<br><br><br><table><tr><td>
<form name="loginbox" action="login.php" method="post">
<font class=text12b><?php echo pcrtlang("Username"); ?>:</font></td><td><input type="text" name="username" class="textbox"></td></tr>
<tr><td><font class=text12b><?php echo pcrtlang("Password"); ?>:</font></td><td><input type="password" name="password" class="textbox"></td></tr>
<tr><td colspan=2 style="text-align:center;"><input type="hidden" name="RURI" value="<?php echo "$ruri"; ?>"><input type="hidden" name="METHOD" value="<?php echo "$method"; ?>"><br>
<input type="submit" value="<?php echo pcrtlang("Login"); ?>" class="button">

</form></td></tr></table>

<?php

if (isset($failedlogin)) {
echo "<br><br><div class=notify style=\"width:250px\"><font class=textred12>".pcrtlang("Sorry, Invalid username/password combination").".</font></div>";
}

?>


</center>

<!-- pcrt1.37 -->

</body>

</html>
