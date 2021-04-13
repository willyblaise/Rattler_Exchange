<?php

/*
*  Rattler Book Exchange
*
*
*  register.php
*
*
*  Users register new accounts
*  through this page.
*
*  Sends out E-mail to verify account
*  Creates new row in Users table
*
*  Calls inc/page_start.php
*  Calls inc/page_end.php
*  Calls inc/validation_email.php
*
*
*/


include("inc/page_start.php");

if(!empty($_POST['justvalidate']) && !empty($_POST['rowid']) && $_POST['justvalidate']==2 && $_POST['rowid'])
{
	echo '<center>';

	$query = "SELECT email, firstname, lastname, verification FROM Users WHERE id=" . $_POST['rowid'];
	$result = mysql_query($query);
	if($result)
	{
		$row = mysql_fetch_array($result);
		if($row['verification']=="Yes")
		{
			echo '<font color=red>Invalid account id (Account already verified)</font>';
		}
		else
		{
			include("inc/validation_email.php");
			$message = create_mail_body($row['firstname'],$row['lastname'],WEBADDRESS,$row['verification']);
			mail($row['email'],"Rattler Book Exchange - Verification Email",$message);
			echo 'Verification email sent to ' . $row['email'];
		}
		mysql_free_result($result);
	}
	else
	{
		echo '<font color=red>Invalid account id (Inexistant)</font>';
	}

	echo '</center>';
	include("inc/page_end.php");
	die('');
}

$draw_table=true;

