<?php
session_start();

$num1 = rand(1, 10);
$num2 = rand(1, 10);
$_SESSION['captcha_result'] = $num1 + $num2;

$image = imagecreatetruecolor(150, 50);
$bg = imagecolorallocate($image, 255, 255, 255);
$textColor = imagecolorallocate($image, 0, 0, 0);
$noiseColor = imagecolorallocate($image, 150, 150, 150);

imagefilledrectangle($image, 0, 0, 150, 50, $bg);

for ($i = 0; $i < 5; $i++) {
    imageline($image, rand(0,150), rand(0,50), rand(0,150), rand(0,50), $noiseColor);
}

imagestring($image, 5, 20, 15, "$num1 + $num2 = ?", $textColor);

header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);