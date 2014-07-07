<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains a renderer for the ejournal class
 * A custom renderer class that extends the plugin_renderer_base and is used by the ejournal module.
 *
 * @package   mod_ejournal
 * @copyright 2013 G.Roberts Cardiff Met
 * @license   
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/ejournal/class/locallib.php');

class mod_ejournal_renderer extends plugin_renderer_base {

    /**
     * Render the header.
     *
     * @param assign_header $header
     * @return string
     */
    public function render_ejournal_header(ejournal_header $header) {
        
        $o = '';
		
        $debug = false;

        if($debug)
        {
            echo 'in renderer.render_ejournal_header <br />';
            echo '<hr />';
            echo '<pre>';
            print_r($header);
            echo '</pre>';	
        }

        /*GR to possibly add back in
		if ($header->subpage) {
            $this->page->navbar->add($header->subpage);
        }*/

        // Page information i.e. at top level above the LHS menu and the Plug-in area
        $this->page->set_url('/mod/ejournal/view.php', array('id' => $header->coursemoduleid));		
        //$this->page->set_title(get_string('pluginname', 'ejournal'));
        $this->page->set_heading(format_string($header->course->fullname));

        //$this->page->set_context($context);

        
        $this->page->requires->css('/mod/ejournal/styles/styles.css');    
        
        // If less than IE10 then include the ie9 stylesheet
        $o .= '<!--[if lt IE 10]>';
        $o .= '<link rel="stylesheet" type="text/css" href="styles/ie9.css" />';
        $o .= '<![endif]-->';

        //$this->page->requires->css('/mod/ejournal/styles/ie9.css');                
        $this->page->requires->js('/mod/ejournal/js/general.js',true);

        $o .= $this->output->header();
        if ($header->preface) {
            $o .= $header->preface;
        }
		
	// this is the Plug in heading i.e. below the Page heading
        $heading = format_string($header->ejournal->name, false, array('context' => $header->context));
        $o .= $this->output->heading($heading);

        if ($header->showintro) {
            $o .= $this->output->box_start('generalbox boxaligncenter', 'intro');
            $o .= format_module_intro('ejournal', $header->ejournal, $header->coursemoduleid);
            $o .= $this->output->box_end();
        }

        return $o;
    }

    
    public function render_ejournal_summary_table(ejournal_summary_table $userresponse)
    {
        global $DB, $CFG;

        $debug = false;

        $o = '';
        
        $o .= '<div class="teacher_summary">';
        $o .= '<div class="B">'.$userresponse->title.'</div><br />';

        if($debug)
        { 	
            echo 'In renderer.render_ereflect_user_response with id '.$userresponse->ejournal_id.'<br />';
            echo '<pre>';
            print_r($userresponse);
            echo '</pre>';
        }

        $table = new html_table();
        $table->width = '100%';        
        //$table->attributes = array('class'=>'teacher_summary');

        $nrows = 0;
        foreach($userresponse->user_details as $userrec)
        {
            if($debug)
            {
                echo '<hr />User Loop;<br />';
                echo '<pre>';
                print_r($userrec);
                echo '</pre>';
            }

            // Unique key values that need to be accounted for 
            // outside of the Summary table
            $key_arr = array();
            $key_arr[] = 'viewpost';
            //$key_arr[] = 'id';
            $key_arr[] = 'response_reqd';

            $nrows += 1;

            // Header
            if($nrows==1)
            {			
                $n = 0;
                foreach($userrec as $key => $value)
                {
                    if($debug){echo 'Key is '.$key.', Value is '.$value.'<br />';}
                    $n += 1;

                    //$key!='viewbutton' && $key!='id')
                    if(!in_array($key, $key_arr))
                    {
                        if($key != 'id')
                        {
                            $cell[$n] = new html_table_cell($key);
                        }
                    }
                    else
                    {
                        // View button Header
                        if($key=='viewpost')
                        {  
                            if($value=='YES')
                            {
                                $cell[$n] = new html_table_cell(get_string('summary_view_posts','ejournal')); // 
                                $cell[$n]->attributes = array('class'=>'TAC');														
                            }
                            else
                            {
                                $cell[$n] = new html_table_cell(); // 
                                $cell[$n]->attributes = array('class'=>'TAC');														
                            }
                        }
                    }
                }
                $row = new html_table_row();
                $row->attributes = array('class'=>'tableheader parenttableheader ');							
                $row->cells = $cell;
                $table->data[] = $row;						
            }
	
            // Data
            $user_id = '';
            $n = 0;
            foreach($userrec as $key => $value)
            {
                if($debug){echo 'Key is '.$key.', Value is '.$value.'<br />';}

                if($key=='id')
                {
                    $user_id = $value;
                }

                $n += 1;

                if(!in_array($key, $key_arr))
                {
                    if($key != 'id')
                    {                    
                        $celld[$n] = new html_table_cell($value);

                        if($key=='Picture')
                        {
                            $celld[$n]->attributes = array('class'=>'col5');
                        }
                        else if($key=='Profile')
                        {
                            $celld[$n]->attributes = array('class'=>'col15');
                        }
                        else if($key=='Email')
                        {
                            $celld[$n]->attributes = array('class'=>'col20');
                        }
                        else
                        {
                            $celld[$n]->attributes = array('class'=>'col10');
                        }
                    }
                }
                else
                {
                    if($key=='viewpost')
                    {
                        if($value=='YES')
                        {
                            $urlparams = array('id' => $userresponse->courseid, 'action' => 'VIEWPOSTBYUSER', 'student_id' => $user_id, 'ereflect_id' => $userresponse->params->ereflect_id );
                            $viewanswersurl = new moodle_url('/mod/ejournal/view.php', $urlparams);							
                            $viewanswers = '<a href="'.$viewanswersurl.'"><i class="fa fa-eye fa-2x"></i></a>';					
                            //
                            $celld[$n] = new html_table_cell($viewanswers);
                            $celld[$n]->attributes = array('class'=>'TAC col10');							
                        }
                        else
                        {
                            $celld[$n] = new html_table_cell();
                            $celld[$n]->attributes = array('class'=>'TAC col10');
                        }                        
                    }
                    $response = '';
                    if($key=='response_reqd' && $value == true )                       
                    {
                        $response = 'teacherresponse';
                    }                        
                }								
            }
           
						
            $row2 = new html_table_row();	
            if($response)
            {
                $row2->attributes = array('class'=>$response);							            
            }
            $row2->cells = $celld;
            $table->data[] = $row2;			
        } 
		
        $o .= html_writer::table($table);
        
        $o .= '</div> <!-- End of teacher_summary class -->';        
		
        /*echo '<pre>';
        print_r($CFG);
        echo '</pre>';*/
				
        return $o;		
		
    }    	

	/**
     * Render the generic form
     * @param assign_form $form The form to render
     * @return string
     */
    public function render_ejournal_form(ejournal_form $form) {
	
        $o = '';
		
        /*echo 'In renderer.render_ejournal_form';
        echo '<pre>';
        print_r($form);
        echo '</pre>';*/	
		
        /*gr to place back in at some stage!!!!
        if ($form->jsinitfunction) {
        $this->page->requires->js_init_call($form->jsinitfunction, array());
        }*/
        $o .= $this->output->box_start('boxaligncenter ' . $form->classname);
        $o .= $this->moodleform($form->form);
			
        $o .= $this->output->box_end();
        return $o;
    }
	
    /**
     * Helper method dealing with the fact we can not just fetch the output of moodleforms
     *
     * @param moodleform $mform
     * @return string HTML
     */
    protected function moodleform(moodleform $mform) {

        $o = '';
        ob_start();
        $mform->display();
        $o = ob_get_contents();
        ob_end_clean();		

        return $o;
    }
		
    /**
     * Page is done - render the footer.
     *
     * @return void
     */
    public function render_footer() {
        return $this->output->footer();
    }

}

