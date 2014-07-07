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
 * Library of interface functions and constants for module ejournal
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the ejournal specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod_ejournal
 * @copyright  2013 Graeme Roberts  Cardiff Met
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** example constant */
//define('ejournal_ULTIMATE_ANSWER', 42);

////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function ejournal_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:         return true;
        case FEATURE_SHOW_DESCRIPTION:  return true;

        default:                        return null;
    }
}

/**
 * Saves a new instance of the ejournal into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $ejournal An object from the form in mod_form.php
 * @param mod_ejournal_mod_form $mform
 * @return int The id of the newly inserted ejournal record
 */
function ejournal_add_instance(stdClass $ejournal, mod_ejournal_mod_form $mform = null) {
	
    global $CFG, $DB;
    //require_once(dirname(__FILE__) . '/class/locallib.php'); //might need to put this back in
    		
    $grdebug = false;

    if($grdebug)
    {
        echo 'in ejournal_add_instance <br />';
    }

    $ejournal->timecreated = time();
    $ejournal->completion_message  = '';          // updated later    
	
    if($grdebug)
    {
        echo 'time created = '.$ejournal->timecreated.', before insert_record<br />';

        echo '<pre>';
        print_r($ejournal);
        echo '</pre>';
    }
        
    // Setting of opendate and closedate if the check boxes are empty
    /*leave out due to Restrict Access section
    if (empty($ejournal->useopendate)) {*
        $ejournal->opendate = 0;
        echo 'set opendate to zero<br />';
    }
    if (empty($ejournal->useclosedate)) {
        $ejournal->closedate = 0;
        echo 'set closedate to zero<br />';
    }*/
        
    # You may have to add extra stuff in here #
    //return $DB->insert_record('ejournal', $ejournal);
    $ejournal->id = $DB->insert_record('ejournal', $ejournal);
	
    if($grdebug)
    {
        echo 'after insert_record<br />';	
    }
	
    // new stuff goes here for update of editor 	
    // we need to use context now, so we need to make sure all needed info is already in db
    $cmid = $ejournal->coursemodule;
	
    $DB->set_field('course_modules', 'instance', $ejournal->id, array('id' => $cmid));
    $context = context_module::instance($cmid);
	
    if($grdebug)
    {
        echo '<pre>';	
        print_r($context);
        echo '</pre>';
     }	

        
    /*if ($draftitemid = $ejournal->post_entry_editor['itemid']) {
                
        $ejournal->post_entry = file_save_draft_area_files($draftitemid, $context->id, 'mod_ejournal', 'post_entry',
                0, ejournal::instruction_editors_options($context), $ejournal->post_entry_editor['text']);        
s
        // re-save the record with the replaced URLs in editor fields
        $DB->update_record('ejournal', $ejournal);        
    }*/


    return $ejournal->id;	
}

/**
 * Updates an instance of the ejournal in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $ejournal An object from the form in mod_form.php
 * @param mod_ejournal_mod_form $mform
 * @return boolean Success/Fail
 */
function ejournal_update_instance(stdClass $ejournal, mod_ejournal_mod_form $mform = null) {
    global $DB;

    $ejournal->timemodified = time();
    $ejournal->id = $ejournal->instance;

    // Setting of opendate and closedate if the check boxes are empty
    /* Leave out due to Restrict access section
    if (empty($ejournal->useopendate)) {
        $ejournal->opendate = 0;
    }
    if (empty($ejournal->useclosedate)) {
        $ejournal->closedate = 0;
    } */   

    return $DB->update_record('ejournal', $ejournal);
    
    /*if($DB->update_record('ejournal', $ejournal))
    {
        $context = context_module::instance($ejournal->coursemodule);    
        if ($draftitemid = $ejournal->post_entry_editor['itemid']) {
                
            $ejournal->post_entry = file_save_draft_area_files($draftitemid, $context->id, 'mod_ejournal', 'post_entry',
                    0, ejournal::instruction_editors_options($context), $ejournal->post_entry_editor['text']);        

            // re-save the record with the replaced URLs in editor fields
            $DB->update_record('ejournal', $ejournal);        
        }
    }
    return true;*/
}

