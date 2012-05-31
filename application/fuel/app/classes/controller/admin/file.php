<?php

class Controller_Admin_File extends Controller_Admin {

	public function action_index() {
		$data['files'] = Model_File::find('all', array(
			    'related' => array(
				'user'
			    ),
			));
		$this->template->title = "Files";
		$this->template->content = View::forge('admin\file/index', $data);
	}

	public function action_view($id = null) {
		$data['file'] = Model_File::find($id);

		$this->template->title = "File";
		$this->template->content = View::forge('admin\file/view', $data);
	}

	public function action_create($id = null) {

		if (Input::param() != array()) {

			try {

				// CHMOD has to be written in octal notation
				Upload::process(array(
				    'path' => DOCROOT . 'files',
				    'randomize' => false,
				    'ext_whitelist' => array('csv', 'zip', 'wrk'),
				    'file_chmod' => 0666,
				    'path_chmod' => 0777,
				    'normalize' => true
				));

				// Called upon upload save
				Upload::register('after', function (&$file) {

						// Extract files (if necessary) and return array
						$files = Unpack::extract($file['saved_to'] . $file['saved_as']);

						foreach ($files as $f) {

							$ext = substr($f, strrpos($f, '.') + 1);

							$model = Model_File::forge();

							$model->path = $f;
							$model->type = $ext;
							$model->name = '';
							$model->latitude = '';
							$model->longitude = '';

							$execute = true;

							switch ($ext) {

								case 'csv':

									// read 1st line
									$fh = fopen($f, 'r');
									list($lat, $lng, $name) = str_replace('"', '', explode(",", fgets($fh, 4096)));
									fclose($fh);

									$model->name = $name;
									$model->latitude = $lat;
									$model->longitude = $lng;

									$ignore = 2;
									$columns = 'TimeStamps,PossiblePower,WindSpeed,RegimePossible,OutputPower,RegimeOutput,TimeStampsR,file_id';

									break;
								case 'wrk':

									$ignore = 1;
									$columns = 'sig,tot_bytes,trailer_offset,trailer_size,img_type,mm_predict,pixel_size,date_time,rad_name,east_uppb,north_uppb,hei_uppb,store_slope,store_icept,store_offset,store_quant,geo_lon,geo_lat,signal2,pixel_values,file_id';
									
									break;
								default:
									$execute = false;
									break;
							}

							// get id
							$model->save();

							if ($execute) {
								$cmd = 'D:\\wamp\\www\\fagprojekt-wdv\\application\\release\\DataParser.exe --file=' . $f . ' --type=' . $ext . ' --fileid=' . $model->id . '';
								$t = exec($cmd, $out, $retval);
								if ($retval == 1 || $retval == 2) {
									if ($ext == 'wrk')
									{
										$fp = @fopen(str_replace($ext,'csv',$f), 'r');
										if ($fp) {
											$array = explode("\n", fread($fp, filesize(str_replace($ext,'csv',$f))));
										}
										DB::insert('file_wrks')->columns(explode(',',$columns))->values(explode(',',$array[1]))->execute();
									}
									else
									{
										$result = DB::query("
											LOAD DATA INFILE '".str_replace('\\\\\\\\', '\\\\', str_replace('\\', '\\\\', str_replace($ext,'csv',$f)))."'
											INTO TABLE fagprojekt.file_csvs
											FIELDS TERMINATED BY ',' ENCLOSED BY '\"'
											LINES TERMINATED BY '\n'
											IGNORE 2 LINES
											(TimeStamps,PossiblePower,WindSpeed,RegimePossible,OutputPower,RegimeOutput,TimeStampsR,file_id)
										")->execute();
									}
								}
							}
						}
					});

				// Check for any errors
				if (!Upload::get_errors()) {

					// Save upload
					Upload::save();

					// Redirect to header
					//Response::redirect('admin/file');
				} else {
					Session::set_flash('error', 'Something went wrong with upload');
				}
			} catch (Orm\ValidationFailed $e) {

				Session::set_flash('error', $e->getMessage());
			}
		}

		$fieldset = Fieldset::forge('file')->add_model('Model_File');

		// For upload
		$fieldset->set_config('form_attributes', array('enctype' => 'multipart/form-data'));

		$fieldset->add('submit', '', array('type' => 'submit', 'value' => 'Create', 'class' => 'btn medium primary'));

		$this->template->title = "Files";
		$this->template->content = View::forge('admin/file/create');
	}

	public function action_delete($id = null) {
		
		// Tables are related at SQL level
		$query = DB::delete('files')
		->where('id', '=', $id)
		->execute();
		
		if($query) {
			Session::set_flash('success', 'Deleted file #' . $id);
		} else {
			Session::set_flash('error', 'Could not delete file #' . $id);
		}

		Response::redirect('admin/file');
	}

}

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
					$files = $unzip->extract($path);
				} catch (FuelException $e) {
					//Session::set_flash('error', $unzip->error_string());
				}

				foreach ($files as $file) {
					self::_unpack($file);
				}

				break;
			//case 'tar':
			//	break;
			default:
				self::$_output_files[] = $path;
		}
	}

}