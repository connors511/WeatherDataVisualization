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
				);
				
				Upload::process($upload_config);
				Unpack::setup($upload_config);

				// Called upon upload save
				Upload::register('after', function (&$file) {

						// Extract files (if necessary) and return array
						$files = Unpack::extract($file['saved_to'].$file['saved_as']);

						foreach ($files as $f) {

							$ext = substr($f, strrpos($f, '.') + 1);

							$model = Model_File::forge();

							$model->path = $f;
							$model->type = $ext;
							$model->offset = Input::param('offset', 0);
							$model->name = '0';
							$model->latitude = '';
							$model->longitude = '';

							$model->save();
						}
					});

				// Check for any errors
				if (!Upload::get_errors()) {

					// Try to save upload
					Upload::save();

					// Redirect to header
					Response::redirect('admin/file');
				} else {
					Debug::dump(Upload::get_errors());
					Session::set_flash('error', "Something went wrong with upload.");
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
