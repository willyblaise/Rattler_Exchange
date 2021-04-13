<?php

/*
*  Rattler Book Exchange
*
*
*  add_listing.php
*
*
*  Add a book listing
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

if(!empty($_POST['title']))
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
		$query = "INSERT INTO Listings SET sellerid=" . $_SESSION['userid'] . ", title='" . $_POST['title'] . "', author='" . $_POST['author'] . "', ISBN='" . $_POST['isbn'] . "', ISBN2='" . str_replace("-","",str_replace(" ","",$_POST['isbn'])) . "', edition='" . $_POST['edition'] . "', quality=" . $_POST['quality'] . ", haggleable=" . $_POST['haggleable'] . ", price=" . $_POST['price'];
		$result = mysql_query($query);
		if($result)
		{
			echo 'Listing Added.';
			$_POST['title'] = NULL;
			$_POST['author'] = NULL;
			$_POST['isbn'] = NULL;
			$_POST['edition'] = NULL;
			$_POST['quality'] = NULL;
			$_POST['price'] = NULL;
			$_POST['haggleable'] = NULL;
		}
		else
		{
			echo '<font color=red>MySQL Error. Listing could not be added.</font><BR />';
		}
	}
}

?>

<form action="add_listing.php" method=POST enctype="multipart/form-data">
<input type = hidden name = "MAX_FILE_SIZE" value = 100000000>
<TABLE cellpadding=10>
	<TR>
		<TD>
			Title
		</TD>
		<TD>
			<input name="title" size=40 <?php echo ( empty($_POST['title']) ?  '' : 'value="' . $_POST['title'] . '"'  ); ?>>
		</TD>
	</TR>
	<TR>
		<TD>
			Author
		</TD>
		<TD>
			<input name="author" size=40 <?php echo ( empty($_POST['author']) ?  '' : 'value="' . $_POST['author'] . '"'  ); ?>>
		</TD>
	</TR>
	<TR>
		<TD>
			ISBN<BR />
			<font size=2>(10 or 13 digits)</font>
		</TD>
		<TD>
			<input name="isbn" size=14 maxlength=16 <?php echo ( empty($_POST['isbn']) ?  '' : 'value="' . $_POST['isbn'] . '"'  ); ?>>
		</TD>
	</TR>
	<TR>
		<TD>
			Edition
		</TD>
		<TD>
			<input name="edition" STYLE="width:20;" maxlength=2 <?php echo ( empty($_POST['edition']) ?  '' : 'value="' . $_POST['edition'] . '"'  ); ?>>
		</TD>
	</TR>
	<TR>
		<TD>
			Quality
		</TD>
		<TD>
			<select name="quality">
				<option value=1 <?php echo ( (!empty($_POST['quality']) && $_POST['quality']==1) ?  'selected=yes' : ''  ); ?>>New</option>
				<option value=2 <?php echo ( (!empty($_POST['quality']) && $_POST['quality']==2) ?  'selected=yes' : ''  ); ?>>Barely Used</option>
				<option value=3 <?php echo ( (empty($_POST['quality']) || $_POST['quality']==3) ?  'selected=yes' : ''  ); ?>>Fair</option>
				<option value=4 <?php echo ( (!empty($_POST['quality']) && $_POST['quality']==4) ?  'selected=yes' : ''  ); ?>>Poor</option>
			</select>
		</TD>
	</TR>
	<TR>
		<TD>
			Asking Price
		</TD>
		<TD>
			$<input name="price" size=5 STYLE="width:30;" maxlength=3 <?php echo ( empty($_POST['price']) ?  '' : 'value="' . $_POST['price'] . '"'  ); ?>>.00
		</TD>
	</TR>
	<TR>
		<TD>
			Haggleable
		</TD>
		<TD>
			<input type=radio name="haggleable" <?php echo ( (empty($_POST['haggleable']) || $_POST['haggleable']==1) ?  'checked=yes' : ''  ); ?> value=1> Yes <input type=radio name="haggleable" <?php echo ( (!empty($_POST['haggleable']) && $_POST['haggleable']==0) ?  'checked=yes' : ''  ); ?> value=0> No
		</TD>
	</TR>
	<TR>
		<TD>
			Image<BR />
			<font size=2>(Optional)</font>
		</TD>
		<TD>
			<input type=file name="image" <?php echo ( !empty($_FILES['image']['name']) ?  'value="' . $_FILES['image']['name'] . '"' : ''  ); ?>>
		</TD>
	</TR>
	<TR>
		<TD>
			&nbsp;
		</TD>
		<TD>
			<input type=submit value="Add Book Listing">
		</TD>
	</TR>
</TABLE>
</form>

<?php include("inc/page_end.php") ?>