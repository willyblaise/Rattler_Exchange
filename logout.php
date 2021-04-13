<?php

/*
*  Rattler Book Exchange
*
*
*  logout.php
*
*
*  Resets session variables
*
*  Calls inc/page_start.php
*  Calls inc/page_end.php
*
*
*/

session_start();

$message=0;

if(!empty($_SESSION['userid']) && $_SESSION['userid'])
{
	$message=1;
	session_unset();
}

include("inc/page_start.php");

if($message)
{
	echo '<center>Logged Out.</center><meta http-equiv="refresh" content="0;url=index.php">';
}
else
{
	echo '<center><font color=red>Not Logged In.</font></center><meta http-equiv="refresh" content="0;url=index.php">';
}

include("inc/page_end.php");

?>