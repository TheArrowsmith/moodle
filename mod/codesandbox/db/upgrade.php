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
 * Upgrade script for mod_codesandbox
 *
 * @package    mod_codesandbox
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_codesandbox_upgrade($oldversion) {
    global $DB;
    
    $dbman = $DB->get_manager();
    
    if ($oldversion < 2024010101) {
        
        // Define field language to be added to codesandbox.
        $table = new xmldb_table('codesandbox');
        $field = new xmldb_field('language', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'python', 'starter_code');
        
        // Conditionally launch add field language.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        
        // Define field allowed_languages to be added to codesandbox.
        $field = new xmldb_field('allowed_languages', XMLDB_TYPE_TEXT, null, null, null, null, null, 'language');
        
        // Conditionally launch add field allowed_languages.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        
        // Define field language to be added to codesandbox_submissions.
        $table = new xmldb_table('codesandbox_submissions');
        $field = new xmldb_field('language', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'python', 'code');
        
        // Conditionally launch add field language.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        
        // Codesandbox savepoint reached.
        upgrade_mod_savepoint(true, 2024010101, 'codesandbox');
    }
    
    return true;
}