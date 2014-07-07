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
 * Internal library of functions for module ejournal
 * 
 * 
 * All the ejournal specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package   mod_ejournal
 * @copyright 2013 G.Roberts Cardiff Met
 * @license   
 */
 
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/ejournal/class/renderable.php');
//require_once($CFG->libdir . '/formslib.php');

/** @var string action to be used to return to this page
*              (without repeating any form submissions etc).
*/


/**
 * Does something really useful with the passed things
 *
 * @param array $things
 * @return object
 */
 
//function wiggy_do_something_useful(array $things) {
//    return new stdClass();
//}


//
// Graeme_r This is based on locallib.php from the Assign module i.e. mod\assign\locallib.php
//

class ejournal {

    /** @var stdClass the assignment record that contains the global settings for this assign instance */
    private $instance;
	
    /** @var assign_renderer the custom renderer for this module */
    private $output;
		
    private $returnaction = 'view';
		
    /** @var string instructions for the submission phase */
    public $completion_message;
	
	
    /**
     * Constructor for the base ejournal class.
     *
     * @param mixed $coursemodulecontext context|null the course module context
     *                                   (or the course context if the coursemodule has not been
     *                                   created yet).
     * @param mixed $coursemodule the current course module if it was already loaded,
     *                            otherwise this class will load one from the context as required.
     * @param mixed $course the current course  if it was already loaded,
     *                      otherwise this class will load one from the context as required.
     */
    public function __construct ($coursemodulecontext, $coursemodule, $course ) {
	
        global $PAGE;

        $this->context = $coursemodulecontext;
        $this->coursemodule = $coursemodule;
        $this->course = $course;

        // Load the capabilities for this user and questionnaire, if not creating a new one.
        /*if (!empty($this->coursemodule->id)) {
            //echo 'coursemodule id: '.$this->coursemodule->id.'<br />';	
            //$this->capabilities = questionnaire_load_capabilities($this->coursemodule->id);
            $this->capabilities = $this->questionnaire_load_capabilities($this->coursemodule->id);
        }*/
		
        // Temporary cache only lives for a single request - used to reduce db lookups.
        $this->cache = array();

        // GR Look into this later
        //$this->submissionplugins = $this->load_plugins('assignsubmission');
        //$this->feedbackplugins = $this->load_plugins('assignfeedback');
    }
	
    public function view ( stdclass $parameters )
    {
        global $CFG, $DB, $USER;

        $debug = false;
        $o = '';	
        $mform 	= null;			
        $nextpageparams = array();	
        $notices = array();		

        $instance = $this->get_instance();
        $module = $this->get_course_module();
        $module_name = $module->modname;
        $context = $this->get_context();

        // Testing to see what priveleges are
        //$this->users_who_can_teach();
        //$this->users_who_can_complete();
          

        if($debug)
        {
            echo 'In locallib.view <br />';

            echo 'Instance: <br />';
            echo '<pre>';
            print_r($instance);
            echo '</pre>';

            echo 'Now Module <br />';			
            echo '<pre>';
            print_r($module);
            echo '</pre>';
            echo 'Module Name = '.$module_name.'<br />';

            echo '<hr />';
            echo 'Parameters: <br />';
            echo '<pre>';
            print_r($parameters);
            echo '</pre>';
            
            echo '<hr />';
            echo 'Context: <br />';
            echo '<pre>';
            print_r($context);
            echo '</pre>';            
        }	
        
		
        // GR New since passing in class of parameters into View function
        //$ejournal_id = $parameters->ejournal_id;
        //$eq_id = $parameters->eq_id;
        $action = strtoupper($parameters->action);
        
        if($debug){echo 'action = '.$action.'<br />';}
        
        if($action == 'ADDPOSTPROCESS')
        {
            if($debug){echo 'In locallib->view.ADDPOSTPROCESS <br />';}

            if (optional_param('addpost', null, PARAM_RAW)) 
            {   
                if ($this->process_new_post($mform, $notices, $parameters))
                {
                    //echo 'Passed process, ereflect_id: '.$parameters->ereflect_id.'<br />';
                    //exit();
                    // Ensure you redirect so that you can't get a resubmit
                    $action = 'redirect';
                    $nextpageparams['id'] = $module->id;				
                    $nextpageparams['ereflect_id'] = $parameters->ereflect_id;	
                    $nextpageparams['student_id'] = $parameters->student_id;	
                    
                    // Need to cater for whether a student or a teacher
                    // If a teacher, then go to this
                    $nextpageparams['action'] = 'VIEWPOSTBYUSER'; // i.e. ADDPOST
                    // If a student then go to VIEWPOSTBYEREFLECT
                }
            }
            else if (optional_param('requery', null, PARAM_RAW))
            {
                if($debug){echo 'In requery section <br />';}
                
                $action = '';        // View Drop down lists   
            }
            else if (optional_param('cancelandreturn', null, PARAM_RAW)) 
            {
                if($debug){echo 'In cancel and return section of ADDPOSTPROCESS <br />';}

                $action = 'RETURNTOMENU';
                
                /*$action = 'redirect';
                $nextpageparams['id'] = $module->id;				
                $nextpageparams['action'] = 'RETURNTOMENU';*/
            }
            else if (optional_param('viewpdf', null, PARAM_RAW)) 
            {
                if($debug){echo 'in viewpdf optional param as part of submit';}
                
                $parameters->view_pdf = true;
                
                $action = 'VIEWPOSTBYUSER';
            }            
            
            /* not required now - just pdf instead, but leave in for time being.
             * else if (optional_param('viewereflect', null, PARAM_RAW)) 
            {
                // Get the course module id for the ereflect in question
                // Get Student Details
                $sql = 'SELECT e.id, cm.id coursemoduleid, e.name 
                        FROM {ereflect} e
                        JOIN {course_modules} cm ON (e.course = cm.course AND e.id = cm.instance)
                        JOIN {modules} m ON (cm.module = m.id AND m.name = \'ereflect\')
                        WHERE e.id= ?
                        ORDER BY e.timecreated';
                
                        //AND e.status = \'STUDENTENTRY\'

                $eref = $DB->get_records_sql($sql, array($parameters->ereflect_id));
            
                $erffarr = array();
                foreach($eref as $erffarr)
                {
                    if($debug)
                    {
                        echo '<pre>';
                        print_r($erffarr);
                        echo '</pre>';
                    }

                    // Its an object within an array
                    $id = $erffarr->id;
                    $cm_id = $erffarr->coursemoduleid;
                    $name = $erffarr->name;
                }
                
                //$nextpageparams = array("id" => $coursemod->id, "user_id" => $parameters->student_id, "action" => "VIEWSTUDENTANSWERSFROMEJOURNAL", "view" => "EJOURNALVIEW", "ejournal_id" => $instance->id );
                $nextpageparams = array("id" => $cm_id, "student_id" => $parameters->student_id, "action" => "VIEWSTUDENTANSWERSFROMEJOURNAL", "view" => "EJOURNALVIEW", "ejournal_id" => $instance->id );
                $nextpageurl = new moodle_url('/mod/ereflect/view.php', $nextpageparams);

                redirect($nextpageurl);
                return;
            }*/
        }        
		
        $returnparams = ''; 
        $this->register_return_link($action, $returnparams);

        //  Now show the right view pane
        if ($action == 'redirect') 
        {
            //echo 'In redirect <br />';
            $nextpageurl = new moodle_url('/mod/ejournal/view.php', $nextpageparams);

            //$nextpageurl = new moodle_url('/course/modedit.php', $nextpageparams);			
            //echo 'Next page url: '.$nextpageurl.'<br />';
            //exit();
            redirect($nextpageurl);
            return;
        }
        else if($action == 'VIEWPOSTBYEREFLECT')
        {
            if($debug){ echo 'In VIEWPOSTBYEREFLECT';}            
            
               // If a Teacher then ability to view response statistics
            if($this->isateacher( $USER->id ) ||
                     has_capability('mod/ejournal:grade', $context) )
            {
               $o .= $this->view_response_stats($notices, $parameters );			
            }
            else
            {
                $parameters->student_id = $USER->id;
                $o .= $this->view_mainpost_page( $mform, $notices, $parameters );
            }
            // else student then just show mainposts p[age
            //$o .= $this->view_mainpost_page( $mform, $notices, $parameters );
        }
        else if($action == 'VIEWPOSTBYUSER')
        {
            if($debug){ echo 'In VIEWPOSTBYUSER';}
            
            if($this->student_teacher_check( $parameters ) ||
                    has_capability('mod/ejournal:grade', $context) )
            {            
                // Always a teacher coming from this drop down list, and so go straight to mainpost page
                $o .= $this->view_mainpost_page( $mform, $notices, $parameters );
            }
            else
            {
                // You can only view the student and teacher posts if you are indeed the student or teacher in question                
                $notices[] = get_string('notstudentteacher','ejournal');
                
                // Entry point for the Plugin i.e. To choose eReflect and User id from drop down list
                $o .= $this->view_ereflect_dropdownlist( $mform, $notices, $parameters );                
            }
        }
        else if($action == 'VIEWPOSTBYUSERFROMEREFLECT')
        {
             // Always a teacher coming from this drop down list, and so go straight to mainpost page
             $o .= $this->view_mainpost_page( $mform, $notices, $parameters );        
        }
        else if($action == 'RETURNTOMENU')
        {
            $course = $this->get_course();
            $section = 1;
            redirect(course_get_url($course, $section, array('sr' => 0)));
            return;
        }		
        else
        {
            /*if($this->isateacher( $USER->id ))
            {               
               $o .= $this->view_response_stats($notices, $parameters );			
            }            
            else
            {*/
                // is a student
                $o .= $this->view_ereflect_dropdownlist( $mform, $notices, $parameters );
            //}
        }
												
        return $o;
    }
	   
