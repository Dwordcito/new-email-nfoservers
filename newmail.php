<?php
error_reporting(E_ERROR | E_PARSE);
$newemail = $_POST['email'];
$newpass = $_POST['password'];
$usuario = $_POST['usercookie'];
$password = $_POST['passcookie'];

if(is_null($newemail) || is_null($newpass) || $usuario != "webuser" || $password != "password"){
  die("error");
}

$nfouser = "nfouser";
$nfopass = "nfopassword";
$nfoservername = "nfoservername";
$domain = "domain to create email";


$ch = curl_init('https://www.nfoservers.com/login_showbox.pl');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,
            "email=".$nfouser."&password=".$nfopass."&referrer=&submitted=Log+in");

curl_setopt($ch, CURLOPT_HEADER, 1);
$result = curl_exec($ch);

preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
$cookies = array();
foreach($matches[1] as $item) {
    parse_str($item, $cookie);
    $cookies = array_merge($cookies, $cookie);
}
curl_close ($ch);

$ch = curl_init("https://www.nfoservers.com/control/mail.pl?name=".$nfoservername."&typeofserver=website");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE,"cookietoken=".$cookies["cookietoken"]."; email=".$nfouser."; logged_in=1; name=".$nfoservername."; password=".$nfopass."; type=website");
$result = curl_exec($ch);
curl_close($ch);

$DOM = new DOMDocument;
$DOM->loadHTML($result);
$postData = "";
$items = $DOM->getElementsByTagName('tr');
for ($i = 0; $i < $items->length; $i++){
  $element = $items->item($i)->getAttribute('id');
  if($element!=""){
    $elements = explode("_", $items->item($i)->getAttribute('id'));
    if($elements[1]!=$domain){
      if(is_null($elements[2])){
        $postData .= "newpassword_".$elements[1]."=&";
        $postData .= "newemail_".$elements[1]."=&";
        $postData .= "option_spamfilter_oldval_".$elements[1]."=undef&";
        $postData .= "option_spamfilter_".$elements[1]."=on&";
        $postData .= "option_spamfilter_theiroldval_".$elements[1]."=undef&";
        $postData .= "alias_ck_oldval_".$elements[1]."=0&";
        $postData .= "alias_oldval_".$elements[1]."=&";
        $postData .= "alias_".$elements[1]."=&";
      } else {
        $postData .= "password_".$elements[1]."_".$elements[2]."=[click+to+change]&";
        $postData .= "email_".$elements[1]."_".$elements[2]."=".$elements[2]."&";
        $postData .= "option_spamfilter_oldval_".$elements[1]."_".$elements[2]."=1&";
        $postData .= "option_spamfilter_".$elements[1]."_".$elements[2]."=on&";
        $postData .= "option_spamfilter_theiroldval_".$elements[1]."_".$elements[2]."=1&";
        $postData .= "alias_ck_oldval_".$elements[1]."_".$elements[2]."=0&";
        $postData .= "alias_oldval_".$elements[1]."_".$elements[2]."=&";
        $postData .= "alias_".$elements[1]."_".$elements[2]."=&";
      }

    } else {
      if(is_null($elements[2])){
        $postData .= "newpassword_".$elements[1]."=".$newpass."&";
        $postData .= "newemail_".$elements[1]."=".$newemail."&";
        $postData .= "option_spamfilter_oldval_".$elements[1]."=undef&";
        $postData .= "option_spamfilter_".$elements[1]."=on&";
        $postData .= "option_spamfilter_theiroldval_".$elements[1]."=undef&";
        $postData .= "alias_ck_oldval_".$elements[1]."=0&";
        $postData .= "alias_oldval_".$elements[1]."=&";
        $postData .= "alias_".$elements[1]."=&";
      }else {
        $postData .= "password_".$elements[1]."_".$elements[2]."=[click+to+change]&";
        $postData .= "email_".$elements[1]."_".$elements[2]."=".$elements[2]."&";
        $postData .= "option_spamfilter_oldval_".$elements[1]."_".$elements[2]."=1&";
        $postData .= "option_spamfilter_".$elements[1]."_".$elements[2]."=on&";
        $postData .= "option_spamfilter_theiroldval_".$elements[1]."_".$elements[2]."=1&";
        $postData .= "alias_ck_oldval_".$elements[1]."_".$elements[2]."=0&";
        $postData .= "alias_oldval_".$elements[1]."_".$elements[2]."=&";
        $postData .= "alias_".$elements[1]."_".$elements[2]."=&";
      }
    }
  }
}
curl_close($ch);

$ch = curl_init('https://www.nfoservers.com/control/mail.pl');
$options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_ENCODING       => "",
        CURLOPT_USERAGENT      => $domain,
        CURLOPT_AUTOREFERER    => true,
        CURLOPT_CONNECTTIMEOUT => 120,
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_POST           => 1,
        CURLOPT_POSTFIELDS     => "cookietoken=".$cookies["cookietoken"]."&name=".$nfoservername."&typeofserver=website&".$postData."&submit=Submit+changes",
        CURLOPT_COOKIE         => "cookietoken=".$cookies["cookietoken"]."; email=".$nfouser."; logged_in=1; name=".$nfoservername."; password=".$nfopass."; type=website",
    );

curl_setopt_array($ch, $options);
curl_exec($ch);
curl_close ($ch);

?>
