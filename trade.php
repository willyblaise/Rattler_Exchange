<?php

/*
*  Rattler Book Exchange
*
*
*  trade.php
*
*
*  Accept or Decline offers here
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

if(empty($_POST['saleid']) || empty($_POST['button']))
{
	echo '<center><font color=red>Redirection Error.</font></center><meta http-equiv="refresh" content="2;url=index.php">';
	include("inc/page_end.php");
	die('');
}

//use later for exceptions to make redraw of table
$redraw_whenwhere = false;

if($_POST['button']=="Trade Completed")
{
	$query = "SELECT listingid FROM Sales WHERE id=" . $_POST['saleid'];
	$result = mysql_query($query);
	if($result)
	{
		if(mysql_num_rows($result) && $row = mysql_fetch_array($result))
		{
			echo '<center>
<form action="edit_listing.php?listingid=' . $row['listingid'] . '" method=POST><BR />
Congratulations on a successfull book trade.<BR />
To remove all information about this listing press below.<BR />
<input type=submit value="Delete Book Listing" name="button">
</form>
</center>
			';
			mysql_free_result($result);
		}
		else
		{
			mysql_free_result($result);
			echo '<center><font color=red>Database Error.</font></center>';
			include("inc/page_end.php");
			die('');
		}
	}
	else
	{
		echo '<center><font color=red>Database Error.</font></center>';
		include("inc/page_end.php");
		die('');
	}
}
else if($_POST['button']=="Arrange Meeting")
{
	$query = "SELECT listingid, buyerid FROM Sales WHERE id=" . $_POST['saleid'];
	$result = mysql_query($query);
	if($result)
	{
		$row = mysql_fetch_array($result);
		mysql_free_result($result);
	}
	else
	{
		echo '<center><font color=red>Invalid Sale ID.</font></center><meta http-equiv="refresh" content="2;url=index.php">';
		include("inc/page_end.php");
		die('');
	}

	if(strlen($_POST['minute'])==1)
		$_POST['minute'] = "0" . $_POST['minute'];

	$compiled_time = $_POST['month'] . "/" . $_POST['day'] . " " . $_POST['hour'] . ":" . $_POST['minute'];

	if(!$redraw_whenwhere)
	{
		$query = "INSERT INTO Accepts SET listingid=" . $row['listingid'] . ", fromwho=1, timewish='" . $compiled_time . "', buyerid=" . $row['buyerid'] . ", sellerid=" . $_SESSION['userid'] . ", message='" . $_POST['message'] . "', saleid=" . $_POST['saleid'] . ", locationwish=" . $_POST['location'];
		$result = mysql_query($query);

		//email
		echo '<center>The process of arranging a meeting has started.<BR />To view the status click on "Meetings" on the left hand navigation</center>';
	}
}
else if($_POST['button']=="Start Trade" || $redraw_whenwhere)
{
	$query = "SELECT listingid, buyerid FROM Sales WHERE id=" . $_POST['saleid'];
	$result = mysql_query($query);
	if($result)
	{
		$row = mysql_fetch_array($result);
		mysql_free_result($result);
	}
	else
	{
		echo '<center><font color=red>Invalid Sale ID.</font></center><meta http-equiv="refresh" content="2;url=index.php">';
		include("inc/page_end.php");
		die('');
	}
	$query = "SELECT id FROM Accepts WHERE listingid=" . $row['listingid'] . " AND buyerid=" . $row['buyerid'] . " AND sellerid=" . $_SESSION['userid'];
	$result = mysql_query($query);
	if($result && mysql_fetch_array($result))
	{
		mysql_free_result($result);
		echo '<center><font color=red>Trade already Started. This is redirect.</font></center><meta http-equiv="refresh" content="2;url=index.php">';
		include("inc/page_end.php");
		die('');
	}
	if($result)
		mysql_free_result($result);

	$current['month'] = (int)date("n");
	$current['day'] = (int)date("j");
	$current['hour'] = 12;
	$current['minute'] = 0;

	echo '
<form action="trade.php" method=POST>
<input type=hidden name="saleid" value=' . $_POST['saleid'] . '>
<TABLE>
	<TR>
		<TD>
			Preffered Meeting Location:
		</TD>
		<TD width=400>
			<select name="location">';

	$query = "SELECT id, name FROM Locations";
	$result = mysql_query($query);
	if($result && mysql_num_rows($result))
	{
		while($row = mysql_fetch_array($result))
		{
			echo '<option value=' . $row['id'] . '>' . $row['name'] . '</option>';
		}
	}
	else
	{
		echo '<option value=-1 selected=yes>University Center Lounge</option>';
	}
	if($result)
		mysql_free_result($result);

	echo '
			</select>
		</TD>
	</TR>
	<TR>
		<TD>
			Preffered Meeting Date/Time:
		</TD>
		<TD>
			<select name="month">
				<option value=1' . (($current['month']==1) ? ' selected=yes' : ' ' ) . '>1 January</option>
				<option value=2' . (($current['month']==2) ? ' selected=yes' : ' ' ) . '>2 February</option>
				<option value=3' . (($current['month']==3) ? ' selected=yes' : ' ' ) . '>3 March</option>
				<option value=4' . (($current['month']==4) ? ' selected=yes' : ' ' ) . '>4 April</option>
				<option value=5' . (($current['month']==5) ? ' selected=yes' : ' ' ) . '>5 May</option>
				<option value=6' . (($current['month']==6) ? ' selected=yes' : ' ' ) . '>6 June</option>
				<option value=7' . (($current['month']==7) ? ' selected=yes' : ' ' ) . '>7 July</option>
				<option value=8' . (($current['month']==8) ? ' selected=yes' : ' ' ) . '>8 August</option>
				<option value=9' . (($current['month']==9) ? ' selected=yes' : ' ' ) . '>9 September</option>
				<option value=10' . (($current['month']==10) ? ' selected=yes' : ' ' ) . '>10 October</option>
				<option value=11' . (($current['month']==11) ? ' selected=yes' : ' ' ) . '>11 November</option>
				<option value=12' . (($current['month']==12) ? ' selected=yes' : ' ' ) . '>12 December</option>
			</select>
			/
			<input type=text name="day" size=1 value="' . $current['day'] . '">
			&nbsp; &nbsp; &nbsp; &nbsp;
			<input type=text name="hour" size=1 value="' . $current['hour'] . '">
			:
			<input type=text name="minute" size=1 maxlength=2 value="' . ($current['minute']<10 ? "0" . $current['minute'] : $current['minute'] ) . '"> (00:01 - 24:00)
		</TD>
	</TR>
	<TR>
		<TD>
			Message:
		</TD>
		<TD>
			<textarea name="message" cols=50 rows=20></textarea>
		</TD>
	</TR>
	<TR>
		<TD>
			&nbsp;
		</TD>
		<TD>
			<input type=submit name="button" value="Arrange Meeting">
		</TD>
	</TR>
</TABLE>';
}
else if(strpos(" " . $_POST['button'],"Decline")==1)
{
	if($_POST['button']=="Decline Trade")
	{
		if(strlen($_POST['reason'])<3)
		{
			echo '<center><font color=red>Please provide a reason</font></center><BR />';
		}
		else
		{
			$query = "SELECT listingid, buyerid FROM Sales WHERE id=" . $_POST['saleid'];
			$result = mysql_query($query);
			if($result)
			{
				$row = mysql_fetch_array($result);
				$buyerid = $row['buyerid'];
				$listingid = $row['listingid'];
				mysql_free_result($result);
			}
			else
			{
				echo '<center><font color=red>Invalid Sale ID.</font></center><meta http-equiv="refresh" content="2;url=index.php">';
				include("inc/page_end.php");
				die('');
			}
			$query = "SELECT title FROM Listings WHERE id=" . $listingid;
			$result = mysql_query($query);
			if($result)
			{
				$row = mysql_fetch_array($result);
				mysql_free_result($result);
			}
			else
			{
				echo '<center><font color=red>Invalid Sale ID.</font></center><meta http-equiv="refresh" content="2;url=index.php">';
				include("inc/page_end.php");
				die('');
			}

			//Declined e-mail
			$query = "INSERT INTO Declines SET buyerid=" . $buyerid . ", sellerid=" . $_SESSION['userid'] . ", booktitle='" . $row['title'] . "', reason='" . $_POST['reason'] . "'";
			mysql_query($query);
			$query = "DELETE FROM Sales WHERE id=" . $_POST['saleid'];
			mysql_query($query);
			$query = "DELETE FROM Accepts WHERE listingid=" . $listingid . " AND buyerid=" . $buyerid . " AND sellerid=" . $_SESSION['userid'];
			mysql_query($query);
			echo '<center>The trade request has been declined.</center>';
			include("inc/page_end.php");
			die('');
		}
	}
	echo '
<BR />
<form action="trade.php" method=POST>
<input type=hidden name="saleid" value=' . $_POST['saleid'] . '>
&nbsp; &nbsp; &nbsp; Reason for Declination:<BR />
&nbsp; &nbsp; &nbsp; <textarea name="reason" rows=10 cols=50></textarea><BR />
&nbsp; &nbsp; &nbsp; <input type=submit name="button" value="Decline Trade">
</form>';
}

?>



<?php include("inc/page_end.php") ?>