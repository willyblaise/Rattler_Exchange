<?php

/*
*  Rattler Book Exchange
*
*
*  search.php
*
*
*  Search book listings page
*
*  Calls inc/page_start.php
*  Calls inc/page_end.php
*  Calls inc/actionfield_functions.php
*    Calls inc/sorting_functions.php
*
*
*/


include("inc/page_start.php");

?>

<BR />
<TABLE>
	<TR>
		<TD>
<form action="search.php" method=GET>
<TABLE cellpadding=2 align=center>
	<TR>
		<TD>
			Title
		</TD>
		<TD>
			<input name="title" size=40 <?php echo ( empty($_GET['title']) ?  '' : 'value="' . $_GET['title'] . '"'  ); ?>>
		</TD>
	</TR>
	<TR>
		<TD>
			Author
		</TD>
		<TD>
			<input name="author" size=40 <?php echo ( empty($_GET['author']) ?  '' : 'value="' . $_GET['author'] . '"'  ); ?>>
		</TD>
	</TR>
	<TR>
		<TD>
			ISBN<BR />
			<font size=2>(10 or 13 digits)</font>
		</TD>
		<TD>
			<input name="isbn" size=14 maxlength=16 <?php echo ( empty($_GET['isbn']) ?  '' : 'value="' . $_GET['isbn'] . '"'  ); ?>>
		</TD>
	</TR>
	<TR>
		<TD>
			Edition
		</TD>
		<TD>
			<input name="edition" STYLE="width:20;" maxlength=2 <?php echo ( empty($_GET['edition']) ?  '' : 'value="' . $_GET['edition'] . '"'  ); ?>>
		</TD>
	</TR>
	<TR>
		<TD>
			&nbsp;
		</TD>
		<TD>
			<input type=submit value="Search Listings">
		</TD>
	</TR>
</TABLE>
</form>
		</TD>
		<TD width=250 align=right>

<?php

$i = 0;

if(!empty($_GET['title']))
{
	$query = "SELECT id, image, title, author, ISBN, edition, quality, haggleable, price FROM Listings WHERE title LIKE '%" . $_GET['title'] . "%'";
	$result = mysql_query($query);

	if($result)
	{
		while($row = mysql_fetch_array($result))
		{
			$matches[$i] = $row;
			$matches[$i++]['relevance']=5;
		}
		mysql_free_result($result);
	}
}
if(!empty($_GET['author']))
{
	$query = "SELECT id, image, title, author, ISBN, edition, quality, haggleable, price FROM Listings WHERE author LIKE '%" . $_GET['author'] . "%'";
	$result = mysql_query($query);

	if($result)
	{
		while($row = mysql_fetch_array($result))
		{
			$newmatch = true;
			for($j=0;$j<$i;$j++)
			{
				if($matches[$j]['id']==$row['id'])
				{
					$matches[$j]['relevance'] += 3;
					$newmatch=false;
					break;
				}
			}
			if($newmatch)
			{
				$matches[$i] = $row;
				$matches[$i++]['relevance'] = 3;
			}
		}
		mysql_free_result($result);
	}
}
if(!empty($_GET['edition']))
{
	for($j=0;$j<$i;$j++)
	{
		if($matches[$j]['edition']==$_GET['edition'])
		{
			$matches[$j]['relevance']++;
		}
	}
}
if(!empty($_GET['isbn']))
{
	$query = "SELECT id, image, title, author, ISBN, edition, quality, haggleable, price FROM Listings WHERE ISBN2 LIKE '%" . str_replace("-","",str_replace(" ","",$_GET['isbn'])) . "%'";
	$result = mysql_query($query);

	if($result)
	{
		while($row = mysql_fetch_array($result))
		{
			$newmatch = true;
			for($j=0;$j<$i;$j++)
			{
				if($matches[$j]['id']==$row['id'])
				{
					$matches[$j]['relevance'] += 10;
					$newmatch=false;
					break;
				}
			}
			if($newmatch)
			{
				$matches[$i] = $row;
				$matches[$i++]['relevance'] = 10;
			}
		}
		mysql_free_result($result);
	}
}

