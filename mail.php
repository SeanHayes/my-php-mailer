<?php
	echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Send Mail</title>
</head>
<body>
<?php
	$eol = "\r\n";
	$mime_boundary = md5(time());
	
	//Format:	[day of week], [day of month] [Month] [Year] [hour]:[minute]:[second] ±[time zone offset]
	//Example:	Fri, 23 Jan 2009 00:07:51 -0500
	$date = $_POST['date'];
	$from = $_POST['from'];
	$to = $_POST['to'];
	$cc = $_POST['cc'];
	$bcc = $_POST['bcc'];
	$subject = $_POST['subject'];
	
	//Construct Headers
	$headers = 'Date: '.$date.$eol;
	$headers .= 'From: '.$from.$eol;
	if($cc != '')
	{
		$headers .= 'Cc: '.$cc.$eol;
	}
	if($bcc != '')
	{
		$headers .= 'Bcc: '.$bcc.$eol;
	}
	$headers .= 'Reply-To: '.$from.$eol;
	$headers .= 'Return-Path: '.$from.$eol;
	$headers .= 'Message-ID: <'.time().'-'.$from.'>'.$eol;
	$headers .= 'X-Mailer: PHP'.$eol;
	// Boundry for marking the split & Multitype Headers
	$headers .= 'MIME-Version: 1.0'.$eol;
	//$headers .= 'Content-Type: multipart/mixed; boundary="'.$mime_boundary.'"'.$eol.$eol;
	$headers .= 'Content-type: multipart/mixed; boundary="'.$mime_boundary.'"';
	
	//Construct message
	$message = '--'.$mime_boundary.$eol;
	$htmlalt_mime_boundary = $mime_boundary.'_htmlalt';
	
	$message .= 'Content-Type: multipart/alternative; boundary="'.$htmlalt_mime_boundary.'"'.$eol.$eol;
	//Contruct message body
	$html = $_POST['message'];
	$html = str_replace(
		array('<br />','</li>','</ul>','</ol>','</tr>','</table>','<h1>','</h1>','<h2>','</h2>','<h3>','</h3>'),
		array('<br />'."\n",'</li>'."\n",'</ul>'."\n",'</ol>'."\n",'</tr>'."\n",'</table>'."\n","\n".'<h1>','</h1>'."\n","\n".'<h2>','</h2>'."\n","\n".'<h3>','</h3>'."\n"),
		$html);
	//Contruct text version of html
	$message .= '--'.$htmlalt_mime_boundary.$eol;
	$message .= 'Content-Type: text/plain; charset=utf-8'.$eol;
	$message .= 'Content-Transfer-Encoding: 8bit'.$eol.$eol;
	$txt = $html;
	$txt = str_replace('<li>', '*', $txt);
	$txt = str_replace(array('<td>','<th>'), "\t", $txt);
	$txt = htmlspecialchars_decode(strip_tags($txt));
	$message .= $txt.$eol.$eol;
	
	//Contruct html version of body
	$message .= '--'.$htmlalt_mime_boundary.$eol;
	$message .= 'Content-Type: text/html; charset=utf-8'.$eol;
	$message .= 'Content-Length: text/html; charset=utf-8'.$eol;
	$message .= 'Content-Transfer-Encoding: 8bit'.$eol.$eol;
	$message .= $html.$eol.$eol;
	
	//finish message body
	$message .= '--'.$htmlalt_mime_boundary.'--'.$eol.$eol;
	
	//Construct attachments.
	$files = $_FILES;
	$attachment_list = array();
	foreach($files as $key => $val)
	{
		$name = $files[$key]['name'];
		$tmp_name = $files[$key]['tmp_name'];
		$type = $files[$key]['type'];
		$error = $files[$key]['error'];
		$size = $files[$key]['size'];
		if($error == 0 && $size > 0)
		{
			//Open file
			$fHandle = fopen($tmp_name, 'r');
			if($fHandle != false)
			{
				//Read file and base64 encode it
				$fContent = fread($fHandle, filesize($tmp_name));
				$fContent = chunk_split(base64_encode($fContent));
				//Add headers
				$message .= '--'.$mime_boundary.$eol;
				$message .= 'Content-Type: '.$type.'; name="'.$name.'"'.$eol;
				$message .= 'Content-Transfer-Encoding: base64'.$eol;
				$message .= 'Content-Description: '.$name.$eol;
				$message .= 'Content-Disposition: attachment; filename="'.$name.'"'.$eol.$eol;
				//Add file, base64 encoded
				$message .= $fContent.$eol.$eol;
				$attachment_list[] = $name;
			}
		}
		//delete file here
		if(file_exists($tmp_name))
		{
			unlink($tmp_name);
		}
	}
	$message .= '--'.$mime_boundary.'--'.$eol.$eol;
	echo "\n<h2>Headers</h2>\n<p>".str_replace($eol, '<br />', htmlspecialchars($headers)).'</p>';
	echo "\n<h2>Message (text)</h2>\n<p>".$txt.'</p>';
	echo "\n<h2>Attachments</h2>\n<ol>";
	for($i = 0; $i < count($attachment_list); $i++)
	{
		echo '<li>'.$attachment_list[$i].'</li>';
	}
	echo '</ol>';
	$mail_sent = mail($to, $subject, $message, $headers);
	echo "<h2>Status</h2>\n";
	if($mail_sent)
	{
		echo '<p>Your email has been successfully sent.</p>';
	}
	else
	{
		echo '<p>There was an error with your email.</p>';
	}

?>
</body>
</html>
