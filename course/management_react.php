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
 * Course and category management interface using React components.
 *
 * @package    core_course
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->dirroot . '/lib/react_helper.php');
require_once($CFG->dirroot . '/local/courseapi/classes/jwt.php');

$categoryid = optional_param('categoryid', 1, PARAM_INT); // Default to category 1 (Miscellaneous)
$selectedcategoryid = optional_param('selectedcategoryid', $categoryid, PARAM_INT);
$courseid = optional_param('courseid', null, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);
$search = optional_param('search', '', PARAM_RAW);
$blocklist = optional_param('blocklist', null, PARAM_INT);
$modulelist = optional_param('modulelist', '', PARAM_ALPHANUMEXT);
$viewmode = optional_param('view', 'default', PARAM_ALPHA);

require_login();
$context = context_system::instance();

// For testing, just check if user is admin
if (!is_siteadmin()) {
    redirect('/');
}

$PAGE->set_context($context);
$PAGE->set_url('/course/management_react.php', array(
    'categoryid' => $categoryid,
    'view' => $viewmode,
));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('coursecatmanagement', 'moodle'));
$PAGE->set_heading(get_string('coursecatmanagement', 'moodle'));

// Generate JWT token for API authentication
$token = local_courseapi\jwt::create_token($USER->id);

// Gather user capabilities
$capabilities = array(
    'moodle/category:manage' => has_capability('moodle/category:manage', $context),
    'moodle/course:create' => has_capability('moodle/course:create', $context),
    'moodle/course:update' => has_capability('moodle/course:update', $context),
    'moodle/course:visibility' => has_capability('moodle/course:visibility', $context),
    'moodle/course:delete' => has_capability('moodle/course:delete', $context),
    'moodle/site:config' => has_capability('moodle/site:config', $context),
    'moodle/backup:backupcourse' => has_capability('moodle/backup:backupcourse', $context),
    'moodle/site:approvecourse' => has_capability('moodle/site:approvecourse', $context),
);

echo $OUTPUT->header();

// Container for React app
echo html_writer::start_div('', array('id' => 'course-management-app'));
echo html_writer::end_div();

// Mount React component
render_react_component('CourseManagementApp', 'course-management-app', array(
    'token' => $token,
    'initialCategoryId' => $selectedcategoryid,
    'initialCourseId' => $courseid,
    'viewMode' => $viewmode,
    'page' => $page,
    'perPage' => $perpage,
    'search' => $search,
    'capabilities' => $capabilities,
));

// Add some custom styles to make it full height
echo html_writer::tag('style', '
    #course-management-app {
        height: calc(100vh - 200px);
        margin: -15px;
        background: white;
    }
    #page-header,
    .breadcrumb-nav {
        display: none;
    }
');

echo $OUTPUT->footer();