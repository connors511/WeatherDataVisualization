<?php

class Controller_Rest_Jobs extends Controller_Rest
{

	public function get_list()
	{
		$files = \Model_File::find()
			->where('name', '=', '0')
			->count();

		$this->response($files);
	}

}