    private function view_ereflect_dropdownlist($mform, $notices, $params ) 
    {
        global $CFG, $DB; //, , $USER, $PAGE;
		
        $debug = false;
				
        $instance = $this->get_instance();		
        $cm = $this->get_course_module();

        $o = '';	
        $o .= $this->get_renderer()->render(new ejournal_header($instance,
                                              $this->get_context(),
                                              $this->show_intro(),
                                              $this->get_course_module()->id,
                                              $this->get_course()),
                                              get_string('ereflectdropdownlist','ejournal'));
													  			
        if($debug)
        {
            echo 'In locallib.view_ereflect_dropdownlist <br />';
            
            echo 'Instance <br />';
            echo '<pre>';
            print_r($instance);
            echo '</pre>';
            
            echo 'Parameters <br />';
            echo '<pre>';
            print_r($params);
            echo '</pre>';            
        }
        
            	
        // Notices - This is out of the box
        foreach ($notices as $notice) {
            $o .= $this->get_renderer()->notification($notice);
        }        
                
        $sql = 'SELECT id, name 
                FROM {ereflect}
                WHERE course = ?
                AND status = \'STUDENTENTRY\'
                ORDER BY timecreated';
					
        $eref = $DB->get_records_sql($sql, array($instance->course));
        
        if($debug)
        {
            echo 'Ereflect entries: <br />';
            echo '<pre>';
            print_r($eref);
            echo '</pre>';            
        }
        
        $erffarr2 = array();
        //$erffarr2[''] = 'please choose';
        foreach($eref as $erffarr)
        {
            if($debug)
            {
                echo '<pre>';
                print_r($erffarr);
                echo '</pre>';
            }
            
            // Its an object within an array
            $id = $erffarr->id;
            $name = $erffarr->name;
            
            $erffarr2[$id] = $name;                      
        }
                
        $field = 'ereflect_id';        
        $attributes = array();
        $selected = array();

        $o .= '<div class="div_field_row"><label>'.get_string('ereflect','ejournal').'</label>';
        $o .= html_writer::select( $erffarr2, $field, 2 );
        
        $urlparams = array('id' => $cm->id, 'action'=>'VIEWPOSTBYEREFLECT');
        $ereflecturl = new moodle_url('/mod/ejournal/view.php', $urlparams);
        $ereflect = '<button id="id_ereflectgo" onclick="ereflectgo(\''.$ereflecturl.'\');">Go</button>';
        
        $o .= $ereflect;
        $o .= '</div>';   
        
        /* NEED TO PLACE A RETURN TO MAIN MENU BUTTON HERE */
        /*$returnparams = array("id" => $cm->id, "action" => 'RETURNTOMENU' );
        $returnurl = new moodle_url('/mod/ejournal/view.php', $returnparams);
        $o .= '&nbsp;&nbsp;<a href="'.$returnurl.'"><button>'.get_string('returntocourse','ejournal').'</button></a>';*/

        
        $o .= $this->view_footer();
	
        return $o;
    }
    
    /**
     * Function view_user_dropdownlist
     * This can only be observed and used by a teacher  
     **/
    private function view_user_dropdownlist($mform, $notices, $params ) 
    {
        global $CFG, $DB;
		
        $debug = false;
		
        require_once($CFG->dirroot . '/mod/ejournal/class/mainpost_form.php');
		
        $instance = $this->get_instance();		
        $cm = $this->get_course_module();

        $o = '';	
        $o .= $this->get_renderer()->render(new ejournal_header($instance,
                                              $this->get_context(),
                                              $this->show_intro(),
                                              $this->get_course_module()->id,
                                              $this->get_course()),
                                              get_string('userdropdownlist','ejournal'));
													  			
        if($debug)
        {
            echo 'In locallib.view_user_dropdownlist<br />';
            
            echo 'Instance <br />';
            echo '<pre>';
            print_r($instance);
            echo '</pre>';
            
            echo 'Parameters <br />';
            echo '<pre>';
            print_r($params);
            echo '</pre>';
            
        }
        
        // Notices - This is out of the box
        foreach ($notices as $notice) {
            $o .= $this->get_renderer()->notification($notice);
        }        
        
        // Firstly show the eReflect questionnaire details
        $o .= $this->get_ereflect_details( $params );

        // Now, show the User drop down list
        $usersarr = $this->ereflect_users();
        
        $arrusers = array();
        foreach($usersarr as $u)
        {
            if($debug)
            {
                echo '<pre>';
                print_r($u);
                echo '</pre>';
            }
            
            // Its an object within an array
            $id = $u->id;
            $name = $u->firstname.' '.$u->lastname;
            
            $arrusers[$id] = $name;                      
        }
                
        $field = 'student_id';        
        $attributes = array();
        $selected = array();

        $o .= '<div class="div_field_row"><label>'.get_string('user','ejournal').'</label>';
        $o .= html_writer::select( $arrusers, $field, 2 );
        
        $urlparams = array('id' => $cm->id, 'action'=>'VIEWPOSTBYUSER', 'ereflect_id' => $params->ereflect_id );
        $userurl = new moodle_url('/mod/ejournal/view.php', $urlparams);
        $userdet = '<button id="id_ereflectgo" onclick="usergo(\''.$userurl.'\');">Go</button>';
        
        $o .= $userdet;
        $o .= '</div>';
                                       	 
        
        $o .= $this->view_footer();
	
        return $o;
    }    
        
