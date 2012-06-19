<?php

class Unpack {

	protected static $_output_files = array();
	protected static $_config = array();

	public function __construct() {
		self::$_output_files = array();
	}

	public static function extract($path) {
		self::_unpack($path);
		return self::$_output_files;
	}
	
	public static function setup($config) {
		self::$_config = $config;
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
				try {
					// Whitelisted extensions
					$unzip->allow(self::$_config['ext_whitelist']);
					
					// Unpack
					$files = $unzip->extract($path);
					
					foreach ($files as $file) {
						self::_unpack($file);
					}
					$unzip->close();
				
					// Remove zip
					unlink($path);
					
				} catch (FuelException $e) {
					//Session::set_flash('error', $unzip->error_string());
				}
				
				break;
			//case 'tar':
			//	break;
			default:
				
				$newpath = $path;
				if(self::$_config['normalize']) {
					// Get file info
					$path_parts = pathinfo($path);

					// Rename
					$newpath = $path_parts['dirname'].'/'.\Fuel\Core\Inflector::friendly_title($path_parts['filename'],self::$_config['normalize_separator']).'.'.$path_parts['extension'];
					if ($path != $newpath)
					{
						rename($path, $newpath);
					}
				}
				
				chmod($newpath, 0777);

				self::$_output_files[] = $newpath;
				break;
		}
	}

}
