<?php

/*
*  Rattler Book Exchange
*
*
*  my_listings.php
*
*
*  Lists all of the user's posted
*  book listings
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

$query = "SELECT id, title, price, haggleable FROM Listings WHERE sellerid=" . $_SESSION['userid'];
$result = mysql_query($query);
if($result)
{
	$query = "SELECT id, listingid, buyerid, haggle, haggleprice FROM Sales WHERE sellerid=" . $_SESSION['userid'];
	$result2 = mysql_query($query);
	if($result2)
	{
		$i = 0;
		while($row = mysql_fetch_array($result2))
		{
			$users[$i++] = $row['buyerid'];
			if(empty($m[$row['listingid']]))
				$m[$row['listingid']] = 0;
			$sales_info[$row['listingid']][ $m[$row['listingid']]++ ] = $row;

			if(!$row['haggle'])
				$buyers[$row['listingid']]++;
			else
				$haggles2[$row['listingid']]++;
		}
		mysql_free_result($result2);
	}

	if($i)
	{
		$query = "SELECT id, firstname, lastname, email FROM Users WHERE id=" . $users[0];
		for($j=1;$j<$i;$j++)
		{
			$query .= " OR id=" . $users[$j];
		}
		$result2 = mysql_query($query);
		if($result2)
		{
			while($row = mysql_fetch_array($result2))
			{
				$users2[$row['id']] = $row;
			}
			mysql_free_result($result2);
		}
	}

	echo '
<BR />
<TABLE align=center cellpadding=2>
	<TR>
		<TD width=250>
			<B>Book Title</B>
		</TD>
		<TD width=100>
			<B>Asking Price</B>
		</TD>
		<TD width=50>
			<B>Buyers</B>
		</TD>
		<TD width=50>
			<B>Haggles</B>
		</TD>
	</TR>';
	while($row = mysql_fetch_array($result))
	{
		echo '
		<TR>
			<TD>
				<a href="edit_listing.php?listingid=' . $row['id'] . '"><font color=black>' . $row['title'] . '</font></a>
			</TD>
			<TD>
				$' . $row['price'] . '.00
			</TD>
			<TD ' . ( !empty($buyers[$row['id']]) ? "bgcolor=#FF9090" : "" ) . '>
				' . ( !empty($buyers[$row['id']]) ? $buyers[$row['id']] : 0 ) . '
			</TD>
			<TD ' . ( !empty($haggles2[$row['id']]) ? "bgcolor=#FFFF00" : "" ) . '>
				' . ( !$row['haggleable'] && empty($haggles[$row['id']]) ? "N/A" : ( !empty($haggles2[$row['id']]) ? $haggles2[$row['id']] : 0 ) ) . '
			</TD>
		</TR>
		<TR>
			<TD colspan=4 bgcolor=#C0C0C0>
<TABLE cellpadding=2>';
		$listingid = $row['id'];
		for($k=0;$k<$m[$row['id']];$k++)
		{
			echo '
	<TR>
		<TD width=100>
			' . $users2[ $sales_info[$listingid][$k]['buyerid'] ]['lastname'] . ', ' . $users2[ $sales_info[$listingid][$k]['buyerid'] ]['firstname'] . '
		</TD>
		<TD width=150>
			' . $users2[ $sales_info[$listingid][$k]['buyerid'] ]['email'] . '
		</TD align=center valign=middle>
		<TD width=80 ' . ($sales_info[$listingid][$k]['haggle'] ? 'bgcolor=#FFFF00' : 'bgcolor=#FF9090') . '>
			' . ( $sales_info[$listingid][$k]['haggle'] ? '$' . $sales_info[$listingid][$k]['haggleprice'] . ' Haggle' : 'Buyer' ) . '
		</TD>
		<form action="trade.php" method=POST>
		<input type=hidden name="saleid" value=' . $sales_info[$listingid][$k]['id'] . '>
		<TD align=center valign=middle>';

			$query = "SELECT final FROM Accepts WHERE listingid=" . $listingid . " AND buyerid=" . $sales_info[$listingid][$k]['buyerid'] . " AND sellerid=" . $_SESSION['userid'];
			$result = mysql_query($query);
			$disp_button = 0;
			if($result)
			{
				while($row2 = mysql_fetch_array($result))
				{
					if(!$disp_button)
						$disp_button = 1;
					if($row2['final'])
						$disp_button = 2;
				}
			}
			if(!$disp_button)
			{
				echo '<input type=submit name="button" value="Start Trade">';
			}
			else if($disp_button==1)
			{
				echo '<input type=submit name="button" value="Continue Trade">';
			}
			else if($disp_button==2)
			{
				echo '<input type=submit name="button" value="Trade Completed">';
			}
			
			echo '<input type=submit name="button" value="Decline">
		</TD>
		</form>
	</TR>';

		}

		echo '
</TABLE>&nbsp;
			</TD>
		</TR>';
	}
	echo '
</TABLE>';
	mysql_free_result($result);
}
else
{
	echo '<center><font color=red>MySQL Error. Couldn\'t retrieve list.</font></center>';
}

?>



<?php include("inc/page_end.php") ?>