<?php

class Controller_Admin_File extends Controller_Admin {

	public function action_index() {
		$data['files'] = Model_File::find('all', array(
			    'related' => array(
				'user'
			    ),
			));
		$this->template->title = "Files";
		$this->template->content = View::forge('admin/file/index', $data);

	}

	public function action_view($id = null) {
		$data['file'] = Model_File::find($id);

		$this->template->title = "File";
		$this->template->content = View::forge('admin/file/view', $data);
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
							$model->name = '0';
							$model->latitude = '';
							$model->longitude = '';

							$model->save();
						}
					});

				// Check for any errors
				if (!Upload::get_errors()) {

					// Save upload
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
