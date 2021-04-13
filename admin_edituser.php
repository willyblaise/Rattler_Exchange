<?php

/*
*  Rattler Book Exchange
*
*
*  admin_edituser.php
*
*
*  Admins can change information
*  about users from this page
*
*  Calls inc/page_start.php
*  Calls inc/page_end.php
*
*
*/


include("inc/page_start.php");

if(!logged_in() || !is_admin())
{
	echo '<center>Not Admin.</center><meta http-equiv="refresh" content="2;url=index.php">';
	include("inc/page_end.php");
	die('');
}

if(!empty($_POST['rowid']))
{
	$verify = false;
	if(!empty($_POST['verified']) && $_POST['verified']==2)
	{
		$verify = true;
	}

	$admin = 0;
	if(!empty($_POST['admin']) && $_POST['admin']==2)
	{
		$admin = 1;
	}

	$query = "UPDATE Users SET email='" . $_POST['email'] . "', active=" . $_POST['active'] . ($verify ? ", verification='Yes'" : "" ) . ", admin=" . $admin . " WHERE id=" . $_POST['rowid'];
	$result = mysql_query($query);
	if($result)
	{
		echo 'User Updated.<BR />';
	}

	//Note that 2nd query necessary because
	//we don't need to change verification field
	//if a random verification string already exists
	if(!$verify)
	{
		//check for verification code
		$query = "SELECT verification FROM Users WHERE id=" . $_POST['rowid'];
		$result = mysql_query($query);
		if($result)
		{
			if(mysql_num_rows($result)==1)
			{
				$row = mysql_fetch_array($result);
				if($row['verification']=="Yes")
				{
					//change to random verification code
					$query = "UPDATE Users SET verification='" . verification_code() . "' WHERE id=" . $_POST['rowid'];
					$result2 = mysql_query($query);
					if(!$result2)
					{
						echo '<font color=red>MySQL Error changing verification status.</font><BR />';
					}
				}
			}
			else
			{
				echo '<font color=red>MySQL Error changing verification status.</font><BR />';
			}
			mysql_free_result($result);
		}
	}
}

?>

<BR />
<TABLE border=1 align=center>
	<TR>
		<TD bgcolor=#909090>
			<a href=<?php echo 'admin_edituser.php?sort=email&asc=' . (!empty($_GET['asc']) && $_GET['asc']==2 ? 1 : 2 ); ?> ><font color=#DDDD40>E-Mail</a></font>
		</TD>
		<TD bgcolor=#909090>
			<a href=<?php echo 'admin_edituser.php?sort=last&asc=' . (!empty($_GET['asc']) && $_GET['asc']==2 ? 1 : 2 ); ?> ><font color=#DDDD40>Lastname</a></font>
		</TD>
		<TD bgcolor=#909090>
			<a href=<?php echo 'admin_edituser.php?sort=first&asc=' . (!empty($_GET['asc']) && $_GET['asc']==2 ? 1 : 2 ); ?> ><font color=#DDDD40>Firstname</a></font>
		</TD>
		<TD bgcolor=#909090>
			<a href=<?php echo 'admin_edituser.php?sort=active&asc=' . (!empty($_GET['asc']) && $_GET['asc']==2 ? 1 : 2 ); ?> ><font color=#DDDD40>Active</a></font>
		</TD>
		<TD bgcolor=#909090>
			<a href=<?php echo 'admin_edituser.php?sort=frozen&asc=' . (!empty($_GET['asc']) && $_GET['asc']==2 ? 1 : 2 ); ?> ><font color=#DDDD40>Frozen</a></font>
		</TD>
		<TD bgcolor=#909090>
			<a href=<?php echo 'admin_edituser.php?sort=verified&asc=' . (!empty($_GET['asc']) && $_GET['asc']==2 ? 1 : 2 ); ?> ><font color=#DDDD40>Verified</a></font>
		</TD>
		<TD bgcolor=#909090>
			<a href=<?php echo 'admin_edituser.php?sort=admin&asc=' . (!empty($_GET['asc']) && $_GET['asc']==2 ? 1 : 2 ); ?> ><font color=#DDDD40>Admin</a></font>
		</TD>
		<TD bgcolor=#909090>
			&nbsp;
		</TD>
	</TR>

<?php

if(empty($_GET['sort']))
	$_GET['sort'] = "email";

if(empty($_GET['asc']))
	$_GET['asc'] = 1;

$sortby = $_GET['sort'];

if($_GET['sort']=="first")
{
	$sortby = "firstname";
}
else if($_GET['sort']=="last")
{
	$sortby = "lastname";
}
else if($_GET['sort']=="frozen")
{
	if($_GET['asc']==1)
		$_GET['asc'] = 2;
	else
		$_GET['asc'] = 1;

	$sortby = "active";
}
else if($_GET['sort']=="verified")
{
	$sortby = "verification";
}

$query = "SELECT id, email, lastname, firstname, active, verification, admin FROM Users ORDER BY " . $sortby . " " . ( $_GET['asc']==1 ? "" : "DESC" );
$result = mysql_query($query);
if($result)
{
	while($row = mysql_fetch_array($result))
	{
		echo '
	<form action="admin_edituser.php" method=POST>
	<TR>
		<TD bgcolor=#909090>
			<input type=text name="email" value="' . $row['email'] . '">
		</TD>
		<TD bgcolor=#909090>
			<input type=text size=15 name="lastname" value="' . $row['lastname'] . '">
		</TD>
		<TD bgcolor=#909090>
			<input type=text size=15 name="firstname" value="' . $row['firstname'] . '">
		</TD>
		<TD bgcolor=#909090 align=center>
			<input type=radio name="active" value=1 ' . ($row['active']==1 ? "checked=yes" : "" ) . '
		</TD>
		<TD bgcolor=#909090 align=center>
			<input type=radio name="active" value=0 ' . ($row['active']==1 ? "" : "checked=yes" ) . '
		</TD>
		<TD bgcolor=#909090 align=center>
			<input type=checkbox name="verified" value=2 ' . ($row['verification']=="Yes" ? "checked=yes" : "" ) . '
		</TD>
		<TD bgcolor=#909090 align=center>
			<input type=checkbox name="admin" value=2 ' . ($row['admin']==1 ? "checked=yes" : "" ) . '
		</TD>
		<TD bgcolor=#909090>
			<input type=hidden name="rowid" value=' . $row['id'] . '>
			<input type=submit value="Update">
		</TD>
	</TR>
	</form>
		';
	}
	mysql_free_result($result);
}

?>

</TABLE>

<?php include("inc/page_end.php") ?>