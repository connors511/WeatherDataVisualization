<?php

class Controller_Admin_File extends Controller_Admin {

	public function action_index() {

		$data['total_items'] = Model_File::find()->count();
		
		$data['pagination'] = $this->set_pagination(Uri::create('admin/file/index'), 4, $data['total_items'], 20);

		$data['files'] = Model_File::find('all', array(
			    'related' => array(
				'user'
			    ),
			    'limit' => Pagination::$per_page,
			    'offset' => Pagination::$offset,
			    'order_by' => array('updated_at' => 'desc')
			));
		
		$this->template->title = "Files";
		$this->template->content = View::forge('admin/file/index', $data, false);
	}
	
	public function action_search($term = null) {
		
		$data['total_items'] = Model_File::find()->where('name','LIKE','%'.$term.'%')->count();
		
		$data['pagination'] = $this->set_pagination(Uri::create('admin/file/search/'.$term), 5, $data['total_items'], 20);

		$data['files'] = Model_File::find('all', array(
			    'related' => array(
				'user'
			    ),
			    'where' => array(
				array('name','LIKE','%'.$term.'%')
			    ),
			    'limit' => Pagination::$per_page,
			    'offset' => Pagination::$offset,
			    'order_by' => array('updated_at' => 'desc')
			));
		
		$this->template->title = "Files";
		$this->template->content = View::forge('admin/file/index', $data, false);
	}

	public function action_create($id = null) {

		if (Input::param() != array()) {

			try {
				
				// CHMOD has to be written in octal notation
				$upload_config = array(
				    'path' => DOCROOT.'files',
				    'randomize' => false,
				    'ext_whitelist' => array('csv', 'zip', 'wrk'),
				    'file_chmod' => 0666,
				    'path_chmod' => 0777,
				    'normalize' => true,
				    'normalize_separator' => '_',
				    'auto_rename' => false,
				);
				
				Upload::process($upload_config);
				Unpack::setup($upload_config);

				// Called upon upload save
				Upload::register('after', function (&$file) {
						// Extract files (if necessary) and return array
						$files = Unpack::extract($file['saved_to'].$file['saved_as']);
						
						$uploaded = 0;
						foreach ($files as $f) {

							$ext = substr($f, strrpos($f, '.') + 1);

							$model = Model_File::forge();

							$model->path = $f;
							$model->type = $ext;
							$model->offset = Input::param('offset', 0);
							$model->name = '0';
							$model->latitude = '';
							$model->longitude = '';
							
							$existing_file = Model_File::find('first', array(
							    'where' => array(
								array('path', 'LIKE', '%'.basename($model->path)),
							    )
							));
							
							// Check for duplicates
							if(!$existing_file) {
								$model->save();
								$uploaded++;
							} else {
								// Remove only the duplicate file if it is on another path
								if($existing_file->path != $model->path)
									unlink($model->path);
							}
						}
						
						// Set messages
						$messages[] = "Saved ".$uploaded." files.";
						if(($skipped = count($files) - $uploaded) != 0) {
							$messages[] = "Skipped ".$skipped." files.";
						}
						Session::set_flash('success', $messages);
					});

				// Check for any errors.
				// Note: A file with an already existing filename will not be uploaded, nor will be yield errors.
				if (!Upload::get_errors()) {
					
					// Save upload
					Upload::save();

					// Redirect
					Response::redirect('admin/file');
				} else {
					// Set errors
					foreach(Upload::get_errors() as $file_error) {
						foreach($file_error['errors'] as $error) {
							$messages[] = $error['message'];
						}
					}
					Session::set_flash('error', $messages);
				}
			} catch (Orm\ValidationFailed $e) {

				Session::set_flash("error", $e->getMessage());
			}
		}

		$fieldset = Fieldset::forge('file')->add_model('Model_File');

		//$fieldset->set_config('form', "\n\t\tCUNT{open}\n\t\t<table>\n\n\t\t</table>\n\t\t{close}\n");

		$fieldset->build();

		// For upload
		$fieldset->set_config('form_attributes', array('enctype' => 'multipart/form-data'));

		$fieldset->add('submit', '', array('type' => 'submit', 'value' => 'Create', 'class' => 'btn btn-primary'));
		
		$this->template->title = "Files";
		$this->template->content = View::forge('admin/file/create');
	}

	public function action_delete($id = null) {

		// Tables are related at SQL level
		//Remove file
		$file = Model_File::find($id);
		unlink($file->path);


		$query = DB::delete('files')
			->where('id', '=', $id)
			->execute();

		if ($query) {
			Session::set_flash('success', 'Deleted file #' . $id);
		} else {
			Session::set_flash('error', 'Could not delete file #' . $id);
		}

		Response::redirect('admin/file');
	}
	
	public function action_mass() {
		
		$files = Input::param('chk');
		$submit_type = Input::param('submit_type');
		
		if($files && $submit_type) {	
			switch($submit_type) {
				case 'del':		
					$message = array(
					    'success' => 'Deleted '.count($files).' files',
					    'error' => 'Could not delete files'
					);
					
					foreach($files as $id) {
						$file = Model_File::find($id);
						unlink($file->path);
					}

					$query = DB::delete('files')
					->where('id', 'IN', Input::param('chk'))
					->execute();
					
					break;
				default:
					$query = false;
					$message = array(
					    'error' => 'How did you get here?'
					);
					break;
			}
		
			if ($query) {
				Session::set_flash('success', $message['success']);
			} else {
				Session::set_flash('error', $message['error']);
			}
		}

		
		
		Response::redirect('admin/file');
	}

}
