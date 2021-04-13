<?php

/*
*  Rattler Book Exchange
*
*
*  index.php
*
*
*  Homepage file
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

if(empty($_POST['saleid']))
{
	echo '<center>Invalid Redirect.</center><meta http-equiv="refresh" content="2;url=index.php">';
	include("inc/page_end.php");
	die('');
}

if(!empty($_POST['acceptid']))
{
	mysql_query("UPDATE Accepts SET final=1 WHERE id=" . $_POST['acceptid']);
	echo '<center>You have decided upon a Date, Time and Location.</center>';
}

echo '<BR />';

$query = "SELECT id FROM Accepts WHERE final=1 AND saleid=" . $_POST['saleid'];
$result = mysql_query($query);
$finalid = 0;
if($result)
{
	if(mysql_num_rows($result) && $row = mysql_fetch_array($result))
		$finalid = $row['id'];
	mysql_free_result($result);
}

$query = "SELECT listingid, buyerid, sellerid FROM Accepts WHERE saleid=" . $_POST['saleid'];
$result = mysql_query($query);
$fromwho_me = 1;
if($result)
{
	if(mysql_num_rows($result) && $row = mysql_fetch_array($result))
	{
		$g_listingid = $row['listingid'];
		$g_buyerid = $row['buyerid'];
		$g_sellerid = $row['sellerid'];
		if($g_buyerid==$_SESSION['userid'])
		{
			$fromwho_me = 2;
		}
	}
	mysql_free_result($result);
}

$query = "SELECT id, name FROM Locations";

$result = mysql_query($query);
if($result)
{
	while($row = mysql_fetch_array($result))
	{
		$locations[$row['id']] = $row;
	}
	mysql_free_result($result);
}
$locations[-1]['name'] = "University Center Lounge";

if(!$finalid)
{
	if(!empty($_POST['button']) && $_POST['button']=="Add Message")
	{
		if(empty($_POST['message']) || strlen($_POST['message'])<3)
		{
			echo '<center><font color=#FF0000>Please include a message when posting.</font><BR /></center>';
		}
		else
		{
			if(strlen($_POST['minute'])==1)
				$_POST['minute'] = "0" . $_POST['minute'];

			$compiled_time = $_POST['month'] . "/" . $_POST['day'] . " " . $_POST['hour'] . ":" . $_POST['minute'];

			$query = "INSERT INTO Accepts SET listingid=" . $g_listingid . ", fromwho=" . $fromwho_me . ", timewish='" . $compiled_time . "', buyerid=" . $g_buyerid . ", sellerid=" . $g_sellerid . ", message='" . $_POST['message'] . "', saleid=" . $_POST['saleid'] . ", locationwish=" . $_POST['location'];
			$result = mysql_query($query);

			//email
			echo '<center>Message Successfully added</center><BR />';
		}
	}
	$current['month'] = (int)date("n");
	$current['day'] = (int)date("j");
	$current['hour'] = 12;
	$current['minute'] = 0;

	echo '
<form action="view_meeting.php" method=POST>
<input type=hidden name="saleid" value=' . $_POST['saleid'] . '>
<TABLE align=center>
	<TR>
		<TD>
			<font color=red>Write a Message</font>
		</TD>
	</TR>
	<TR>
		<TD>
			&nbsp; &nbsp; &nbsp; &nbsp;<textarea rows=10 cols=50 name="message"></textarea>
		</TD>
	</TR>
	<TR>
		<TD>
			&nbsp; &nbsp; &nbsp; &nbsp;<select name="month">
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
			<input type=text name="minute" size=1 maxlength=2 value="' . ($current['minute']<10 ? "0" . $current['minute'] : $current['minute'] ) . '"> (00:01 - 24:00)<BR />
			&nbsp; &nbsp; &nbsp; &nbsp;<select name="location">
	';

	$locations2 = current($locations);
	$size = count($locations) - 1;
	if($size)
	{
		do
		{
			echo '<option value=' . $locations2['id'] . '>' . $locations2['name'] . '</option>';
			$i++;
		}
		while($i < $size && $locations2 = next($locations2));
	}
	else
	{
		echo '<option value=-1 selected=yes>University Center Lounge</option>';
	}

	echo '
			</select><BR />
			&nbsp; &nbsp; &nbsp; &nbsp;<input type=submit name="button" value="Add Message">
		</TD>
	</TR>
</TABLE>
</form>
<BR />';
}

$query = "SELECT id, fromwho, timewish, locationwish, message FROM Accepts WHERE saleid=" . $_POST['saleid'] . " ORDER BY id DESC";
$result = mysql_query($query);
if($result && mysql_num_rows($result))
{

	echo '<TABLE align=center>';

	$row = mysql_fetch_array($result);

	do
	{
		echo '
	<TR>
		<TD>';
		if($fromwho_me==$row['fromwho'])
			echo '<font color=blue>Outgoing Message</font>';
		else
			echo '<font color=green>Incoming Message</font>';

		echo '
		</TD>
	</TR>
	<TR>
		<TD>
			&nbsp; &nbsp; &nbsp; &nbsp;<textarea readonly="readonly" rows=10 cols=50>' . $row['message'] . '</textarea>
		</TD>
	</TR>
	<TR>
		<TD ' . ($row['id']==$finalid ? 'bgcolor=#C0C0C0' : '' ) . '>
			&nbsp; &nbsp; &nbsp; &nbsp;Preffered Date/Time: ' . $row['timewish'] . '<BR />
			&nbsp; &nbsp; &nbsp; &nbsp;Preffered Location: ' . $locations[$row['locationwish']]['name'] . '
		</TD>
	</TR>
';

		if(!$finalid && $row['fromwho']!=$fromwho_me)
		{
		echo '
	<TR>
		<form action="view_meeting.php" method=POST>
		<input type=hidden name="saleid" value=' . $_POST['saleid'] . '>
		<input type=hidden name="acceptid" value=' . $row['id'] . '>
		<TD align=center>
			<input type=submit name="button" value="Accept Date/Time/Location Prefference">
		</TD>
	</TR>
		';
		}
		echo '
	<TR>
		<TD>
			<BR />
		</TD>
	</TR>';
	}
	while($row = mysql_fetch_array($result));

	echo '</TABLE>';

	mysql_free_result($result);
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