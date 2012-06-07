<?php

/**
 * The Welcome Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 * 
 * @package  app
 * @extends  Controller
 */
class Controller_Chart extends Controller
{

	/**
	 * The basic welcome message
	 * 
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		$cols = Model_File_Csv::properties();
		unset($cols['id'], $cols['TimeStamps'], $cols['TimeStampsR'], $cols['file_id']);
		View::set_global('columns', array_keys($cols));
		View::set_global('lat',Input::get('lat'));
		View::set_global('lng',Input::get('lng'));
		return Response::forge(View::forge('chart/index'));
	}

	/**
	 * The 404 action for the application.
	 * 
	 * @access  public
	 * @return  Response
	 */
	public function action_404()
	{
		return Response::forge(ViewModel::forge('welcome/404'), 404);
	}
}
