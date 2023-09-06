<?php

function sendMail($rcpt,$subject,$mailfrom,$text)
{
    require_once(ROOT.DS.'inc'.DS.'PHPMailer.php');
    require_once(ROOT.DS.'inc'.DS.'Parsedown.php');
    $p = new Parsedown;
    $mail = new PHPMailer();

    ob_start();

    $mail->CharSet   = 'UTF-8';
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = EMAIL_SMTP_HOST;                    // Set the SMTP server to send through
    if(defined('EMAIL_SMTP_USER') && EMAIL_SMTP_USER!='')
    {
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = EMAIL_SMTP_USER;                     // SMTP username
        $mail->Password   = EMAIL_SMTP_PW;                               // SMTP password
    }
    else
        $mail->SMTPAuth   = false;
    
    if(defined('EMAIL_SMTP_SECURE') && EMAIL_SMTP_SECURE===true)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
    else
    {
        $mail->SMTPSecure = false;
        $mail->SMTPAutoTLS = false;
    }
    $mail->Port       = EMAIL_SMTP_PORT;                                    // TCP port to connect to

    //Recipients
    $noreply = 'noreply@'.substr(EMAIL_ALTERNATIVE,strpos(EMAIL_ALTERNATIVE,'@')+1);
    $mail->addReplyTo($mailfrom);
    $mail->SetFrom($noreply);
    if(strpos($rcpt,',')!==false)
        $rcpt = explode(',',$rcpt);
    if(is_array($rcpt))
        foreach($rcpt as $add)
            $mail->addAddress($add);
    else
        $mail->addAddress($rcpt);     // Add a recipient

    if(defined('SMTP_EHLO_DOMAIN') && SMTP_EHLO_DOMAIN)
        $mail->Hostname = SMTP_EHLO_DOMAIN;
    
    $mail->SMTPOptions = [
        'socket' => [
            'bindto' => "0:0",
        ],
    ];

    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $p->text($text);
    $mail->AltBody = $text;

    $mail->send();

    $output = ob_get_clean();

    return $output;
}

function getUserIP()
{
	$client  = @$_SERVER['HTTP_CLIENT_IP'];
	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote  = $_SERVER['REMOTE_ADDR'];
	
    if(strpos($forward,','))
    {
        $a = explode(',',$forward);
        $forward = trim($a[0]);
    }
	if(filter_var($forward, FILTER_VALIDATE_IP))
	{
		$ip = $forward;
	}
    elseif(filter_var($client, FILTER_VALIDATE_IP))
	{
		$ip = $client;
	}
	else
	{
		$ip = $remote;
	}
	return $ip;
}

function gen_uuid() {
  return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      // 32 bits for "time_low"
      mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

      // 16 bits for "time_mid"
      mt_rand( 0, 0xffff ),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 4
      mt_rand( 0, 0x0fff ) | 0x4000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      mt_rand( 0, 0x3fff ) | 0x8000,

      // 48 bits for "node"
      mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
  );
}

/*
* @param $path string Path to the file that should be uploaded
* @param $hash string Optional. File name we want on pictshare for the file
*/
function pictshareUploadImage($path,$hash=false)
{
    if(!file_exists($path)) return false;
    $request = curl_init('https://pictshare.net/api/upload.php');
    
    curl_setopt($request, CURLOPT_POST, true);
    curl_setopt(
        $request,
        CURLOPT_POSTFIELDS,
        array(
        'file' => curl_file_create($path),
        'hash'=>$hash
        ));

    // output the response
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    $json = json_decode(curl_exec($request).PHP_EOL,true);

    // close the session
    curl_close($request);

    return $json;
}