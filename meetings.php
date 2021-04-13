<?php

/*
*  Rattler Book Exchange
*
*
*  meetings.php
*
*
*  View Pending and Arrange Meetings
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

$query = "SELECT timewish, locationwish, listingid, final, buyerid, sellerid, saleid FROM Accepts WHERE buyerid=" . $_SESSION['userid'] . " OR sellerid=" . $_SESSION['userid'];
$result = mysql_query($query);
$i = 0;
if($result)
{
	while($row = mysql_fetch_array($result))
	{
		if( empty( $found_ids[ $row['saleid'] ] ) )
		{
			$listing_ids[$i] = $row['listingid'];
			$sales_ids[$i] = $row['saleid'];
			$found_ids[$row['saleid']] = true;
			if($row['buyerid']==$_SESSION['userid'])
			{
				$is_buyer[$row['saleid']] = true;
				$partner_id[$i++] = $row['sellerid'];
			}
			else
			{
				$is_seller[$row['saleid']] = true;
				$partner_id[$i++] = $row['buyerid'];
			}
		}
		if($row['final'])
		{
			$completed_ids[$row['saleid']] = true;
			$datetime[$row['saleid']] = $row['timewish'];
			$location[$row['saleid']] = $row['locationwish'];
		}
	}
	mysql_free_result($result);
}

if($i)
{
	$query = "SELECT id, email, firstname, lastname, gender, image FROM Users WHERE id=" . $partner_id[0];

	for($j=1;$j<$i;$j++)
	{
		$query .= " OR id=" . $partner_id[$j];
	}

	$result = mysql_query($query);
	if($result)
	{
		while($row = mysql_fetch_array($result))
		{
			$userinfo[$row['id']] = $row;
		}
		mysql_free_result($result);
	}

	$query = "SELECT id, title, price FROM Listings WHERE id=" . $listing_ids[0];

	for($j=1;$j<$i;$j++)
	{
		$query .= " OR id=" . $listing_ids[$j];
	}

	$result = mysql_query($query);
	if($result)
	{
		while($row = mysql_fetch_array($result))
		{
			$listinginfo[$row['id']] = $row;
		}
		mysql_free_result($result);
	}

	$query = "SELECT id, haggle, haggleprice FROM Sales WHERE id=" . $sales_ids[0];

	for($j=1;$j<$i;$j++)
	{
		$query .= " OR id=" . $sales_ids[$j];
	}

	$result = mysql_query($query);
	if($result)
	{
		while($row = mysql_fetch_array($result))
		{
			$saleinfo[$row['id']] = $row;
		}
		mysql_free_result($result);
	}

	$query = "SELECT id, name FROM Locations";

	$result = mysql_query($query);
	if($result)
	{
		while($row = mysql_fetch_array($result))
		{
			$locations[$row['id']] = $row;
		}
		mysql_free_result($result);
	}
	$locations[-1]['name'] = "University Center Lounge";
}

print_r($sale_ids);
echo '
<TABLE align=center>
	<TR>
		<TD>
			<BR />
			<center><B><font size=4>Meetings still to be arranged</font></B></center>
		</TD>
	</TR>
	<TR>
		<TD>
			<BR />
			<B>Books you are buying</B>
		</TD>
	</TR>';


for($j=0;$j<$i;$j++)
{
	if(empty($completed_ids[$sales_ids[$j]]) && !empty($is_buyer[$sales_ids[$j]]))
	{
		echo '
	<TR>
		<form action="view_meeting.php" method=POST>
		<input type=hidden name="saleid" value=' . $sales_ids[$j] . '>
		<TD bgcolor=#C0C0C0>
			&nbsp; &nbsp;' . $listinginfo[$listing_ids[$j]]['title'] . '<BR />
			&nbsp; &nbsp; &nbsp; &nbsp;' . $userinfo[$partner_id[$j]]['lastname'] . ', ' . $userinfo[$partner_id[$j]]['firstname'] . '
			&nbsp; &nbsp; &nbsp; &nbsp;' . $userinfo[$partner_id[$j]]['email'] . '<BR />
			&nbsp; &nbsp; &nbsp; &nbsp;Price: $' . $listinginfo[$listing_ids[$j]]['price'] . '.00<BR />
			' . ($saleinfo[$sales_ids[$j]]['haggle'] ? '&nbsp; &nbsp; &nbsp; &nbsp;Haggle Price: $' . $saleinfo[$sales_ids[$j]]['haggleprice'] . '.00<BR />' : '' ) . '
			&nbsp; &nbsp; &nbsp; &nbsp;<input type=submit value="View Messages"><BR />
		</TD>
		</form>
	</TR>';
	}
}

echo '
	<TR>
		<TD>
			<BR />
			<B>Books you are selling</B>
		</TD>
	</TR>';

for($j=0;$j<$i;$j++)
{
	if(empty($completed_ids[$sales_ids[$j]]) && !empty($is_seller[$sales_ids[$j]]))
	{
		echo '
	<TR>
		<form action="view_meeting.php" method=POST>
		<input type=hidden name="saleid" value=' . $sales_ids[$j] . '>
		<TD bgcolor=#C0C0C0>
			&nbsp; &nbsp;' . $listinginfo[$listing_ids[$j]]['title'] . '<BR />
			&nbsp; &nbsp; &nbsp; &nbsp;' . $userinfo[$partner_id[$j]]['lastname'] . ', ' . $userinfo[$partner_id[$j]]['firstname'] . '
			&nbsp; &nbsp; &nbsp; &nbsp;' . $userinfo[$partner_id[$j]]['email'] . '<BR />
			&nbsp; &nbsp; &nbsp; &nbsp;Price: $' . $listinginfo[$listing_ids[$j]]['price'] . '.00<BR />
			' . ($saleinfo[$sales_ids[$j]]['haggle'] ? '&nbsp; &nbsp; &nbsp; &nbsp;Haggle Price: $' . $saleinfo[$sales_ids[$j]]['haggleprice'] . '.00<BR />' : '' ) . '
			&nbsp; &nbsp; &nbsp; &nbsp;<input type=submit value="View Messages"><BR />
		</TD>
		</form>
	</TR>';
	}
}

echo '
	<TR>
		<TD>
			<BR />
			<BR />
			<center><B><font size=4>Scheduled Meetings</font></B></center>
		</TD>
	</TR>
	<TR>
		<TD>
			<BR />
			<B>Books you are buying</B>
		</TD>
	</TR>';

for($j=0;$j<$i;$j++)
{
	if(!empty($completed_ids[$sales_ids[$j]]) && !empty($is_buyer[$sales_ids[$j]]))
	{
		echo '
	<TR>
		<form action="view_meeting.php" method=POST>
		<input type=hidden name="saleid" value=' . $sales_ids[$j] . '>
		<TD bgcolor=#C0C0C0>
			&nbsp; &nbsp;' . $listinginfo[$listing_ids[$j]]['title'] . '<BR />
			&nbsp; &nbsp; &nbsp; &nbsp;' . $userinfo[$partner_id[$j]]['lastname'] . ', ' . $userinfo[$partner_id[$j]]['firstname'] . '
			&nbsp; &nbsp; &nbsp; &nbsp;' . $userinfo[$partner_id[$j]]['email'] . '<BR />
			&nbsp; &nbsp; &nbsp; &nbsp;<img src="' . $userinfo[$partner_id[$j]]['image'] . '" /><BR />
			&nbsp; &nbsp; &nbsp; &nbsp;Gender: ' . ( $userinfo[$partner_id[$j]]['gender']==1 ? 'M' : 'F' ) . '<BR />
			&nbsp; &nbsp; &nbsp; &nbsp;' . ($saleinfo[$sales_ids[$j]]['haggle'] ? 'Haggled Price: $' . $saleinfo[$sales_ids[$j]]['haggleprice'] . '.00<BR />' : 'Price: $' . $listinginfo[$listing_ids[$j]]['price'] . '.00<BR />' ) . '
			&nbsp; &nbsp; &nbsp; &nbsp;<input type=submit value="View Messages"><BR /></form>
			<TABLE align=center>
				<TR>
					<TD bgcolor=#E0A0A0 align=center width=200>
						Date/Time: ' . $datetime[$sales_ids[$j]] . '<BR />
						Location: ' . $locations[$location[$sales_ids[$j]]]['name'] . '<BR />
					</TD>
				</TR>
			</TABLE>
		</TD>
		
	</TR>';
	}
}

echo '
	<TR>
		<TD>
			<BR />
			<B>Books you are selling</B>
		</TD>
	</TR>';

for($j=0;$j<$i;$j++)
{
	if(!empty($completed_ids[$sales_ids[$j]]) && !empty($is_seller[$sales_ids[$j]]))
	{
		echo '
	<TR>
		<form action="view_meeting.php" method=POST>
		<input type=hidden name="saleid" value=' . $sales_ids[$j] . '>
		<TD bgcolor=#C0C0C0>
			&nbsp; &nbsp;' . $listinginfo[$listing_ids[$j]]['title'] . '<BR />
			&nbsp; &nbsp; &nbsp; &nbsp;' . $userinfo[$partner_id[$j]]['lastname'] . ', ' . $userinfo[$partner_id[$j]]['firstname'] . '
			&nbsp; &nbsp; &nbsp; &nbsp;' . $userinfo[$partner_id[$j]]['email'] . '<BR />
			&nbsp; &nbsp; &nbsp; &nbsp;<img src="' . $userinfo[$partner_id[$j]]['image'] . '" /><BR />
			&nbsp; &nbsp; &nbsp; &nbsp;Gender: ' . ( $userinfo[$partner_id[$j]]['gender']==1 ? 'M' : 'F' ) . '<BR />
			&nbsp; &nbsp; &nbsp; &nbsp;' . ($saleinfo[$sales_ids[$j]]['haggle'] ? 'Haggled Price: $' . $saleinfo[$sales_ids[$j]]['haggleprice'] . '.00<BR />' : 'Price: $' . $listinginfo[$listing_ids[$j]]['price'] . '.00<BR />' ) . '
			&nbsp; &nbsp; &nbsp; &nbsp;<input type=submit value="View Messages"></form>
			<TABLE align=center>
				<TR>
					<TD bgcolor=#E0A0A0 align=center width=200>
						Date/Time: ' . $datetime[$sales_ids[$j]] . '<BR />
						Location: ' . $locations[$location[$sales_ids[$j]]]['name'] . '
						<form action="trade.php" method=post>
							<input type=hidden name="saleid" value=' . $sales_ids[$j] . '>
							<input type=submit name="button" value="Trade Completed">
						</form>
					</TD>
				</TR>
			</TABLE>
		</TD>
		</form>
	</TR>';
	}
}

echo '
</TABLE>
<BR />';

?>



<?php include("inc/page_end.php") ?>