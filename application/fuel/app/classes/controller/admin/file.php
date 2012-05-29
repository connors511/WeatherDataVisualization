<?php
class Controller_Admin_File extends Controller_Admin 
{

	public function action_index()
	{
		$data['files'] = Model_File::find('all');
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
		if (Input::method() == 'POST')
		{
			
			$val = Model_File::validate('create');
			
			Upload::process(array(
				'path' => DOCROOT.DS.'files',
				'randomize' => true,
				'ext_whitelist' => array('csv'),
				'file_chmod'	=> 777
			));
			
			Upload::register('after', function (&$file) {
				
				if ($file['error'] == Upload::UPLOAD_ERR_OK)
				{
					$model = Model_File::forge(array(
						'path'      => $file['saved_to'].$file['saved_as'],
						'type'      => $file['extension'],
						'user_id'   => 0
					));
					
					if($model and $model->save()) {
						
						// Outputs all the result of shellcommand "ls", and returns
						// the last output line into $last_line. Stores the return value
						// of the shell command in $retval.
						$cmd = 'D:\\wamp\\www\\fagprojekt-wdv\\fagprojekt\\release\\DataParser.exe --file='.$file['saved_to'].$file['saved_as'].' --type='.$file['extension'].' --fileid='.$model->id.'';
						$last_line = system($cmd, $retval);
						
						if ($retval < 2) {
							$result = DB::query("
							    LOAD DATA INFILE '".str_replace('\\\\\\\\','\\\\',str_replace('\\','\\\\',$file['saved_to'].$file['saved_as']))."' 
							    INTO TABLE fagprojekt.file_csvs 
							    FIELDS TERMINATED BY ',' ENCLOSED BY '\"' 
							    LINES TERMINATED BY '\n'
							    IGNORE 1 LINES
							    (TimeStamps,PossiblePower,WindSpeed,RegimePossible,OutputPower,RegimeOutput,TimeStampsR,file_id)
							")->execute();
						
							//die(DB::last_query());
						
							Session::set_flash('success', 'Added file #'.$model->id.'.');
							Response::redirect('admin/file');
						} else {
							Session::set_flash('error', 'Could not save file.');
							$model->delete();
						}
						
					}
					
				}
			}
			);
			
			if ($val->run())
			{
				if(Upload::is_valid()) {
					Upload::save();
				}
			}
			else
			{
				Session::set_flash('error', $val->show_errors());
			}
		}

		$this->template->title = "Files";
		$this->template->content = View::forge('admin\file/create');

	}

	//public function action_edit($id = null)
	//{
	//	$file = Model_File::find($id);
	//	$val = Model_File::validate('edit');
	//
	//	if ($val->run())
	//	{
	//		$file->path = Input::post('path');
	//
	//		if ($file->save())
	//		{
	//			Session::set_flash('success', 'Updated file #' . $id);
	//
	//			Response::redirect('admin/file');
	//		}
	//
	//		else
	//		{
	//			Session::set_flash('error', 'Could not update file #' . $id);
	//		}
	//	}
	//
	//	else
	//	{
	//		if (Input::method() == 'POST')
	//		{
	//			$file->path = $val->validated('path');
	//
	//			Session::set_flash('error', $val->show_errors());
	//		}
	//		
	//		$this->template->set_global('file', $file, false);
	//	}
	//
	//	$this->template->title = "Files";
	//	$this->template->content = View::forge('admin\file/edit');
	//
	//}

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