/**
 * Removes an instance of the ejournal from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function ejournal_delete_instance($id) {
    global $DB;

    if (!$ejournal = $DB->get_record('ejournal', array('id' => $id))) {
        return false;
    }

    # Delete any dependent records here #

    $DB->delete_records('ejournal', array('id' => $ejournal->id));

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return stdClass|null
 */
function ejournal_user_outline($course, $user, $mod, $ejournal) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $ejournal the module instance record
 * @return void, is supposed to echp directly
 */
function ejournal_user_complete($course, $user, $mod, $ejournal) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in ejournal activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function ejournal_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link ejournal_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function ejournal_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@see ejournal_get_recent_mod_activity()}

 * @return void
 */
function ejournal_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function ejournal_cron () {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function ejournal_get_extra_capabilities() {
    return array();
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * Is a given scale used by the instance of ejournal?
 *
 * This function returns if a scale is being used by one ejournal
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $ejournalid ID of an instance of this module
 * @return bool true if the scale is used by the given ejournal instance
 */
function ejournal_scale_used($ejournalid, $scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('ejournal', array('id' => $ejournalid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of ejournal.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param $scaleid int
 * @return boolean true if the scale is used by any ejournal instance
 */
function ejournal_scale_used_anywhere($scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('ejournal', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the give ejournal instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $ejournal instance object with extra cmidnumber and modname property
 * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return void
 */
function ejournal_grade_item_update(stdClass $ejournal, $grades=null) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $item = array();
    $item['itemname'] = clean_param($ejournal->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax']  = $ejournal->grade;
    $item['grademin']  = 0;

    grade_update('mod/ejournal', $ejournal->course, 'mod', 'ejournal', $ejournal->id, 0, null, $item);
}

/**
 * Update ejournal grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $ejournal instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 * @return void
 */
function ejournal_update_grades(stdClass $ejournal, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $grades = array(); // populate array of grade objects indexed by userid

    grade_update('mod/ejournal', $ejournal->course, 'mod', 'ejournal', $ejournal->id, 0, $grades);
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function ejournal_get_file_areas($course, $cm, $context) {
      
    $areas = array();
    $areas['post_entry']          = get_string('post_entry', 'ejournal');
    
    return $areas;

}

/**
 * File browsing support for ejournal file areas
 *
 * @package mod_ejournal
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function ejournal_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {

    global $CFG, $DB, $USER;
   
    $fs = get_file_storage();    
    
    if ($filearea == 'post_entry') 
    {
        // always only itemid 0
        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        if (!$storedfile = $fs->get_file($context->id, 'mod_ejournal', $filearea, 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'mod_ejournal', $filearea, 0);
            } else {
                // not found
                return null;
            }
        }
        return new file_info_stored($browser, $context, $storedfile, $urlbase, $areas[$filearea], false, true, true, false);
    }
}

/**
 * Serves the files from the ejournal file areas
 *
 * @package mod_ejournal
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the ejournal's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 */
function ejournal_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options=array()) {
    global $DB, $CFG;
    
    $debug = false;

    if($debug){echo 'in right place <br />';}
   
    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }
    
    if($debug){echo 'before require login<br />';}

    //require_login($course, true, $cm);
    
    if($debug){echo 'after require login <br />';  }
    
   $componentid = (int)array_shift($args);    
   
   if($debug){echo 'Component id = '.$componentid.'<br />';}

    if ($filearea === 'post_entry') {
        
        if($debug){echo 'Filearea = '.$filearea.'<br />';}

        //array_shift($args); // itemid is ignored here
        $relativepath = implode('/', $args);
        $relativepath = str_replace('%20', ' ', $relativepath ); // GR added replace any instances of %20 with a space
        
        if($debug){echo 'relative path: '.$relativepath.'<br />';}
                
        //$fullpath = "/$context->id/mod_ejournal/$filearea/0/$relativepath";
        $fullpath = "/$context->id/mod_ejournal/$filearea/$componentid/$relativepath";
        
        if($debug){echo 'Fullpath = '.$fullpath.'<br />';}

        $fs = get_file_storage();
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            if($debug){echo 'in send_file_not_found <br />';}
            send_file_not_found();
        }
        else
        {
            if($debug){echo 'found file<br />';}
        }

        // finally send the file
        send_stored_file($file, null, 0, $forcedownload, $options);

    }
    
    return false;
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding ejournal nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the ejournal module instance
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
function ejournal_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm) {
}

/**
 * Extends the settings navigation with the ejournal settings
 *
 * This function is called when the context for the page is a ejournal module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $ejournalnode {@link navigation_node}
 */
function ejournal_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $ejournalnode=null) {
}

function ejournal_print_overview( $courses, &$htmlarray )
{
    global $USER, $CFG, $DB;
    //require_once($CFG->libdir.'/gradelib.php');
    
    $debug = false;
    
    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return array();
    }

    if($debug)
    {
        /*echo 'Ejournal<br />';
        echo '<pre>';
        print_r($ejournal);
        echo '</pre>';*/
            
        echo 'Courses<br />';
        echo '<pre>';
        print_r($courses);
        echo '</pre>';
        echo 'HTMLarray<br />';
        echo '<pre>';
        print_r($htmlarray);
        echo '</pre>';     
    }
    
    if (!$ejournals = get_all_instances_in_courses('ejournal',$courses)) {
        return;
    }
    
    if($debug)
    {
        echo 'All ejournals in courses';
        echo '<pre>';
        print_r($ejournals);
        echo '</pre>';    
    }
    
    // if the ereflect is open i.e. between opendate and closedate
    // OR 

    $ejournalids = array();    

    // Do assignment_base::isopen() here without loading the whole thing for speed.
    foreach ($ejournals as $key => $ejournal) {        
        
        $ejournalids[] = $ejournal->id;
        $totalpostcount = 0;  // POST count        

        if($debug)
        {
            echo 'Ejournalids';
            echo '<pre>';
            print_r($ejournalids);
            echo '</pre>';
        }
        
        if (!$ereflects = get_all_instances_in_courses('ereflect',$courses)) {
            return;
        }        
        
        if($debug)
        {
            echo 'Found Ejournal';
            echo '<pre>';
            print_r($ejournal);
            echo '</pre>';
            
            echo 'EReflects <br />';
            echo '<pre>';
            print_r($ereflects);
            echo '</pre>';     
        }                    
            
        // for eJournal
        // if ejournal for course, then eJournal exists
        // 
        //  For Student
        //  nothing for Student
        //
        //  for Teacher
        // for each ereflect questinnaire and each student
        //   Get max time created  of student responses and see if greater than teachers response
        //   if, so, then build up a count 
        //   if count > 0 then show that teacher much respont to 
        //   
        
        // Definitely something to print, now include the constants we need.
        require_once($CFG->dirroot . '/mod/ejournal/class/locallib.php');                              
        
        $strejournal = get_string('ereflect', 'ejournal');
        $strduedate = get_string('duedate', 'ejournal');
        $strduedateno = get_string('duedateno', 'ejournal');
        
        $cmid = $ejournal->coursemodule;	
        $cm   = get_coursemodule_from_id('ejournal', $cmid, 0, false, MUST_EXIST); // gets coursemodule description based on the id of the coursemodule    
        //$DB->set_field('course_modules', 'instance', $ejournal->id, array('id' => $cmid));
        $context = context_module::instance($cmid);
        $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
        
        $ejournalc = new ejournal($context, $cm, $course);
        
        if($ejournalc->isateacher( $USER->id )) {
         
            // We do all possible database work here *outside* of the loop to ensure this scales.
            //list($sqlereflectids, $ereflectidparams) = $DB->get_in_or_equal($ereflectids);
            
            $str = '';            
            foreach ($ereflects as $ereflect) {
                
                $postcount = 0;
                
                if($debug)
                {
                    echo 'Got ereflect in loop:<br />';
                    echo '<pre>';
                    print_r($ereflect);
                    echo '</pre>';
                }
                
                // Do not show assignments that are not open.
                /*if (!in_array($ereflect->id, $ereflectids)) {
                    continue;  // continue to next iteration
                }*/
                
                // Check eReflect questionnaire is in corret status i.e. Student Entry
                if( isset($ereflect->status) && $ereflect->status != 'STUDENTENTRY')
                {
                    continue;
                }        
                
                // Only include the ereflects that belong to the course
                if($ejournal->course != $ereflect->course)
                {
                    continue;
                }
                
                // Student enrolled on Course
                if($usersarr = $ejournalc->ereflect_users())               
                {
               
                    $arrusers = array();
                    foreach($usersarr as $u)
                    {
                        if($debug)
                        {
                            echo '<pre>';
                            print_r($u);
                            echo '</pre>';
                        }
        
                        //get latest entry date for student // student_entry
                        //where id = student
                        $sql = 'SELECT MAX(timecreated) timecreated
                                FROM {ejournal_details}
                                WHERE ereflect_id = ?
                                AND student_id = ?
                                AND user_id = ?';

                        $studentdate = $DB->get_records_sql($sql, array($ereflect->id, $u->id, $u->id ));

                        $sd = array();
                        foreach($studentdate as $sd)
                        {
                            if($debug)
                            {
                                echo 'Sd:<br />';
                                echo '<pre>';
                                print_r($sd);
                                echo '</pre>';
                                echo 'Student Time created: '. $sd->timecreated.'<br />';                                                    
                            }
                        }                    
                    
                        $sql = 'SELECT MAX(timecreated) timecreated
                                FROM {ejournal_details}
                                WHERE ereflect_id = ?
                                AND student_id = ?
                                AND user_id = ?';

                        $teacherdate = $DB->get_records_sql($sql, array($ereflect->id, $u->id, $USER->id ));
                    
                        $td = array();
                        foreach($teacherdate as $td)
                        {
                            if($debug)
                            {
                                echo 'Teacher Date:<br />';
                                echo '<pre>';
                                print_r($td);
                                echo '</pre>';
                                echo 'Teacher Time created: '. $td->timecreated.'<br />';
                            }
                        }
                    
                        // If Student Time Created greater than Teacher Time created then
                        // 
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
                            $postcount += 1;
                            $totalpostcount += 1;
                        }                    
                    }
                }  // Check each user enrolled on the course
                
                // Only show the Ereflect in question if there is at least 1 record
                // where the timecreated by the student is greater than the time created
                // by the teacher
                
                if($postcount > 0) {
                
                    $dimmedclass = '';
                    if (!$ereflect->visible) {
                        $dimmedclass = ' class="dimmed"';
                    }
                    
                    $urlparams = array('id' => $ejournal->coursemodule, 'ereflect_id' => $ereflect->id, 'action' => 'VIEWPOSTBYEREFLECT');
                    $href = new moodle_url('/mod/ejournal/view.php', $urlparams);
                    //$href = $CFG->wwwroot . '/mod/ejournal/view.php?id=' . $ejournal->coursemodule .
                    //            '&ereflect_id=' . $ereflect->id . '&' ;
                    
                    $str .= '<div class="ejournal overview">' .
                           '<div class="name">' .$strejournal . 
                            '<a ' . $dimmedclass . 'title="' . $strejournal . '" ' .
                                'href="' . $href . '">' .format_string($ereflect->name) . '</a></div>';
                    if (isset($ereflect->closedate) && $ereflect->closedate != 0) {
                        $closedate = userdate($ereflect->closedate);
                        $str .= '<div class="info">' . $strduedate . ': ' . $closedate . '</div>';
                    } else {
                        $str .= '<div class="info">' . $strduedateno . '</div>';
                    }
                    $str .= '<div class="info">' . get_string('strpostsoutst','ejournal', $postcount) . '</div>';
                    $str .= '</div> <!-- ejournal overview -->';                      
                }
                
                if($debug) { echo 'String: '.$str.'<br />'; }
            }
            
            
        } // is a teacher        

        
        // Only Add to [course][ejournal] array if there is a count
        // greater than zero
        if($totalpostcount > 0) {
            if (empty($htmlarray[$ejournal->course]['ejournal'])) {
              $htmlarray[$ejournal->course]['ejournal'] = $str;
            } else {
              $htmlarray[$ejournal->course]['ejournal'] .= $str;
            }   
        }
        
        if($debug)
        {
            echo 'htmlarray <br />';
            echo '<pre>';
            print_r($htmlarray);
            echo '</pre>';
        }
       
    }

    if (empty($ejournalids)) {
        // No eJournals to look at - we're done.
        return true;
    }    
       
    
}
