<?php

class Controller_Rest_Radar extends Controller_Rest
{

	public function get_list()
	{

		$cache = 'radar.' . Input::get('id') . '.' . Input::get('f') . '-' . Input::get('t');

		$filename = APPPATH . 'cache' . DS . str_replace('.', '/', $cache) . '.cache';

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
		catch (Exception $e)
		{
			if (!($e instanceof \CacheNotFoundException or $e instanceof \CacheExpiredException))
			{
				// Hopefully doesnt happen
				throw $e;
			}
			$from = Input::get('f', false);
			// ORM is too slow!
			$query = DB::select('file_wrks.id', 'date_time')
				->from('file_wrks')
				->join('files')
				->on('files.id', '=', 'file_wrks.file_id')
				->where('latitude', '=', Input::get('lat'))
				->and_where('longitude', '=', Input::get('lng'));
			if ($from)
			{
				$query->and_where('date_time', '>', $from);
			}
			$query = $query->order_by('date_time')
				->execute()
				->as_array();
			
			$result = array();
			foreach ($query as $row)
			{
				//JS handles dates in microseconds
				$result[] = array(/*(int) strtotime($row['date_time']) * 1000,*/ "radar/{$row['id']}.png");
			}

			Cache::set($cache, $result);
		}
		$this->response = $resp;
		$this->response($result);
	}

}
