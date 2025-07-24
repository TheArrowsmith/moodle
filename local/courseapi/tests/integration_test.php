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
 * Integration tests for Course Management API Phase 2
 *
 * @package    local_courseapi
 * @category   test
 * @copyright  2025 Course Management API
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/courseapi/classes/external.php');

use local_courseapi\external;

/**
 * Integration test cases for Course Management API Phase 2
 * Tests complete workflows and edge cases
 */
class local_courseapi_integration_testcase extends advanced_testcase {
    
    /**
     * Test complete course lifecycle workflow
     */
    public function test_complete_course_lifecycle() {
        global $DB;
        
        $this->resetAfterTest();
        $this->setAdminUser();
        
        // Step 1: Create a course
        $coursedata = [
            'fullname' => 'Complete Lifecycle Test',
            'shortname' => 'LIFECYCLE001',
            'category' => 1,
            'summary' => 'Testing complete course lifecycle',
            'format' => 'topics',
            'numsections' => 5
        ];
        
        $course = external::create_course(
            $coursedata['fullname'],
            $coursedata['shortname'],
            $coursedata['category'],
            $coursedata['summary'],
            $coursedata['format'],
            $coursedata['numsections']
        );
        
        $this->assertIsArray($course);
        $courseid = $course['id'];
        
        // Step 2: Add activities to the course
        $section = $DB->get_record('course_sections', ['course' => $courseid, 'section' => 1]);
        
        $activity1 = external::create_activity(
            $courseid,
            $section->id,
            'forum',
            'Discussion Forum',
            'Course discussions',
            true
        );
        
        $activity2 = external::create_activity(
            $courseid,
            $section->id,
            'assign',
            'First Assignment',
            'Submit your work here',
            true
        );
        
        // Step 3: Update course section
        $updated_section = external::update_section(
            $section->id,
            'Week 1: Introduction',
            true,
            '<p>Welcome to the course!</p>'
        );
        
        $this->assertEquals('Week 1: Introduction', $updated_section['name']);
        
        // Step 4: Get course details with all includes
        $details = external::get_course_details(
            $courseid,
            ['enrollmentmethods', 'completion'],
            true
        );
        
        $this->assertEquals($coursedata['fullname'], $details['fullname']);
        $this->assertEquals(2, $details['activitycount']);
        
        // Step 5: Delete the course
        external::delete_course($courseid, true);
        
        // Verify course is deleted
        $this->assertFalse($DB->record_exists('course', ['id' => $courseid]));
        $this->assertFalse($DB->record_exists('course_modules', ['course' => $courseid]));
    }
    
    /**
     * Test course creation with special characters
     */
    public function test_course_creation_special_characters() {
        $this->resetAfterTest();
        $this->setAdminUser();
        
        $specialcases = [
            [
                'fullname' => 'Course with "Quotes" & Ampersands',
                'shortname' => 'SPECIAL001',
                'summary' => '<p>HTML & "special" \'characters\'</p>'
            ],
            [
                'fullname' => 'Ñoño\'s Café Course',
                'shortname' => 'UTF8COURSE',
                'summary' => 'Testing UTF-8: 你好世界 مرحبا بالعالم'
            ],
            [
                'fullname' => 'Course (with) [brackets] {braces}',
                'shortname' => 'BRACKETS001',
                'summary' => 'Testing <script>alert("XSS")</script> prevention'
            ]
        ];
        
        foreach ($specialcases as $case) {
            $course = external::create_course(
                $case['fullname'],
                $case['shortname'],
                1,
                $case['summary']
            );
            
            // Verify data is stored correctly
            $this->assertEquals($case['fullname'], $course['fullname']);
            $this->assertEquals($case['shortname'], $course['shortname']);
            
            // Get details to verify retrieval
            $details = external::get_course_details($course['id']);
            $this->assertEquals($case['fullname'], $details['fullname']);
            
            // Summary should be cleaned of dangerous content
            $this->assertStringNotContainsString('<script>', $details['summary']);
        }
    }
    
