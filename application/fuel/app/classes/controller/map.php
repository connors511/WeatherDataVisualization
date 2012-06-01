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
class Controller_Map extends Controller_Base
{

	public $template = 'template';
	
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

		$this->template->css = array(
			'http://code.leafletjs.com/leaflet-0.3.1/leaflet.css',
			'http://code.jquery.com/ui/1.8.19/themes/base/jquery-ui.css'
		);
		
		$this->template->js = array(
			'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js',
		    'http://code.jquery.com/ui/1.8.19/jquery-ui.min.js',
		    'http://code.leafletjs.com/leaflet-0.3.1/leaflet.js',
		    'map.content.js'
		);
		
		$this->template->title = "Map";
		$this->template->content = $view;
		
		//return Response::forge($view);
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