if(!empty($_POST['email']))
{

	$draw_table=false;

	//Get length of email ignoring spaces on end
	$strlen = strlen($_POST['email']);
	while($_POST['email'][$strlen - 1]==' ' && $strlen>12)
	{
		$strlen--;
		$_POST['email'][$strlen] = '/0';
	}

	if(substr($_POST['email'],$strlen - 12,12)!="stmarytx.edu" || !( $_POST['email'][$strlen - 13]=='@' || substr($_POST['email'],$strlen - 18,6)=="@mail." ))
	{
		$draw_table=true;
		$asterisk['email'] = true;
		echo '<font color=red>Invalid Email. Valid StMU Email address required.</font><BR />';
	}
	else
	{
		$query = "SELECT id, verification, active FROM Users WHERE email='" . $_POST['email'] . "'";
		$result = mysql_query($query);
		if($result && mysql_num_rows($result))
		{
			echo '<font color=red>Specified E-Mail has already been used to register an account.<BR />';

			$row = mysql_fetch_array($result);
			if($row['active'])
			{
				echo 'If you forgot your password you can visit the <a href="forgotpw.php">Forgot Password</a> Page.';
			}
			else if($row['verification']=="Yes")
			{
				echo 'The account appears to be suspended. Please contact <a href="mailto:chronic@ghwchronic.com">Customer Service</a> for assistance.';
			}
			else
			{
				echo 'The account is not validated however. Would you like to Resend the verification E-Mail?<BR />
				<form action="register.php" method=POST>
				<input type=hidden name="justvalidate" value=2>
				<input type=hidden name="rowid" value=' . $row['id'] . '>
				<input type=submit value="Resend Verification E-Mail">
				</form>
				';
			}

			echo '</font>';

			mysql_free_result($result);
			include("inc/page_end.php");
			die('');
		}
		if($result)
			mysql_free_result($result);
	}
	if(empty($_POST['password1']))
	{
		$draw_table=true;
		$asterisk['password1'] = true;
		echo '<font color=red>Empty Password Field.</font><BR />';
	}
	if(empty($_POST['password2']))
	{
		$draw_table=true;
		$asterisk['password2'] = true;
		echo '<font color=red>Empty Repeat Password Field.</font><BR />';
	}
	if(!empty($_POST['password1']) && !empty($_POST['password2']) && $_POST['password1']!=$_POST['password2'])
	{
		$draw_table=true;
		$asterisk['password1'] = true;
		$asterisk['password2'] = true;
		echo '<font color=red>Password fields do not match.</font><BR />';
	}
	if(empty($_POST['firstname']))
	{
		$draw_table=true;
		$asterisk['firstname'] = true;
		echo '<font color=red>Empty Firstname Field.</font><BR />';
	}
	if(empty($_POST['firstname']))
	{
		$draw_table=true;
		$asterisk['lastname'] = true;
		echo '<font color=red>Empty Lastname Field.</font><BR />';
	}
}
if($draw_table)
{
	if(empty($_POST['email']))
		$_POST['email']="";
	if(empty($_POST['firstname']))
		$_POST['firstname']="";
	if(empty($_POST['lastname']))
		$_POST['lastname']="";
	echo '

<form action="register.php" method=post enctype="multipart/form-data">
<input type = hidden name = "MAX_FILE_SIZE" value = 100000000>

<table border=1>
	<tr>
		<td>
			' . (!empty($asterisk['email']) && $asterisk['email'] ? '<font color=red>* </font>' : '' ) . '
			StMU Email:
		</td>
		<td>
			<input type=text name="email" value="' . $_POST['email'] . '">
		</td>
	</tr>

	<tr>
		<td>
			' . (!empty($asterisk['password1']) && $asterisk['password1'] ? '<font color=red>* </font>' : '' ) . '
			Password:
		</td>
		<td>
			<input type=password name="password1">
		</td>
	</tr>

	<tr>
		<td>
			' . (!empty($asterisk['password2']) && $asterisk['password2'] ? '<font color=red>* </font>' : '' ) . '
			Repeat Password:
		</td>
		<td>
			<input type=password name="password2">
		</td>
	</tr>

	<tr>
		<td>
			' . (!empty($asterisk['firstname']) && $asterisk['firstname'] ? '<font color=red>* </font>' : '' ) . '
			First Name:
		</td>
		<td>
			<input type=text name="firstname" value="' . $_POST['firstname'] . '">
		</td>
	</tr>

	<tr>
		<td>
			' . (!empty($asterisk['lastname']) && $asterisk['lastname'] ? '<font color=red>* </font>' : '' ) . '
			Last Name:
		</td>
		<td>
			<input type=text name="lastname" value="' . $_POST['lastname'] . '">
		</td>
	</tr>

	<tr>
		<td>
			Gender:
		</td>
		<td>
			<input type=radio name="gender" value="M" checked=1> Male<br />
			<input type=radio name="gender" value="F" > Female
		</td>
	</tr>

	<tr>
		<td>
			Photo (Not Required):
		</td>
		<td>
			<input type=file name="image">
		</td>
	</tr>

	<tr>
		<td>
			&nbsp;
		</td>
		<td>
			<input type=submit value="Register">
		</td>
	</tr>
	
	
</table>
</form>
	';
}
else
{
	$verification = verification_code();

	$imagefile = "UserImages/Default.jpg";
	if(!empty($_FILES['image']['name']) && strlen($_FILES['image']['name']))
	{
		$imagefile = "UserImages/" . $verification . substr($_FILES['image']['name'],strrpos($_FILES['image']['name'],'.'));
		rename($_FILES['image']['tmp_name'],$imagefile);
	}

	$gender = ($_POST['gender']=="M" ? 1 : 2);
	$query = "INSERT INTO Users SET email='" . $_POST['email'] . "', password='" . $_POST['password1'] . "', firstname='" . $_POST['firstname'] . "', lastname='" . $_POST['lastname'] . "', gender=" . $gender . ", verification='" . $verification . "', image='" . $imagefile . "', active=0, admin=0";
	$result = mysql_query($query);
	if(!$result)
	{
		echo '<center><font color=red>MySQL Query Error. Couldn\'t complete registration.</font></center>';
		include("inc/page_end.php");
		die('');
	}

	include("inc/validation_email.php");
	$message = create_mail_body($_POST['firstname'],$_POST['lastname'],WEBADDRESS,$verification);

	mail($_POST['email'],"Rattler Book Exchange - Verification Email",$message);

	echo 'Success. Please follow the link in the E-Mail sent to ' . $_POST['email'] . ' to activate account.';
}

include("inc/page_end.php");

?>