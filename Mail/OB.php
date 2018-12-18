<?php
/*
 * Copyright 2018, LEE& COMPANY, INC.
 *
 *     http://waffle.at
 *
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

	$mbox_home	=	__DIR__;
	require     $mbox_home . '/vendor/autoload.php';
	require     $mbox_home . '/mbox.php';

	$DEBUG = false; 
	$subjects =	[
		"OB-E.html" => "[GIBH] THANK YOU FOR VISITING GOOSE ISLAND BREWHOUSE",
		"OB-K.html" => "[GIBH] THANK YOU FOR VISITING GOOSE ISLAND BREWHOUSE",
	];
	
	$email_template = [
		"OB-E.html" => [
			"{{name}}"	=>	"Customer Name",
						],
		"OB-K.html" => [
			"{{name}}"	=>	"Customer Name",
						],

	];

	$mbox = new mbox(__DIR__ . '/OB.ini');

	$mbox->mail->SetFrom($mbox->mail_props['setFrom'], '[GIBH]');
	$BCC 	= $mbox->mail_props['setFrom'];
	
 	$conn = mysqli_connect(
                $mbox->mail_props['db.host'],
                $mbox->mail_props['db.id'],
        		$mbox->mail_props['db.pwd'],
        		$mbox->mail_props['db.db']);

	//$sql = "select distinct * from ob_email where `SNS User Email` is not null limit 5"; 
	$sql = "select distinct `SNS User Email`, `Nation` from ob_email";
        print($sql."\n");

    	$result = mysqli_query($conn, $sql);
        $inx =0;

        while($row = mysqli_fetch_array($result)) {
		$t_name = "OB-E.html";
		if($row["Nation"] === "REPUBLIC OF KOREA") $t_name = "OB-K.html";

		$mbox->mail->ClearAllRecipients();
		$mbox->mail->addAddress($row["SNS User Email"]);

$mbox->mail->addBCC($BCC);
$mbox->mail->addBCC("at54street@gmail.com");
$mbox->mail->addBCC("kwangmin.lee@waffle.at");

		$mbox->mail->Subject =	$subjects[$t_name];
		$mbox->mail->SMTPDebug = 0;
		if($DEBUG) {
			//print($row["Connect Time"] 	. " ");
			//print($row["SNS Type"] 		. " ");
			//print($row["SNS User Name"]	. " ");
			//print($row["SNS User ID"]	. " ");
			//print($row["Birthday"] 		. " ");
			//print($row["Gender"] 		. " ");
			print($row["SNS User Email"]	. " ");
			print($row["Nation"] . "\n");
		}

		//$email_template[$t_name]["{{name}}"] = $row["SNS User Name"];
		$mbox->setTemplate($t_name, $email_template[$t_name]);
		$mbox->mail->send();
		print("A mail[". $t_name . "] sent to =>" . $row["SNS User Email"] . " \n"); 
        }
        mysqli_close($conn);
?>
