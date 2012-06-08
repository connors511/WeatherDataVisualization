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
				$path = str_replace('public/', '', DOCROOT) . 'release' . DS . 'osx' . DS . 'DataParser';
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
						
						// Import CSV to SQL
						try {
							$result = \Fuel\Core\DB::query("
								LOAD DATA INFILE '" . $file_name . "'
								INTO TABLE fagprojekt.file_csvs
								FIELDS TERMINATED BY ',' ENCLOSED BY '\"'
								LINES TERMINATED BY '\n'
								IGNORE 2 LINES
								(".implode(',',\Model_File_Csv::get_columns()).")
							")->execute();
						} 
						catch(\Database_Exception $e)
						{
							$result = \Fuel\Core\DB::query("
								LOAD DATA LOCAL INFILE '" . $file_name . "'
								INTO TABLE fagprojekt.file_csvs
								FIELDS TERMINATED BY ',' ENCLOSED BY '\"'
								LINES TERMINATED BY '\n'
								IGNORE 2 LINES
								(".implode(',',\Model_File_Csv::get_columns()).")
							")->execute();
						}
						
						// Convert timezones to UTC
						$update = \Fuel\Core\DB::update('file_csvs')->
							set(array(
							    'file_csvs.TimeStamps' => \Fuel\Core\DB::expr("DATE_FORMAT(CONVERT_TZ(STR_TO_DATE(file_csvs.TimeStamps, '%Y%m%d%H%i'),'{$file->offset}','UTC'),'%Y%m%d%H%i')"),
							    'file_csvs.TimeStampsR' => \Fuel\Core\DB::expr("CONVERT_TZ(file_csvs.TimeStampsR,'{$file->offset}','UTC')")
							))->
							where('file_csvs.file_id','=',$file->id)->
							execute();
						break;
					case 'wrk':

						$fp = @fopen(str_replace($file->type, 'csv', $file->path), 'r');
						if ($fp) {
							$array = explode("\n", fread($fp, filesize(str_replace($file->type, 'csv', $file->path))));
						}
						list($lat, $lng, $name) = explode(",", $array[0]);
						
						$file->latitude = $lat;
						$file->longitude = $lng;
						$file->name = $name;
						
						$columns = \Model_File_Wrk::get_columns();
						$values = explode(',',$array[2]);
						
						// $columns starts with 1 because [0] => id is unset.
						$date_time_key = array_search('date_time',$columns)-1;
						
						// Convert timezone to UTC
						$values[$date_time_key] = \Fuel\Core\DB::expr("DATE_FORMAT(CONVERT_TZ(STR_TO_DATE('{$values[$date_time_key]}', '%Y%m%d%H%i%s'),'{$file->offset}','UTC'),'%Y%m%d%H%i%s')");

						$query = \Fuel\Core\DB::insert('file_wrks')->columns($columns)->values($values)->execute();
						
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

