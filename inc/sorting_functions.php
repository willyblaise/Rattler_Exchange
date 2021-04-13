<?php

/*
*  Rattler Book Exchange
*
*
*  sorting_functions.php
*
*
*  This contains sorting functions
*  for different variable types
*
*
*/

if(!function_exists("return_order")){

/************************************
*
*  Sorting functions
*
*  Return arrays that contain the order
*  of the items you specified
*
*  Main Function:
*    return_order($array,$flags,$column1=NULL,$column2=NULL);
*
*
************************************/


/************************************
*  $flags[] - string
*  flags:
*   A - alphabetical (A - Z)
*   a - alphabetical (Z - A)
*   N - numerical (Ascending)
*   n - numerical (Descending)
*   D - date (Newest to Oldest)
*   d - date (Oldest to Newest)
*
*   example:
*   $flags = "n" will arrange in numerical order
*   $flags = "na" will arrange in numerical order and alphabetical order secondarily
*
*
*  Example Usage:
  $var[0]["IQ"] = 100;
  $var[0]["name"] = "Cindy"
  $var[1]["IQ"] = 120;
  $var[1]["name"] = "John"
  $var[2]["IQ"] = 115;
  $var[2]["name"] = "Ashley"
  $var[3]["IQ"] = 106;
  $var[3]["name"] = "Jacob"
  $var[4]["IQ"] = 100;
  $var[4]["name"] = "Carl"
  $var[5]["IQ"] = 100;
  $var[5]["name"] = "Kevin"
  $var[6]["IQ"] = 84;
  $var[6]["name"] = "Jeremy"
  $var[7]["IQ"] = 120;
  $var[7]["name"] = "Amy"
  $var[8]["IQ"] = 100;
  $var[8]["name"] = "Jason"

  //Arrange Descending by IQ (highest IQ on top) and secondarily by name (A-Z) (if 2 or more IQs are the same)
  $order = return_order($var,"nA","IQ","name");

  for($i=0;$i<count($order);$i++)
      echo '<BR>IQ: ' . $var[$order[$i]]["IQ"] . ' - ' . $var[$order[$i]]["name"];
*
************************************/
function return_order($array,$flags,$column1=NULL,$column2=NULL)
{
	$order[0] = -1;
	if(empty($flags[1]))
	{
		switch($flags[0])
		{
			default: $flag = 1; $ascending=0; break;
			case 'N': $flag = 1; $ascending=1; break;
			case 'a': $flag = 2; $ascending=0; break;
			case 'A': $flag = 2; $ascending=1; break;
			case 'D': $flag = 3; $ascending=0; break;
			case 'd': $flag = 3; $ascending=1; break;
		}
		$order = _return_single_order($array,$flag,$column1,$ascending);
	}
	else
	{
		switch($flags[0])
		{
			default: $flag[0] = 1; $ascending[0]=0; break;
			case 'N': $flag[0] = 1; $ascending[0]=1; break;
			case 'a': $flag[0] = 2; $ascending[0]=0; break;
			case 'A': $flag[0] = 2; $ascending[0]=1; break;
			case 'D': $flag[0] = 3; $ascending[0]=0; break;
			case 'd': $flag[0] = 3; $ascending[0]=1; break;
		}
		switch($flags[1])
		{
			default: $flag[1] = 1; $ascending[1]=0; break;
			case 'N': $flag[1] = 1; $ascending[1]=1; break;
			case 'a': $flag[1] = 2; $ascending[1]=0; break;
			case 'A': $flag[1] = 2; $ascending[1]=1; break;
			case 'D': $flag[1] = 3; $ascending[1]=0; break;
			case 'd': $flag[1] = 3; $ascending[1]=1; break;
		}
		$order = _return_dual_order($array,$flag[0],$flag[1],$column1,$column2,$ascending[0],$ascending[1]);
	}

	return $order;
}

//$array[$i];
function return_numeric_order($variable,$ascending=1)
{
	$array[0] = -1;
	$sizeofvar = count($variable);
	for($i=0;$i<$sizeofvar;$i++)
	{
		$array[$i] = $i;
	}

	$done = false;
	$end = 0;
	while(!$done)
	{
		$done = true;
		for($i=0;$i<$sizeofvar - 1;$i++)
		{
			if(
			($ascending && $variable[$array[$i]]>$variable[$array[$i + 1]]) ||
			(!$ascending && $variable[$array[$i]]<$variable[$array[$i + 1]])
			)
			{
				$Temp = $array[$i];
				$array[$i] = $array[$i + 1];
				$array[$i + 1] = $Temp;
				$done = false;
			}
		}
		$end++;
	}

	return $array;
}

//$2d_array[$i][$column]
function return_numeric_order2($variable,$column,$ascending=1)
{
	$array[0] = -1;
	$sizeofvar = count($variable);
	for($i=0;$i<$sizeofvar;$i++)
	{
		$array[$i] = $i;
	}

	$done = false;
	$end = 0;
	while(!$done)
	{
		$done = true;
		for($i=0;$i<$sizeofvar - $end - 1;$i++)
		{
			if(
			($ascending && $variable[$array[$i]][$column]>$variable[$array[$i + 1]][$column]) ||
			(!$ascending && $variable[$array[$i]][$column]<$variable[$array[$i + 1]][$column])
			)
			{
				$Temp = $array[$i];
				$array[$i] = $array[$i + 1];
				$array[$i + 1] = $Temp;
				$done = false;
			}
		}
		$end++;
	}

	return $array;
}

//Newest to Oldest by default
//$date_array[$i]['month', etc.];
function return_date_order($date_variables,$ascending=0)
{
	$array[0] = -1;
	if(!function_exists("subtract_date_arrays"))
		include("date_functions.php");

	$sizeofvar = count($date_variables);
	for($i=0;$i<$sizeofvar;$i++)
	{
		$array[$i] = $i;
	}

	$done = false;
	$end = 0;
	while(!$done)
	{
		$done = true;
		for($i=0;$i<$sizeofvar - $end - 1;$i++)
		{
			if(
			($ascending && date_in_minutes($date_variables[$array[$i]])>date_in_minutes($date_variables[$array[$i + 1]])) || 
			(!$ascending && date_in_minutes($date_variables[$array[$i]])<date_in_minutes($date_variables[$array[$i + 1]]))
			)
			{
				$Temp = $array[$i];
				$array[$i] = $array[$i + 1];
				$array[$i + 1] = $Temp;
				$done = false;
			}
		}
		$end++;
	}

	return $array;
}

//Newest to Oldest by default
//$date_array[$i][$column]['month', etc.];
function return_date_order2($date_variables,$column,$ascending=0)
{
	$array[0] = -1;
	if(!function_exists("subtract_date_arrays"))
		include("date_functions.php");

	$sizeofvar = count($date_variables);
	for($i=0;$i<$sizeofvar;$i++)
	{
		$array[$i] = $i;
	}

	$done = false;
	$end = 0;
	while(!$done)
	{
		$done = true;
		for($i=0;$i<$sizeofvar - $end - 1;$i++)
		{
			if(
			($ascending && date_in_minutes($date_variables[$array[$i]][$column])>date_in_minutes($date_variables[$array[$i + 1]][$column])) || 
			(!$ascending && date_in_minutes($date_variables[$array[$i]][$column])<date_in_minutes($date_variables[$array[$i + 1]][$column]))
			)
			{
				$Temp = $array[$i];
				$array[$i] = $array[$i + 1];
				$array[$i + 1] = $Temp;
				$done = false;
			}
		}
		$end++;
	}

	return $array;
}

//Returns true if $left comes before $right in alphabetic order
function comes_first($left,$right)
{
	$array[0] = -1;
	$i=0;
	if(strlen($left)<strlen($right))
		$i = strlen($left);
	else
		$i = strlen($right);

	for($j=0;$j<$i;$j++)
	{
		if($left[$j]<$right[$j])
			return true;
		else if($left[$j]>$right[$j])
			return false;
	}

	//if $right[0,$i-1]==$left[0,$i-1] and $left is shorter
	//then left comes before right still
	if($i==strlen($left))
		return true;

	return false;
}

//A to Z
//$strings[$i];
function return_alphabetical_order($strings,$ascending=1)
{
	$array[0] = -1;
	$sizeofvar = count($strings);
	for($i=0;$i<$sizeofvar;$i++)
	{
		$array[$i] = $i;
	}

	$done = false;
	$end = 0;
	while(!$done)
	{
		$done = true;
		for($i=0;$i<$sizeofvar - $end - 1;$i++)
		{
			$left = strtolower($strings[$array[$i]]);
			$right = strtolower($strings[$array[$i + 1]]);
			if(
			($ascending && !comes_first($left,$right)) ||
			(!$ascending && comes_first($left,$right))
			)
			{
				$Temp = $array[$i];
				$array[$i] = $array[$i + 1];
				$array[$i + 1] = $Temp;
				$done = false;
			}
		}
		$end++;
	}

	return $array;
}

//$strings[$i][$column];
function return_alphabetic_order2($strings,$column,$ascending=1)
{
	$array[0] = -1;
	$sizeofvar = count($strings);
	for($i=0;$i<$sizeofvar;$i++)
	{
		$array[$i] = $i;
	}

	$end = 0;
	do
	{
		$done = true;
		for($i=0;$i<$sizeofvar - $end - 1;$i++)
		{
			$left = strtolower($strings[$array[$i]][$column]);
			$right = strtolower($strings[$array[$i + 1]][$column]);
			if(
			($ascending && !comes_first($left,$right)) ||
			(!$ascending && comes_first($left,$right))
			)
			{
				$Temp = $array[$i];
				$array[$i] = $array[$i + 1];
				$array[$i + 1] = $Temp;
				$done = false;
			}
		}
		$end++;
	} while(!$done);

	return $array;
}

//$order:
//1 = Number
//2 = Alphabetic
//3 = Date
function _return_single_order($array,$order,$column=NULL,$ascending=1)
{
	$order2[0] = -1;
	if($column)
	{
		switch($order)
		{
			case 1: $order2 = return_numeric_order2($array,$column,$ascending); break;
			case 2: $order2 = return_alphabetic_order2($array,$column,$ascending); break;
			case 3: $order2 = return_date_order2($array,$column,$ascending); break;
		}
	}
	else
	{
		switch($order)
		{
			case 1: $order2 = return_numeric_order($array,$ascending); break;
			case 2: $order2 = return_alphabetic_order($array,$ascending); break;
			case 3: $order2 = return_date_order($array,$ascending); break;
		}
	}

	return $order2;
}

//$order:
//1 = Number
//2 = Alphabetic
//3 = Date
function _return_dual_order($array,$order1,$order2,$column1=NULL,$column2=NULL,$ascending1=1,$ascending2=1)
{
	$r_order1[0] = -1;
	$r_order2[0] = -1;
	if($column1)
	{
		switch($order1)
		{
			case 1: $r_order1 = return_numeric_order2($array,$column1,$ascending1); break;
			case 2: $r_order1 = return_alphabetic_order2($array,$column1,$ascending1); break;
			case 3: $r_order1 = return_date_order2($array,$column1,$ascending1); break;
		}
	}
	else
	{
		switch($order1)
		{
			case 1: $r_order1 = return_numeric_order($array,$ascending1); break;
			case 2: $r_order1 = return_alphabetic_order($array,$ascending1); break;
			case 3: $r_order1 = return_date_order($array,$ascending1); break;
		}
	}

	if($column2)
	{
		switch($order2)
		{
			case 1: $r_order2 = return_numeric_order2($array,$column2,$ascending2); break;
			case 2: $r_order2 = return_alphabetic_order2($array,$column2,$ascending2); break;
			case 3: $r_order2 = return_date_order2($array,$column2,$ascending2); break;
		}
	}
	else
	{
		switch($order2)
		{
			case 1: $r_order2 = return_numeric_order($array,$ascending2); break;
			case 2: $r_order2 = return_alphabetic_order($array,$ascending2); break;
			case 3: $r_order2 = return_date_order($array,$ascending2); break;
		}
	}

	if(!function_exists("get_order"))
	{
		function get_order($index,$orderarray)
		{
			$sizeofvar = count($orderarray);
			for($i = 0;$i<$sizeofvar;$i++)
				if($orderarray[$i]==$index)
					return $i;
			return -1;
		}
	}

	$end = -1;
	$sizeofvar = count($array);
	do
	{
		$start = $end + 1;
		for($end += 1;$end<$sizeofvar - 1;$end++)
		{
			if(!$column1)
			{
				if($array[$r_order1[$end]]!=$array[$r_order1[$end + 1]])
				{
					break;
				}
			}
			else
			{
				if($array[$r_order1[$end]][$column1]!=$array[$r_order1[$end + 1]][$column1])
				{
					break;
				}
			}
		}

		for($i=$start;$i<=$end - 1;$i++)
		{
			for($j=$i + 1;$j<=$end;$j++)
			{
				//echo 'Checking ' . $array[$r_order1[$i]][$column2] . '(' . get_order($r_order1[$i],$r_order2) . ') and ' . $array[$r_order1[$j]][$column2] . '(' . get_order($r_order1[$j],$r_order2) . ')<BR>';
				if(get_order($r_order1[$i],$r_order2) > get_order($r_order1[$j],$r_order2))
				{
					//echo 'Switching ' . $array[$r_order1[$i]][$column2] . ' and ' . $array[$r_order1[$j]][$column2] . '<BR>';
					$val = $r_order1[$j];
					$r_order1[$j] = $r_order1[$i];
					$r_order1[$i] = $val;
				}
			}
		}
	} while($end<$sizeofvar - 1);

	return $r_order1;
}

}
?>