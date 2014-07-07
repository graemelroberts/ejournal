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
 * Defines the version of template
 *
 * This code fragment is called by moodle_needs_upgrading() and
 * /admin/index.php
 *
 * @package    mod_ejournal
 * @copyright  March 2014 Graeme Roberts
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//$module->version   = 2014032100;        // Change option feedback to text (from char 255)
//$module->version   = 2014032601;        // Additions to mod_form.php
//$module->version   = 2014032606;        // Additions to mod_form.php
//$module->version   = 2014032701;        // Ereflect Questionnaire literal
//$module->version   = 2014032702;        // Ereflect Questionnaire error if not chosen
//$module->version   = 2014032800;        // 'Your post' literal
//$module->version   = 2014040100;        // Literals for the previous posts
//$module->version   = 2014040101;        // New 'Requery' button
//$module->version   = 2014040102;        // New 'Requery' button
//$module->version   = 2014040200;        // new literls on eReflect questionnaire page
//$module->version   = 2014040201;        // new literls on eReflect questionnaire page
//$module->version   = 2014040202;        // new literls on eReflect questionnaire page
//$module->version   = 2014040203;        // capabilities
//$module->version   = 2014040204;        // capabilities
//$module->version   = 2014040300;        // email variables
//$module->version   = 2014040400;        // email variables x2
//$module->version   = 2014040700;        // email variables x2
//$module->version   = 2014040800;        // addition of student_id to the mdl_ejournal_details
//$module->version   = 2014040801;        // changing type of student_id to not null
//$module->version   = 2014040802;        // adding foreign key for student_id field
//$module->version   = 2014040902;        // adding foreign key for student_id field
//$module->version   = 2014040903;        // userdropdownlist
//$module->version   = 2014040904;        // mainpostpage
//$module->version   = 2014041100;        // Teacher Summary
//$module->version   = 2014041101;        // Teacher Summary literals
//$module->version   = 2014050100;        // Removal of post_order_by field from ejournal table
//$module->version   = 2014050700;        // Removal of opendate and closedate
//$module->version   = 2014050900;        // Addition of return to course button
//$module->version   = 2014051400;        // Literals for ejournal_print_overview in lib.php
//$module->version   = 2014051900;        // More Literals for ejournal_print_overview in lib.php
//$module->version   = 2014052000;        // Missing pluginname in ejournal language file
//$module->version   = 2014052001;        // access changes - addition of mod/ejournal:grade
//$module->version   = 2014052002;        // access changes - addition of mod/ejournal:addinstance
//$module->version   = 2014052200;        // mod/ejournal:submit is required  for locallib (function ereflect_users() 1359)
//$module->version   = 2014052300;        // mod/ejournal:submit is required  for locallib (function ereflect_users() 1359)
$module->version   = 2014052800;        // mod/ejournal:submit is required  for locallib (function ereflect_users() 1359)
$module->requires  = 2010031900;      // Requires this Moodle version (release for moodle 2.5)
$module->cron      = 0;               // Period for cron to check this module (secs)
$module->component = 'mod_ejournal'; // To check on upgrade, that module sits in correct place
