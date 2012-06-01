<?php

namespace Fuel\Tasks;

class Preparedata {

	public static function run() {

		// Get files not yet data parsed
		$files = \Model_File::find('all', array(
			    'where' => array(
				array('name', '=', '0')
			    ),
			));

		foreach ($files as $file) {

			// Run DataParser
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
				$path = str_replace('public/', '', DOCROOT) . 'release' . DS . 'win' . DS . 'DataParser.exe';
			} else if (strtoupper(substr(PHP_OS, 0, 6)) === 'DARWIN') {
				$path = '.' . str_replace('public/', '', DOCROOT) . 'release' . DS . 'osx' . DS . 'DataParser';
			} else {
				$path = str_replace('public/', '', DOCROOT) . 'release' . DS . 'nix' . DS . 'DataParser';
			}
			
			$cmd = $path . ' --file=' . $file->path . ' --type=' . $file->type . ' --fileid=' . $file->id . '';
			$t = exec($cmd, $out, $retval);

			// On success
			if ($retval == 1 || $retval == 2) {

				switch ($file->type) {

					case 'csv':

						// read 1st line
						$fh = fopen($file->path, 'r');
						list($lat, $lng, $name) = str_replace('"', '', explode(",", fgets($fh, 4096)));
						fclose($fh);

						$file->name = trim($name);
						$file->latitude = $lat;
						$file->longitude = $lng;

						$file_name = str_replace('\\\\\\\\', '\\\\', str_replace('\\', '\\\\', $file->path));
						
						$result = \Fuel\Core\DB::query("
							LOAD DATA LOCAL INFILE '" . $file_name . "'
							INTO TABLE fagprojekt.file_csvs
							FIELDS TERMINATED BY ',' ENCLOSED BY '\"'
							LINES TERMINATED BY '\n'
							IGNORE 2 LINES
							(TimeStamps,PossiblePower,WindSpeed,RegimePossible,OutputPower,RegimeOutput,TimeStampsR,file_id)
						")->execute();
						break;
					case 'wrk':

						$columns = 'sig,tot_bytes,trailer_offset,trailer_size,img_type,mm_predict,pixel_size,date_time,east_uppb,north_uppb,hei_uppb,store_slope,store_icept,store_offset,store_quant,signal2,pixel_values,file_id';

						$fp = @fopen(str_replace($file->type, 'csv', $file->path), 'r');
						if ($fp) {
							$array = explode("\n", fread($fp, filesize(str_replace($file->type, 'csv', $file->path))));
						}
						list($lat, $lng, $name) = explode(",", $array[0]);
						$file->latitude = $lat;
						$file->longitude = $lng;
						$file->name = $name;

						\Fuel\Core\DB::insert('file_wrks')->columns(explode(',', $columns))->values(explode(',', $array[2]))->execute();

						break;
					default:
						break;
				}
			}
			else
			{
				$file->name = '1';
			}
			
			// Save changes to files
			$file->save();
		}
	}

}

/* End of file task */

