<?php
/*
 * Copyright 2018, LEE& COMPANY, INC. 
 *
 *     http://waffle.at
 *
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class mbox
{
  public $mail;
  public $mail_props;

  public $Address;
  public $CC;
  public $BCC;

  public function __construct($filepath)
  {
	$this->mail_props 		= parse_ini_file($filepath); 

	$this->mail = new PHPMailer(true);
    $this->mail->isSMTP();
    $this->mail->SMTPDebug 	= 		0;
	$this->mail->ContentType= 		"text/html";  
    $this->mail->CharSet	=		"UTF-8"; 
    $this->mail->Encoding 	= 		"base64";
    $this->mail->Host =           	$this->mail_props['Host'];
    $this->mail->Port =           	$this->mail_props['Port'];
    $this->mail->SMTPSecure =     	$this->mail_props['SMTPSecure'];
    $this->mail->SMTPAuth =       	$this->mail_props['SMTPAuth'];
    $this->mail->Username =       	$this->mail_props['Username'];
    $this->mail->Password =       	$this->mail_props['Password'];
    $this->mail->setFrom          	($this->mail_props['setFrom']);

    $this->mail->isHTML           	($this->mail_props['isHtml']);
    $this->mail->Subject =       	$this->mail_props['Subject'];
    $this->mail->Body =      		$this->mail_props['Body'];

	/*
    foreach ($this->mail_props['Attachment'] as $attach) {
        $this->mail->addAttachment($attach);
    }
	*/

  }

  /**
   * Return PHPMailer class.
   * @return PHPMailer
   */
  public function getPHPMailer()
  {
    return $this->mail;
  }

  public function setSubject($subject)
  {
    $this->mail->Subject =		$subject;
  }

  /**
   * set Email Body.
   * @return 
   */
  public function setBody($email_body)
  {
    $this->mail->Body =		$email_body;
  }

  /**
   * set Email Body.
   * @return 
   */
  public function getHtmlMsgBody($fields, $result)
  {
	$dbtable = $this->getTableContent($fields, $result);
	$msgbody = "
		<html>
			<head></head>
			<body>

				$dbtable

			</body>
		</html>
	";
	return $msgbody;
  }

  public function setTemplate($fname, $inlines) {

	

	$conts = file_get_contents($fname, __DIR__);
	foreach($inlines as $key => $inline) {
		$conts = str_replace($key,$inline,$conts);
	}
	$this->setBody($conts);
  }


  public function getHtmlMsgBody2($fields, $result) 
  {
	$dbtable = $this->getTableContent($fields, $result);
	$template = file_get_contents('email.html', __DIR__);
	$template = str_replace('{{Greeting}}',	'안녕하세요? ', $template);
	$template = str_replace('{{Information1}}','Waffle 쿠폰 사용 현황은 다음과 같습니다', $template);
	$template = str_replace('{{Information2}}', $dbtable, $template);

	$template = str_replace('{{GO}}','세부정보확인', $template);

	$template = str_replace('{{Information3}}','본 서비스에 대해서 추가적인 궁금한점이 있으시면 고객 지원 센터로 연락 부탁합니다', $template);
	$template = str_replace('{{Goodbye}}','Good luck! Hope it works', $template);
	return $template;
  }

	
  public function getTableContent($fields, $result) {
    $body =     '<table class="w3-table">';
    $body.=     '<tr class="w3-light-grey">';
   
    foreach ($fields as $field) {
        $body.= "<th>" . $field . "</th>";
    }
    $body.=     "</tr>";

	while($row = mysqli_fetch_array($$this->result)) {
        $body.=     "<tr>";
        foreach ($fields as $field) {
            $body.= "<td>" . $row[$field] . "</td>";
        }
        $body.=     "</tr>";
    }
	return $body;
  }

  public function getCampainData($num)
  {
    $conn = mysqli_connect(
		$this->mail_props['db.host'],
		$this->mail_props['db.id'],
      	$this->mail_props['db.pwd'],
      	$this->mail_props['db.db']);

 	$sql = $this->mail_props["camp$num.sql"];
    $result = mysqli_query($conn, $sql);
	$body = $this->getTableContent($this->mail_props["camp$num.field"],$result);
	return $body;
  }

  /**
   * return execute Campaign 
   * @return 
   */
  public function campaign($num, $subject)
  {
	$this->mail->Subject = $subject;
    //$this->setSubject("Daily Report of ". date("Y/m/d"));

	switch($num)  {
    	case 1: 
    		$inlines = array(
        		"{{inline1}}" => "Daily Statistics Information",
        		"{{inline2}}" => date('l jS \of F Y h:i:s A'),
        		"{{inline3}}" => "---------------------------------------------- ",
        		"{{inline4}}" => $this->getCampainData($num),
    		);
			$this->setTemplate('email1.html', $inlines);
			break;
		case 2:
		case 3:
    		$inlines = array(
        		"[CLIENTS.COMPANY_NAME]" => "LEE& Company",
        		"[Writer]" => 	"Jason Park",
        		"[[LIST_VIEW]]" => $this->getCampainData($num),
    		);
			$this->setTemplate('email2.html', $inlines);
			break;
		case 4:
    		$inlines = array(
        		"{{inline1}}" => "일간 통계 정보",
        		"{{inline2}}" => date_default_timezone_set('UTC'),
        		"{{inline3}}" => "---------------------------------------------- ",
        		"{{inline4}}" => $this->getCampainData($num),
    		);
			$this->setTemplate('email3.html', $inlines);
			break;
		default:
    		$inlines = array(
        		"{{inline1}}" => "월간 방문자/이메일 현황 통계 정보",
        		"{{inline2}}" =>  date('F Y'),
        		"{{inline3}}" => "---------------------------------------------- ",
        		"{{inline4}}" => $this->getCampainData($num),
    		);
			$this->setTemplate('email3.html', $inlines);
			break;
	}
  }

  /**
   * send emails.
   * @return Result
   */
  public function send()
  {
    return $this->mail->send();
  }

  public function send_ind() 
  {
	foreach ($this->Address as $addr) {
		$this->mail->ClearAllRecipients();
		$this->mail->addAddress ( $addr );
		$this->mail->send();
	}

	foreach ($this->CC as $addr) {
		$this->mail->ClearAllRecipients();
		$this->mail->addCC ( $addr );
		$this->mail->send();
	}

	foreach ($this->BCC as $addr) {
		$this->mail->ClearAllRecipients();
		$this->mail->addCC ( $addr );
		$this->mail->send();
	}
  }

  public function dbconn() {
    $conn = mysqli_connect(
		$this->mail_props['db.host'],
		$this->mail_props['db.id'],
      	$this->mail_props['db.pwd'],
      	$this->mail_props['db.db']);
	return $conn;
  }

  public function sendFromDB($emails, $tname, $colsub, $colcont) 
  {
	$conn = $this->dbconn();
	$sql = "select $colsub, $colcont from $tname";
	print($sql."\n");

    $result = mysqli_query($conn, $sql);
	$inx =0;
	while($row = mysqli_fetch_array($result)) {
    	$this->mail->Subject =       	$row[$colsub];
    	$this->mail->Body =        		$row[$colcont];
		$this->mail->ClearAllRecipients();
		$this->mail->AddAddress($emails[$inx]);
		$inx++;
		$this->mail->send();
	}
	mysqli_close($conn);
  }

  public function getAddressFromDB($tname, $colname) 
  {
	$conn = $this->dbconn();
 	$sql = "select $colname from $tname";
    $result = mysqli_query($conn, $sql);
	$add_list = array();
	while($row = mysqli_fetch_array($result)) {
		array_push($add_list, $row[$colname]);
	}
	mysqli_close($conn);
	return $add_list;
  }

  public function setAddress($add_list)
  {
	$this->mail->ClearAddresses();
	$this->Address = $add_list;
  }

  public function setCC($add_list)
  {
	$this->mail->ClearCCs();
	$this->CC = $add_list;
  }

  public function setBCC($add_list)
  {
	$this->mail->ClearBCCs();
	$this->BCC = $add_list;
  }

  public function addAddress($add_list)
  {
	foreach($add_list as $addr) { array_push($this->Address, $addr); }
  }

  public function addCC($add_list)
  {
	foreach($add_list as $addr) { array_push($this->CC, $addr); }
  }

  public function addBCC($add_list)
  {
	foreach($add_list as $addr) { array_push($this->BCC, $addr); }
  }

  public function gencoupons() {
	$conn = $this->dbconn();
	$sql="CREATE TABLE IF Not Exists Coupons( 
                               coupon  int(11) unsigned NOT NULL, 
                               userid  varchar(30) default null, 
                               email   varchar(45) default null, 
                               created_at timestamp DEFAULT CURRENT_TIMESTAMP, 
                               issued_at  datetime default null, 
                               sent_at    datetime default null, 
                               used_at    datetime default null,
							   primary key(coupon)
					)";
	print("$sql\n");
	if(mysqli_query($conn, $sql)) {
		print("Coupons created!\n");
	} else {
		print("Error Coupons table:".mysqli_error($conn));
	}

    $arr = array();
	$num = 10000;
    while ($num > count($arr)) {
        $i = rand(1000000, 9999999);
        $arr[$i] = $i;
    }


    $sql = "insert into Coupons (coupon) values(?)";
    $stmt = $conn->prepare($sql);

    foreach($arr as $c) {
        $stmt->bind_param("i", $c);
        $stmt->execute();
    }
    mysqli_close($conn);
  }


  public function getcoupon($userid, $email) {
	$conn = $this->dbconn();
	$sql = "select coupon from Coupons 
				where userid like '$userid' and
				email like '$email' limit 1";
	print("$sql\n");

	$coupon = 0;
	if($result = mysqli_query($conn, $sql)) {
		$row = mysqli_fetch_row($result);
		$coupon = $row[0];	
		print("coupon:$coupon \n");
	} else {
		print("No coupon related to ($userid, $email) \n");
	}

	if($coupon==0) {
		$sql = "Select coupon from Coupons 
					where userid is null and email is null limit 1";
		if($result = mysqli_query($conn, $sql)) {
			$row = mysqli_fetch_row($result);
			$coupon = $row[0];	
		}

		print("coupon:$coupon \n");
		if($coupon <> 0) {
			$sql = "update Coupons set userid = '$userid' ,
					email = '$email' where coupon = $coupon"; 
			print("$sql\n");
			if(mysqli_query($conn, $sql)) {
				print("coupon[$coupon] is generated for user[$userid, $email] \n");
			} else {
				print("something wrong !!! \n");
			}
		}

	}
    mysqli_close($conn);
	return $coupon;
  }

  public function getvisitors() {
	$sql = "SELECT id, 
				name as sns, 
				email, 
				time as collect_dt, 
				if(update_time=null, 1,2) as visit_cnt,
				ifnull(update_time, ifnull(time,'-')) as visit_dt,
				if(update_time=null, '1st visit', 'Re-visit') as mail_ty,
				ifnull(null,'-') as coupon_num,
				ifnull(null,'-') as coupon_mail_dt,  
				ifnull(null,'-') as coupon_issue_dt,
				ifnull(null,'-') as coupon_sent_dt, 
				ifnull(null,'-') as coupon_used_dt 
			FROM User";
	print("$sql\n");

	$conn = $this->dbconn();
    $result = mysqli_query($conn, $sql);
	$visitors = array();
	while($row = mysqli_fetch_array($result)) {
		$visitor = new visitor(
			$row['id'],
			$row['sns'],
			$row['email'],
			$row['collect_dt'],
			$row['visit_cnt'],
			$row['visit_dt'],
			$row['mail_ty'],
			$row['coupon_num'],
			$row['coupon_mail_dt'],
			$row['coupon_issue_dt'],
			$row['coupon_sent_dt'],
			$row['coupon_used_dt']
		);
		array_push($visitors, $visitor);
	}
	mysqli_close($conn);
	return $visitors;
  }

  public function assignCoupons($visitors) {
	foreach ($visitors as $visitor) {
		$visitor->coupon_num = $this->getcoupon($visitor->sns, $visitor->email);
		print("$visitor->coupon_num \n");
    }
  	return $visitors;
  }

  public function getvisitorsWithCoupons() {
	$visitors = $this->getvisitors();
	foreach ($visitors as $visitor) {
		$visitor->coupon_num = $this->getcoupon($visitor->sns, $visitor->email);
		print("$visitor->coupon_num \n");
    }
	return $visitors;
  }






}
