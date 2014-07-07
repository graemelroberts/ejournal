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
 * This file keeps track of upgrades to the graemetest module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * 
 * @package   mod_ereflect
 * @copyright 2013 G.Roberts Cardiff Met
 * @license   
*/

defined('MOODLE_INTERNAL') || die();

/**
 * Execute graemetest upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_ejournal_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    // And upgrade begins here. For each one, you'll need one
    // block of code similar to the next one. Please, delete
    // this comment lines once this file start handling proper
    // upgrade code.

    // if ($oldversion < YYYYMMDD00) { //New version in version.php
    //
    // }
    
    if($oldversion< 2014040800)
    {		        
        $table = new xmldb_table('ejournal_details');	
	$field = new xmldb_field('student_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null,'user_id');
		
        // Add field questions_per_page
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
		
        upgrade_mod_savepoint(true, 2014040800, 'ejournal');	
        
        //$table->add_field('opendate', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        //$table->add_field('closedate', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
    }    

    
    if($oldversion< 2014040801)
    {		        
        $table = new xmldb_table('ejournal_details');	
	$field = new xmldb_field('student_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null,'user_id');
		
        // Add field questions_per_page
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_type($table, $field, true, true);
        }
		
        upgrade_mod_savepoint(true, 2014040801, 'ejournal');	        
    }    
    
    if($oldversion< 2014040802)
    {		        
        $table = new xmldb_table('ejournal_details');	    
        $table->add_key('student_id', XMLDB_KEY_FOREIGN, array('student_id'), 'user', array('id'));		    
        
        upgrade_mod_savepoint(true, 2014040802, 'ejournal');	        
    }

  
        
    // try to add foreign key again !!
    if($oldversion< 2014040900)
    {		        
        $table = new xmldb_table('ejournal_details');	
	$field = new xmldb_field('student_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null,'user_id');
		
        // Add field questions_per_page
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_type($table, $field, true, true);
        }

        $table->add_key('student_id', XMLDB_KEY_FOREIGN, array('student_id'), 'user', array('id'));		    
        
        upgrade_mod_savepoint(true, 2014040900, 'ejournal');
    }
    
    // try to add foreign key again !!
    if($oldversion< 2014050100)
    {		            
        $table = new xmldb_table('ejournal');	
        $field = new xmldb_field('post_order_by');

        // Add field questions_per_page
        if ($dbman->field_exists($table, $field)) 
        {
            $dbman->drop_field ($table, $field, true, true);                 
        }
        
        upgrade_mod_savepoint(true, 2014050100, 'ejournal');        
    }

    if($oldversion< 2014050700)
    {		            
        $table = new xmldb_table('ejournal');	
        $field = new xmldb_field('opendate');

        // Add field questions_per_page
        if ($dbman->field_exists($table, $field)) 
        {
            $dbman->drop_field ($table, $field, true, true);                 
        }

        $field = new xmldb_field('closedate');

        // Add field questions_per_page
        if ($dbman->field_exists($table, $field)) 
        {
            $dbman->drop_field ($table, $field, true, true);                 
        }

        upgrade_mod_savepoint(true, 2014050700, 'ejournal');        
    }
    
    
    // Lines below (this included)  MUST BE DELETED once you get the first version
    // of your module ready to be installed. They are here only
    // for demonstrative purposes and to show how the graemetest
    // iself has been upgraded.

    // For each upgrade block, the file graemetest/version.php
    // needs to be updated . Such change allows Moodle to know
    // that this file has to be processed.

    // To know more about how to write correct DB upgrade scripts it's
    // highly recommended to read information available at:
    //   http://docs.moodle.org/en/Development:XMLDB_Documentation
    // and to play with the XMLDB Editor (in the admin menu) and its
    // PHP generation posibilities.
	
 
	
    // And that's all. Please, examine and understand the 3 example blocks above. Also
    // it's interesting to look how other modules are using this script. Remember that
    // the basic idea is to have "blocks" of code (each one being executed only once,
    // when the module version (version.php) is updated.

    // Lines above (this included) MUST BE DELETED once you get the first version of
    // yout module working. Each time you need to modify something in the module (DB
    // related, you'll raise the version and add one upgrade block here.

    // Final return of upgrade result (true, all went good) to Moodle.
    return true;
}
