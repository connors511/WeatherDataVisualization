<?php
class Controller_Admin_User extends Controller_Admin 
{

	public function action_index()
	{
		
		$data['total_items'] = Model_User::find()->count();
		$data['groups'] = \Config::get('simpleauth.groups',array());
		$data['pagination'] = $this->set_pagination(Uri::create('admin/file/index'), 4, $data['total_items'], 20);

		$data['users'] = Model_User::find('all', array(
			    'limit' => Pagination::$per_page,
			    'offset' => Pagination::$offset,
			    'order_by' => array('updated_at' => 'desc')
			));
		
		$this->template->title = "Users";
		$this->template->content = View::forge('admin\user/index', $data, false);

	}

	public function action_create()
	{
		if (Input::method() == 'POST')
		{
			$val = Model_User::validate('create');
			
			if ($val->run())
			{
				$user = Model_User::forge(array(
					'username' => Input::post('username'),
					'password' => Auth::hash_password(Input::post('password')),
					'group' => Input::post('group'),
					'email' => Input::post('email'),
					'last_login' => 0,
					'login_hash' => '',
					'profile_fields' => '',
				));

				if ($user and $user->save())
				{
					Session::set_flash('success', 'Added user #'.$user->id.'.');

					Response::redirect('admin/user');
				}

				else
				{
					Session::set_flash('error', 'Could not save user.');
				}
			}
			else
			{
				Session::set_flash('error', $val->show_errors());
			}
		}
		
		$view = View::forge('admin\user/create');
		$view->set_global('groups', \Config::get('simpleauth.groups','name'));

		$this->template->title = "Users";
		$this->template->content = $view;

	}

	public function action_edit($id = null)
	{
		$user = Model_User::find($id);
		$val = Model_User::validate('edit');

		if ($val->run())
		{
			$user->username = Input::post('username');
			$user->password = Auth::hash_password(Input::post('password'));
			$user->group = Input::post('group');
			$user->email = Input::post('email');
			//$user->last_login = Input::post('last_login');
			//$user->login_hash = Input::post('login_hash');
			//$user->profile_fields = Input::post('profile_fields');

			if ($user->save())
			{
				Session::set_flash('success', 'Updated user #' . $id);

				Response::redirect('admin/user');
			}

			else
			{
				Session::set_flash('error', 'Could not update user #' . $id);
			}
		}

		else
		{
			if (Input::method() == 'POST')
			{
				$user->username = $val->validated('username');
				$user->password = $val->validated('password');
				$user->group = $val->validated('group');
				$user->email = $val->validated('email');
				$user->last_login = $val->validated('last_login');
				$user->login_hash = $val->validated('login_hash');
				$user->profile_fields = $val->validated('profile_fields');

				Session::set_flash('error', $val->show_errors());
			}
			
			$this->template->set_global('user', $user, false);
		}

		$this->template->title = "Users";
		$this->template->content = View::forge('admin\user/edit');

	}

	public function action_delete($id = null)
	{
		if ($user = Model_User::find($id))
		{
			$user->delete();

			Session::set_flash('success', 'Deleted user #'.$id);
		}

		else
		{
			Session::set_flash('error', 'Could not delete user #'.$id);
		}

		Response::redirect('admin/user');

	}


}