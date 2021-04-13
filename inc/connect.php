<?php

/*
*  Rattler Book Exchange
*
*
*  connect.php
*
*
*  This file is called to connect
*  to the MySQL database defined
*  in the inc/defines.php file
*
*  Calls inc/defines.php
*
*
*/

include("defines.php");

if(!mysql_connect(SERVER,USERNAME,PASSWORD))
{
	echo mysql_error();
	die('Unable to connect to the MySQL server');
}

if(!@mysql_select_db(DATABASE))
{
	die('Unable to connect to the MySQL database');
}

?>