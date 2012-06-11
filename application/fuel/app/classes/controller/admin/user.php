<?php
class Controller_Admin_User extends Controller_Admin 
{

	public function action_index()
	{
		
		$data['total_items'] = Model_User::find()->count();
		$data['groups'] = \Config::get('simpleauth.groups');
		$data['pagination'] = $this->set_pagination(Uri::create('admin/file/index'), 4, $data['total_items'], 20);

		$data['users'] = Model_User::find('all', array(
			    'limit' => Pagination::$per_page,
			    'offset' => Pagination::$offset,
			    'order_by' => array('updated_at' => 'desc')
			));
		
		$this->template->title = "Users";
		$this->template->content = View::forge('admin/user/index', $data, false);

	}

	public function action_create()
	{
		
		$user = Model_User::forge();
		
		if (Input::param() != array()) {
			try {
				
				$user->username = Input::param('username');
				$user->password = Auth::hash_password(Input::param('password'));
				$user->group = Input::param('group');
				$user->email = Input::param('email');
				$user->last_login = 0;
				$user->login_hash = '';
				$user->profile_fields = '';
				
				$user->save();
				
				Response::redirect('admin/user');
			
			} catch (Orm\ValidationFailed $e) {
				
				Session::set_flash('error', $e->getMessage());
			
			}
			
		}
		
		$fieldset = Fieldset::forge('user')->add_model($user)->populate($user, true);
		$fieldset->add('submit', '', array('type' => 'submit', 'value' => 'Create', 'class' => 'btn btn-primary'));
		
		$fieldset->field('group')->set_options($this->_get_group_options());
		$fieldset->field('group')->set_value(1);
		
		$view = View::forge('admin/user/create');
		
		$this->template->title = "Users";
		$this->template->content = $view;

	}

	public function action_edit($id = null)
	{
		
		$user = Model_User::find($id);
		
		if (Input::param() != array()) {
			try {
				
				$user->username = Input::param('username');
				
				$user->password = Auth::hash_password(Input::param('password'));
				$user->group = Input::param('group');
				$user->email = Input::param('email');
				$user->last_login = 0;
				$user->login_hash = '';
				$user->profile_fields = '';
				
				$user->save();
				
				Response::redirect('admin/user');
			
			} catch (Orm\ValidationFailed $e) {
				
				Session::set_flash('error', $e->getMessage());
			
			}
			
		}
		
		$fieldset = Fieldset::forge('user')->add_model($user)->populate($user, true);
		$fieldset->add('submit', '', array('type' => 'submit', 'value' => 'Update', 'class' => 'btn btn-primary'));
		
		$fieldset->field('password')->set_value('');
		
		$fieldset->field('group')->set_options($this->_get_group_options());
		
		$view = View::forge('admin/user/edit');
		$this->template->set_global('user', $user, false);
		
		$this->template->title = "Users";
		$this->template->content = $view;

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
	
	private function _get_group_options() {
		foreach(\Config::get('simpleauth.groups') as $group_id => $group) {
			$groups[$group_id] = Inflector::singularize($group['name']);
		}
		return $groups;
	}


}