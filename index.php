<?php
	echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Send Mail</title>
</head>
<body>
<form method="post" action="mail.php" enctype="multipart/form-data">
	Date: <input id="date" name="date" value="<?php echo date('r'); ?>"/><br />
	From: <input id="from" name="from" value=""/><br />
	To: <input id="to" name="to" value=""/><br />
	Cc: <input id="cc" name="cc" value=""/><br />
	Bcc: <input id="bcc" name="bcc" value=""/><br />
	Subject: <input id="subject" name="subject" value=""/><br />
	Post: <br />
	<textarea id="message" name="message" rows="15" cols="20"></textarea><br />
	Attachments: <br />
	<input id="attachment1" name="attachment1" type="file" /><br />
	<input id="attachment2" name="attachment2" type="file" /><br />
	<input type="submit" value="Submit"/>
</form>
</body>
</html>
