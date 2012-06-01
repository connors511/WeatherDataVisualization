<?php

class Controller_File_Csv extends Controller_Rest {

	public function get_list() {

		//Check for correct type and limit (to take care of the db)
		if (!Model_File_Csv::has_key(Input::get('c')))
			die("Undefined column");

		// ORM is too slow!
		$query = DB::select(Input::get('c'))->
			from('file_csvs')->
			where('file_id', '=', Input::get('id') )->
			and_where('TimeStamps', 'between', array(Input::get('f'), Input::get('t')))->
			order_by('id')->
			execute()->
			as_array();

		foreach ($query as $row) {

			if ($row[Input::get('c')] == "-9999") {
				$result[] = null;
			} else {
				//JS handles dates in microseconds
				$result[] = array((int) strtotime($row['TimeStampsR']) * 1000, (float) $row[Input::get('c')]);
			}
		}

		if (isset($result))
			$this->response($result);
	}

}
