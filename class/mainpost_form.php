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
 * ereflect add Question Submission form
 *
 * @package   mod_ejournal
 * @copyright 2014 G.Roberts Cardiff Met
 * @license   
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->libdir . '/formslib.php');
//require_once($CFG->dirroot . '/mod/ejournal/class/locallib.php');

class mod_ejournal_mainpost_form extends moodleform {

    /** @var array $instance - The data passed to this form */
    private $instance;

    /**
     * Define this form - called by the parent constructor
     */
    public function definition() {
        $mform = $this->_form;
		
        $debug = true;	
        /*$this, $eq, $params, $instance*/
        /*$this, $data, $ejd, $params, $instance*/
        
        list($ejournal, $data, $params, $instance) = $this->_customdata;
        // Instance variable is used by the form validation function.
        $this->instance = $instance;
        
        $context = $ejournal->get_context();        
		
        if($debug)
        {
            echo 'In mainpost_form.mod_ejournal_mainpost_form, about to print <br />';

            /*echo '<pre>';			
            print_r($ejournal_details);
            echo '</pre>';	
            echo '<hr />';*/

            /*if(isset($errors))
            {
                echo 'Errors: ';
                echo '<pre>';
                print_r($errors);
                echo '</pre>';
                echo '<hr />';
            }*/
        }

        $params->action = 'ADDPOSTPROCESS';

        $ejournal->addpost_form_elements($mform, $params );
	
        // Have to set 
        /*if ($data) {
            $this->set_data($data);
        }*/
        $mform->setDefault('id',$context->instanceid);
    }
	
    public function validation($data, $files) {
	
        global $DB;
        $debug = false;		
        $errors = parent::validation($data, $files);
        
        if($debug)
        {
            echo 'In validation for mainpost_form with id : '.$this->instance->id.'<br />';
            echo '<pre>';
            print_r($this->instance);
            echo '</pre>';
        }

        /*if(isset($data['ereflect_id']) && strlen($data['ereflect_id']))
        {
            $errors['ereflect_id']  = get_string('ereflect_missing','mod_ejournal');
        }*/
            
        return $errors;
    }		
	
    public function data_preprocessing(&$defaultvalues) 
    {
        global $DB;
        
        $debug = true;
        
        if($debug)
        {
            echo '<pre>';
            echo 'in data_preprocessing<br />';
            print_r($defaultvalues);
            echo '</pre>';
        }        
    	
        $draftid_editor = file_get_submitted_draft_itemid('post_entry');
        $currenttext = file_prepare_draft_area($draftid_editor, null, 'mod_ejournal', 'post_entry', 0);
        $defaultvalues['post_entry_editor'] = array('text' => $currenttext, 'format' => FORMAT_HTML, 'itemid'=>$draftid_editor);
    }

}

