<?php

/*
*  Rattler Book Exchange
*
*
*  editinfo.php
*
*
*  Users can edit their info
*  from this page.
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

if(!empty($_POST['submitted']) && $_POST['submitted']==2)
{
	echo '<center>';
	if(empty($_POST['password_old']))
	{
		echo '<font color=red>Password required to change user info.</font>';
	}
	else
	{
		$query = "SELECT password FROM Users WHERE id=" . $_SESSION['userid'];
		$result = mysql_query($query);
		if($result && mysql_num_rows($result)==1)
		{
			$row = mysql_fetch_array($result);
			if($row['password']==$_POST['password_old'])
			{
				$query = "UPDATE Users SET gender=" . $_POST['gender'];
				if(!empty($_POST['firstname']))
					$query .= ", firstname='" . $_POST['firstname'] . "'";
				if(!empty($_POST['lastname']))
					$query .= ", lastname='" . $_POST['lastname'] . "'";
				if(!empty($_POST['password_new1']))
				{
					if(!empty($_POST['password_new2']) && $_POST['password_new1']==$_POST['password_new2'])
						$query .= ", password='" . $_POST['password_new1'] . "'";
					else
						echo '<font color=red>New Passwords didn\'t match.</font><BR />';
				}
				if(!empty($_FILES['image']['name']) && strlen($_FILES['image']['name']))
				{
					$quick_query = "SELECT image FROM Users WHERE id=" . $_SESSION['userid'];
					$result2 = mysql_query($quick_query);
					if($result2)
					{
						$row = mysql_fetch_array($result2);
						$oldpic = $row['image'];
						mysql_free_result($result2);
					}
					$verification = verification_code();
					$imagefile = "UserImages/" . $verification . substr($_FILES['image']['name'],strrpos($_FILES['image']['name'],'.'));
					rename($_FILES['image']['tmp_name'],$imagefile);
					$query .= ", image='" . $imagefile . "'";
				}

				$query .= " WHERE id=" . $_SESSION['userid'];
				$result2 = mysql_query($query);
				if($result2)
				{
					if(!empty($oldpic))
						unlink($oldpic);
					echo 'Successfully Updated Account Information';
				}
				else
				{
					echo '<font color=red>MySQL Error</font>';
				}
			}
			else
			{
				echo '<font color=red>Password Mismatch.</font>';
			}
		}
		else
		{
			echo '<font color=red>MySQL Error</font>';
		}
		if($result)
			mysql_free_result($result);
	}
	echo '</center>';
}

$query = "SELECT firstname, lastname, gender, image FROM Users WHERE id=" . $_SESSION['userid'];
$result = mysql_query($query);
if($result)
{
	$row = mysql_fetch_array($result);
	mysql_free_result($result);
}

?>


<form action="editinfo.php" method=post enctype="multipart/form-data">
<input type=hidden name="MAX_FILE_SIZE" value=100000000>
<input type=hidden name="submitted" value=2>

<table border=1>
	<tr>
		<td colspan=2 bgcolor=#FFFF00>
			<font color=#0000FF>Password Required to change account information.
		</td>
	</tr>
	<tr>
		<td>
			Password:
		</td>
		<td>
			<input type=password name="password_old">
		</td>
	</tr>
	<tr>
		<td colspan=2 bgcolor=#FF0000>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td>
			New Password (Optional):
		</td>
		<td>
			<input type=password name="password_new1">
		</td>
	</tr>
	<tr>
		<td>
			New Password Repeat:
		</td>
		<td>
			<input type=password name="password_new2">
		</td>
	</tr>
	<tr>
		<td>
			First Name:
		</td>
		<td>
			<input type=text name="firstname" value=<?php echo '"' . $row['firstname'] . '"'; ?>>
		</td>
	</tr>
	<tr>
		<td>
			Last Name:
		</td>
		<td>
			<input type=text name="lastname" value=<?php echo '"' . $row['lastname'] . '"'; ?>>
		</td>
	</tr>
	<tr>
		<td>
			Gender:
		</td>
		<td>
			<input type=radio name="gender" value=1 <?php if($row['gender']!=2) echo 'checked=yes'; ?>> Male<BR />
			<input type=radio name="gender" value=2 <?php if($row['gender']==2) echo 'checked=yes'; ?>> Female
		</td>
	</tr>
	<tr>
		<td>
			Photo (Not Required):
			<?php
			if(strlen($row['image']))
				echo '<BR /><img src="' . $row['image'] . '">';
			?>
		</td>
		<td>
			<input type=file name="image" value="' . $_FILES['image']['name'] . '">
		</td>
	</tr>
	<tr>
		<td>
			&nbsp;
		</td>
		<td>
			<input type=submit value="Update Info">
		</td>
	</tr>
</table>
</form>

<?php include("inc/page_end.php") ?>