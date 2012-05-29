<?php
class Controller_Admin_File extends Controller_Admin 
{

	public function action_index()
	{
		$data['files'] = Model_File::find('all',array(
		    'related' => array(
			'user'
		    ),
		));
		$this->template->title = "Files";
		$this->template->content = View::forge('admin\file/index', $data);

	}

	public function action_view($id = null)
	{
		$data['file'] = Model_File::find($id);

		$this->template->title = "File";
		$this->template->content = View::forge('admin\file/view', $data);

	}

	public function action_create($id = null)
	{
		
		
		if (Input::param() != array()) {
			try {
				
				// CHMOD has to be written in octal notation
				Upload::process(array(
					'path' => DOCROOT.'files',
					'randomize' => false,
					'ext_whitelist' => array('csv'),
					'file_chmod'	=> 0666,
					'path_chmod'	=> 0777,
					'normalize'	=> true
				));
				
				Upload::register('after', function (&$file) {
					
					$model = Model_File::forge();
		
					$model->path = $file['saved_as'];
					$model->type = $file['extension'];
					
					$model->name = '';
					$model->latitude = '';
					$model->longitude = '';
					
					$model->save();
					
					switch($file['extension']) {
						case 'csv':
							
							$fh = fopen($file['saved_to'].$file['saved_as'], 'r');
							list($lat, $lng, $name) = str_replace('"','',explode(",",fgets($fh, 4096)));
							fclose($fh);
							
							$model->name = $name;
							$model->latitude = $lat;
							$model->longitude = $lng;
							
							$cmd = 'D:\\wamp\\www\\fagprojekt-wdv\\application\\release\\DataParser.exe --file='.$file['saved_to'].$file['saved_as'].' --type='.$file['extension'].' --fileid='.$model->id.'';
							system($cmd, $retval);
							if ($retval < 2) {
								$result = DB::query("
									LOAD DATA INFILE '".str_replace('\\\\\\\\', '\\\\', str_replace('\\', '\\\\', $file['saved_to'].$file['saved_as']))."' 
									INTO TABLE fagprojekt.file_csvs 
									FIELDS TERMINATED BY ',' ENCLOSED BY '\"' 
									LINES TERMINATED BY '\n'
									IGNORE 2 LINES
									(TimeStamps,PossiblePower,WindSpeed,RegimePossible,OutputPower,RegimeOutput,TimeStampsR,file_id)
								")->execute();
							}
							break;
						default:
							break;
					}
					
					$model->save();
				});
				
				if(!Upload::get_errors()) {
					
					Upload::save();
					
					Response::redirect('admin/file');
					
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

	public function action_delete($id = null)
	{
		if ($file = Model_File::find($id))
		{
			$file->delete();

			Session::set_flash('success', 'Deleted file #'.$id);
		}

		else
		{
			Session::set_flash('error', 'Could not delete file #'.$id);
		}

		Response::redirect('admin/file');

	}


}