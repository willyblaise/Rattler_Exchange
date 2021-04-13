<?php

/*
*  Rattler Book Exchange
*
*
*  navigation.php
*
*
*  Displays left navigation pane
*
*
*/

?>

<a href="index.php">Home</a><BR />
<a href="search.php">Search Listings</a><BR />

<?php

if(logged_in())
{
	$query = "SELECT sellerid, buyerid, haggle FROM Sales WHERE sellerid=" . $_SESSION['userid'] . " OR buyerid=" . $_SESSION['userid'];
	$result = mysql_query($query);
	$sales = 0;
	$haggles = 0;
	$offer_sales = 0;
	$offer_haggles = 0;
	if($result)
	{
		while($row = mysql_fetch_array($result))
		{
			if($row['sellerid']==$_SESSION['userid'])
			{
				if($row['haggle'])
					$haggles++;
				else
					$sales++;
			}
			else
			{
				if($row['haggle'])
					$offer_haggles++;
				else
					$offer_sales++;
			}
		}
		mysql_free_result($result);
	}
	$query = "SELECT listingid, final FROM Accepts WHERE buyerid=" . $_SESSION['userid'] . " OR sellerid=" . $_SESSION['userid'];
	$result = mysql_query($query);
	$pending_meetings = 0;
	$scheduled_meetings = 0;
	if($result)
	{
		$i = 0;
		while($row = mysql_fetch_array($result))
		{
			if(empty($meetings[$row['listingid']]['exists']))
			{
				$i_meetings[$i++] = $row['listingid'];
				$meetings[$row['listingid']]['exists'] = true;
			}
			if($row['final'])
				$meetings[$row['listingid']]['final'] = true;
		}
		mysql_free_result($result);
		for($j=0;$j<$i;$j++)
		{
			if($meetings[$i_meetings[$j]]['final'])
				$scheduled_meetings++;
			else
				$pending_meetings++;
		}
	}
	$query = "SELECT id FROM Declines WHERE buyerid=" . $_SESSION['userid'];
	$result = mysql_query($query);
	$num_declines = 0;
	if($result)
	{
		while($row = mysql_fetch_array($result))
			$num_declines++;
		mysql_free_result($result);
	}
	echo '<BR />
	<a href="editinfo.php">Edit Account Info</a><BR />
	<a href="add_listing.php">Add Listing</a><BR />
	<a href="my_listings.php">My Listings</a> (' . $sales . ' - ' . $haggles . ')<BR />
	<a href="my_offers.php">My Offers</a> (' . $offer_sales . ' - ' . $offer_haggles . ' - ' . $num_declines . ')<BR />
	<a href="meetings.php">Meetings</a> (' . $pending_meetings . ' - ' . $scheduled_meetings . ')<BR />
';
}

if(is_admin())
{
	echo '<BR />
	<a href="admincontrol.php">Admin Control Panel</a><BR />';
}

?>