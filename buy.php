<?php

/*
*  Rattler Book Exchange
*
*
*  buy.php
*
*
*  Buy a listing from this page
*
*  Calls inc/page_start.php
*  Calls inc/page_end.php
*
*
*/


include("inc/page_start.php");

if(empty($_POST['listingid']) && !empty($_GET['listingid']))
	$_POST['listingid'] = $_GET['listingid'];

if(!logged_in())
{
	echo '<center>Not Logged In.</center><meta http-equiv="refresh" content="2;url=index.php">';
	include("inc/page_end.php");
	die('');
}

if(empty($_POST['listingid']))
{
	echo '<center>Invalid Redirect.</center><meta http-equiv="refresh" content="2;url=index.php">';
	include("inc/page_end.php");
	die('');
}

$query = "SELECT * FROM Listings WHERE id=" . $_POST['listingid'];
$result = mysql_query($query);
if($result && mysql_num_rows($result)==1)
{
	$row = mysql_fetch_array($result);
	mysql_free_result($result);

	if($row['sellerid']==$_SESSION['userid'])
	{
		echo '<center>Cannot buy your own book.</center><meta http-equiv="refresh" content="2;url=index.php">';
		include("inc/page_end.php");
		die('');
	}

	$query = "SELECT haggle FROM Sales WHERE buyerid=" . $_SESSION['userid'] . " AND listingid=" . $_POST['listingid'];
	$result = mysql_query($query);
	$bought = false;
	$print_overwrite_message = false;
	if($result)
	{
		if(mysql_num_rows($result)==1)
		{
			$row2 = mysql_fetch_array($result);
			if(!$row2['haggle'])
				$bought = true;
			else
				$print_overwrite_message = true;
		}
		mysql_free_result($result);
	}

	if(!empty($_POST['unbuy']) && $_POST['unbuy']==1)
	{
		$query = "DELETE FROM Sales WHERE buyerid=" . $_SESSION['userid'] . " AND listingid=" . $_POST['listingid'];
		$result = mysql_query($query);
		$query = "DELETE FROM Accepts WHERE buyerid=" . $_SESSION['userid'] . " AND listingid=" . $_POST['listingid'];
		mysql_query($query);
		if($result)
		{
			//email
			$bought = false;
			echo '<BR /><center><font color=#00FF00>Seller has been notified that you no longer wish to buy this book.</font></center><BR />';
		}
		else
		{
			echo '<font color=#FF0000>MySQL Database Error. Could not complete action.</font>';
		}	
	}
	else if(!empty($_POST['secondview']) && $_POST['secondview']==1 && !$bought)
	{
		$query = "DELETE FROM Sales WHERE buyerid=" . $_SESSION['userid'] . " AND listingid=" . $_POST['listingid'];
		$result = mysql_query($query);
		$print_overwrite_message = false;
		$query = "INSERT INTO Sales SET listingid=" . $_POST['listingid'] . ", buyerid=" . $_SESSION['userid'] . ", haggle=0, haggleprice=0, sellerid=" . $row['sellerid'];
		$result = mysql_query($query);
		if($result)
		{
			//email
			$bought = true;
			echo '<BR /><center><font color=#00FF00>Seller has been notified that you wish to buy this book.</font></center><BR />';
		}
		else
		{
			echo '<font color=#FF0000>MySQL Database Error. Could not complete action.</font>';
		}	
	}

	if($print_overwrite_message)
		echo '<center><font color=#FF0000>Buying this book overwrites your current haggle on it.</center></font><BR />';

	echo '
<TABLE border=1 align=center cellpadding=4>
	<TR>
		<TD colspan=3 bgcolor=#AAFFAA>
			Price: $' . $row['price'] . '.00
		</TD>
	</TR>
	<TR>
		<TD width=200>
			' . $row['title'] . '<BR />
			' . $row['author'] . '<BR />
			' . $row['edition'] . edition_string( $row['edition']) . ' Edition<BR />
			ISBN: ' . $row['ISBN'] . '
		</TD>
		<TD width=75 align=center>
			' . quality_picture($row['quality']) . '<BR>' . quality_string($row['quality']) . '
		</TD>
		<TD width=150>
			' . (!empty($row['sales']) ? $row['sales'] : 0 ) . ' Prospective Buyers<BR />
			' . ($row['haggleable'] ? (!empty($row['haggles']) ? $row['haggles'] : 0 ) . ' Pending Haggles' : '') . '
		</TD>
	</TR>
	<TR>
		<TD colspan=3>
			' . (empty($row['image']) ? 'No Image Available' : '<img src="' . $row['image'] . '">' ) . '
		</TD>
	</TR>
	<TR>
		<form action="buy.php" method=POST>
		<input type=hidden name="listingid" value=' . $row['id'] . '>
		<input type=hidden name="secondview" value=1>
		' . ($bought ? '<input type=hidden name="unbuy" value=1>' : '' ) . '
		<TD colspan=3>
			' . ($bought ? 'The owner of this book has been notified that you wish to buy this book.<BR /><input type=submit value="UnBuy">' : '<input type=submit value="Confirm Buy">' ) . '
		</TD>
		</form>
	</TR>
</TABLE>
<BR />';
	if($row['haggleable'])
	{
		echo '<a href="haggle.php?listingid=' . $_POST['listingid'] . '">Haggle Page</a><BR /><BR />';
	}
}
else
{
	if($result)
		mysql_free_result($result);
	echo '<center>Invalid Redirect.</center><meta http-equiv="refresh" content="2;url=index.php">';
	include("inc/page_end.php");
	die('');
}

?>

<?php include("inc/page_end.php") ?>