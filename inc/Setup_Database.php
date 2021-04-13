<?php

/*
*  Rattler Book Exchange
*
*
*  Setup_Database.php
*
*
*  Creates MySQL Tables in the
*  database specified in inc/defines.php
*
*  Calls inc/connect.php
*
*
*/

include("connect.php");

$i = 0;
//$query[$i] = "CREATE TABLE  (  )" ; $i++;
$query[$i] = "CREATE TABLE Users ( id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, email TEXT, password TEXT, firstname TEXT, lastname TEXT, gender INT, image TEXT, active INT DEFAULT 1, verification TEXT, admin INT )" ; $i++;
$query[$i] = "CREATE TABLE Listings ( id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, sellerid INT, title TEXT, author TEXT, ISBN TEXT, ISBN2 TEXT, edition INT, image TEXT, quality INT, haggleable INT, price INT )" ; $i++;
$query[$i] = "CREATE TABLE Sales ( id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, listingid INT, sellerid INT, buyerid INT, haggle INT, haggleprice INT )" ; $i++;
$query[$i] = "CREATE TABLE Declines ( id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, buyerid INT, sellerid INT, booktitle TEXT, reason TEXT )" ; $i++;
$query[$i] = "CREATE TABLE Accepts ( id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, buyerid INT, sellerid INT, listingid INT, fromwho INT, timewish TEXT, locationwish INT, final INT, message TEXT, saleid INT )" ; $i++;
$query[$i] = "CREATE TABLE Locations ( id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, name TEXT, description TEXT )" ; $i++;

//Queries
for($j=0;$j<$i;$j++)
{
	$result = mysql_query($query[$j]);

	if(!$result)
	{
		echo 'Bad Query - MySQL Error: ' . mysql_error() . '<BR />';
	}
}

?>

Finished.