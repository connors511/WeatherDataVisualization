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
	public function action_index($id = false)
	{
		if (!$id)
		{
			throw new HttpNotFoundException();
		}
		$res = DB::select('*')->from('file_wrks')->where('id', Input::get('id', $id))->execute()->as_array();
		$res = $res[0];
		if (!file_exists(DOCROOT . 'assets/radar/' . $res['id'] . '.png'))
		{
			$colors = explode(' ', $res['pixel_values']);

			// Set the image 
			$img = imagecreatetruecolor($res['east_uppb'], $res['north_uppb']);
			imagesavealpha($img, true);

			// Fill the image with transparent color 
			$color = imagecolorallocatealpha($img, 0x00, 0x00, 0x00, 127);
			imagefill($img, 0, 0, $color);

			$_colors = array(
			    array(
				64,
				array(0, 0, 0)
			    ),
			    array(
				10,
				array(140, 140, 140)
			    ),
			    array(
				10,
				array(0, 255, 255)
			    ),
			    array(
				10,
				array(0, 149, 255)
			    ),
			    array(
				10,
				array(0, 0, 255)
			    ),
			    array(
				10,
				array(0, 255, 0)
			    ),
			    array(
				10,
				array(0, 200, 0)
			    ),
			    array(
				10,
				array(0, 160, 0)
			    ),
			    array(
				11,
				array(0, 110, 0)
			    ),
			    array(
				10,
				array(255, 255, 0)
			    ),
			    array(
				10,
				array(255, 200, 0)
			    ),
			    array(
				10,
				array(255, 126, 4)
			    ),
			    array(
				10,
				array(255, 0, 0)
			    ),
			    array(
				10,
				array(200, 0, 4)
			    ),
			    array(
				10,
				array(151, 0, 4)
			    ),
			    array(
				10,
				array(255, 0, 255)
			    ),
			    array(
				10,
				array(175, 0, 175)
			    ),
			    array(
				30,
				array(255, 255, 255)
			    )
			);

			$cmap = array();
			$index = 0;
			foreach ($_colors as $c)
			{
				$_col = imagecolorallocatealpha($img, $c[1][0], $c[1][1], $c[1][2], 0);
				$cmap = array_merge($cmap, array_fill($index, $c[0], $_col));
				$index += $c[0];
			}
			$border = imagecolorallocatealpha($img, 0x00, 0x00, 0x00, 100);
			for ($i = 0; $i < $res['east_uppb']; $i++)
			{
				for ($j = 0; $j < $res['north_uppb']; $j++)
				{
					$tmp = $colors[$i * 240 + $j];
					if ($tmp != 0 && $tmp != 255)
					{
						// 255 = out of range, 0 = no clouds
						//$col = imagecolorallocatealpha($img, $cmap[$tmp][0], $cmap[$tmp][1], $cmap[$tmp][2],0);
						imagesetpixel($img, $i, $j, $cmap[$tmp]);
					}
					if ($tmp == 0)
					{
						imagesetpixel($img, $i, $j, $border);
					}
				}
			}


			imagepng($img, DOCROOT . 'assets/radar/' . $res['id'] . '.png');
		}
		//header('Content-Type: image/png');
		$str = file_get_contents(DOCROOT . 'assets/radar/' . $res['id'] . '.png');
		$resp = new Response();
		$resp->set_header('Content-Type', 'image/png');
		$resp->body = $str;
		return $resp;

		// Destroy image 
		//imagedestroy($img); 
	}

}
