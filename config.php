<?php  // Moodle configuration file

if (!defined('MOODLE_INTERNAL')) {
    define('MOODLE_INTERNAL', true);
}

error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'moodle';
$CFG->dbuser    = 'root';
$CFG->dbpass    = 'root';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '8889',
  'dbsocket' => '',
);

$CFG->wwwroot   = 'http://localhost:8888';
$CFG->dataroot  = '/Users/george/moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

// Enable debugging
$CFG->debug = 32767; // E_ALL
$CFG->debugdisplay = 1;

// Custom file types - Add support for markdown files
$CFG->customfiletypes = array(
    (object)array(
        'extension' => 'md',
        'type' => 'text/markdown',
        'icon' => 'text',
        'groups' => array('document'),
        'string' => 'markdown'
    ),
    (object)array(
        'extension' => 'markdown',
        'type' => 'text/markdown', 
        'icon' => 'text',
        'groups' => array('document'),
        'string' => 'markdown'
    )
);

// Force cache refresh for JavaScript changes
$CFG->jsrev = time(); // Use current timestamp to force refresh

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
