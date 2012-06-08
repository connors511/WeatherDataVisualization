<?php

class Controller_File_Csv extends Controller_Rest {

	public function get_list() {

		//Check for correct type and limit (to take care of the db)
		if (!Model_File_Csv::has_key(Input::get('c')))
			die("Undefined column");

		$cache = Input::get('c') .'.'. Input::get('f') .'-'. Input::get('t');
		
		$filename = APPPATH . 'cache' . DS . str_replace('.','/',$cache) . '.cache';
		
		$resp = new Response();
		$resp->set_header('Cache-Control', 'private, max-age=10800, pre-check=10800');
		$resp->set_header('Expires', date(DATE_RFC822, strtotime(" 7 day")));
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) and (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime($img)))
		{
			// send the last mod time of the file back
			$resp->set_header('Last-Modified', gmdate('D, d M Y H:i:s', filemtime($img)) . ' GMT');
			$resp->set_status(304);
			return $resp;
		}
		
		try
		{
			$result = Cache::get($cache, true);
		}
		catch(Exception $e)
		{
			if (!($e instanceof \CacheNotFoundException or $e instanceof \CacheExpiredException))
			{
				// Hopefully doesnt happen
				throw $e;
			}
			// ORM is too slow!
			$query = DB::select(Input::get('c'),'TimeStampsR')->
				from('file_csvs')->
				where('file_id', '=', Input::get('id') )->
				and_where('TimeStamps', 'between', array(Input::get('f'), Input::get('t')))->
				order_by('TimeStampsR')->
				execute()->
				as_array();

			$result = array();
			foreach ($query as $row) {

				if ($row[Input::get('c')] == "-9999") {
					$result[] = null;
				} else {
					//JS handles dates in microseconds
					$result[] = array((int) strtotime($row['TimeStampsR']) * 1000, (float) $row[Input::get('c')]);
				}
			}
			
			Cache::set($cache, $result);
		}
		$this->response = $resp;
		$this->response($result);
	}

}