$query = "SELECT listingid, haggle FROM SALES";
$result = mysql_query($query);

for($j=0;$j<$i;$j++)
{
	$matches[$j]['haggles'] = 0;
	$matches[$j]['sales'] = 0;
}
if($result)
{
	while($row = mysql_fetch_array($result))
	{
		for($j=0;$j<$i;$j++)
		{
			if($matches[$j]['id']==$row['listingid'])
			{
				if($row['haggle'])
					$matches[$j]['haggles']++;
				else
					$matches[$j]['sales']++;
				break;
			}
		}
	}
	mysql_free_result($result);
}

include("inc/actionfield_functions.php");

$arrangement_field = get_arrangement_button(0,1,1);

//Add arrangement variables to the first box
add_arrangement_button($arrangement_field["primary"],"Relevance","relevance","n","N");
add_arrangement_button($arrangement_field["primary"],"Price","price","n","N");
add_arrangement_button($arrangement_field["primary"],"Quality","quality","n","N");

//Add arrangement variables to the second box
add_arrangement_button($arrangement_field["secondary"],"Price","name","a","A");
add_arrangement_button($arrangement_field["secondary"],"Relevance","relevance","n","N");
add_arrangement_button($arrangement_field["secondary"],"Quality","quality","n","N");

//Get the order from the arrangement

set_arrangement_button(
	$arrangement_field["primary"]["display"],
	$arrangement_field["secondary"]["display"],
	$arrangement_field["primary"]["selected"],
	$arrangement_field["secondary"]["selected"],
	$arrangement_field["primary"]["ascending"],
	$arrangement_field["secondary"]["ascending"]
);

echo '
		</TD>
	</TR>
</TABLE>';

if(!empty($matches))
	$order = get_arrangement_order($matches,$arrangement_field);
else
	$order[0] = -1;

$size = count($order);
if($order[0]==-1)
	$size = 0;

for($i=0;$i<$size;$i++)
{
	echo '
<TABLE border=1 align=center cellpadding=4>
	<TR>
		<TD colspan=3 bgcolor=#AAFFAA>
			Price: $' . $matches[$order[$i]]['price'] . '.00
		</TD>
	</TR>
	<TR>
		<TD width=200>
			' . $matches[$order[$i]]['title'] . '<BR />
			' . $matches[$order[$i]]['author'] . '<BR />
			' . $matches[$order[$i]]['edition'] . edition_string( $matches[$order[$i]]['edition']) . ' Edition<BR />
			ISBN: ' . $matches[$order[$i]]['ISBN'] . '
		</TD>
		<TD width=75 align=center>
			' . quality_picture($matches[$order[$i]]['quality']) . '<BR>' . quality_string($matches[$order[$i]]['quality']) . '
		</TD>
		<TD width=150>
			' . (!empty($matches[$order[$i]]['sales']) ? $matches[$order[$i]]['sales'] : 0 ) . ' Prospective Buyers<BR />
			' . ($matches[$order[$i]]['haggleable'] ? (!empty($matches[$order[$i]]['haggles']) ? $matches[$order[$i]]['haggles'] : 0 ) . ' Pending Haggles' : '') . '
		</TD>
	</TR>
	<TR>
		<TD colspan=3>
			' . (empty($matches[$order[$i]]['image']) ? 'No Image Available' : '<img src="' . $matches[$order[$i]]['image'] . '">' ) . '
		</TD>
	</TR>
	<TR>
		<form action="buy.php" method=GET>
		<input type=hidden name="listingid" value=' . $matches[$order[$i]]['id'] . '>
		<TD>
			<input type=submit value="Buy">
		</TD>
		</form>
		<form action="haggle.php" method=GET>
		<input type=hidden name="listingid" value=' . $matches[$order[$i]]['id'] . '>
		<TD colspan=2>
			' . ($matches[$order[$i]]['haggleable'] ? '$<input type=text name="haggleprice" size=3 maxlen=3>.00 <input type=submit value="Haggle">' : '&nbsp;' ) . '
		</TD>
		</form>
	</TR>
</TABLE>
<BR />
	';
}

?>

<?php include("inc/page_end.php") ?>