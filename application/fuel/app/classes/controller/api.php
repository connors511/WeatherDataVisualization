<?php

class Controller_Api extends Controller_Base
{

	public function action_upload()
	{
		$user = Model_User::find('first', array(
		    'where' => array(
			array('password','=', Input::server('HTTP_X_KEY'))
		    )
		));
		if ($user == null)
		{
			return new \Response(\View::forge('401'), 401);
		}
		Auth::instance()->force_login($user->id);
		if (!Input::server('HTTP_X_TIMEZONE',false))
		{
			return new \Response(\View::forge('401'), 401);
		}
		
		try
		{

			// CHMOD has to be written in octal notation
			$upload_config = array(
			    'path' => DOCROOT . 'files',
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
			Upload::register('after', function (&$file)
				{

					// Extract files (if necessary) and return array
					$files = Unpack::extract($file['saved_to'] . $file['saved_as']);

					foreach ($files as $f)
					{

						$ext = substr($f, strrpos($f, '.') + 1);

						$model = Model_File::forge();

						$model->path = $f;
						$model->type = $ext;
						$model->offset = Input::server('HTTP_X_TIMEZONE');
						$model->name = '0';
						$model->latitude = '';
						$model->longitude = '';

						$model->save();
					}
				});

			// Check for any errors
			if (!Upload::get_errors())
			{

				// Try to save upload
				Upload::save();

				// Redirect to header
				Response::redirect('admin/file');
			}
			else
			{
				Debug::dump(Upload::get_errors());
				Session::set_flash('error', "Something went wrong with upload.");
			}
		}
		catch (Orm\ValidationFailed $e)
		{

			Session::set_flash("error", $e->getMessage());
		}
	}

}