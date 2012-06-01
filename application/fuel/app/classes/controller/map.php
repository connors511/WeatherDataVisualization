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
class Controller_Map extends Controller
{

	/**
	 * The basic welcome message
	 * 
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		$radars = DB::select('latitude','longitude')
			->from('files')
			->where('type','=','wrk')
			->where('name','NOT IN',array('0','1'))
			->group_by('latitude')
			->group_by('longitude')
			->execute()
			->as_array();
		
		$wind = DB::select('latitude','longitude','name')
			->from('files')
			->where('type','=','csv')
			->where('name','NOT IN',array('0','1'))
			->group_by('latitude')
			->group_by('longitude')
			->execute()
			->as_array();
		$view = View::forge('map/index');
		$view->set_global('radars',$radars);
		$view->set_global('windmills',$wind);
		return Response::forge($view);
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
