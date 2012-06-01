<?php 
$link = mysql_connect('localhost','root',''); 
if (!$link) { 
	die('Could not connect to MySQL: ' . mysql_error()); 
} 
mysql_select_db('fagprojekt');

$res = mysql_query("SELECT img_file FROM file_wrk WHERE id = 4");
$row = mysql_fetch_array($res);
$colors = explode(' ', $row[0]);

// Set the image 
$img = imagecreatetruecolor(240,240); 
imagesavealpha($img, true); 

// Fill the image with transparent color 
$color = imagecolorallocatealpha($img,0x00,0x00,0x00,127); 
imagefill($img, 0, 0, $color); 

for($i = 0; $i < 240; $i++)
{
	for($j = 0; $j < 240; $j++)
	{
		$tmp = $colors[$i * 240 + $j];
		if ($tmp != 0 && $tmp != 255) {
			// 255 = out of range, 0 = no clouds
			$col = imagecolorallocatealpha($img, (int)$tmp, (int)$tmp, (int)$tmp,0);
			imagesetpixel($img, $i, $j, $col);
		}
	}
}

//header('Content-Type: image/png');
imagepng($img, "test.png");
$str = base64_encode(file_get_contents('test.png')); 
echo strlen($str);

// Destroy image 
//imagedestroy($img); 
?> 