    /* Screen is specific for Modifying the questions only  */
    private function view_mainpost_page($mform, $notices, $params ) 
    {
        global $CFG, $DB; // $USER, $PAGE;
		
        $debug = false;
		
        require_once($CFG->dirroot . '/mod/ejournal/class/mainpost_form.php');
		
        $instance = $this->get_instance();
        $context = $this->get_context();

        $o = '';	
        $o .= $this->get_renderer()->render(new ejournal_header($instance,
                                              $this->get_context(),
                                              $this->show_intro(),
                                              $this->get_course_module()->id,
                                              $this->get_course()),
                                              get_string('mainpostpage','ejournal'));
													  			
        if($debug)
        {
            echo 'In locallib.view_mainpost_page <br />';
            
            echo 'Instance <br />';
            echo '<pre>';
            print_r($instance);
            echo '</pre>';
            
            echo 'Parameters <br />';
            echo '<pre>';
            print_r($params);
            echo '</pre>';            
            
            echo 'Context <br />';
            echo '<pre>';
            print_r($context);
            echo '</pre>';           
        }

        // Firstly show the eReflect questionnaire details
        $o .= $this->get_ereflect_details( $params );
        
        // Show the user details
        //$o .= $this->get_user_details( $params );
        if(isset($params->student_id) && strlen($params->student_id))
        {
            $o .= $this->get_student_profile ( $params->student_id );
        }
       
        // Notices - This is out of the box
        foreach ($notices as $notice) {
            $o .= $this->get_renderer()->notification($notice);
        }
        
        // Drop down list to sort the Post Order by
        $o .= $this->get_orderby_list( $params );        
                           
        $data = new stdClass();        
        if (!$mform) {
            
            if($debug)
            {
                echo 'Not mform <br />';
            }
            $mform = new mod_ejournal_mainpost_form(null, array($this, $data, $params, $instance));
        }	
        else
        {
            if($debug)
            {
                echo 'Is mform <br />';
            }
        }
        $data = $mform->get_data();
        
        // If order_by is set and dateasc or userasc then show here or if its not set then show here
        if(!isset($params->order_by) || !(strlen($params->order_by)))
        {
            $params->order_by = 'dateasc';
        }        
    
        // Show the editor field before the posted details if date descending or user, date descending
        if(isset($params->order_by) && (($params->order_by == 'datedesc') || ($params->order_by == 'userdesc')) )
        {  
            // Only show the form if a student or teacher
            // i.e. admin to just see the post history
            //if($this->student_teacher_check( $params ) )
            //{
                $o .= $this->get_renderer()->render(new ejournal_form('', $mform));
            //}
        }        
        
        // Now show the post details history
        $o .= $this->get_post_details( $params );
 
        if( ($params->order_by == 'dateasc') || ($params->order_by == 'userasc') )
        {  
            // Only show the form if a student or teacher
            // i.e. admin to just see the post history
            //if($this->student_teacher_check( $params ) )
            //{            
                $o .= $this->get_renderer()->render(new ejournal_form('', $mform));
            //}
        }

        if(isset($params->view_pdf) && $params->view_pdf)
        {
            //$o .= 'Will add some Javascript code here to pop up the window<br />';
            
            // Get the course module id for the ereflect in question
            $sql = 'SELECT e.id, cm.id coursemoduleid, e.name 
                    FROM {ereflect} e
                    JOIN {course_modules} cm ON (e.course = cm.course AND e.id = cm.instance)
                    JOIN {modules} m ON (cm.module = m.id AND m.name = \'ereflect\')
                    WHERE e.id= ?
                    AND e.status = \'STUDENTENTRY\'
                    ORDER BY e.timecreated';

            $eref = $DB->get_records_sql($sql, array($params->ereflect_id));
            
            $erffarr = array();
            foreach($eref as $erffarr)
            {
                if($debug)
                {
                    echo '<pre>';
                    print_r($erffarr);
                    echo '</pre>';
                }

                // Its an object within an array
                $id = $erffarr->id;
                $cm_id = $erffarr->coursemoduleid;
                $name = $erffarr->name;
            }
                                                          
            $urlparams = array('id' => $cm_id, 'student_id' => $params->student_id, 'action' => 'VIEWPDF');
            $viewpdfurl = new moodle_url('/mod/ereflect/view.php', $urlparams);
            
            $o .= '<script type="text/javascript">openwindow(\''.$viewpdfurl.'\');</script>';            
        }
        
        
        $o .= $this->view_footer();
	
        return $o;
    }
    
    protected function get_ereflect_details ( $params )
    {
        global $DB;
        
        $instance = $this->get_instance();
        
        $debug = false;
        
        $o = '';
        
        if($debug)
        {
            echo 'In get_ereflect_details';
            echo '<pre>';
            print_r($params);
            echo '</pre>';
        }
        
        // Get Student Details
        $table = 'ereflect';	
        $conditions = array('id' => $params->ereflect_id);
        $eref = $DB->get_record($table, $conditions);	

        if($debug)
        {
            echo 'Ereflect entries: <br />';
            echo '<pre>';
            print_r($eref);
            echo '</pre>';          
        }
        
        $o .= '<div id="div_eq">';
        $o .= '   <div id="div_eq_name">'.get_string('ereflect','ejournal').' '.$eref->name.'</div>';
        
        // Need to ensure that the description field is showing any images etc.
        // and getting rid of any pluginfile text etc.
        
        //$o .= 'Description: '.$eref->intro.'<br />';  
        
        // To get the Module id for the ereflect
        $table = 'modules';
        $return = "id";
        $conditions = array("name" => 'ereflect');
        $moduleid = $DB->get_field($table, $return, $conditions, MUST_EXIST);        
        
        // To get the context for the ereflect in question 
        $table = 'course_modules';
        $return = "id";
        $conditions = array("course" => $instance->course, "instance" => $eref->id, "module" => $moduleid);
        $returnvalue = $DB->get_field($table, $return, $conditions, MUST_EXIST);
                        
        
        //$contextereflect = context::instance_by_id($returnvalue);
        if($debug)
        {
            echo 'Return value = '.$returnvalue.'<br />';
        }
        
        $context_ereflect = context_module::instance($returnvalue);
        
        if($debug)
        {
            echo 'Got eReflect context<br />';
            echo '<pre>';
            print_r($context_ereflect);
            echo '</pre>';    
        }
        
        $mesg = file_rewrite_pluginfile_urls($eref->intro, 'pluginfile.php', $context_ereflect->id,
                                'mod_ereflect', 'intro', null, ejournal::instruction_editors_options($context_ereflect));
            
        $o .= '<div id="div_eq_desc_title">'.get_string('ereflect_desc','ejournal').'</div>';
        $o .= '<div id="div_eq_desc">'.$this->output->box(format_text($mesg, FORMAT_HTML, array('overflowdiv'=>true)), array('generalbox')).'</div>';
        $o .= '</div> <!-- End of div_eq -->';
        
        return $o;
                
    }
    
    public function get_student_profile($user_id)
    {
        global $DB;		
        $debug = false;

        if($debug)
        {
            echo 'In get_student_profile <br />';
        }

        //$instance = $this->get_instance();		
        $course = $this->get_course();

        $o = '';

        $table = 'user';
        $conditions = array('id' => $user_id);		
        if($ur = $DB->get_record($table, $conditions))
        {
            if($debug)
            {
                echo '<pre>';
                print_r($ur);
                echo '</pre>';
            }
        }

        $picture = $this->output->user_picture($ur, array('courseid' => $course->id)); 		

        $o .= '<div id="div_eq">';
        $o .= '   <span>'.$picture.'</span>&nbsp;<span>'.$ur->firstname.' '.$ur->lastname.'</span>';
        $o .= '</div>';

        return $o;

    }
		        
    protected function get_post_details( $params )
    {
        global $CFG, $DB, $USER;
        
        $instance = $this->get_instance();
        $context = $this->get_context();
        
        $debug = false;
        
        $o = '';
        //$o .= '<input type="button" value="Press here to show example" onclick="window.location.hash = \'89\';"/>';        
        
        // Get all teachers on this course
        $teachers = $this->ereflect_teachers();      
        
        $n = 0;
        $user_arr = array();
        foreach($teachers as $t)
        {
           $id = $t->id;
           $user_arr[] = $id;
        }
        // Also, add the particular student id to the string
   
        if(isset($params->student_id) && strlen($params->student_id))
        {
            $user_arr[] = $params->student_id;
        }
        
        if($debug)
        {
            echo 'In get_post_details';
            
            echo 'Parameters: <br />';
            echo '<pre>';
            print_r($params);
            echo '</pre>';
            
            echo 'Teachers: <br />';
            echo '<pre>';
            print_r($teachers);
            echo '</pre>';                 
            
            echo 'User Ids: ';
            echo '<pre>';
            print_r($user_arr);
            echo '</pre>';                 

        }
        
        // User array is ready to be used in the SQL....
        // However, we need to have the equivalent number of question marks ?
        //
        $user_arr_cnt = COUNT($user_arr);
        $user_id_str = '';
        for($i=1; $i<=$user_arr_cnt; $i++)
        {
            if($i==1)
            {
                $user_id_str .= '?';
            }
            else
            {
               $user_id_str .= ',?';
            }
        }
                
        /*Need to include the teachers posts as well as the particular students
        $table = 'ejournal_details';	
        $conditions = array('ereflect_id' => $params->ereflect_id, 'user_id' => $params->student_id);
        $ejdet = $DB->get_records($table, $conditions, 'timecreated');*/
        
        $sql = 'SELECT *
                FROM {ejournal_details}
                WHERE ejournal_id = ?
                AND ereflect_id = ?
                AND student_id = ?
                AND user_id IN ('.$user_id_str.')';

        switch ($params->order_by) {
        case 'dateasc':
            $sql .=  ' ORDER BY timecreated';
            break;
        case 'datedesc':
            $sql .=  ' ORDER BY timecreated desc';
            break;
        case 'userasc':
            $sql .=  ' ORDER BY user_id, timecreated';
            break;
        case 'userdesc':
            $sql .=  ' ORDER BY user_id, timecreated desc';
            break;
        }
        
        
        if($debug){ echo 'SQL String: '.$sql.'<br />';}

        $conditions = array_merge( array($instance->id, $params->ereflect_id, $params->student_id ), $user_arr);
        
        if($debug)
        {
            echo 'Where clause<br />';
            echo '<pre>';
            print_r($conditions);
            echo '</pre>';    
        }

        //$ejdet = $DB->get_records_sql($sql, array($params->ereflect_id, $user_str));        
        $ejdet = $DB->get_records_sql($sql, $conditions);        
        
        if($debug)
        {
            echo 'Printing Post history<br />';
            echo '<pre>';
            print_r($ejdet);
            echo '</pre>';    
        }
        
        // Gets the teacher array once
        $teacherid_arr = $this->get_teacherids();
        
        if($debug)
        {
            echo 'teacher ids <br />';
            echo '<pre>';
            print_r($teacherid_arr);
            echo '</pre>';
        }
        
        foreach($ejdet as $ejd)
        {
            if($debug)
            {
                echo '<pre>';
                print_r($ejd);
                echo '</pre>';                  
            }
            
            $posteddate = getdate($ejd->timecreated);
            $vdate = str_pad($posteddate['mday'],2,"0", STR_PAD_LEFT).'/';                    
            $vdate .= str_pad($posteddate['mon'],2,"0", STR_PAD_LEFT).'/';
            $vdate .= str_pad($posteddate['year'],2,"0", STR_PAD_LEFT).' ';
            $vdate .= str_pad($posteddate['hours'],2,"0", STR_PAD_LEFT).':';
            $vdate .= str_pad($posteddate['minutes'],2,"0", STR_PAD_LEFT).':';
            $vdate .= str_pad($posteddate['seconds'],2,"0", STR_PAD_LEFT);        
                        
            // Get User details for each post
            $table = 'user';	
            $conditions = array('id' => $ejd->user_id );
            $ejuser = $DB->get_record($table, $conditions);            
            
            $o .= '<a href="#'.$ejd->id.'"></a>';
            
            if (in_array($ejd->user_id, $teacherid_arr )) {
                // Post box is Teacher Colours
                $o .= '<div class="div_post div_post_teacher div_pp_'.$ejd->id.'">';                
            }
            else
            {
                // Post box is Student colours F2DEDE
                $o .= '<div class="div_post div_post_student div_pp_'.$ejd->id.'">';                
            }
            
            // this is the Plug in heading i.e. below the Page heading
            //$heading = format_string($header->ejournal->name, false, array('context' => $header->context));
            
            $o .= '<a class="collexp" id="minus_id_'.$ejd->id.'" href="#" onclick="collexpandpost(\''.$ejd->id.'\',\'collapse\');"><i class="fa fa-minus"></i></a>';
            $o .= '<a class="collexp" id="plus_id_'.$ejd->id.'" href="#" onclick="collexpandpost(\''.$ejd->id.'\',\'expand\');"><i class="fa fa-plus"></i></a>';
            $o .= '<div class="div_post_header">';            
            $o .= get_string('created','ejournal').' '.$vdate.'&nbsp;&nbsp&nbsp';
                    
            $o .= get_string('name','ejournal').' '.ucfirst($ejuser->firstname).' '.ucfirst($ejuser->lastname);
            $o .= '</div> <!-- End of div_post_header -->';
                        
            $mesg = file_rewrite_pluginfile_urls($ejd->post_entry, 'pluginfile.php', $context->id,
                                'mod_ejournal', 'post_entry', $ejd->id, ejournal::instruction_editors_options($context));

            
            $o .= '<div class="div_post_detail" id="div_pd_'.$ejd->id.'"><span class="B">'.get_string('postdesc','ejournal').'</span>';
            $o .= '<div class="div_pd_text">'.$this->output->box(format_text($mesg, FORMAT_HTML, array('overflowdiv'=>true)), array('generalbox')).'</div>';

            $filesobj = new stdClass();

            $table = 'files';
            if($filesarr = $DB->get_records($table, array('contextid' => $context->id, 'component' => 'mod_ejournal','filearea' => 'post_entry', 'itemid' => $ejd->id), 'mimetype'))
            {
                $o .= '<div class="div_file_upload_text">'.get_string('uploaded_files_text','mod_ejournal').'</div>';
                $o .= '<div class="div_file_uploads">';
                foreach ($filesarr as $key => $value)
                {
                    $filesobj->$key = $value;                
                }

                // If we have an image, then show the image..
                // If we have a file, then show the file as a link to the document etc.
                foreach($filesobj as $f)
                {
                    if( strtoupper($f->mimetype) != 'DOCUMENT/UNKNOWN' && (isset($f->filename) && strlen($f->filename)) || (isset($f->source) && strlen($f->source)) )
                    {    
                        $mimetypearr = array('IMAGE/GIF','IMAGE/PJPEG','IMAGE/PNG','IMAGE/SVG+XML');        

                        if(!in_array(strtoupper($f->mimetype), $mimetypearr)) 
                        {
                            // If file, then display a link instead to open in a new document window
                            if(!empty($f->filename) && $f->filename != '.')
                            {
                                $itemid = 'null';
                                if(isset($f->itemid) && $f->itemid!=0)
                                {
                                    $itemid = $f->itemid;
                                }
                                $filename = $f->filename;
                                //$urllink = "$CFG->wwwroot/pluginfile.php/$context->id/mod_ejournal/post_entry/null/$filename";
                                $urllink = "$CFG->wwwroot/pluginfile.php/$context->id/mod_ejournal/post_entry/$itemid/$filename";
                                $o .= '<div class="div_file_element"><a href="'.$urllink.'" target="_blank"><i class="fa fa-file-text-o fa-2x"></i>&nbsp;&nbsp;'.$filename.'</a></div><br />';
                            }
                        }
                    }
                }
                $o .= '</div> <!-- end of div_file_uploads -->';                
            }
            $o .= '</div> <!-- end of div_post_detail -->';       
            
            // This bit of javascript will expand each post i.e. show the post fully and get rid of the + sign until the -ve is ticked
            $o .= '<script>collexpandpost( \''.$ejd->id.'\', \'expand\')</script>';
        
            $o .= '</div> <!--end of div_post--> ';
        }            
        
        return $o;
    }
    
    protected function get_orderby_list( $params )
    {
        $debug = false;
        
        $cm = $this->get_course_module();
        
        $order_by = array( "dateasc" => "Date Ascending", "datedesc" => "Date Descending", "userasc" => "User, Date Ascending", "userdesc" => "User, Date Descending" );
        
        $o = '';
        $o .= '<div class="div_order_by">';
        $o .= '<label>'.get_string('chooseorderby','ejournal').'</label>';
        
        $selected = 'dateasc';
        if(isset($params->order_by) && strlen($params->order_by))
        {
            $selected = $params->order_by;
        }
        
        // 3rd parameter is the default value to be shown !
        $o .= html_writer::select( $order_by, 'order_by', $selected );
        
        $urlparams = array('id' => $cm->id, 'action'=>'VIEWPOSTBYUSER', 'ereflect_id' => $params->ereflect_id, 'student_id' => $params->student_id );
        $orderbyurl = new moodle_url('/mod/ejournal/view.php', $urlparams);
        $orderby = '<button id="id_ereflectgo" onclick="orderbygo(\''.$orderbyurl.'\');">Go</button>';
        
        $o .= $orderby;
        $o .= '</div>';        
        
        return $o;
    }
    
    public function addpost_form_elements($mform, $params){       
        global $USER, $DB;
		
        $debug = false;

        $context = $this->get_context();
        $instance = $this->get_instance();
        
        if($debug){
            echo 'In addpost_form_elements <br />';
            echo 'Course: '.$instance->course.'<br />';
            
            echo 'Parameters <br />';
            echo '<pre>';
            print_r($params);
            echo '</pre>';
        }      
            
        // Completion Message Editor field i.e. with Picture/File/Movie upload
        
        if($this->student_teacher_check( $params ) )
        {
            $field = 'post_entry';
            $label = get_string($field, 'ejournal');
            //$mform->addElement('editor', 'post_entry_editor', $label, null,
            $mform->addElement('editor', 'post_entry_editor', null, null,
                                ejournal::instruction_editors_options($context));
            //$mform->setDefault('post_entry_editor', get_string('post_entry', 'ejournal'));
        }

        $buttonarray=array();
        if($this->student_teacher_check( $params ) )
        {
            $buttonarray[] = &$mform->createElement('submit', 'addpost', get_string('postnewmessage', 'ejournal'));
        }
        $buttonarray[] = &$mform->createElement('submit', 'requery', get_string('requery','ejournal'));
        $buttonarray[] = &$mform->createElement('cancel', 'cancelandreturn', get_string('cancelandreturn','ejournal'));
        
        $eref_id = '';
        if(isset($params->ereflect_id) && strlen($params->ereflect_id))
        {
            $eref_id = $params->ereflect_id;
        }        
        
        if($this->user_completed($eref_id, $params->student_id))
        {
            $buttonarray[] = &$mform->createElement('submit', 'viewpdf', get_string('viewpdf','ejournal'));        
        }
        // not required  but code left in, just in case
        //$buttonarray[] = &$mform->createElement('submit', 'viewereflect', get_string('viewereflect','ejournal'));  
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');		    
        //$mform->setDefault($field,$value->open_answer);
                        
        // Setting to instanceid doesn't work here as when we set the data, the id gets autopopulated with
        // the enquiry_question.id. Thus have to set the default after doing  $this->set_data($data)
        // in modifyquestion_form.php
        $mform->addElement('hidden', 'id', $context->instanceid);
        $mform->setType('id', PARAM_INT);
        
        $mform->addElement('hidden', 'ejournal_id', $instance->id );
        $mform->setType('ejournal_id', PARAM_INT);
        
                
     
        $mform->addElement('hidden', 'ereflect_id', $eref_id );
        $mform->setType('ereflect_id', PARAM_INT);
        
        $student_id = '';
        if(isset($params->student_id) && strlen($params->student_id))
        {
            $student_id = $params->student_id;
        }

        $mform->addElement('hidden', 'student_id', $student_id );
        $mform->setType('student_id', PARAM_INT);            
        
        $mform->addElement('hidden', 'action', $params->action);
        $mform->setType('action', PARAM_TEXT);		
    }    
    
    function process_new_post(&$mform, &$notices, &$params)
    {
        global $DB, $USER, $CFG;
		
        // Include submission form.
        require_once($CFG->dirroot . '/mod/ejournal/class/mainpost_form.php');		
		
        $instance = $this->get_instance();	
        $context = $this->get_context();
        $cm = $this->get_course_module();
        
        $debug = false;

        $data = new stdClass();
        //list($ejournal, $data, $ejournal_details, $params, $instance) = $this->_customdata;
        $mform = new mod_ejournal_mainpost_form(null, array($this, $data, $params, $instance));
        
        if($data = $mform->get_data())
        {
            
            if($debug)
            {
                echo 'In locallib.process_new_post data<br />';
                echo '<pre>';
                print_r($data);
                echo '</pre>';

                echo '<pre>';
                print_r($instance);
                echo '</pre>';
            }
            
            try 
            { 
                $transaction = $DB->start_delegated_transaction();     			

                $insert = new stdClass();
                $insert->ejournal_id = $instance->id;
                $insert->ereflect_id = $data->ereflect_id;
                $insert->user_id = $USER->id;
                $insert->student_id = $data->student_id;
                $insert->post_entry = 'temporary text';
                $insert->timecreated = time();                

                if($debug){echo 'ejournal_id is set to '.$insert->ejournal_id.'<br />';}			

                $returnid = $DB->insert_record('ejournal_details', $insert);

                if($debug){ echo 'Ejournal_details returnid = '.$returnid.'<br />';}

                if(!isset($returnid)&&!strlen($returnid))
                {					
                    $notices[] = get_string('ejournaldetailsinsertfailed', 'mod_ejournal');
                    return false;
                }
                else
                {
                    if($debug){echo 'In new file entry part <br />';}
                    
                    //$context = context_module::instance($ejournal->coursemodule);    
                    $ejdet = new stdClass();
                    $ejdet->timemodified = time();
                    $ejdet->id = $returnid;
                    
                    if($debug)
                    {
                        echo 'Post entry editor itemid = '.$data->post_entry_editor['itemid'].' <br />';
                        echo 'Post entry editor text = '.$data->post_entry_editor['text'].' <br />';
                    }
                    
                    if ($draftitemid = $data->post_entry_editor['itemid']) 
                    {
                        
                        if($debug){echo 'in right place<br />';}
                        
                        $ejdet->post_entry = file_save_draft_area_files($draftitemid, $context->id, 'mod_ejournal', 'post_entry',
                                $returnid, ejournal::instruction_editors_options($context), $data->post_entry_editor['text']);        

                        if($debug){echo 'about to update record<br />';}
                        
                        // re-save the record with the replaced URLs in editor fields
                        $DB->update_record('ejournal_details', $ejdet);        
                    }                  
                    
                    $transaction->allow_commit();
                    
                    
                    /* Email bit 
                    // Only email to the student if its set on the first screen*/
                    
                    /* post submitted by */
                    /* if current submittor not equal to parameter setting of studnet id
                     * then must be the teacher                  
                     */
                    
                    $submittor = $DB->get_record('user', array('id' => $USER->id));    
                    if($USER->id == $params->student_id)
                    {                    
                        $student = $submittor;
                        //$teacher = $DB->get_record('user', array('id' => $USER->id));    
                        // need to get other teachers
                    }
                    else
                    {
                        $teacher = $submittor;
                        $student = $DB->get_record('user', array('id' => $params->student_id));    
                    }          
                    
                    // Attain Teacher information
                    $studentname =  ucfirst(strtolower($student->firstname)).' '.ucfirst(strtolower($student->lastname));
                    $submittorname = ucfirst(strtolower($submittor->firstname)).' '.ucfirst(strtolower($submittor->lastname));

                    // Email to Student providing submittor was the Teacher i.e. don't send an email to student
                    // if submitted by student
                    if($instance->student_email_notn==1 && ($student->id != $submittor->id))
                    {
                       // Teachername only available when a student...
                       $teachername = ucfirst(strtolower($teacher->firstname)).' '.ucfirst(strtolower($teacher->lastname));
                        
                       // eJournal Post Submitted by &
                       $subject = get_string('email_subject_user','ejournal', $submittorname);
                       
                       $body = get_string('email_dear_user','ejournal',$studentname).'<br />'; // Dear blah
                       // replace this with get_string('email_body_user','ejournal',$teachername)
                       
                       //$body .= 'I have submitted another post for you to read.<br />Regards,<br />'.$teachername.'.';
                       $body .= 'I have submitted another post for you to read.<br />';
                       $body .= 'Regards,<br />';
                       $body .= $teachername;
                                                                      
                       $html_body = '<!DOCTYPE html><html><head></head><body>';
                       $html_body .= $body;
                        
                       $nextpageparams = array("id" => $cm->id, "student_id" => $student->id, "ereflect_id" => $insert->ereflect_id, "action" => "VIEWPOSTBYUSER");
                       $nextpageurl = new moodle_url('/mod/ejournal/view.php', $nextpageparams);
                            
                       $html_body .= '<br /><a href="'.$nextpageurl.'">Go to eJournal post</a>';
                       $html_body .= '</body></html>';
                                                                    
                        //$sender = get_admin();	
                        email_to_user($student, $teacher, $subject, str_replace('<br />',' ', $body), $html_body, null, null);                        
                    }
                    
                    // Email to Teacher providing submittor was the Student i.e. done't send an email to the teacher 
                    // if submitted by the teacher
                    if($instance->teacher_email_notn==1 && ($student->id == $submittor->id))
                    {
                        // Send to each Teacher on the course
                        $uwct = $this->ereflect_teachers();
                        foreach($uwct as $key => $value)
                        {
                            $teacher = $value;
                            
                            if($debug)
                            {
                                echo 'Teacher Array 1 ';
                                echo '<pre>';
                                print_r($value);
                                echo '</pre>';

                                echo 'Teacher Array 2';
                                echo '<pre>';
                                print_r($teacher);
                                echo '</pre>';
                            }
                            
                            // eJournal Post Submitted by &
                            $subject = get_string('email_subject_user','ejournal', $submittorname);
                            
                            $teachername = ucfirst(strtolower($value->firstname)).' '.ucfirst(strtolower($value->lastname));
                            
                            // Dear teacher
                            $body = get_string('email_dear_user', 'ejournal', $teachername).'<br />';
                             
                            //$body .= '<br />'.get_string('email_body_teacher','ereflect',ucfirst(strtolower($USER->firstname)).' '.ucfirst(strtolower($USER->lastname)));

                            // replace this with get_string('email_body_user','ejournal',$teachername)
                            $body .= 'I have submitted another post for you to read.<br />';
                            $body .= 'Regards,<br />';
                            $body .= $studentname;
                            
                            $html_body = '<!DOCTYPE html><html><head></head><body>';
                            $html_body .= $body;
                            
                            $nextpageparams = array("id" => $cm->id, "student_id" => $student->id, "ereflect_id" => $insert->ereflect_id, "action" => "VIEWPOSTBYUSER");
                            $nextpageurl = new moodle_url('/mod/ejournal/view.php', $nextpageparams);

                            $html_body .= '<br /><a href="'.$nextpageurl.'">Go to eJournal post</a>';
                            $html_body .= '</body></html>';
                            
                            // recipient first, then sender.....
                            // body is in summary of email, html_body is in main body email (upon opening)
                            email_to_user($teacher, $student, $subject, str_replace('<br />',' ',$body), $html_body, null, null);
                        }                        
                    }
                    
                    
                    return true;
                         
                }	
            } 
            catch(Exception $e) 
            {     
                $transaction->rollback($e);
                $notices[] = $e;
                return false;
            }
        }
        else
        {
            return false;
        }       
    }
    
    public function ereflect_users()
    {
        global $DB;

        $debug = false;

        if($debug){echo 'in ereflect_users';}

        $context = $this->get_context();
        //$context = context_module::instance($cm->id);

        // First get all users who can complete this questionnaire.
        $group = false;
        $sort = 'u.lastname';
        $cap = 'mod/ejournal:submit';
        
        //$fields = 'u.id, u.username, u.email, u.lastaccess'; Select all fields instead of including this!
        if (!$allusers = get_users_by_capability($context,
                                        $cap,
                                        '',  /* $fields would normally go here */
                                        $sort,
                                        '',
                                        '',
                                        $group,
                                        '',
                                        true)) 
        {
            return false;
        }
        //$allusers = array_keys($allusers);

        if($debug)
        {
            echo '<hr />';
            echo 'All Users who are able to complete questionnaire';
            echo '<pre>';
            print_r($allusers);
            echo '</pre>';
            echo '<hr />';
        }

        return $allusers;

    }
    
    public function view_response_stats($notices, $params )
    {
        global $DB, $CFG, $USER, $PAGE;
		
        $debug = false;
	
        if($debug)
        {
            echo 'In function view_response_stats <br />';
        }
        $instance = $this->get_instance();		
        $course = $this->get_course();
        $cm = $this->get_course_module();
		
        $o = '';	
		
        // Header
        $o .= $this->get_renderer()->render(new ejournal_header($instance,
                                              $this->get_context(),
                                              $this->show_intro(),
                                              $this->get_course_module()->id,
                                              $this->get_course()),'HELLO');    //get_string('viewstatistics','ereflect')
        
        $o .= $this->get_ereflect_details ( $params );
        
        $datestring = new stdClass();
        $datestring->year  = get_string('year');
        $datestring->years = get_string('years');
        $datestring->day   = get_string('day');
        $datestring->days  = get_string('days');
        $datestring->hour  = get_string('hour');
        $datestring->hours = get_string('hours');
        $datestring->min   = get_string('min');
        $datestring->mins  = get_string('mins');
        $datestring->sec   = get_string('sec');
        $datestring->secs  = get_string('secs');

        $data_response = array();
        //$data_complete = array();
        $data_noresponse = array();
        
        // Function to get Students Enroled on the course who all should complete
        $students = $this->ereflect_users();		
        
        foreach ($students as $student) 
        {
            //$user = $DB->get_record('user', array('id' => $student->id));
			
            $profileurl = $CFG->wwwroot.'/user/view.php?id='.$student->id.'&amp;course='.$course->id;
            $profilelink = '<strong><a href="'.$profileurl.'">'.fullname($student).'</a></strong>';
			
            $last_access = format_time(time() - $student->lastaccess, $datestring);										

            // Get literals - these will show as heading literals in each table
            $picture = get_string('user_picture','ejournal');
            $profile = get_string('user_profile','ejournal');
            $user_email = get_string('user_email','ejournal');
            $last_accessed = get_string('last_accessed','ejournal');
			
            // Assign key value pairs in an array that will get passed into class below to create a summary table
            $data = array ( 'id' => $student->id,
                            $picture => $this->output->user_picture($student, array('courseid' => $course->id)), 
                            $profile => $profilelink, 
                            $user_email => $student->email, 
                            $last_accessed => $last_access);
			
            // Check if posted any type of response within ejournal
            /*$table = 'ejournal_details';
            $conditions = array('ejournal_id' => $instance->id, 'ereflect_id' => $params->ereflect_id, 'user_id' => $student->id);*/
            
            $sql = 'SELECT *
                    FROM mdl_ejournal_details
                    WHERE ejournal_id = ?
                    AND ereflect_id = ?
                    AND user_id = ?
                    AND timecreated = (SELECT max(timecreated) FROM mdl_ejournal_details 
                                       WHERE ejournal_id = ? AND ereflect_id = ? AND user_id = ? )';
            
            $conditions = array($instance->id, $params->ereflect_id, $student->id, $instance->id, $params->ereflect_id, $student->id);
            //$ejd = $DB->get_records_sql($sql, $conditions );
            
                      //if($eur = $DB->get_records($table, $conditions, 'timecreated desc'))
            if($eur = $DB->get_record_sql($sql, $conditions ))
            {
                if($debug)
                {
                    echo '<pre>';
                    print_r($eur);
                    echo '</pre>';    
                }
                								
                $created = format_time(time() - $eur->timecreated, $datestring);
                if( (isset($eur->timemodified) && strlen($eur->timemodified)) && $eur->timemodified!=0)
                {
                    $modified = format_time(time() - $eur->timemodified, $datestring);
                    //echo 'Timemodified: '.$eur->timemodified.', Modified: '.$modified.'<br />';
                }
                else
                {
                    $modified = '';
                }
			
                //$user_created = get_string('user_created','mod_ejournal');
                $user_modified = get_string('user_modified','mod_ejournal');

                //$data[$user_created] = $created;
                $data[$user_modified] = $modified;

                $data['viewpost'] = 'YES';
                // A True or false value on whether teacher response required
                // to show up as red or not 
                $data['response_reqd'] = $this->teacher_response_required ( $params->ereflect_id, $student->id );              
                
                $data_response[] = $data;                
            }						
            else
            {
                $data[''] = '';  // Created
                //$data[''] = ''; // Modified

                // Collect the students who answered anything 
                $data['viewpost'] = 'YES';
                $data['response_reqd'] = false;

                $data_noresponse[] = $data;
            }
        }
        
        if($debug)
        {
            echo 'Respondents/Non-respondents<br />';
            echo 'respondents first: Partial<br />';
            echo '<pre>';
            print_r($data_response);
            echo '</pre>';
            echo 'Non-Respondents<br />';			
            echo '<pre>';
            print_r($data_noresponse);
            echo '</pre>';
        }	    

        // Students who have responded
        if(count($data_response)>0)
        {
            $dn = new stdClass();
            foreach($data_response as $key => $value)
            {
                $dn->$key = $value;
            }
            // Students who have completed
            $view_users_completed_details = new ejournal_summary_table(	$dn, 
                                                                        $params,
                                                                        $this->get_course_module()->id, 
                                                                        $instance->id,
                                                                        get_string('respondents', 'ejournal') );

            $o .= $this->get_renderer()->render($view_users_completed_details);
        }        

        // Students who have not responded yet
        if(count($data_noresponse)>0)
        {
            $dn = new stdClass();
            foreach($data_noresponse as $key => $value)
            {
                $dn->$key = $value;
            }
            // Students who have completed
            $view_users_completed_details = new ejournal_summary_table(	$dn, 
                                                                        $params,
                                                                        $this->get_course_module()->id, 
                                                                        $instance->id,
                                                                        get_string('non-respondents', 'ejournal') );

            $o .= $this->get_renderer()->render($view_users_completed_details);
        }
        
        $refreshparams = array("id" => $cm->id );
        $refreshurl = new moodle_url('/mod/ejournal/view.php', $refreshparams);
                            
        $o .= '<a href="'.$refreshurl.'"><button>'.get_string('requery','ejournal').'</button></a>';        
        
        /* NEED TO PLACE A RETURN TO MAIN MENU BUTTON HERE */
        /*$returnparams = array("id" => $cm->id, "action" => 'RETURNTOMENU' );
        $returnurl = new moodle_url('/mod/ejournal/view.php', $returnparams);
        
        $o .= '&nbsp;&nbsp;<a href="'.$returnurl.'"><button>'.get_string('returntocourse','ejournal').'</button></a>';*/
                
        // Footer
        $o .= $this->view_footer();
                
        return $o;
    }
    
    
    public function get_teacherids()
    {
        $debug = false;        
        $teachers = $this->ereflect_teachers();
        
        if($debug)
        {
            echo 'in function get_teacherids() <br />';
        }
        
        $teacherids = array();;
        foreach($teachers as $t)
        {
            $teacherids[] = $t->id;
        }
        
        return $teacherids;
    }
    
    public function isateacher( $user_id )
    {
        global $DB;

        $debug = false;
        
        $teachers = $this->ereflect_teachers();
        
        if($debug)
        {
            echo 'in function isateacher with $user_id '.$user_id.'<br />';
        }
        
        $b_match = false;
        foreach($teachers as $t)
        {
            if($debug)
            {
                echo '<pre>';
                print_r($t);
                echo '</pre>';
            }
            
            if($t->id == $user_id)
            {
                if($debug){echo 'Found Teacher: '.$t->id.'<br />';}
                
                $b_match = true;
                break;
            }
        }
        
        return $b_match;
        
    }
    
    public function ereflect_teachers()
    {
        global $DB;

        $debug = false;

        $context = $this->get_context();

        //$context = context_module::instance($cm->id);

        if($debug)
        {
            echo '<hr />';
            echo 'in locallib.ereflect_teachers';
            echo '<pre>';
            print_r($context);
            echo '</pre>';
            echo '<hr />';
        }		

        // First get all users who can complete this questionnaire.
        $group = false;
        $sort = 'u.lastname';
        //$cap = 'mod/ereflect:submit';
        $cap = 'mod/ejournal:grade';
        //$fields = 'u.id, u.username, u.email, u.lastaccess'; Select all fields instead of including this!

        if (!$allusers = get_users_by_capability($context,
                                        $cap,
                                        '',  /* $fields would normally go here */
                                        $sort,
                                        '',
                                        '',
                                        $group,
                                        '',
                                        true)) 
        {
            return false;
        }
            //$allusers = array_keys($allusers);

        if($debug)
        {
                echo '<hr />';
                echo 'All Users who are able to teach';
                echo '<pre>';
                print_r($allusers);
                echo '</pre>';
                echo '<hr />';
        }

        return $allusers;
    }	    
    
    
    public function student_teacher_check ( $params )
    {
        global $USER;
        
        $debug = false;
        
        if($debug)
        {
            echo 'In student_teacher_check function <br />';
            echo 'Student id: '.$params->student_id.', User id: '.$USER->id.'<br />';
        }
        
        //check if user is a teacher first
        if($this->isateacher($USER->id))
        {
            //echo 'going to return true for being the teacher';
            // its ok for the teacher to amend the student id in the URL
            return true;
        }
        else 
        { 
            // Must be the studnet
            if(isset($params->student_id) && strlen($params->student_id)
               && $USER->id == $params->student_id)
            {
                //echo 'going to return true for correct student';
                return true; // it is a student and the right student that's logged in
            }
            else
            {
                //echo 'going to return false<br />';
                // its not ok for the student to amend the studnet id in the URL
                return false;
            }
        }        
    }
    
    
    /**
     * Get the piece of code as determined in the extended plugin_renderer_base (class mod_ereflect_renderer )
     *
     * @return string
    */
    		
    public function get_renderer() {
        global $PAGE;
        if ($this->output) {
            return $this->output;
        }
        $this->output = $PAGE->get_renderer('mod_ejournal');
        return $this->output;
    }		
   
    
    /**
     * Get the settings for the current instance of this assignment
     *
     * @return stdClass The settings
     */
    public function get_instance() {
        
        global $DB;
        if ($this->instance) {
            return $this->instance;
        }
        if ($this->get_course_module()) {
            $params = array('id' => $this->get_course_module()->instance);
            $this->instance = $DB->get_record('ejournal', $params, '*', MUST_EXIST);
        }
        if (!$this->instance) {
            throw new coding_exception('Improper use of the assignment class. ' .
                                       'Cannot load the assignment record.');
        }
        return $this->instance;
    }

    /**
     * Get the current course module.
     *
     * @return mixed stdClass|null The course module
     */
    public function get_course_module() {
        if ($this->coursemodule) {
            return $this->coursemodule;
        }
        if (!$this->context) {
            return null;
        }

        if ($this->context->contextlevel == CONTEXT_MODULE) {
            $this->coursemodule = get_coursemodule_from_id('ejournal',
                                                           $this->context->instanceid,
                                                           0,
                                                           false,
                                                           MUST_EXIST);
            return $this->coursemodule;
        }
        return null;
    }
	
    /**
     * Get the current course.
     *
     * @return mixed stdClass|null The course
     */
    public function get_course() {
        global $DB;

        if ($this->course) {
            return $this->course;
        }

        if (!$this->context) {
            return null;
        }
        $params = array('id' => $this->get_course_context()->instanceid);
        $this->course = $DB->get_record('course', $params, '*', MUST_EXIST);

        return $this->course;
    }
	
	
    /**
     * Get context module.
     *
     * @return context
    */
    public function get_context() {
        return $this->context;
				
    }
    
    public function register_return_link($action, $params) {
        global $PAGE;
        $params['action'] = $action;
        $currenturl = $PAGE->url;

        $currenturl->params($params);
        $PAGE->set_url($currenturl);
    }
    
    /**
     * Display the page footer.
     *
     * @return string
     */
    protected function view_footer() {
        return $this->get_renderer()->render_footer();
    }
    
    
      /**
     * Based on the current assignment settings should we display the intro.
     *
     * @return bool showintro
     */
    protected function show_intro() {
        /*if ($this->get_instance()->alwaysshowdescription ||
                time() > $this->get_instance()->allowsubmissionsfromdate) {
            return true;
        }*/
        return true;
    }
    
    /**
     * Returns an array of options for the editors that are used for submitting and assessing instructions
     *
     * @param stdClass $context
     * @uses EDITOR_UNLIMITED_FILES hard-coded value for the 'maxfiles' option
     * @return array
     */
    public static function instruction_editors_options(stdclass $context) {
        return array('subdirs' => 1, 'maxbytes' => 0, 'maxfiles' => 99,
                     'changeformat' => 1, 'context' => $context, 'noclean' => 1, 'trusttext' => 0);
    }
    
    public function user_completed( $ereflect_id, $user_id )
    {
        global $DB, $USER;

        $instance = $this->get_instance();

        $table = 'ereflect_user_response';
        $conditions = array('ereflect_id' => $ereflect_id, 'user_id' => $user_id, 'status' => 'COMPLETED' );

        if(!$eur = $DB->get_record($table, $conditions))
        {
            return false;
        }
        return true;
    }        
    
    public function teacher_response_required ( $ereflect_id, $student_id )
    {
        global $DB, $USER;
        
        $debug = false;
        
        $teachers = $this->ereflect_teachers();
        
        /*if($debug)
        {
            echo 'Teachers array<br />';
            echo '<pre>';
            print_r($teachers);
            echo '</pre>';
        }*/
        
        foreach($teachers as $teacher )
        {
            $teacherids[] = $teacher->id;
        }
        
        if($debug)
        {
            echo 'Teachers Id list<br />';
            echo '<pre>';
            print_r($teacherids);
            echo '</pre>';
        }
        
        list($sqlteacherids, $teacheridparams) = $DB->get_in_or_equal($teacherids);        
        
        if($debug)
        {
            echo 'eReflect Id: '.$ereflect_id.', Student Id: '.$student_id.', User Id: '.$student_id.'<br />';
        }
        
        $sql = 'SELECT MAX(timecreated) timecreated
                FROM {ejournal_details}
                WHERE ereflect_id = ?
                AND student_id = ?
                AND user_id = ?';

        $studentdate = $DB->get_records_sql($sql, array($ereflect_id, $student_id, $student_id ));

        $sd = array();
        foreach($studentdate as $sd)
        {
            if($debug)
            {
                echo 'Student Date:<br />';
                echo '<pre>';
                print_r($sd);
                echo '</pre>';
                echo 'Student Time created: '. $sd->timecreated.'<br />';                                                    
            }
        }         
        
        if($debug) {
            echo 'Instance Id: '.$instance->id.', Student Id: '.$student_id.', User Id: '.$sqlteacherids.'<br />';        
        }
        
        $dbparams = array_merge(array($ereflect_id, $student_id), $teacheridparams);        
        
        $teacherdate = $DB->get_recordset_sql(' SELECT MAX(timecreated) timecreated
                                                FROM {ejournal_details}
                                                WHERE ereflect_id = ?
                                                AND student_id = ?
                                                AND user_id ' . $sqlteacherids, $dbparams );

        $td = array();
        foreach($teacherdate as $td)
        {
            if($debug)
            {
                echo 'td:<br />';
                echo '<pre>';
                print_r($td);
                echo '</pre>';
                echo 'Teacher Time created: '. $td->timecreated.'<br />';                                                    
            }
        }        
        
        $sdtimecreated = 0;
        if(isset($sd->timecreated) || strlen($sd->timecreated))
        {
            $sdtimecreated = $sd->timecreated;
        }

        $tdtimecreated = 0;
        if(isset($td->timecreated) || !strlen($td->timecreated))
        {
            $tdtimecreated = $td->timecreated;
        }

        if($sdtimecreated > $tdtimecreated)
        {
            return true;
        }               
        else
        {
            return false;
        }

    }
}