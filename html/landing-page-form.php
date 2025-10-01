<?php
$email = $_POST['email'];
 
$to = 'your@domain.com';
$subject = 'You have new subscriber from Universo Landing Page!';

$body = "";
$body .= "Email: ";
$body .= $email;
$body .= "\n\n";

$headers = 'From: ' .$email . "\r\n";

//$headers = 'From: noreply@domain.com' . "\r\n";

//$body .= "";
//$body .= "Email: ";
//$body .= $email;
//$body .= "\n";

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
mail($to, $subject, $body, $headers);
echo '<span id="valid">Thank you for your subscription!</span>';
}else{
echo '<span id="invalid">Invalid Email, please provide a correct email.</span>';
}
