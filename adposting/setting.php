<?php require_once('../includes/rayicecms.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "login.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "member";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysqli_real_escape_string") ? mysqli_real_escape_string(dbconnect(), $theValue) : mysqli_escape_string(dbconnect(), $theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$colname_rsM = "-1";
if (isset($_GET['memberid'])) {
  $colname_rsM = $_GET['memberid'];
}
mysqli_select_db(dbconnect(),$database_rayicecms);
$query_rsM = sprintf("SELECT * FROM members WHERE memberid = %s", GetSQLValueString($colname_rsM, "int"));
$rsM = mysqli_query(dbconnect(),$query_rsM) or die(mysqli_connect_error());
$row_rsM = mysqli_fetch_assoc($rsM);
$totalRows_rsM = mysqli_num_rows($rsM);

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "registerbox")) {

// UPLOAD CODE BY SYED RAZA ALI START //
   	$allowed_filetypes = array('.jpg','.gif','.bmp','.png');
  	$no_ID = $row_rsM['memberid'];
	$max_filesize = 5250000;
  	$upload_path = '../images/members/';
	if($_FILES['photo']['size'] == 0 || empty($_FILES['photo']['name']))
		{
   		   $filename = $row_rsM['photo'];
		}
	else
		{
   		   $filename = $no_ID."".str_replace(' ', '_', $_FILES['photo']['name']);
		   $ext = substr($filename, strpos($filename,'.'), strlen($filename)-1); // Get the extension from the filename.
 	 	   if(!in_array($ext,$allowed_filetypes))
   		   {die('The file you attempted to upload is not allowed.');} 
   		   if(filesize($_FILES['photo']['tmp_name']) > $max_filesize)
           {die('The file you attempted to upload is too large.');}
           if(!is_writable($upload_path))
           {die('You cannot upload to the specified directory, please CHMOD it to 777.');}   
           if(move_uploaded_file($_FILES['photo']['tmp_name'],$upload_path . $filename))
           {echo 'Your file upload was successful';} 
           else{echo'There was an error during the file upload.  Please try again.';} 
		}
// UPLOAD CODE BY SYED RAZA ALI END /

  $updateSQL = sprintf("UPDATE members SET users=%s, passs=%s, fullname=%s, address=%s, email=%s, photo=%s, zip=%s, city=%s, `state`=%s, country=%s, phone=%s, yahooid=%s, twitter=%s, facebook=%s, status=%s, `position`=%s WHERE memberid=%s",
                       GetSQLValueString($_POST['users'], "text"),
                       GetSQLValueString($_POST['passs'], "text"),
                       GetSQLValueString($_POST['fullname'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($filename, "text"),
                       GetSQLValueString($_POST['zip'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['state'], "text"),
                       GetSQLValueString($_POST['country'], "text"),
                       GetSQLValueString($_POST['phone'], "text"),
                       GetSQLValueString($_POST['yahooid'], "text"),
                       GetSQLValueString($_POST['twitter'], "text"),
                       GetSQLValueString($_POST['facebook'], "text"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['position'], "text"),
                       GetSQLValueString($_POST['memberid'], "int"));

  mysqli_select_db(dbconnect(),$database_rayicecms);
  $Result1 = mysqli_query(dbconnect(),$updateSQL) or die(mysqli_connect_error());

  $updateGoTo = "account.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$currentPage = $_SERVER["PHP_SELF"];

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_setting = "SELECT * FROM settings";
$setting = mysqli_query(dbconnect(),$query_setting) or die(mysqli_connect_error());
$row_setting = mysqli_fetch_assoc($setting);
$totalRows_setting = mysqli_num_rows($setting);

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_blogcates = "SELECT * FROM categories WHERE selecttopic = 'adposting'";
$blogcates = mysqli_query(dbconnect(),$query_blogcates) or die(mysqli_connect_error());
$row_blogcates = mysqli_fetch_assoc($blogcates);
$totalRows_blogcates = mysqli_num_rows($blogcates);

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_pages = "SELECT * FROM pages";
$pages = mysqli_query(dbconnect(),$query_pages) or die(mysqli_connect_error());
$row_pages = mysqli_fetch_assoc($pages);
$totalRows_pages = mysqli_num_rows($pages);


mysqli_select_db(dbconnect(),$database_rayicecms);
$query_parts = "SELECT * FROM parts";
$parts = mysqli_query(dbconnect(),$query_parts) or die(mysqli_connect_error());
$row_parts = mysqli_fetch_assoc($parts);
$totalRows_parts = mysqli_num_rows($parts);

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_theme = "SELECT * FROM themes";
$theme = mysqli_query(dbconnect(),$query_theme) or die(mysqli_connect_error());
$row_theme = mysqli_fetch_assoc($theme);
$totalRows_theme = mysqli_num_rows($theme);


$maxRows_adposting = 10;
$pageNum_adposting = 0;
if (isset($_GET['pageNum_adposting'])) {
  $pageNum_adposting = $_GET['pageNum_adposting'];
}
$startRow_adposting = $pageNum_adposting * $maxRows_adposting;

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_adposting = "SELECT * FROM adposting WHERE status = 'published' ORDER BY adpostingid DESC";
$query_limit_adposting = sprintf("%s LIMIT %d, %d", $query_adposting, $startRow_adposting, $maxRows_adposting);
$adposting = mysqli_query(dbconnect(),$query_limit_adposting) or die(mysqli_connect_error());
$row_adposting = mysqli_fetch_assoc($adposting);

if (isset($_GET['totalRows_adposting'])) {
  $totalRows_adposting = $_GET['totalRows_adposting'];
} else {
  $all_adposting = mysqli_query(dbconnect(),$query_adposting);
  $totalRows_adposting = mysqli_num_rows($all_adposting);
}
$totalPages_adposting = ceil($totalRows_adposting/$maxRows_adposting)-1;

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_links = "SELECT * FROM friendlinks";
$links = mysqli_query(dbconnect(),$query_links) or die(mysqli_connect_error());
$row_links = mysqli_fetch_assoc($links);
$totalRows_links = mysqli_num_rows($links);

mysqli_select_db(dbconnect(),$database_rayicecms);
$query_featuredadposts = "SELECT * FROM adposting WHERE position = 'featured' ORDER BY adpostingid DESC";
$featuredadposts = mysqli_query(dbconnect(),$query_featuredadposts) or die(mysqli_connect_error());
$row_featuredadposts = mysqli_fetch_assoc($featuredadposts);
$totalRows_featuredadposts = mysqli_num_rows($featuredadposts);

$colname_members = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_members = $_SESSION['MM_Username'];
}
mysqli_select_db(dbconnect(),$database_rayicecms);
$query_members = sprintf("SELECT * FROM members WHERE users = %s", GetSQLValueString($colname_members, "text"));
$members = mysqli_query(dbconnect(),$query_members) or die(mysqli_connect_error());
$row_members = mysqli_fetch_assoc($members);
$totalRows_members = mysqli_num_rows($members);

$queryString_adposting = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_adposting") == false && 
        stristr($param, "totalRows_adposting") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_adposting = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_adposting = sprintf("&totalRows_adposting=%d%s", $totalRows_adposting, $queryString_adposting);
?>
<?php include("../configuration.php"); ?>
<!-- code start -->
<?php
	  if(($row_setting['installed'] == "yes") && ($row_setting['selecttopic'] == "adposting"))
	  {
?>
<!-- index start -->
<?php include($theme_path."".$row_setting['theme']."/topdocs.php"); ?>
<title>My Ads</title>
<link href="/themes/<?php echo $row_setting['theme']; ?>/multicms.css" rel="stylesheet" type="text/css">
<?php $favicon ?>
<?php include($theme_path."".$row_setting['theme']."/botdocs.php"); ?>
<?php
if($row_setting['onlinestatus'] == "yes")
{
?>
<!-- HEADER INCLUDED -->
<?php include($theme_path."".$row_setting['theme']."/header.php"); ?><table class="mainbody">
      <tr>
        <?php include($theme_path."".$row_setting['theme']."/leftmenu.php"); ?>
        <td valign="top"><div class="title"> My Account</div>
                  <table class="tables">
                    <tr>
                      <td width="50%"><ul>
                        <li><a href="myads.php">My Ads</a></li>
                        <li><a href="postnewad.php">Post New Ad</a></li>
                        <li><a href="setting.php">Setting</a></li>
                      </ul></td>
                      <td width="50%" align="center">Welcome to <?php echo $_SESSION['MM_Username']; ?></td>
                    </tr>
                    <tr>
                      <td colspan="2"><div class="title">Update your detail</div>
                        <form action="<?php echo $editFormAction; ?>" method="POST" name="registerbox" id="registerbox">
                        <table class="texts">
                          <tr>
                            <td><strong>USERNAME:*</strong></td>
                            <td><input name="users" type="text" class="form" id="users" value="<?php echo $_SESSION['MM_Username']; ?>" readonly required="required"></td>
                          </tr>
                          <tr>
                            <td><strong>PASSWORD:*</strong></td>
                            <td><input name="passs" type="password" class="form" id="passs" value="<?php echo $row_members['passs']; ?>" required="required"></td>
                          </tr>
                          <tr>
                            <td><strong> FULL NAME:*</strong></td>
                            <td><input name="fullname" type="text" class="form" id="fullname" value="<?php echo $row_members['fullname']; ?>" required="required"></td>
                          </tr>
                          <tr>
                            <td><strong> ADDRESS:*</strong></td>
                            <td><input name="address" type="text" class="form" id="address" value="<?php echo $row_members['address']; ?>" required="required"></td>
                          </tr>
                          <tr>
                            <td><strong> EMAIL:*</strong></td>
                            <td><input name="email" type="email" class="form" id="email" value="<?php echo $row_members['email']; ?>" required="required"> </td>
                          </tr>
                          <tr>
                            <td><strong>PHOTO:*</strong></td>
                            <td><input name="photo" type="file" class="formmenusimple" id="photo" />&nbsp;</td>
                          </tr>
                          <tr>
                            <td><strong>ZIP:*</strong></td>
                            <td><input name="zip" type="text" class="form" id="zip" value="<?php echo $row_members['zip']; ?>" required="required"></td>
                          </tr>
                          <tr>
                            <td><strong> CITY:*</strong></td>
                            <td><input name="city" type="text" class="form" id="city" value="<?php echo $row_members['city']; ?>" required="required"></td>
                          </tr>
                          <tr>
                            <td><strong> STATE:*</strong></td>
                            <td><input name="state" type="text" class="form" id="state" value="<?php echo $row_members['state']; ?>" required="required"></td>
                          </tr>
                          <tr>
                            <td><strong> COUNTRY:*</strong></td>
                            <td><input name="country" type="text" class="form" id="country" value="<?php echo $row_members['country']; ?>" required="required"></td>
                          </tr>
                          <tr>
                            <td><strong>PHONE:*</strong></td>
                            <td><input name="phone" type="text" class="form" id="phone" value="<?php echo $row_members['phone']; ?>" required="required"></td>
                          </tr>
                          <tr>
                            <td><strong> YAHOOID:</strong></td>
                            <td><input name="yahooid" type="text" class="form" id="yahooid" value="<?php echo $row_members['yahooid']; ?>"></td>
                          </tr>
                          <tr>
                            <td><strong> TWITTER:</strong></td>
                            <td><input name="twitter" type="text" class="form" id="twitter" value="<?php echo $row_members['twitter']; ?>"></td>
                          </tr>
                          <tr>
                            <td><strong> FACEBOOK:</strong></td>
                            <td><input name="facebook" type="text" class="form" id="facebook" value="<?php echo $row_members['facebook']; ?>"></td>
                          </tr>
                          <tr>
                            <td></td>
                            <td><input name="button" type="submit" class="button" id="button" value="Update"></td>
                          </tr>
                          </table>
                        <input name="status" type="hidden" id="status" value="<?php echo $row_members['status']; ?>">
                        <input name="position" type="hidden" id="position" value="<?php echo $row_members['position']; ?>">
                        <input name="memberid" type="hidden" id="memberid" value="<?php echo $row_members['memberid']; ?>">
                        <input type="hidden" name="MM_update" value="registerbox">
                      </form>
                      </td>
                      </tr>
                  </table></td>
        <?php
include($theme_path."".$row_setting['theme']."/rightmenu.php"); 
?>
      </tr>
    </table><?php
include($theme_path."".$row_setting['theme']."/footer.php"); 
?>
<?php
}
else
{
?>
<div align="center">Site is currently Offline</div>
<?php
}
?>
</body>
</html>
<!-- index end -->
<?php
	  }
	  else
	  {
	  ?>
<script type="text/javascript"> location.replace("/install.php"); </script>
<?php
	  }
?>
<!-- code end -->
<?php
mysqli_free_result($setting);

mysqli_free_result($blogcates);

mysqli_free_result($pages);

mysqli_free_result($theme);

mysqli_free_result($parts);

mysqli_free_result($adposting);

mysqli_free_result($links);

mysqli_free_result($featuredadposts);

mysqli_free_result($members);

mysqli_free_result($rsM);

?>