<?php

class Unpack {

	protected static $_output_files = array();

	public function __construct() {
		self::$_output_files = array();
	}

	public static function extract($path) {
		self::_unpack($path);
		return self::$_output_files;
	}

	protected static function _unpack($path) {

		$ext = substr($path, strrpos($path, '.') + 1);
		switch ($ext) {
			case 'gz':
				// Raising this value may increase performance
				$buffer_size = 4096; // read 4kb at a time
				$out_file_name = str_replace('.gz', '', $path);

				// Open our files (in binary mode)
				$file = gzopen($path, 'rb');
				$out_file = fopen($out_file_name, 'wb');

				// Keep repeating until the end of the input file
				while (!gzeof($file)) {
					// Read buffer-size bytes
					// Both fwrite and gzread and binary-safe
					fwrite($out_file, gzread($file, $buffer_size));
				}

				// Files are done, close files
				fclose($out_file);
				gzclose($file);

				self::_unpack($out_file_name);

				break;
			case 'zip':

				$unzip = new Fuel\Core\Unzip();
				$files = '';
				try {
					$unzip->allow(array('csv', 'zip', 'wrk'));
					$files = $unzip->extract($path);
				} catch (FuelException $e) {
					//Session::set_flash('error', $unzip->error_string());
				}

				foreach ($files as $file) {
					self::_unpack($file);
				}
				
				//TODO: delete zip

				break;
			//case 'tar':
			//	break;
			default:
				self::$_output_files[] = $path;
				break;
		}
	}

}
