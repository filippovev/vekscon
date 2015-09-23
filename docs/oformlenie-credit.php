<?php
session_start();
$admin = 'info@vekscon.ru, inwiz@mail.ru';
if ( isset( $_POST['sendMail'] ) ) {
  $email   = substr( $_POST['email'], 0, 64 );
  $subject = substr( $_POST['subject'], 0, 64 );
  $name  = substr( $_POST['name'], 0, 64 );
  $company = substr( $_POST['company'], 0, 64 );
  $gos = substr( $_POST['gos'], 0, 64 );
  $org = substr( $_POST['org'], 0, 64 );
  $data = substr( $_POST['data'], 0, 94 );
  $kon = substr( $_POST['kon'], 0, 64 );
  $ssilka = substr( $_POST['ssilka'], 0, 64 );
  $na = substr( $_POST['na'], 0, 64 );
  $fone = substr( $_POST['fone'], 0, 64 );
  $message = substr( $_POST['message'], 0, 250 );
  
  $error = '';
  if($_SESSION['secpic']!==strtolower($_POST['secpic'])){
  $error = $error.'<li class="red">Неправильно введен защитный код</li>';
  }
  if ( !empty( $error ) ) {
    $_SESSION['sendMailForm']['error']   = '<p>При заполнении формы были допущены ошибки:</p><ul>'.$error.'</ul>';
    $_SESSION['sendMailForm']['email']   = $email;
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();
  }

  if ( !empty( $_FILES['file']['tmp_name'] ) and $_FILES['file']['error'] == 0 ) {
    $filepath = $_FILES['file']['tmp_name'];
    $filename = $_FILES['file']['name'];
  } else {
    $filepath = '';
    $filename = '';
  }
  
  $body = "НАИМЕНОВАНИЕ ОРГАНИЗАЦИИ:\r\n".$name."\r\n\r\n";
  $body = "НАИМЕНОВАНИЕ ОРГАНИЗАЦИИ:\r\n".$company."\r\n\r\n";
  $body = "НАИМЕНОВАНИЕ ОРГАНИЗАЦИИ:\r\n".$data."\r\n\r\n";
  $body .= "ПРЕДМЕТ ГОСКОНТРАКТА:\r\n".$org."\r\n\r\n";
  $body .= "СТОИМОСТЬ ГОСКОНТРАКТА:\r\n".$gos."\r\n\r\n";
  $body .= "НЕОБХОДИМЫЙ ОБЪЕМ КРЕДИТОВАНИЯ:\r\n".$kon."\r\n\r\n";
  $body .= "СРОК КРЕДИТОВАНИЯ:\r\n".$ssilka."\r\n\r\n";
  $body .= "E-MAIL:\r\n".$email."\r\n\r\n";
  $body .= "ФАМИЛИЯ:\r\n".$subject."\r\n\r\n";
  $body .= "ИМЯ:\r\n".$na."\r\n\r\n";
  $body .= "КОНТАКТНЫЙ ТЕЛЕФОН:\r\n".$fone."\r\n\r\n";
  $body .= "ДОПОЛНИТЕЛЬНАЯ ИНФОРМАЦИЯ:\r\n".$message;
  
  if ( send_mail($admin, $body, $email, $filepath, $filename) )
    $_SESSION['success'] = true;
  else
    $_SESSION['success'] = false;
  header( 'Location: '.$_SERVER['PHP_SELF'] );
  die();
}

// Вспомогательная функция для отправки почтового сообщения с вложением 
function send_mail($admin, $body, $email, $filepath, $filename) 
{ 
  $subject = '=?UTF-8?B?'.base64_encode('КРЕДИТ НА ИСПОЛНЕНИЕ').'?=';
  $boundary = "--".md5(uniqid(time())); // генерируем разделитель
  $headers = "From: ".strtoupper($_SERVER['SERVER_NAME'])." <".$email.">\r\n";    
  $headers .= "Return-path: <".$email.">\r\n";
  $headers .= "MIME-Version: 1.0\r\n"; 
  $headers .="Content-Type: multipart/mixed; boundary=\"".$boundary."\"\r\n"; 
  $multipart = "--".$boundary."\r\n"; 
  $multipart .= "Content-type: text/plain; charset=\"utf-8\"\r\n"; 
  $multipart .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";

  $body = quoted_printable_encode( $body )."\r\n\r\n";
  
  $multipart .= $body;
  
  $file = '';
  if ( !empty( $filepath ) ) {
    $fp = fopen($filepath, "r"); 
    if ( $fp ) { 
      $content = fread($fp, filesize($filepath)); 
      fclose($fp);
      $file .= "--".$boundary."\r\n"; 
      $file .= "Content-Type: application/octet-stream\r\n"; 
      $file .= "Content-Transfer-Encoding: base64\r\n"; 
      $file .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n"; 
      $file .= chunk_split(base64_encode($content))."\r\n"; 
    }
  }
  $multipart .= $file."--".$boundary."--\r\n";

  if( mail($admin, $subject, $multipart, $headers) ) 
    return true;
  else
    return false;
}

function quoted_printable_encode ( $string ) {
   // rule #2, #3 (leaves space and tab characters in tact)
   $string = preg_replace_callback (
   '/[^\x21-\x3C\x3E-\x7E\x09\x20]/',
   'quoted_printable_encode_character',
   $string
   );
   $newline = "=\r\n"; // '=' + CRLF (rule #4)
   // make sure the splitting of lines does not interfere with escaped characters
   // (chunk_split fails here)
   $string = preg_replace ( '/(.{73}[^=]{0,3})/', '$1'.$newline, $string);
   return $string;
}

