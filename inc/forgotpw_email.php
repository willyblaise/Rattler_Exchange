<?php

function create_fp_mail_body($firstname,$lastname,$webaddress,$password)
{
	return "
=========================

  Rattler Book Exchange

=========================

Dear " . $firstname . " " . $lastname . ",

This is an automated E-Mail from the Rattler Book Exchange <" . $webaddress . ">.


Someone has requested this E-Mail to contain the password registered to this E-Mail.


===
Password
===

" . $password . "

	";
}

?>