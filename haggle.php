<?php

/*
*  Rattler Book Exchange
*
*
*  haggle.php
*
*
*  Haggle on a listing from this page
*
*  Calls inc/page_start.php
*  Calls inc/page_end.php
*
*
*/

if(empty($_POST['haggleprice']) && !empty($_GET['haggleprice']))
	$_POST['haggleprice'] = $_GET['haggleprice'];

if(empty($_POST['haggleprice']) || !is_numeric($_POST['haggleprice']))
	$_POST['haggleprice'] = 100;

if(empty($_POST['listingid']) && !empty($_GET['listingid']))
	$_POST['listingid'] = $_GET['listingid'];

include("inc/page_start.php");

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

$query = "SELECT * FROM Listings WHERE haggleable=1 AND id=" . $_POST['listingid'];
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

	$query = "SELECT haggle, haggleprice FROM Sales WHERE buyerid=" . $_SESSION['userid'] . " AND listingid=" . $_POST['listingid'];
	$result = mysql_query($query);
	$bought = false;
	$haggled = false;
	$print_overwrite_message = false;
	if($result)
	{
		if(mysql_num_rows($result)==1)
		{
			$row2 = mysql_fetch_array($result);
			if(!$row2['haggle'])
			{
				$bought = true;
			}
			else
			{
				$haggleprice = $row2['haggleprice'];
				$haggled = true;
			}
		}
		mysql_free_result($result);
	}

	if(!empty($_POST['unbuy']) && $_POST['unbuy']==1 && $haggled)
	{
		$query = "DELETE FROM Sales WHERE buyerid=" . $_SESSION['userid'] . " AND listingid=" . $_POST['listingid'];
		$result = mysql_query($query);
		$query = "DELETE FROM Accepts WHERE buyerid=" . $_SESSION['userid'] . " AND listingid=" . $_POST['listingid'];
		mysql_query($query);
		if($result)
		{
			//email
			$haggled = false;
			echo '<BR /><center><font color=#00FF00>Seller has been notified that you retract your haggle.</font></center><BR />';
		}
		else
		{
			echo '<font color=#FF0000>MySQL Database Error. Could not complete action.</font>';
		}	
	}
	else if(!empty($_POST['secondview']) && $_POST['secondview']==1 && !$haggled)
	{
		$query = "DELETE FROM Sales WHERE buyerid=" . $_SESSION['userid'] . " AND listingid=" . $_POST['listingid'];
		$result = mysql_query($query);
		$bought = false;
		$query = "INSERT INTO Sales SET listingid=" . $_POST['listingid'] . ", buyerid=" . $_SESSION['userid'] . ", haggle=1, haggleprice=" . $_POST['haggleprice'] . ", sellerid=" . $row['sellerid'];
		$result = mysql_query($query);
		if($result)
		{
			//email
			$haggled = true;
			$haggleprice = $_POST['haggleprice'];
			echo '<BR /><center><font color=#00FF00>Seller has been notified that you wish to haggle for $' . $haggleprice . '.00</font></center><BR />';
		}
		else
		{
			echo '<font color=#FF0000>MySQL Database Error. Could not complete action.</font>';
		}	
	}

	if($bought)
		echo '<center><font color=#FF0000>Haggling on this book cancels your current buy status on this book.</center></font><BR />';

$query = "SELECT haggle FROM SALES WHERE listingid=" . $_POST['listingid'];
$result = mysql_query($query);

$sales = 0;
$haggles=0;
if($result)
{
	while($row2 = mysql_fetch_array($result))
	{
		if($row2['haggle'])
			$haggles++;
		else
			$sales++;
	}
	mysql_free_result($result);
}


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
			' . (!empty($sales) ? $sales : 0 ) . ' Prospective Buyers<BR />
			' . ($row['haggleable'] ? (!empty($haggles) ? $haggles : 0 ) . ' Pending Haggles' : '') . '
		</TD>
	</TR>
	<TR>
		<TD colspan=3>
			' . (empty($row['image']) ? 'No Image Available' : '<img src="' . $row['image'] . '">' ) . '
		</TD>
	</TR>
	<TR>
		<form action="haggle.php" method=POST>
		<input type=hidden name="listingid" value=' . $row['id'] . '>
		<input type=hidden name="secondview" value=1>
		' . ($haggled ? '<input type=hidden name="unbuy" value=1>' : '' ) . '
		<TD colspan=3>
			' . ($haggled ? 'The owner of this book has been notified of your haggle of $' . $haggleprice . '.00<BR /><input type=submit value="Un-Haggle">' : '$<input type=text name="haggleprice" value="' . $_POST['haggleprice'] . '" size=3>.00 <input type=submit value="Confirm Haggle">' ) . '
		</TD>
		</form>
	</TR>
</TABLE>
<BR />
<a href="buy.php?listingid=' . $_POST['listingid'] . '">Buy Page</a><BR /><BR />';
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