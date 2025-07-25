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
 * Block to provide quick access to coding progress report
 *
 * @package    block_codeprogress_link
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_codeprogress_link extends block_base {
    
    public function init() {
        $this->title = get_string('pluginname', 'block_codeprogress_link');
    }
    
    public function get_content() {
        global $COURSE, $USER, $CFG;
        
        if ($this->content !== null) {
            return $this->content;
        }
        
        if (empty($this->instance)) {
            return null;
        }
        
        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';
        
        // Only show in course context (not on site home)
        if ($COURSE->id == SITEID) {
            return $this->content;
        }
        
        $context = context_course::instance($COURSE->id);
        
        // Check if user can view the report
        if (has_capability('report/codeprogress:view', $context)) {
            // Use standalone version - no Moodle theme or JavaScript
            $url = new moodle_url('/report/codeprogress/standalone.php', array('course' => $COURSE->id));
            
            // Create URL for integrated version
            $integratedurl = new moodle_url('/report/codeprogress/index.php', array('course' => $COURSE->id));
            
            $this->content->text = '<div class="codeprogress-block">';
            $this->content->text .= '<p>' . get_string('blockdescription', 'block_codeprogress_link') . '</p>';
            
            // Standalone version button
            $this->content->text .= '<div class="text-center mb-2">';
            $this->content->text .= '<a href="' . $url . '" class="btn btn-primary btn-block">';
            $this->content->text .= '<i class="fa fa-bar-chart"></i> ';
            $this->content->text .= get_string('viewreport', 'block_codeprogress_link') . ' (Standalone)';
            $this->content->text .= '</a>';
            $this->content->text .= '</div>';
            
            // Integrated version button
            $this->content->text .= '<div class="text-center mb-2">';
            $this->content->text .= '<a href="' . $integratedurl . '" class="btn btn-info btn-block">';
            $this->content->text .= '<i class="fa fa-bar-chart"></i> ';
            $this->content->text .= get_string('viewreport', 'block_codeprogress_link') . ' (Integrated)';
            $this->content->text .= '</a>';
            $this->content->text .= '</div>';
            
            // Also add a link to the reports list page
            $reportsurl = new moodle_url('/course/reports.php', array('id' => $COURSE->id));
            $this->content->text .= '<div class="text-center mt-2">';
            $this->content->text .= '<a href="' . $reportsurl . '" class="btn btn-secondary btn-sm">';
            $this->content->text .= get_string('allreports', 'block_codeprogress_link');
            $this->content->text .= '</a>';
            $this->content->text .= '</div>';
            
            $this->content->text .= '</div>';
        }
        
        return $this->content;
    }
    
    public function applicable_formats() {
        return array(
            'course-view' => true,
            'mod' => false,
            'my' => false
        );
    }
    
    public function instance_allow_multiple() {
        return false;
    }
    
    public function has_config() {
        return false;
    }
}