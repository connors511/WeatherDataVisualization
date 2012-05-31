<?php
class Model_File extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'name' => array(
			'data_type' => 'varchar',
			'label' => 'Name',
			'form' => array('type' => false),
		),
	    'latitude' => array(
			'data_type' => 'varchar',
			'label' => 'Latitude',
			'form' => array('type' => false),
		),
	    'longitude' => array(
			'data_type' => 'varchar',
			'label' => 'Longitude',
			'form' => array('type' => false),
		),
		'path' => array(
			'data_type' => 'text',
			'label' => 'File',
			'form' => array('type' => 'file','class'=>'span6'),
		),
		'type' => array(
			'data_type' => 'varchar',
			'label' => 'Type',
			'form' => array('type' => false),
		),
		'user_id' => array(
			'data_type' => 'int',
			'label' => 'User ID',
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
	
	protected static $_has_many = array(
	    'file_csvs' => array(
			'cascade_save' => false,
			'cascade_delete' => false,			      
		),
	);
	
	protected static $_belongs_to = array(
	    'user'
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
		'Observer_CreatedUser' => array(
                        'events' => array('before_insert'),
                ),
		'Orm\\Observer_Validation' => array(
			'events' => array('before_save'),
		),
	);

}
