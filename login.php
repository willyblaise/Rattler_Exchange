<?php

/*
*  Rattler Book Exchange
*
*
*  login.php
*
*
*  This file matches provided login
*  information from POST with database
*  information. If a match is found
*  session variables are set to let
*  user login
*
*  Also creates MySQL Database connection
*
*  Calls inc/page_start.php
*  Calls inc/page_end.php
*  Calls inc/connect.php
*
*
*/

session_start();

if(!empty($_POST['username']) && !empty($_POST['password']))
{
	$_POST['username'] = strtolower($_POST['username']);

	//Get length of email ignoring spaces on end
	$strlen = strlen($_POST['username']);
	while($_POST['username'][$strlen - 1]==' ' && $strlen>12)
	{
		$strlen--;
		$_POST['username'][$strlen] = '/0';
	}

	if(substr($_POST['username'],$strlen - 12,12)!="stmarytx.edu" || !( $_POST['username'][$strlen - 13]=='@' || substr($_POST['username'],$strlen - 18,6)=="@mail." ))
	{
		$echo_message = '<font color=red>Invalid Email. Valid StMU Email address required.</font><BR />';
	}
	else
	{
		include("inc/connect.php");

		$query = "SELECT id, password, active, verification, admin FROM Users WHERE email='" . $_POST['username'] . "'";
		$result = mysql_query($query);
		if($result && mysql_num_rows($result)!=1)
		{
			$echo_message = '<font color=red>Invalid Login Email</font>';
			mysql_free_result($result);
		}
		else if($result)
		{
			$row = mysql_fetch_array($result);
			if($_POST['password'] != $row['password'])
			{
				$echo_message = '<font color=red>Invalid Password Match</font>';
			}
			else if($row['active'])
			{
				$_SESSION['userid'] = $row['id'];
				$_SESSION['email'] = $_POST['username'];
				$_SESSION['admin'] = $row['admin'];
				include("inc/page_start.php");
				echo '<center>Successfully Logged In.<meta http-equiv="refresh" content="0;url=index.php"></center>';
				//mysql_free_result($result);
				include("inc/page_end.php");
				die('');
			}
			else if($row['verification']=="Yes")
			{
				$echo_message = '<font color=red>Account Frozen. Please contact <a href="mailto:chronic@ghwchronic.com">Customer Support</a></font>';
			}
			else
			{
				$echo_message = 'The account is not validated. Would you like to Resend the verification E-Mail?<BR />
				<form action="register.php" method=POST>
				<input type=hidden name="justvalidate" value=2>
				<input type=hidden name="rowid" value=' . $row['id'] . '>
				<input type=submit value="Resend Verification E-Mail">
				</form>
				';
			}
			mysql_free_result($result);
		}
	}
}

include("inc/page_start.php");

echo '<center>' . $echo_message;

?>

<BR />
<BR />
<form action="login.php" method=POST>

	<TABLE>
		<TR>
			<TD>
				StMU Email:
			</TD>
			<TD>
				<input type=text name="username" value=<?php echo '"' . $_POST['username'] . '"'; ?> >
			</TD>
		</TR>
		<TR>
			<TD>
				Password:
			</TD>
			<TD>
				<input type=password name="password">
			</TD>
		</TR>
		<TR>
			<TD colspan=2 bgcolor=#AAAAAA>
				<input type=submit value="Login">
			</TD>
		</TR>
	</TABLE>
</form>
</center>

<?php include("inc/page_end.php"); ?>