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
    
    // ===========================
    // Phase 2 API Tests
    // ===========================
    
    /**
     * Test create_course with minimum required fields
     */
    public function test_create_course_minimal() {
        global $DB;
        
        // User needs course creation capability
        $this->setAdminUser();
        
        $coursedata = [
            'fullname' => 'Test Course Creation',
            'shortname' => 'TESTCREATE001',
            'category' => 1
        ];
        
        $result = external::create_course(
            $coursedata['fullname'],
            $coursedata['shortname'],
            $coursedata['category']
        );
        
        // Verify response
        $this->assertIsInt($result['id']);
        $this->assertEquals($coursedata['fullname'], $result['fullname']);
        $this->assertEquals($coursedata['shortname'], $result['shortname']);
        $this->assertEquals($coursedata['category'], $result['category']);
        $this->assertTrue($result['visible']);
        $this->assertEquals('topics', $result['format']);
        $this->assertStringContainsString('/course/view.php?id=' . $result['id'], $result['url']);
        
        // Verify in database
        $course = $DB->get_record('course', ['id' => $result['id']]);
        $this->assertNotFalse($course);
        $this->assertEquals($coursedata['fullname'], $course->fullname);
        $this->assertEquals($coursedata['shortname'], $course->shortname);
    }
    
    /**
     * Test create_course with all optional fields
     */
    public function test_create_course_full() {
        $this->setAdminUser();
        
        $coursedata = [
            'fullname' => 'Full Test Course',
            'shortname' => 'FULLTEST001',
            'category' => 1,
            'summary' => '<p>This is a comprehensive test course</p>',
            'format' => 'weeks',
            'numsections' => 12,
            'startdate' => time(),
            'enddate' => time() + (90 * 24 * 60 * 60), // 90 days from now
            'visible' => false,
            'options' => [
                'showgrades' => false,
                'showreports' => false,
                'maxbytes' => 10485760, // 10MB
                'enablecompletion' => true,
                'lang' => 'en'
            ]
        ];
        
        $result = external::create_course(
            $coursedata['fullname'],
            $coursedata['shortname'],
            $coursedata['category'],
            $coursedata['summary'],
            $coursedata['format'],
            $coursedata['numsections'],
            $coursedata['startdate'],
            $coursedata['enddate'],
            $coursedata['visible'],
            $coursedata['options']
        );
        
        // Verify all fields
        $this->assertEquals($coursedata['fullname'], $result['fullname']);
        $this->assertEquals($coursedata['shortname'], $result['shortname']);
        $this->assertEquals($coursedata['format'], $result['format']);
        $this->assertFalse($result['visible']);
        $this->assertEquals($coursedata['startdate'], $result['startdate']);
        $this->assertEquals($coursedata['enddate'], $result['enddate']);
    }
    
    /**
     * Test create_course with duplicate shortname (should fail)
     */
    public function test_create_course_duplicate_shortname() {
        global $DB;
        
        $this->setAdminUser();
        
        // Get existing course shortname
        $existingshortname = $this->course->shortname;
        
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage('shortnametaken');
        
        external::create_course(
            'Duplicate Course',
            $existingshortname, // This should cause error
            1
        );
    }
    
    /**
     * Test create_course with invalid category (should fail)
     */
    public function test_create_course_invalid_category() {
        $this->setAdminUser();
        
        $this->expectException('moodle_exception');
        
        external::create_course(
            'Test Course',
            'INVALIDCAT001',
            99999 // Non-existent category
        );
    }
    
    /**
     * Test create_course without permission (should fail)
     */
    public function test_create_course_no_permission() {
        // Set as student (no course creation permission)
        $this->setUser($this->student);
        
        $this->expectException('required_capability_exception');
        
        external::create_course(
            'Forbidden Course',
            'FORBIDDEN001',
            1
        );
    }
    
    /**
     * Test delete_course on empty course
     */
    public function test_delete_course_empty() {
        global $DB;
        
        $this->setAdminUser();
        
        // Create a test course to delete
        $course = $this->getDataGenerator()->create_course([
            'fullname' => 'Course to Delete',
            'shortname' => 'DELETE001'
        ]);
        
        // Delete the course
        external::delete_course($course->id, true);
        
        // Verify deletion
        $this->assertFalse($DB->record_exists('course', ['id' => $course->id]));
    }
    
    /**
     * Test delete_course with enrollments and no confirmation
     */
    public function test_delete_course_with_enrollments_no_confirm() {
        $this->setAdminUser();
        
        // This course has enrolled users
        try {
            external::delete_course($this->course->id, false);
            $this->fail('Expected exception for deleting course with enrollments');
        } catch (\moodle_exception $e) {
            $this->assertEquals('course_has_enrollments', $e->errorcode);
            // Verify response includes enrollment count
            $debuginfo = json_decode($e->debuginfo, true);
            $this->assertArrayHasKey('active_users', $debuginfo);
            $this->assertArrayHasKey('requires_confirmation', $debuginfo);
            $this->assertTrue($debuginfo['requires_confirmation']);
        }
    }
    
    /**
     * Test delete_course with enrollments and confirmation
     */
    public function test_delete_course_with_enrollments_confirmed() {
        global $DB;
        
        $this->setAdminUser();
        
        // Create a course with enrollments
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        
        // Delete with confirmation
        external::delete_course($course->id, true);
        
        // Verify deletion
        $this->assertFalse($DB->record_exists('course', ['id' => $course->id]));
    }
    
    /**
     * Test delete_course on non-existent course
     */
    public function test_delete_course_not_found() {
        $this->setAdminUser();
        
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage('invalidrecord');
        
        external::delete_course(99999, true);
    }
    
    /**
     * Test delete_course without permission
     */
    public function test_delete_course_no_permission() {
        $this->setUser($this->teacher); // Teachers can't delete courses by default
        
        $this->expectException('required_capability_exception');
        
        external::delete_course($this->course->id, true);
    }
    
    /**
     * Test get_course_details as enrolled student
     */
    public function test_get_course_details_as_student() {
        $this->setUser($this->student);
        
        $result = external::get_course_details($this->course->id);
        
        // Verify basic fields
        $this->assertEquals($this->course->id, $result['id']);
        $this->assertEquals($this->course->shortname, $result['shortname']);
        $this->assertEquals($this->course->fullname, $result['fullname']);
        $this->assertArrayHasKey('summary', $result);
        $this->assertArrayHasKey('format', $result);
        $this->assertArrayHasKey('startdate', $result);
        $this->assertArrayHasKey('category', $result);
        $this->assertArrayHasKey('url', $result);
        
        // Verify counts
        $this->assertArrayHasKey('enrollmentcount', $result);
        $this->assertArrayHasKey('sectioncount', $result);
        $this->assertArrayHasKey('activitycount', $result);
        $this->assertEquals(3, $result['activitycount']); // We created 3 activities in setUp
        
        // Verify user enrollment info
        $this->assertArrayHasKey('user_enrollment', $result);
        $this->assertTrue($result['user_enrollment']['enrolled']);
        $this->assertContains('student', $result['user_enrollment']['roles']);
    }
    
    /**
     * Test get_course_details as teacher
     */
    public function test_get_course_details_as_teacher() {
        $this->setUser($this->teacher);
        
        $result = external::get_course_details($this->course->id);
        
        // Teachers should see their role
        $this->assertContains('editingteacher', $result['user_enrollment']['roles']);
    }
    
    /**
     * Test get_course_details with includes
     */
    public function test_get_course_details_with_includes() {
        $this->setUser($this->teacher);
        
        $result = external::get_course_details(
            $this->course->id,
            ['enrollmentmethods', 'completion'],
            true
        );
        
        // Verify enrollment methods included
        $this->assertArrayHasKey('enrollment_methods', $result);
        $this->assertIsArray($result['enrollment_methods']);
        $this->assertNotEmpty($result['enrollment_methods']);
        
        // Verify at least manual enrollment exists
        $methods = array_column($result['enrollment_methods'], 'type');
        $this->assertContains('manual', $methods);
        
        // Verify completion info included
        $this->assertArrayHasKey('completion', $result);
        $this->assertArrayHasKey('enabled', $result['completion']);
    }
    
    /**
     * Test get_course_details without enrollment
     */
    public function test_get_course_details_not_enrolled() {
        // Create a user not enrolled in the course
        $otheruser = $this->getDataGenerator()->create_user();
        $this->setUser($otheruser);
        
        $this->expectException('required_capability_exception');
        
        external::get_course_details($this->course->id);
    }
    
    /**
     * Test get_course_details as admin (can view any course)
     */
    public function test_get_course_details_as_admin() {
        $this->setAdminUser();
        
        $result = external::get_course_details($this->course->id);
        
        // Admin should be able to view even without enrollment
        $this->assertEquals($this->course->id, $result['id']);
        
        // Admin might not be enrolled
        if (isset($result['user_enrollment'])) {
            $this->assertArrayHasKey('enrolled', $result['user_enrollment']);
        }
    }
    
    /**
     * Test get_course_details for non-existent course
     */
    public function test_get_course_details_not_found() {
        $this->setAdminUser();
        
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage('invalidrecord');
        
        external::get_course_details(99999);
    }
    
    /**
     * Test get_course_details respects course visibility
     */
    public function test_get_course_details_hidden_course() {
        global $DB;
        
        // Hide the course
        $DB->set_field('course', 'visible', 0, ['id' => $this->course->id]);
        
        // Student shouldn't see hidden course details
        $this->setUser($this->student);
        
        // Students can still access if enrolled, but visibility should be false
        $result = external::get_course_details($this->course->id);
        $this->assertFalse($result['visible']);
    }
}