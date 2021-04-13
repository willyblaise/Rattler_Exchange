<?php

/*
*  Rattler Book Exchange
*
*
*  my_offers.php
*
*
*  Lists all of the book listing
*  a user has bid on and declinations
*
*  Calls inc/page_start.php
*  Calls inc/page_end.php
*
*
*/


include("inc/page_start.php");

if(!logged_in())
{
	echo '<center>Not Logged In.</center><meta http-equiv="refresh" content="2;url=index.php">';
	include("inc/page_end.php");
	die('');
}

if(!empty($_POST['declinationid']))
{
	mysql_query("DELETE FROM Declines WHERE buyerid=" . $_SESSION['userid'] . " AND id=" . $_POST['declinationid']);
}

	echo '
<BR />
<TABLE align=center cellpadding=5>';

$query = "SELECT listingid, haggle, haggleprice FROM Sales WHERE buyerid=" . $_SESSION['userid'];
$result = mysql_query($query);
if($result)
{
	echo '
	<TR>
		<TD width=250>
			<B>Book Title</B>
		</TD>
		<TD width=100>
			<B>Asking Price</B>
		</TD>
		<TD width=100>
			<B>Haggle</B>
		</TD>
	</TR>';

	while($row = mysql_fetch_array($result))
	{
		$query = "SELECT title, price FROM Listings WHERE id=" . $row['listingid'];
		$result2 = mysql_query($query);
		if($result2)
		{
			$row2 = mysql_fetch_array($result2);
			echo '
	<TR>
		<TD>
			<a href="' . ($row['haggle'] ? 'haggle.php' : 'buy.php' ) . '?listingid=' . $row['listingid'] . '">' . $row2['title'] . '</a>
		</TD>
		<TD>
			$' . $row2['price'] . '.00
		</TD>
		<TD>
			' . ($row['haggle'] ? '$' . $row['haggleprice'] . '.00' : 'N/A' ) . '
		</TD>
	</TR>
			';
			mysql_free_result($result2);
		}
	}

	mysql_free_result($result);
}
else
{
	echo '<center><font color=red>MySQL Error. Couldn\'t retrieve list.</font></center>';
}

$query = "SELECT id, booktitle, reason FROM Declines WHERE buyerid=" . $_SESSION['userid'];
$result = mysql_query($query);
if($result)
{
	if(mysql_num_rows($result))
	{
		echo '
	<TR>
		<TD colspan=4>
			<BR />
			<BR />
			<font size=4 color=#FF0000><B>Declines</B></font><BR />
		</TD>
	</TR>';
	}
	while($row = mysql_fetch_array($result))
	{
		
		echo '
	<TR>
		<form action="my_offers.php" method=POST>
		<TD colspan=4>
			' . $row['booktitle'] . '<BR />
			<textarea rows=10 cols=50>' . $row['reason'] . '</textarea><BR />
			<input type=hidden name="declinationid" value=' . $row['id'] . '>
			<input type=submit value="Delete Declination">
		</TD>
		</form>
	</TR>';

	}
	mysql_free_result($result);
}

echo '</TABLE>';

?>



<?php include("inc/page_end.php") ?>