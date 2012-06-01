<?php

class Controller_File extends Controller {

	public function action_upload() {
            
            $this->_submit_upload();
            
		$view = View::forge('file/upload');
		
		$view->title = 'File &raquo; Upload';
		
		$upload_form = Fieldset::forge('upload');
                
                $upload_form->form()->set_attribute('enctype','multipart/form-data');
//		$upload_form->set_config(array('enctype'=>'multipart/form-data'));
//                print_r($upload_form->get_config());
                
		$upload_form->form()->add('file', 'Fil:', array('type' => 'file'));
		$upload_form->form()->add('submit', '', array('type' => 'submit', 'value' => 'Upload'));
	
		$view->set('form', $upload_form->build(Uri::create('file/upload/submit')), false);

		return $view;
	}
        
        private function _submit_upload() {
            
            if(Uri::segment(3) == 'submit' && Input::method() == 'POST') {
			
                Upload::process(array(
		    'path' => DOCROOT.DS.'files',
		    'randomize' => true,
		    'ext_whitelist' => array('csv'),
		));
                        
                Upload::register('after', function (&$file) {
		    if ($file['error'] == Upload::UPLOAD_ERR_OK)
		    {
                        
                        $model = Model_File::forge(array(
                            'path'      => $file['saved_to'].$file['saved_as'],
                            'type'      => $file['extension'],
                            'timestamp' => DB::expr('now()'),
                            'user_id'   => 0
                        ));
                        $model->save();
                        
                        //TODO: more file types
                        
                        // Outputs all the result of shellcommand "ls", and returns
                // the last output line into $last_line. Stores the return value
                // of the shell command in $retval.
                $last_line = system('D:\\wamp\\www\\fagprojekt\\release\\DataParser.exe --file='.$file['saved_to'].$file['saved_as'].' --type='.$file['extension'].' --fileid='.$model->id.'', $retval);
            echo 'RET: ' . $retval;
                if($retval < 2) {
                    $result = DB::query("
				LOAD DATA INFILE '".str_replace('\\\\\\\\','\\\\',str_replace('\\','\\\\',$file['saved_to'].$file['saved_as']))."' 
				INTO TABLE fagprojekt.file_csvs 
				FIELDS TERMINATED BY ',' ENCLOSED BY '\"' 
				LINES TERMINATED BY '\n'
				IGNORE 1 LINES
				(TimeStamps,PossiblePower,WindSpeed,RegimePossible,OutputPower,RegimeOutput,TimeStampsR,file_id)
			")->execute();
                
		    }
                    }
		}
                );
                if( Upload::is_valid() )
	    {
		Upload::save();
                    
                    
                //Response::redirect('file/upload');
            
            
                
            
            
            }
	    


			
		}
            
        }
	

}
