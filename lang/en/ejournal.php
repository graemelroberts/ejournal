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
 * English strings for eJournal
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_eJournal
 * @copyright  2014 Graeme Roberts  Cardiff Met
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'eJournal'; // Title of Module as it appears  when Adding an activity module
$string['modulenameplural'] = 'eJournal';
$string['pluginadministration'] = 'eJournal administration';

//This will be observed when choosing to create an Activity Module
$string['modulename_help'] = '<p>The eJournal allows students to expand on their eReflect questionnaire experiences.</p>';

$string['pluginadministration'] = 'eJournal Administration'; // looks like this is needed, but don't know why
$string['pluginname'] = 'eJournal'; //not sure what this is for ?

/*~~~~~~~~~~~~~~~~~~~~~~~~   ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

$string['ejournal:view'] = 'Preview eJournal';
$string['ejournal:submit'] = 'Submission of posts within the eJournal';
$string['ejournal:grade'] = 'View eJournal Summary information';
$string['ejournal:addinstance'] = 'Add a new instance';

/*~~~~~~~~~~~~~~~~~~~~~~~~~~   Settings Page ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

// Section headings on settings page
$string['layout'] = 'Layout';
$string['submissionsettings'] = 'Submissions Settings';

$string['ejournalname'] = 'eJournal name';
$string['ejournalname_help'] = 'The name given to the particular eJournal plugin';


$string['opendate'] = 'Use Open Date';
$string['opendate_help'] = 'You can specify a date to open the eJournal. Check the check box, and select the date and time you want.
 Users will not be able to view or post into the eJournal before that date. If this is not selected, it will be open immediately.';

$string['closedate'] = 'Use Close Date';
$string['closedate_help'] = 'You can specify a date to close the eJournal. Check the check box, and select the date and time you want.
 Users will not be able to view or post into the eJournal after that date. If this is not selected, it will never be closed.';


$string['post_order_by'] = 'Post Sequence';
$string['post_order_by_help'] = 'Drop down list determining the order of the posts';


$string['student_email_notn'] = 'Student Email Notification';
$string['student_email_notn_help'] = 'Option to email the student every time the teacher has created a new post. This field will be set to \'Yes\' by default.';

$string['teacher_email_notn'] = 'Teacher Email Notification';
$string['teacher_email_notn_help'] = 'Option to email the teacher every time the student has created a new post. This field will be set to \'Yes\' by default.';

// Submit buttons
$string['savechangesandreturntocourse'] = 'Save Changes and return to course';
$string['savechangesanddisplay'] = 'Save and display';

/* ~~~~~~~~~~~~~~~~~~~~~~~~~ Teacher View Summary screen ~~~~~~~~~~~~~~~~~~~~~~~~~*/


$string['user_created'] = 'First Response';
$string['user_modified'] = 'Last Response';

$string['summary_view_posts'] = 'View Posts';

$string['respondents'] = 'Students who have posted against the eReflect Questionnaire within the eJournal';
$string['non-respondents'] = 'Students who have yet to post against the eReflect Questionnaire within the eJournal';

// These Literals will show as headers in each of the 3 Summary Tables
$string['user_picture'] = 'Picture';
$string['user_profile'] = 'Profile';
$string['user_email'] = 'Email';
$string['last_accessed'] = 'Last Login';
$string['user_created'] = 'First Response';
$string['user_modified'] = 'Last Response';

$string['returntocourse'] = 'Return to course';

/*~~~~~~~~~~~~~~~~~~~~~~~~~~   Main Post Page ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

$string['ereflect'] = 'eReflect Questionnaire: ';
$string['ereflect_desc'] = 'Description';

$string['ereflect_missing'] = 'Please choose an eReflect questionnaire';

$string['post_entry'] = 'Your Post';

$string['chooseorderby'] = 'Choose Order Sequence for below';

$string['created'] = 'Created: ';
$string['name'] = 'Name: ';
$string['postdesc'] = 'Post Entry';
$string['filesdesc'] = 'The following files have also been posted';
$string['uploaded_files_text'] = 'The following files have also been posted';
$string['user'] = 'eReflect User: ';

$string['ereflectdropdownlist'] = 'eReflect Drop down list';
$string['userdropdownlist'] = 'User Drop down list';
$string['mainpostpage'] = 'User Drop down list';

/* Email entries */
$string['email_subject_user'] = 'eJournal Post Submitted by {$a}';
$string['email_dear_user'] = 'Dear {$a},';
$string['email_body_user'] = 'I have submitted another post for you to read.<br />Regards,<br />{$a}';

// Errorrs
$string['notstudentteacher'] = 'You can only view the student and teacher eJournal discussion if you are indeed the student or teacher in question';

// EJournal Post Form Buttons
$string['postnewmessage'] = 'POST';
$string['requery'] = 'eJournal Home';
$string['cancelandreturn'] = 'Cancel & return';
$string['viewpdf'] = 'View PDF';
$string['viewereflect'] = 'View Questionnaire';

/****************** course overview page *************************/

$string['activityoverview'] = 'You have eJournals that need attention';
$string['notsubmittedyet'] = 'Not submitted yet';
$string['duedate'] = 'Due date';
$string['duedateno'] = 'No due date';
$string['strpostsoutst'] = '{$a} post(s) requires a response';
