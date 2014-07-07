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
 * The main ereflect configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_ejournal
 * @copyright  2013 Graeme Roberts
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
//require_once(dirname(__FILE__) . '/class/locallib.php');
require_once($CFG->libdir . '/filelib.php');

/**
 * Module instance settings form
 */
class mod_ejournal_mod_form extends moodleform_mod {

    /** @var array $instance - The data passed to this form */

    /**
     * Defines forms elements
     */
    public function definition() {

        $mform = $this->_form;
		
        $context = $this->context;
        
        $debug = false;
        
        if($debug)
        {   
            echo 'In ereflect_mod_form<br />';
            echo '<pre>';
            print_r($context);
            echo '</pre>';
            echo '<hr />';
            exit();
        }
        
        /*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~    General  Section   ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  START   */		
        
        // Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field
        $field = 'name';
        $mform->addElement('text', $field, get_string('ejournalname', 'ejournal'), array('id' => 'module_heading', 'size' => '255'));
        $mform->setType($field, PARAM_TEXT);
        $mform->addRule($field, null, 'required', null, 'client');
        $mform->addRule($field, get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton($field, 'ejournalname', 'ejournal');

        // Adding the standard "intro" and "introformat" fields
        $this->add_intro_editor();

        /*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~    General  Section   ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  END   */				
        
        /*  Do not need as 'Restrict Access' section uses 'Allow access from' and 'Allow access to'        
        $mform->addElement('header', 'timinghdr', get_string('timing', 'form'));        
        $mform->setExpanded('timinghdr');		        
        
        $enableopengroup = array();
        $enableopengroup[] =& $mform->createElement('checkbox', 'useopendate', get_string('opendate', 'ereflect'));
        $enableopengroup[] =& $mform->createElement('date_time_selector', 'opendate', '');
        $mform->addGroup($enableopengroup, 'enableopengroup', get_string('opendate', 'ereflect'), ' ', false);
        $mform->addHelpButton('enableopengroup', 'opendate', 'questionnaire');
        $mform->disabledIf('enableopengroup', 'useopendate', 'notchecked');

        $enableclosegroup = array();
        $enableclosegroup[] =& $mform->createElement('checkbox', 'useclosedate', get_string('closedate', 'ereflect'));
        $enableclosegroup[] =& $mform->createElement('date_time_selector', 'closedate', '');
        $mform->addGroup($enableclosegroup, 'enableclosegroup', get_string('closedate', 'ereflect'), ' ', false);
        $mform->addHelpButton('enableclosegroup', 'closedate', 'questionnaire');
        $mform->disabledIf('enableclosegroup', 'useclosedate', 'notchecked');        
        */
		
        /*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~    Layout Section   ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  START   */		
                		
        /*This field is now replace
         $mform->addElement('header', 'layout', get_string('layout', 'ejournal'));		
        $mform->setExpanded('layout');		

        $field = 'post_order_by';
        $attributes=array();
        $ps_arr = array( "1"=>"Date descending", "2"=> "Date ascending" );        
        $mform->addElement('select', $field, get_string($field, 'ejournal'), $ps_arr, $attributes);
        $mform->setDefault($field,1);
        */
        
        /*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~    Submission Settings Section   ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  END  */		

        $mform->addElement('header', 'submissionsettings', get_string('submissionsettings', 'ejournal'));
        $mform->setExpanded('submissionsettings');

        // Email Post to Student
        $field = 'student_email_notn';
        $mform->addElement('selectyesno', $field, get_string( $field, 'ejournal'));		
        $mform->addHelpButton($field, $field, 'ejournal');
        $mform->setDefault($field, 1);

        // Email Post to Teacher
        $field = 'teacher_email_notn';
        $mform->addElement('selectyesno', $field, get_string( $field, 'ejournal'));
        $mform->addHelpButton($field, $field, 'ejournal');
        $mform->setDefault($field, 1);

        //-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        //$this->standard_coursemodule_elements();		
		
        //$mform->addElement('button', 'intro', get_string("buttonlabel"));		

        // GR added from NEWMODULE_DOCUMENTATION
        $features = new object();

        $features->groups           = false;  
        $features->groupings        = false;  
        $features->groupmembersonly = false; 
        $features->idnumber 	    = false;

        $this->standard_coursemodule_elements($features);
		
        //-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        // Different ways of adding action buttons
        //$this->add_action_buttons();
        //$mform->addElement('submit', 'savequickgrades', 'hello test');

        // This is KEY as it will submit with this action and then use the 2nd parameter values below (savechangesanddisplay or savchangesandcontinue)
        // in locallib.view 
        $mform->setType('action', PARAM_ALPHA);
				
        $buttonarray=array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton2', get_string('savechangesandreturntocourse', 'ejournal'));
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechangesanddisplay','ejournal'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }


}
