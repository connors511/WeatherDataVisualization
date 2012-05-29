<?php

class Controller_File_Csv extends Controller_Rest
{

	public function get_list() {
		
                //Check for correct type and limit (to take care of the db)
//<<<<<<< HEAD
//=======
                //if(!Model_File_Csv::has_key(Input::get('c')) || (int)(Input::get('t') - Input::get('f')) > 20000)
			//die("Undefined column or too big timespan");
//>>>>>>> 25b13d01808be82b0a2746fc568bd7a85cdeaef8
                
                //$query = Model_File_Csv::find()->
                //    where('file_id',Input::get('id'))->
                //    where('TimeStamps', 'between', array(Input::get('f'),Input::get('t')))->
                //    order_by('TimeStampsR','desc')->
                //    get();
                    
                // ORM is too slow!
				$query = DB::select('*')->
					from('file_csvs')->
					where('file_id', Input::get('id',1))->
					and_where('TimeStamps', 'between', array(Input::get('f'),Input::get('t')))->
					order_by('id')->
					execute()->as_array();
		
                foreach($query as $row) {
                    
			if($row[Input::get('c')] == "-9999") {
			        $result[] = null;
			} else {
				//JS handles dates in microseconds
				$result[] = array((int)strtotime($row['TimeStampsR'])*1000, (float)$row[Input::get('c')]);
			}
                }
                
                if(isset($result))
			$this->response($result);

	}

}