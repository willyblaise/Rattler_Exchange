<?php

/*
*  Rattler Book Exchange
*
*
*  defines.php
*
*
*  This file is contains the MySQL
*  database login information.
*
*  Also contains the following functions:
*    logged_in()
*      -returns true if the user is logged into an account
*
*
*/

if(!defined("SERVER")){

define("SERVER","localhost");
define("USERNAME","root");
define("DATABASE","books2");

/*
define("SERVER","p50mysql51.secureserver.net");
define("USERNAME","GHWChronic");
define("DATABASE","GHWChronic");
*/

if(file_exists("inc/password.php"))
	include("inc/password.php");
else if(file_exists("password.php"))
	include("password.php");
else


	define("PASSWORD","password");

define("WEBADDRESS","localhost/books2");

function logged_in()
{
	if(empty($_SESSION['userid']) || $_SESSION['userid']==0)
		return false;

	return true;
}

function is_admin()
{
	if(empty($_SESSION['admin']) || $_SESSION['admin']==0)
		return false;

	return true;
}

function verification_code()
{

	$verification = "A0AA0A0A00";
	$verification[0] = chr( rand(0,1) ? rand(65,90) : rand(97,122) );
	$verification[1] = chr(rand(48,57));
	$verification[2] = chr( rand(0,1) ? rand(65,90) : rand(97,122) );
	$verification[3] = chr( rand(0,1) ? rand(65,90) : rand(97,122) );
	$verification[4] = chr(rand(48,57));
	$verification[5] = chr( rand(0,1) ? rand(65,90) : rand(97,122) );
	$verification[6] = chr(rand(48,57));
	$verification[7] = chr( rand(0,1) ? rand(65,90) : rand(97,122) );
	$verification[8] = chr(rand(48,57));
	$verification[9] = chr(rand(48,57));
	return $verification;
}

function quality_string($quality)
{
	switch($quality)
	{
		case 1: return "New";
		case 2: return "Barely Used";
		case 3: return "Fair";
	}
	return "Poor";
}

function quality_picture($quality)
{
	$string = '<img src="Images/';
	switch($quality)
	{
		case 1: $string .= "new";
		case 2: $string .= "used";
		case 3: $string .= "fair";
		default: $string .= "poor";
	}
	$string .= '.jpg">';
	return $string;
}

function edition_string($edition)
{
	switch($edition)
	{
		case 1: return "st";
		case 2: return "nd";
		case 3: return "rd";
	}
	return "th";
}

}
?>