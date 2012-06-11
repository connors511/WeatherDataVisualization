<?php

class Model_User extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'username' => array(
			'data_type' => 'varchar',
			'label' => 'Username',
			'validation' => array('required', 'min_length' => array(3), 'max_length' => array(255)),
			'form' => array('type' => 'text', 'class' => 'span4'),
		),
		'password' => array(
			'data_type' => 'varchar',
			'label' => 'Password',
			'validation' => array('required', 'min_length' => array(3), 'max_length' => array(255)),
			'form' => array('type' => 'password', 'class' => 'span4'),
		),
		'email' => array(
			'data_type' => 'varchar',
			'label' => 'E-mail',
			'validation' => array('required', 'valid_email', 'min_length' => array(3), 'max_length' => array(255)),
			'form' => array('type' => 'text', 'class' => 'span4'),
		),
		'group' => array(
			'data_type' => 'int',
			'label' => 'Group',
			'validation' => array('required', 'max_length' => array(255)),
			'form' => array('type' => 'select', 'class' => 'span4', 'options' => array(),'value'=>''),
		),
		'last_login' => array(
			'data_type' => 'int',
			'label' => 'Last Login',
			'form' => array('type' => false),
		),
		'login_hash' => array(
			'data_type' => 'varchar',
			'label' => 'Login Hash',
			'form' => array('type' => false),
		),
		'profile_fields' => array(
			'data_type' => 'text',
			'label' => 'Profile Fields',
			'form' => array('type' => false),
		),
		'created_at' => array(
			'data_type' => 'int',
			'label' => 'Created At',
			'form' => array('type' => false),
		),
		'updated_at' => array(
			'data_type' => 'int',
			'label' => 'Updated At',
			'form' => array('type' => false),
		),
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => false,
		),
		'Orm\\Observer_Validation' => array(
			'events' => array('before_save'),
		),
	);
	
	public static function _init() {
		foreach(\Config::get('simpleauth.groups') as $group_id => $group) {
			$groups[$group_id] = Inflector::singularize($group['name']);
		}
		self::$_properties['group']['form']['options'] = $groups;
		self::$_properties['group']['form']['value'] = 1;
	}
}
