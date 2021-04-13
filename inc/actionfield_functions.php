<?php

/*
*  Rattler Book Exchange
*
*
*  actionfield_functions.php
*
*
*  This contains the arrangement
*  field functions
*
*
*/



/************************************
*
*  This file contains functions for
*  initializing and displaying the 1
*  action field:
*    Rearrange Field
*
*
************************************/


/************************************
*
*  Creates the arrangement button
*
*  Example Usage:
*  set_arrangement_button(
*				$arrangement_field["primary"]["display"],
*				$arrangement_field["secondary"]["display"],
*				$arrangement_field["primary"]["selected"],
*				$arrangement_field["secondary"]["selected"],
*				$arrangement_field["primary"]["ascending"],
*				$arrangement_field["secondary"]["ascending"]
*			);
*
************************************/
function set_arrangement_button($fields1,$fields2,$selected1=0,$selected2=0,$ascending1=1,$ascending2=1,$target=NULL)
{
	echo '<TABLE border=0 cellpadding=2><TR><TD width=1><form method=POST ' . (empty($target) ? '' : 'target=' . $target) . '><select name="arrangement_p">';
	$sizeofvar = count($fields1);
	for($i=0;$i<$sizeofvar;$i++)
	{
		echo '<option value="' . $i . '" ' . (($selected1 == $i) ? "selected=yes" : "") . '>' . $fields1[$i] . '</option>';
	}
	echo '</select>';
	echo '<input type=hidden name="ft" value=1>';
	echo ' <input type=checkbox value="1" ' . (($ascending1 != 0) ? "checked=yes" : "") . ' name="arrangement_p_asc"><font size=2>Ascending</font></TD>';

	if(!empty($fields2))
	{
		echo '<TD width=1> <select name="arrangement_s">';
		$sizeofvar = count($fields2);
		for($i=0;$i<$sizeofvar;$i++)
		{
			echo '<option value="' . $i . '" ' . (($selected2 == $i) ? "selected=yes" : "") . '>' . $fields2[$i] . '</option>';
		}
		echo '</select> <input type=checkbox value="1" ' . (($ascending2 != 0) ? "checked=yes" : "") . ' name="arrangement_s_asc"><font size=2>Ascending</font></TD>';
	}

	echo '</TR><TR><TD align=center colspan=' . ((empty($fields2) == 1) ? '1' : '2') . '><input type=submit value="Rearrange"></TD></TR></TABLE></form>';
}

/************************************
*
*  Returns an array containing the
*  order of the unsorted data
*
*  Example Usage:
*  $order = get_arrangement_order($G_Scripts,$arrangement_field);
*
************************************/
function get_arrangement_order($unsorted_data,$arrangement_field,$secondary=1)
{
	if(!function_exists("return_order"))
		include("inc/sorting_functions.php");

	if($secondary)
	{
		return return_order(
			$unsorted_data,
			$arrangement_field["primary"]["flag"]
				[$arrangement_field["primary"]["selected"]]
				[$arrangement_field["primary"]["ascending"]] .
			$arrangement_field["secondary"]["flag"]
				[$arrangement_field["secondary"]["selected"]]
				[$arrangement_field["secondary"]["ascending"]],
			$arrangement_field["primary"]["field"]
				[$arrangement_field["primary"]["selected"]],
			$arrangement_field["secondary"]["field"]
				[$arrangement_field["secondary"]["selected"]]
			);
	}

	//Single arrangement
	return return_order(
			$unsorted_data,
			$arrangement_field["primary"]["flag"]
				[$arrangement_field["primary"]["selected"]]
				[$arrangement_field["primary"]["ascending"]],
			$arrangement_Field["primary"]["field"]
				[$arrangement_field["primary"]["selected"]]
			);
}

/************************************
*
*  Will receieve the POST variables
*  for calculating arrangement
*
*  Example Usage:
*  $arrangement_field = get_arrangement_button();
*
************************************/
function get_arrangement_button($ascending1=1,$secondary=1,$ascending2=1)
{
	if(empty($_POST['ft']))
	{
		$arrangement_field["primary"]["ascending"] = $ascending1;
		$arrangement_field["primary"]["selected"] = 0;
		if($secondary)
		{
			$arrangement_field["secondary"]["ascending"] = $ascending2;
			$arrangement_field["secondary"]["selected"] = 0;
		}
	}
	else
	{
		$arrangement_field["primary"]["ascending"] =   empty($_POST['arrangement_p_asc']) ? 0 : $_POST['arrangement_p_asc'];
		$arrangement_field["primary"]["selected"] =        empty($_POST['arrangement_p']) ? 0 : $_POST['arrangement_p'];

		if($secondary)
		{
			$arrangement_field["secondary"]["ascending"] = empty($_POST['arrangement_s_asc']) ? 0 : $_POST['arrangement_s_asc'];
			$arrangement_field["secondary"]["selected"] =      empty($_POST['arrangement_s']) ? 0 : $_POST['arrangement_s'];
		}
	}
	return $arrangement_field;
}


/************************************
*
*  Adds a button to the arrangement
*  field variable you specify
*
*  Example Usage:
*  add_arrangement_button($arrangement_field["primary"],"Name","name","a","A");//1st in 1st box
*  add_arrangement_button($arrangement_field["primary"],"Most Recent","Date","d","D");//2nd in 1st box
*  add_arrangement_button($arrangement_field["secondary"],"Most Recent","Date","d","D");//1st in 2nd box
*  add_arrangement_button($arrangement_field["secondary"],"Name","name","a","A");//2nd in 2nd box
*
************************************/
function add_arrangement_button(&$arrangement_field,$display,$field,$flag_desc='n',$flag_asc='N')
{
	$n = empty($arrangement_field["display"]) ? 0 : count($arrangement_field["display"]);
	$arrangement_field["display"][$n] = $display;
	$arrangement_field["field"][$n]   = $field;
	$arrangement_field["flag"][$n][0] = $flag_desc;
	$arrangement_field["flag"][$n][1] = $flag_asc;
}

?>