    /**
     * Test concurrent course operations
     */
    public function test_concurrent_course_operations() {
        global $DB;
        
        $this->resetAfterTest();
        $this->setAdminUser();
        
        // Create multiple courses rapidly
        $courses = [];
        for ($i = 1; $i <= 5; $i++) {
            $courses[] = external::create_course(
                "Concurrent Course $i",
                "CONCURRENT00$i",
                1
            );
        }
        
        $this->assertCount(5, $courses);
        
        // Verify all courses exist
        foreach ($courses as $course) {
            $exists = $DB->record_exists('course', ['id' => $course['id']]);
            $this->assertTrue($exists);
        }
        
        // Delete all courses
        foreach ($courses as $course) {
            external::delete_course($course['id'], true);
        }
        
        // Verify all deleted
        foreach ($courses as $course) {
            $exists = $DB->record_exists('course', ['id' => $course['id']]);
            $this->assertFalse($exists);
        }
    }
    
    /**
     * Test course with maximum values
     */
    public function test_course_extreme_values() {
        $this->resetAfterTest();
        $this->setAdminUser();
        
        // Test with very long names (respecting Moodle limits)
        $longname = str_repeat('A', 254); // Moodle's max is 255
        $course = external::create_course(
            $longname,
            'EXTREME001',
            1,
            str_repeat('<p>Long summary text. </p>', 100),
            'topics',
            52, // Max sections
            time(),
            time() + (365 * 24 * 60 * 60 * 10) // 10 years
        );
        
        $this->assertEquals(254, strlen($course['fullname']));
        $this->assertEquals(52, $course['numsections'] ?? 52);
        
        // Clean up
        external::delete_course($course['id'], true);
    }
    
    /**
     * Test error recovery scenarios
     */
    public function test_error_recovery() {
        global $DB;
        
        $this->resetAfterTest();
        $this->setAdminUser();
        
        // Scenario 1: Try to create course with taken shortname
        $course1 = external::create_course('First Course', 'DUPE001', 1);
        
        try {
            external::create_course('Second Course', 'DUPE001', 1);
            $this->fail('Expected exception for duplicate shortname');
        } catch (\moodle_exception $e) {
            $this->assertEquals('shortnametaken', $e->errorcode);
        }
        
        // Scenario 2: Try to delete course twice
        external::delete_course($course1['id'], true);
        
        try {
            external::delete_course($course1['id'], true);
            $this->fail('Expected exception for non-existent course');
        } catch (\moodle_exception $e) {
            $this->assertEquals('invalidrecord', $e->errorcode);
        }
        
        // Scenario 3: Invalid category recovery
        try {
            external::create_course('Invalid Cat Course', 'INVCAT001', 99999);
            $this->fail('Expected exception for invalid category');
        } catch (\moodle_exception $e) {
            // Should handle gracefully
            $this->assertNotNull($e);
        }
    }
    
    /**
     * Test permission inheritance in course hierarchy
     */
    public function test_permission_inheritance() {
        global $DB;
        
        $this->resetAfterTest();
        
        // Create users with different roles
        $manager = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $student = $this->getDataGenerator()->create_user();
        
        // Assign system roles
        $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        role_assign($managerroleid, $manager->id, context_system::instance());
        
        // Manager should be able to create courses
        $this->setUser($manager);
        $course = external::create_course('Manager Course', 'MANAGERCOURSE', 1);
        $this->assertIsArray($course);
        
        // Enrol teacher and student
        $this->getDataGenerator()->enrol_user($teacher->id, $course['id'], 'editingteacher');
        $this->getDataGenerator()->enrol_user($student->id, $course['id'], 'student');
        
        // Teacher can view but not delete
        $this->setUser($teacher);
        $details = external::get_course_details($course['id']);
        $this->assertEquals($course['id'], $details['id']);
        
        try {
            external::delete_course($course['id'], true);
            $this->fail('Teacher should not be able to delete course');
        } catch (\required_capability_exception $e) {
            $this->assertNotNull($e);
        }
        
        // Student can view course details
        $this->setUser($student);
        $details = external::get_course_details($course['id']);
        $this->assertEquals($course['id'], $details['id']);
        $this->assertTrue($details['user_enrollment']['enrolled']);
        
        // Clean up
        $this->setUser($manager);
        external::delete_course($course['id'], true);
    }
}