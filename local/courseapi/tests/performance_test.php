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
 * Performance tests for Course Management API Phase 2
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
 * Performance test cases for Course Management API
 * Tests API performance under various load conditions
 */
class local_courseapi_performance_testcase extends advanced_testcase {
    
    /**
     * Test course creation performance
     */
    public function test_bulk_course_creation_performance() {
        $this->resetAfterTest();
        $this->setAdminUser();
        
        $iterations = 50; // Create 50 courses
        $times = [];
        
        for ($i = 1; $i <= $iterations; $i++) {
            $start = microtime(true);
            
            external::create_course(
                "Performance Test Course $i",
                "PERF" . str_pad($i, 6, '0', STR_PAD_LEFT),
                1
            );
            
            $times[] = microtime(true) - $start;
        }
        
        // Calculate statistics
        $avg_time = array_sum($times) / count($times);
        $max_time = max($times);
        $min_time = min($times);
        
        // Performance assertions
        $this->assertLessThan(1.0, $avg_time, 'Average course creation time should be under 1 second');
        $this->assertLessThan(2.0, $max_time, 'Maximum course creation time should be under 2 seconds');
        
        // Log performance metrics for review
        mtrace("Course Creation Performance:");
        mtrace("  Average: " . round($avg_time * 1000, 2) . "ms");
        mtrace("  Min: " . round($min_time * 1000, 2) . "ms");
        mtrace("  Max: " . round($max_time * 1000, 2) . "ms");
    }
    
    /**
     * Test get_course_details performance with large courses
     */
    public function test_course_details_performance() {
        global $DB;
        
        $this->resetAfterTest();
        $this->setAdminUser();
        
        // Create a course with many activities
        $course = $this->getDataGenerator()->create_course();
        
        // Add 100 activities across 10 sections
        for ($section = 0; $section < 10; $section++) {
            for ($activity = 0; $activity < 10; $activity++) {
                $this->getDataGenerator()->create_module('forum', [
                    'course' => $course->id,
                    'section' => $section,
                    'name' => "Activity $section-$activity"
                ]);
            }
        }
        
        // Enroll 200 users
        for ($i = 1; $i <= 200; $i++) {
            $user = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($user->id, $course->id);
        }
        
        // Test performance of getting course details
        $times = [];
        $iterations = 20;
        
        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            
            $details = external::get_course_details($course->id, [], true);
            
            $times[] = microtime(true) - $start;
        }
        
        $avg_time = array_sum($times) / count($times);
        
        // Should handle large courses efficiently
        $this->assertLessThan(0.5, $avg_time, 'Course details retrieval should be under 500ms even for large courses');
        
        // Verify data accuracy
        $this->assertEquals(100, $details['activitycount']);
        $this->assertEquals(200, $details['enrollmentcount']);
    }
    
    /**
     * Test concurrent course operations
     */
    public function test_concurrent_operations() {
        $this->resetAfterTest();
        $this->setAdminUser();
        
        $start = microtime(true);
        $operations = [];
        
        // Simulate concurrent operations
        for ($i = 1; $i <= 10; $i++) {
            // Create course
            $course = external::create_course(
                "Concurrent Course $i",
                "CONC" . str_pad($i, 6, '0', STR_PAD_LEFT),
                1
            );
            
            // Get details
            $details = external::get_course_details($course['id']);
            
            // Create some activities
            $section = get_course_section(0, $course['id']);
            external::create_activity(
                $course['id'],
                $section->id,
                'forum',
                "Forum $i",
                "Description",
                true
            );
            
            $operations[] = $course['id'];
        }
        
        $total_time = microtime(true) - $start;
        
        // All operations should complete quickly
        $this->assertLessThan(10.0, $total_time, 'Concurrent operations should complete within 10 seconds');
        $this->assertCount(10, $operations);
    }
    
    /**
     * Test memory usage for large result sets
     */
    public function test_memory_usage() {
        $this->resetAfterTest();
        $this->setAdminUser();
        
        $initial_memory = memory_get_usage();
        
        // Create courses with large descriptions
        $large_summary = str_repeat('<p>This is a very long course description. </p>', 1000);
        
        for ($i = 1; $i <= 20; $i++) {
            external::create_course(
                "Memory Test Course $i",
                "MEM" . str_pad($i, 6, '0', STR_PAD_LEFT),
                1,
                $large_summary
            );
        }
        
        $peak_memory = memory_get_peak_usage();
        $memory_used = ($peak_memory - $initial_memory) / 1024 / 1024; // Convert to MB
        
        // Memory usage should be reasonable
        $this->assertLessThan(100, $memory_used, 'Memory usage should stay under 100MB for bulk operations');
        
        mtrace("Memory usage: " . round($memory_used, 2) . "MB");
    }
    
    /**
     * Test API response time consistency
     */
    public function test_response_time_consistency() {
        $this->resetAfterTest();
        $this->setAdminUser();
        
        // Create a test course
        $course = external::create_course('Consistency Test', 'CONSIST001', 1);
        
        // Test get_course_details multiple times
        $times = [];
        for ($i = 0; $i < 100; $i++) {
            $start = microtime(true);
            external::get_course_details($course['id']);
            $times[] = microtime(true) - $start;
        }
        
        // Calculate standard deviation
        $avg = array_sum($times) / count($times);
        $variance = 0;
        foreach ($times as $time) {
            $variance += pow($time - $avg, 2);
        }
        $std_dev = sqrt($variance / count($times));
        
        // Response times should be consistent
        $this->assertLessThan($avg * 0.5, $std_dev, 'Response times should be consistent (low standard deviation)');
        
        mtrace("Response time consistency:");
        mtrace("  Average: " . round($avg * 1000, 2) . "ms");
        mtrace("  Std Dev: " . round($std_dev * 1000, 2) . "ms");
    }
    
    /**
     * Test deletion performance for large courses
     */
    public function test_large_course_deletion_performance() {
        global $DB;
        
        $this->resetAfterTest();
        $this->setAdminUser();
        
        // Create a large course
        $course = $this->getDataGenerator()->create_course();
        
        // Add 50 activities
        for ($i = 0; $i < 50; $i++) {
            $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        }
        
        // Enroll 100 users
        for ($i = 0; $i < 100; $i++) {
            $user = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($user->id, $course->id);
        }
        
        // Add grades for each user
        $gradeitem = $DB->get_record('grade_items', [
            'courseid' => $course->id,
            'itemtype' => 'course'
        ]);
        
        $users = $DB->get_records('user_enrolments', [], '', 'userid');
        foreach ($users as $enrolment) {
            $grade = new stdClass();
            $grade->itemid = $gradeitem->id;
            $grade->userid = $enrolment->userid;
            $grade->rawgrade = rand(0, 100);
            $grade->finalgrade = $grade->rawgrade;
            $DB->insert_record('grade_grades', $grade);
        }
        
        // Test deletion performance
        $start = microtime(true);
        external::delete_course($course->id, true);
        $deletion_time = microtime(true) - $start;
        
        // Even large courses should delete in reasonable time
        $this->assertLessThan(30.0, $deletion_time, 'Large course deletion should complete within 30 seconds');
        
        // Verify complete deletion
        $this->assertFalse($DB->record_exists('course', ['id' => $course->id]));
        $this->assertFalse($DB->record_exists('course_modules', ['course' => $course->id]));
        $this->assertFalse($DB->record_exists('grade_grades', ['itemid' => $gradeitem->id]));
        
        mtrace("Large course deletion time: " . round($deletion_time, 2) . "s");
    }
}