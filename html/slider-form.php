<?php
$name = $_POST['slider-name'];
$email = $_POST['slider-email'];
$studyLevel = $_POST['slider-study-level'];
$course = $_POST['slider-course'];
 
$to = 'your@domain.com';
$subject = 'You Have New Subscriber from Universo!';

$body = "";
$body .= "Name: ";
$body .= $name;
$body .= "\n\n";

$body .= "";
$body .= "Email: ";
$body .= $email;
$body .= "\n";

$body .= "";
$body .= "Study Level: ";
$body .= $studyLevel;
$body .= "\n";

$body .= "";
$body .= "Selected Course: ";
$body .= $course;
$body .= "\n";

$headers = 'From: ' .$email . "\r\n";

//$headers = 'From: noreply@domain.com' . "\r\n";

//$body .= "";
//$body .= "Email: ";
//$body .= $email;
//$body .= "\n";

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
mail($to, $subject, $body, $headers);
echo '<span id="valid"><i class="icon icon-check"></i>Your Email was sent!</span>';
}else{
echo '<span id="invalid">Invalid Email, please provide a correct email.</span>';
}
