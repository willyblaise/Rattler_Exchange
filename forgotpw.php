<?php

/*
*  Rattler Book Exchange
*
*
*  forgotpw.php
*
*
*  Resends user's password to their
*  E-Mail
*
*  Calls inc/page_start.php
*  Calls inc/page_end.php
*  Calls inc/forgotpw_email.php
*
*
*/


include("inc/page_start.php");

if(!empty($_POST['email']))
{
	//Get length of email ignoring spaces on end
	$strlen = strlen($_POST['email']);
	while($_POST['email'][$strlen - 1]==' ' && $strlen>12)
	{
		$strlen--;
		$_POST['email'][$strlen] = '/0';
	}

	echo '<center>';

	if(substr($_POST['email'],$strlen - 12,12)!="stmarytx.edu" || !( $_POST['email'][$strlen - 13]=='@' || substr($_POST['email'],$strlen - 18,6)=="@mail." ))
	{
		echo '<font color=red>Invalid Email. Valid StMU Email address required.</font><BR />';
	}
	else
	{
		$query = "SELECT firstname, lastname, password FROM Users WHERE email='" . $_POST['email'] . "'";
		$result = mysql_query($query);
		if($result && mysql_num_rows($result)==1)
		{
			$row = mysql_fetch_array($result);

			include("inc/forgotpw_email.php");
			$message = create_fp_mail_body($row['firstname'],$row['lastname'],WEBADDRESS,$row['password']);

			mail($_POST['email'],"Rattler Book Exchange - Forgot PW",$message);

			echo 'Password sent to ' . $_POST['email'] . '</center>';

			mysql_free_result($result);
			include("inc/page_end.php");
			die('');
		}
		else
		{
			echo '<font color=red>E-Mail (' . $_POST['email'] . ') does not exist in our database.</font><BR /><BR />';
		}
		if($result)
			mysql_free_result($result);
	}

	echo '</center>';
}

?>

<form action="forgotpw.php" method=POST>
Forgot Password Page<BR />
	<TABLE>
		<TR>
			<TD>
				StMU Email:
			</TD>
			<TD>
				<input type=text name="email" value=<?php echo '"' . $_POST['email'] . '"'; ?>>
			</TD>
		</TR>
		<TR>
			<TD colspan=2>
				<input type=submit value="Send Password">
			</TD>
		</TR>
	</TABLE>
</form>

<?php include("inc/page_end.php") ?>