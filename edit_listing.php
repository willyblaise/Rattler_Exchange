<?php

/*
*  Rattler Book Exchange
*
*
*  edit_listing.php
*
*
*  Edit or delete a book listing
*
*  Calls inc/page_start.php
*  Calls inc/page_end.php
*
*  GET variables
*    listingid
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

if(empty($_GET['listingid']))
{
	echo '<center><font color=red>Redirection Error.</font></center><meta http-equiv="refresh" content="2;url=index.php">';
	include("inc/page_end.php");
	die('');
}

if(!empty($_POST['title']) && !empty($_POST['button']) && $_POST['button']=="Update Book Listing")
{
	if(empty($_POST['author']) || empty($_POST['isbn']) || empty($_POST['edition']) || empty($_POST['price']))
	{
		echo '<font color=red>You have left some fields blank.</font><BR />';
	}
	else if(!is_numeric($_POST['edition']))
	{
		echo '<font color=red>Invalid Edition</font><BR />';
	}
	else if(!is_numeric($_POST['price']) || $_POST['price']<=0)
	{
		echo '<font color=red>Invalid Price</font><BR />';
	}
	else
	{
		$query = "UPDATE Listings SET sellerid=" . $_SESSION['userid'] . ", title='" . $_POST['title'] . "', author='" . $_POST['author'] . "', ISBN='" . $_POST['isbn'] . "', edition='" . $_POST['edition'] . "', quality=" . $_POST['quality'] . ", haggleable=" . $_POST['haggleable'] . ", price=" . $_POST['price'] . " WHERE id=" . $_GET['listingid'];
		$result = mysql_query($query);
		if($result)
		{
			//TODO: rename image and delete old image
			echo 'Listing Updated.';
		}
		else
		{
			echo '<font color=red>MySQL Error. Listing could not be updated.</font><BR />';
		}
	}
}
else if(!empty($_POST['button']) && $_POST['button']=="Delete Book Listing")
{
	$query = "DELETE FROM Listings WHERE sellerid=" . $_SESSION['userid'] . " AND id=" . $_GET['listingid'];
	$result = mysql_query($query);
	$query = "DELETE FROM Sales WHERE sellerid=" . $_SESSION['userid'] . " AND listingid=" . $_GET['listingid'];
	$result2 = mysql_query($query);
	$query = "DELETE FROM Accepts WHERE sellerid=" . $_SESSION['userid'] . " AND listingid=" . $_GET['listingid'];
	$result3 = mysql_query($query);

	if($result && $result2 && $result3)
	{
		echo 'Book Listing Deleted.';
	}
	else
	{
		echo '<center><font color=red>Error Deleting Book Listing.</font></center>';
	}
	include("inc/page_end.php");
	die('');
}

$query = "SELECT title, author, ISBN, edition, image, quality, haggleable, price FROM Listings WHERE id=" . $_GET['listingid'];
$result = mysql_query($query);
if($result && mysql_num_rows($result)==1)
{
	$row = mysql_fetch_array($result);
	mysql_free_result($result);
}
else
{
	echo '<center><font color=red>Invalid Listing ID.</font></center><meta http-equiv="refresh" content="2;url=index.php">';
	mysql_free_result($result);
	include("inc/page_end.php");
	die('');
	
}

?>

<form action=<?php echo '"edit_listing.php?listingid=' . $_GET['listingid'] . '"'; ?> method=POST enctype="multipart/form-data">
<input type = hidden name = "MAX_FILE_SIZE" value = 100000000>
<TABLE cellpadding=10>
	<TR>
		<TD>
			Title
		</TD>
		<TD>
			<input name="title" size=40 <?php echo ( empty($row['title']) ?  '' : 'value="' . $row['title'] . '"'  ); ?>>
		</TD>
	</TR>
	<TR>
		<TD>
			Author
		</TD>
		<TD>
			<input name="author" size=40 <?php echo ( empty($row['author']) ?  '' : 'value="' . $row['author'] . '"'  ); ?>>
		</TD>
	</TR>
	<TR>
		<TD>
			ISBN<BR />
			<font size=2>(10 or 13 digits)</font>
		</TD>
		<TD>
			<input name="isbn" size=14 maxlength=16 <?php echo ( empty($row['ISBN']) ?  '' : 'value="' . $row['ISBN'] . '"'  ); ?>>
		</TD>
	</TR>
	<TR>
		<TD>
			Edition
		</TD>
		<TD>
			<input name="edition" STYLE="width:20;" maxlength=2 <?php echo ( empty($row['edition']) ?  '' : 'value="' . $row['edition'] . '"'  ); ?>>
		</TD>
	</TR>
	<TR>
		<TD>
			Quality
		</TD>
		<TD>
			<select name="quality">
				<option value=1 <?php echo ( (!empty($row['quality']) && $row['quality']==1) ?  'selected=yes' : ''  ); ?>>New</option>
				<option value=2 <?php echo ( (!empty($row['quality']) && $row['quality']==2) ?  'selected=yes' : ''  ); ?>>Barely Used</option>
				<option value=3 <?php echo ( (empty($row['quality']) || $row['quality']==3) ?  'selected=yes' : ''  ); ?>>Fair</option>
				<option value=4 <?php echo ( (!empty($row['quality']) && $row['quality']==4) ?  'selected=yes' : ''  ); ?>>Poor</option>
			</select>
		</TD>
	</TR>
	<TR>
		<TD>
			Asking Price
		</TD>
		<TD>
			$<input name="price" size=5 STYLE="width:30;" maxlength=3 <?php echo ( empty($row['price']) ?  '' : 'value="' . $row['price'] . '"'  ); ?>>.00
		</TD>
	</TR>
	<TR>
		<TD>
			Haggleable
		</TD>
		<TD>
			<input type=radio name="haggleable" <?php echo ( ($row['haggleable']==1) ?  'checked=yes' : ''  ); ?> value=1> Yes <input type=radio name="haggleable" <?php echo ( (empty($row['haggleable']) || $row['haggleable']==0) ?  'checked=yes' : ''  ); ?> value=0> No
		</TD>
	</TR>
	<TR>
		<TD>
			Image<BR />
			<font size=2>(Optional)</font>
			echo image
		</TD>
		<TD>
			<input type=file name="image">
		</TD>
	</TR>
	<TR>
		<TD>
			&nbsp;
		</TD>
		<TD>
			<input type=submit value="Update Book Listing" name="button">
			<input type=submit value="Delete Book Listing" name="button">
		</TD>
	</TR>
</TABLE>
</form>

<?php include("inc/page_end.php") ?>