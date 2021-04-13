<?php

/*
*  Rattler Book Exchange
*
*
*  page_start.php
*
*
*  This file is called to create
*  the template up to the location
*  where content is placed.
*    -Header
*    -Login Field
*    -Navigation Pane
*
*  Also creates MySQL Database connection
*
*  Calls inc/connect.php
*  Calls inc/navigation.php
*
*
*/

if(!session_id())
	session_start();

if(!defined("SERVER"))
	include("inc/connect.php");


?>

<HTML>
<head>
	<title>Rattler Book Exchange</title>
</head>
<body>

<TABLE align=center border=1>
	<TR>
		<TD colspan=2 align=center>
			[Header]
		</TD>
		<TD>
			<?php
			if(!logged_in())
			{
				echo '
				<form action="login.php" method=POST>
					<font size=2>StMU Email:</font>
					<input type=text name="username" />
					<font size=2>Password:</font>
					<input type=password name="password" />
					<input type=submit value="Login" />
					<BR /><a href="register.php"><font size=2 color=#000000>Register</font></a> <a href="forgotpw.php"><font size=2 color=#000000>Forgot pw</font></a>
				</form>
				';
			}
			else
			{
				echo '
				<form action="logout.php">
					Logged in as:<BR />
					' . $_SESSION['email'] . '<BR />
					<input type=submit value="Logout">
				</form>
				';
			}
			?>
		</TD>
	</TR>
	<TR height=200 valign=top>
		<TD>
			<BR />
<?php include("inc/navigation.php"); ?>
		</TD>
		<TD colspan=2>