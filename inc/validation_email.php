<?php

function create_mail_body($firstname,$lastname,$webaddress,$verification)
{
	return "
=========================

  Rattler Book Exchange

=========================

Dear " . $firstname . " " . $lastname . ",

This is an automated E-Mail from the Rattler Book Exchange <" . $webaddress . ">.


The account registered with this E-Mail is not activated untill you click the link below:

http://" . $webaddress . "/verification.php?vcode=" . $verification . "

	";
}

?>