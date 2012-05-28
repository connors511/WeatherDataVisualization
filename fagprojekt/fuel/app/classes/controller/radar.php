<?php

/**
 * The Welcome Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 * 
 * @package  app
 * @extends  Controller
 */
class Controller_Radar extends Controller
{

	/**
	 * The basic welcome message
	 * 
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
                
                $res = DB::select('*')->from('file_wrk')->where('id',Input::get('id',1))->execute()->as_array();
                $res = $res[0];
                if (!file_exists(DOCROOT.'assets/radar/'.$res['id'].'.png'))
                {
                    $colors = explode(' ', $res['pixel_values']);
                    
                    // Set the image 
                    $img = imagecreatetruecolor($res['east_uppb'],$res['north_uppb']); 
                    imagesavealpha($img, true); 
                    
                    // Fill the image with transparent color 
                    $color = imagecolorallocatealpha($img,0x00,0x00,0x00,127); 
                    imagefill($img, 0, 0, $color); 
                    
                    for($i = 0; $i < $res['east_uppb']; $i++)
                    {
                            for($j = 0; $j < $res['north_uppb']; $j++)
                            {
                                    $tmp = $colors[$i * 240 + $j];
                                    if ($tmp != 0 && $tmp != 255) {
                                            // 255 = out of range, 0 = no clouds
                                            $col = imagecolorallocatealpha($img, (int)$tmp, (int)$tmp, (int)$tmp,0);
                                            imagesetpixel($img, $i, $j, $col);
                                    }
                            }
                    }
                    
                    
                    imagepng($img, DOCROOT.'assets/radar/'.$res['id'].'.png');
                }
                header('Content-Type: image/png');
                $str = file_get_contents(DOCROOT.'assets/radar/'.$res['id'].'.png'); 
                echo $str;
                
                // Destroy image 
                //imagedestroy($img); 
	}
}