function quoted_printable_encode_character ( $matches ) {
   $character = $matches[0];
   return sprintf ( '=%02x', ord ( $character ) );
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Векскон финансовый партнер</title>
<meta name="keywords" content="тендерный кредит, банковская гарантия, кредит на исполнение, госзаказ"/>
<meta name="description" content="тендерный кредит банковская гарантия кредит на исполнение госзаказ"/>
<link rel="stylesheet" type="text/css" href="css/main.css"/>
</head>
<body>
<div id="main2"><div id="logoph"><?php $type="text"; include("logo.php"); ?></div>
<a href="http://www.vekscon.ru"><div id="glav"></div></a><div id="textraz"><span class="mail">ОФОРМЛЕНИЕ ЗАЯВКИ КРЕДИТА НА ИСПОЛНЕНИЕ</span><br /><br />

				<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
				<table width="800" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td valign="top" width="250"><table cellpadding="0" cellspacing="0" align="left">
    <tr>
      <td valign="top" align="left"><H2>Данные контракта</H2>
<br /><br />Наименование организации<br>
          <input type="text" name="data" maxlength="94" value="<?php echo $data ?>"/></td>
    </tr>
    <tr>
      <td>Стоимость госконтракта<br><input type="text" name="gos" maxlength="64" value="<?php echo $gos ?>" /></td>
    </tr>
	<tr>
      <td align="left">Предмет государственного контракта<br><input type="text" name="org" maxlength="64" value="<?php echo $org ?>" /></td>
    </tr>
	<tr>
      <td>Необходимый объем кредитования<br><input type="text" name="kon" maxlength="64" value="<?php echo $kon ?>" /> </td>
    </tr>
	<tr>
      <td>Срок кредитования<br><input type="text" name="ssilka" maxlength="64" value="<?php echo $ssilka ?>" /></td>
    </tr>
	<tr>
      <td align="left">Контрактная документация<br><input type="file" name="file" /><br><br />
	  </td>
    </tr>
	</table></td>
    <td>&nbsp;</td>
    <td valign="top" width="180" align="left"><table align="left" cellpadding="0" cellspacing="0">
	    <tr>
      <td> <H2>Контактные данные</H2><br><br>E-mail*<br>
        <input type="text" name="email" maxlength="64" value="<?php echo $email ?>"/></td>
    </tr>
    <tr>
      <td>Фамилия*<br><input type="text" name="subject" maxlength="64" value="<?php echo $subject ?>" /></td>
    </tr>
    <tr>
      <td>Имя*<br><input type="text" name="na" maxlength="64" value="<?php echo $na ?>"/></td>
    </tr>
	 <tr>
      <td>Контактный телефон*<br><input type="text" name="fone" maxlength="64" value="<?php echo $fone ?>"  /></td>
    </tr>
    <tr>
      <td>Дополнительная информация<br><textarea name="message" class="index"  rows="3" cols="20" /><?php echo $message ?></textarea><br /><br /><table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><img src="secpic.php" alt="защитный код" /></td>
    <td>&nbsp;</td>
    <td><input type="text" name="secpic" maxlength="5" size="10"></td>
  </tr>
</table>
<input type="submit" name="sendMail" value="Отправить заявку" /></td>
    </tr>
  </table></td>
    <td>&nbsp;</td>
    <td width="250" valign="top" align="left"><?php
if ( isset( $_SESSION['sendMailForm'] ) ) {
  echo $_SESSION['sendMailForm']['error'];
  $email   = htmlspecialchars ( $_SESSION['sendMailForm']['email'] );
  $subject = htmlspecialchars ( $_SESSION['sendMailForm']['subject'] );
  $name    = htmlspecialchars ( $_SESSION['sendMailForm']['name'] );
  $company    = htmlspecialchars ( $_SESSION['sendMailForm']['company'] );
  $gos    = htmlspecialchars ( $_SESSION['sendMailForm']['gos'] );
  $org = htmlspecialchars ( $_SESSION['sendMailForm']['org'] );
  $data = htmlspecialchars ( $_SESSION['sendMailForm']['data'] );
  $kon = htmlspecialchars ( $_SESSION['sendMailForm']['kon'] );
  $ssilka = htmlspecialchars ( $_SESSION['sendMailForm']['ssilka'] );
  $na = htmlspecialchars ( $_SESSION['sendMailForm']['na'] );
  $fone = htmlspecialchars ( $_SESSION['sendMailForm']['fone'] );
  $message = htmlspecialchars ( $_SESSION['sendMailForm']['message'] );
  unset( $_SESSION['sendMailForm'] );
} else {
  $name  = '';
  $company  = '';
  $gos  = '';
  $org = '';
  $data = '';
  $kon = '';
  $ssilka = '';
  $na = '';
  $fone = '';
  $email   = '';
  $subject = '';
  $message = '';
}
if ( isset( $_SESSION['success'] ) ) {
  if ( $_SESSION['success'] )
    echo '<p><span class="green">Спасибо! Заявка отправлена.</a></p>';
  else
    echo '<p>ошибка при отправке заявки</p>';
  unset( $_SESSION['success'] );
}
?></td>
  </tr>
</table>
</form>
<a href="anketa_zaemshika.doc"><div id="nizr2"></div></a>
</div>
</div>
</body>
</html>
