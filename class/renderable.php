<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains the definition for the renderable classes for the assignment
 *
 * @package   mod_ejournal
 * @copyright 2014 G.Roberts Cardiff Met
 * @license   
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Renderable header
 * @package   mod_ejournal
 * @copyright 2014
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ejournal_header implements renderable {
    /** @var stdClass the assign record  */
    public $ejournal = null;
    /** @var mixed context|null the context record  */
    public $context = null;
    /** @var bool $showintro - show or hide the intro */
    public $showintro = false;
    /** @var int coursemoduleid - The course module id */
    public $coursemoduleid = 0;
    /** @var string $subpage optional subpage (extra level in the breadcrumbs) */
    public $subpage = '';
    /** @var string $preface optional preface (text to show before the heading) */
    public $preface = '';
	
    // @var stdClass the ejournal_questions record  
    public $course = null;

    /**
     * Constructor
     *
     * @param stdClass $ejournal  - the ejournal database record
     * @param mixed $context context|null the course module context
     * @param bool $showintro  - show or hide the intro
     * @param int $coursemoduleid  - the course module id
     * @param string $subpage  - an optional sub page in the navigation
     * @param string $preface  - an optional preface to show before the heading
     */
    public function __construct(stdClass $ejournal,
                                $context,
                                $showintro,
                                $coursemoduleid,
				$course,
                                $subpage='',
                                $preface='') {
        $this->ejournal = $ejournal;
        $this->context = $context;
        $this->showintro = $showintro;
        $this->coursemoduleid = $coursemoduleid;
	$this->course = $course;
        $this->subpage = $subpage;
        $this->preface = $preface;
    }
}





class ejournal_form implements renderable {
    /** @var moodleform $form is the edit submission form */
    public $form = null;
    /** @var string $classname is the name of the class to assign to the container */
    public $classname = '';
    /** @var string $jsinitfunction is an optional js function to add to the page requires */
    public $jsinitfunction = '';

    /**
     * Constructor
     * @param string $classname This is the class name for the container div
     * @param moodleform $form This is the moodleform
     * @param string $jsinitfunction This is an optional js function to add to the page requires
     */
    public function __construct($classname, moodleform $form, $jsinitfunction = '') {
        $this->classname = $classname;
        $this->form = $form;
        $this->jsinitfunction = $jsinitfunction;
    }
}


class ejournal_modifyquestion implements renderable {

    /** @var int courseid */
    public $courseid = 0;
    /** @var int coursemoduleid */
    public $coursemoduleid = 0;
    /** @var int the view (STUDENT_VIEW OR GRADER_VIEW) */
    public $view = self::GRADER_VIEW;

    /**
     * Constructor
     *
     * @param int $coursemoduleid
     * @param int $courseid
     * @param string $view
     */
    public function __construct($coursemoduleid,
                                $courseid,
                                $view) {
        $this->coursemoduleid = $coursemoduleid;
        $this->courseid = $courseid;
        $this->view = $view;
    }
}

class ejournal_summary_table implements renderable {

  // @var stdClass the ereflect_questions record  
  public $user_details = null;
  
  public $params = null;
  
  // $var int $courseid
  public $courseid;
  
  // @var int $ereflect_id 
  public $ejournal_id = null;
  
  // $var title
  public $title;
  
  
  /** 
   * Constructor
   * @param int $ereflect_id
   * @param array $ereflect_questions
   *
  */
  
   public function __construct( stdClass $user_details,
                                stdClass $params,
				$courseid,
                                $courseinstance,
				$title)								
   {
		$this->user_details = $user_details;
                $this->params = $params;
		$this->courseid = $courseid;
		$this->ejournal_id = $courseinstance;		
		$this->title = $title;
   }
 
}








