<?php

/*
*  Rattler Book Exchange
*
*
*  admincontrol.php
*
*
*  This page links to all of
*  the admin controls for the
*  website
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

?>

<center>
<BR />
<BR />
<TABLE cellpadding=15>
	<TR>
		<TD align=center>
			<a href="admin_edituser.php"><img src="Images/edit_users.png" border=0></a><BR/>
			<font face="Arial"><B>Manage Users</B></font>
		</TD>
		<TD>
			Change Index Page info
		</TD>
	</TR>
	<TR>
		<TD>
			View All Listings
		</TD>
		<TD>
			View All Haggles
		</TD>
	</TR>
</TABLE>
</center>

<?php include("inc/page_end.php") ?>