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
 * PHPUnit tests for local_courseapi external functions
 *
 * @package    local_courseapi
 * @category   test
 * @copyright  2025 Course Management API
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/courseapi/classes/external.php');
require_once($CFG->dirroot . '/local/courseapi/classes/jwt.php');

use local_courseapi\external;
use local_courseapi\jwt;

/**
 * Test cases for Course Management API external functions
 */
class local_courseapi_external_testcase extends advanced_testcase {
    
    /** @var stdClass Test course */
    private $course;
    
    /** @var stdClass Test teacher */
    private $teacher;
    
    /** @var stdClass Test student */
    private $student;
    
    /**
     * Set up test data
     */
    protected function setUp() {
        $this->resetAfterTest();
        
        // Create test course
        $this->course = $this->getDataGenerator()->create_course();
        
        // Create test users
        $this->teacher = $this->getDataGenerator()->create_user();
        $this->student = $this->getDataGenerator()->create_user();
        
        // Enrol users
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, 'student');
        
        // Create some test activities
        $section0 = $this->getDataGenerator()->create_module('forum', ['course' => $this->course->id, 'section' => 0]);
        $section1 = $this->getDataGenerator()->create_module('assign', ['course' => $this->course->id, 'section' => 1]);
        $section1b = $this->getDataGenerator()->create_module('quiz', ['course' => $this->course->id, 'section' => 1]);
    }
    
    /**
     * Test get_course_management_data as teacher
     */
    public function test_get_course_management_data_teacher() {
        $this->setUser($this->teacher);
        
        $result = external::get_course_management_data($this->course->id);
        
        $this->assertEquals($this->course->fullname, $result['course_name']);
        $this->assertGreaterThanOrEqual(2, count($result['sections']));
        
        // Check first section has activities
        $section0 = $result['sections'][0];
        $this->assertEquals(1, count($section0['activities']));
        $this->assertEquals('forum', $section0['activities'][0]['modname']);
        
        // Check second section has activities
        $section1 = $result['sections'][1];
        $this->assertEquals(2, count($section1['activities']));
    }
    
    /**
     * Test get_course_management_data as student (should fail)
     */
    public function test_get_course_management_data_student() {
        $this->setUser($this->student);
        
        $this->expectException('required_capability_exception');
        external::get_course_management_data($this->course->id);
    }
    
    /**
     * Test update_activity
     */
    public function test_update_activity() {
        $this->setUser($this->teacher);
        
        // Get course modules
        $modinfo = get_fast_modinfo($this->course);
        $cms = $modinfo->get_cms();
        $cm = reset($cms);
        
        // Update activity
        $newname = 'Updated Activity Name';
        $result = external::update_activity($cm->id, $newname, false);
        
        $this->assertEquals($newname, $result['name']);
        $this->assertFalse($result['visible']);
        
        // Verify in database
        $updated_cm = get_coursemodule_from_id('', $cm->id);
        $this->assertEquals(0, $updated_cm->visible);
    }
    
    /**
     * Test update_section
     */
    public function test_update_section() {
        global $DB;
        
        $this->setUser($this->teacher);
        
        // Get first section
        $section = $DB->get_record('course_sections', ['course' => $this->course->id, 'section' => 1]);
        
        // Update section
        $newname = 'Updated Section Name';
        $newsummary = '<p>Updated summary</p>';
        $result = external::update_section($section->id, $newname, true, $newsummary);
        
        $this->assertEquals($newname, $result['name']);
        $this->assertTrue($result['visible']);
        $this->assertContains('Updated summary', $result['summary']);
    }
    
    /**
     * Test create and delete activity
     */
    public function test_create_delete_activity() {
        global $DB;
        
        $this->setUser($this->teacher);
        
        // Get a section
        $section = $DB->get_record('course_sections', ['course' => $this->course->id, 'section' => 1]);
        
        // Create activity
        $result = external::create_activity(
            $this->course->id,
            $section->id,
            'assign',
            'Test Assignment',
            'This is a test assignment',
            true
        );
        
        $this->assertIsInt($result['id']);
        $this->assertEquals('Test Assignment', $result['name']);
        $this->assertEquals('assign', $result['modname']);
        $this->assertTrue($result['visible']);
        
        // Delete activity
        external::delete_activity($result['id']);
        
        // Verify deletion
        $this->assertFalse($DB->record_exists('course_modules', ['id' => $result['id']]));
    }
    
    /**
     * Test JWT authentication
     */
    public function test_jwt_authentication() {
        // Test token creation and verification
        $token = jwt::create_token($this->teacher->id, $this->course->id);
        $this->assertIsString($token);
        
        $payload = jwt::verify_token($token);
        $this->assertEquals($this->teacher->id, $payload->user_id);
        $this->assertEquals($this->course->id, $payload->course_id);
        
        // Test user authentication
        $result = jwt::authenticate_user($this->teacher->username, 'password', $this->course->id);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('user', $result);
        $this->assertEquals($this->teacher->id, $result['user']['id']);
    }
    
    /**
     * Test get_user_info
     */
    public function test_get_user_info() {
        $this->setUser($this->teacher);
        
        $result = external::get_user_info();
        
        $this->assertEquals($this->teacher->id, $result['id']);
        $this->assertEquals($this->teacher->username, $result['username']);
        $this->assertEquals($this->teacher->firstname, $result['firstname']);
        $this->assertEquals($this->teacher->lastname, $result['lastname']);
    }
    
    /**
     * Test reorder_section_activities
     */
    public function test_reorder_section_activities() {
        global $DB;
        
        $this->setUser($this->teacher);
        
        // Get section with multiple activities
        $section = $DB->get_record('course_sections', ['course' => $this->course->id, 'section' => 1]);
        $activities = explode(',', $section->sequence);
        
        // Reverse the order
        $reversed = array_reverse($activities);
        
        $result = external::reorder_section_activities($section->id, $reversed);
        $this->assertEquals('success', $result['status']);
        
        // Verify new order
        $updated_section = $DB->get_record('course_sections', ['id' => $section->id]);
        $this->assertEquals(implode(',', $reversed), $updated_section->sequence);
    }
}