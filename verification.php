<?php

/*
*  Rattler Book Exchange
*
*
*  verification.php
*
*
*  Verify user E-Mails by matching
*  code from GET variables with
*  MySQL database
*
*  Calls inc/page_start.php
*  Calls inc/page_end.php
*
*  GET variables
*    vcode
*
*
*/


include("inc/page_start.php");

if(!empty($_GET['vcode']))
{
	if(strlen($_GET['vcode'])==10)
	{
		$query = "SELECT id, email FROM Users WHERE verification='" . $_GET['vcode'] . "' AND active=0";
		$result = mysql_query($query);
		if($result && mysql_num_rows($result)==1)
		{
			$row = mysql_fetch_array($result);
			$query = "UPDATE Users SET verification='Yes', active=1 WHERE id=" . $row['id'];
			$result2 = mysql_query($query);
			if($result2)
			{
				echo '
<form action="login.php" method=POST>
Account activated. Please login:<BR />
	<TABLE>
		<TR>
			<TD>
				StMU Email:
			</TD>
			<TD>
				<input type=text name="username" value="' . $row['email'] . '">
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
			<TD colspan=2>
				<input type=submit value="Login">
			</TD>
		</TR>
	</TABLE>
</form>
				';
				include("inc/page_end.php");
				die('');
			}
		}
		if($result)
			mysql_free_result($result);
	}
}

?>

Invalid Verification Code.

<?php include("inc/page_end.php") ?>