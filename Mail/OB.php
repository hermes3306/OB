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
	//$BCC 	= "kwangmin.lee@waffle.at";
	$CC 	= $mbox->mail_props['setFrom'];
	
 	$conn = mysqli_connect(
                $mbox->mail_props['db.host'],
                $mbox->mail_props['db.id'],
        	$mbox->mail_props['db.pwd'],
        	$mbox->mail_props['db.db']);

	$sql = "select distinct * from ob_email where `SNS User Email` is not null limit 5"; 
        print($sql."\n");

    	$result = mysqli_query($conn, $sql);
        $inx =0;

        while($row = mysqli_fetch_array($result)) {
		$t_name = "OB-E.html";
		if($row["Nation"] === "REPUBLIC OF KOREA") $t_name = "OB-K.html";
		print("html:" . $t_name);

		$mbox->mail->ClearAllRecipients();
		$mbox->mail->addAddress("at54street@gmail.com");
		$mbox->mail->addAddress("XXXX.XXXX." . $row["SNS User Email"]);
		//$mbox->mail->addBCC($BCC);
		$mbox->mail->addCC($CC);
		$mbox->mail->Subject =	$subjects[$t_name];

		$mbox->mail->SMTPDebug = 1;

		//$email_template[$t_name]["{{name}}"] = $row["SNS User Name"];
		$mbox->setTemplate($t_name, $email_template[$t_name]);
		$mbox->mail->send();

		print($row["Connect Time"] 	. " ");
		print($row["SNS User Email"]	. " ");
		print($row["SNS Type"] 		. " ");
		print($row["SNS User Name"]	. " ");
		print($row["SNS User ID"]	. " ");
		print($row["Birthday"] 		. " ");
		print($row["Gender"] 		. " ");
		print($row["Nation"] . "\n");
        }
        mysqli_close($conn);

?>
