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
 * English strings for markdownfile
 *
 * @package    mod_markdownfile
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'Markdown file';
$string['modulenameplural'] = 'Markdown files';
$string['modulename_help'] = 'The markdown file module enables a teacher to provide markdown content as a course resource.

Markdown content can be uploaded as a file or entered directly using a text editor. The markdown will be converted to HTML when displayed to students.

A markdown file may be used:

* To share course notes or summaries
* To embed formatted content including headings, lists, links, and code blocks
* To provide documentation or instructions in an easy-to-read format';

$string['markdownfile:addinstance'] = 'Add a new markdown file';
$string['markdownfile:view'] = 'View markdown file content';

$string['markdownfilename'] = 'Name';
$string['markdownfilename_help'] = 'The display name of this markdown file resource.';

$string['pluginname'] = 'Markdown file';
$string['pluginadministration'] = 'Markdown file administration';

$string['content'] = 'Markdown content';
$string['content_help'] = 'Enter the content in markdown format. This will be converted to HTML when displayed.';
$string['contentheader'] = 'Content';
$string['contenttype'] = 'Content source';

$string['uploadfile'] = 'Upload markdown file';
$string['entermarkdown'] = 'Enter markdown text';
$string['selectfile'] = 'Select file';

$string['displayselect'] = 'Display';
$string['displayselect_help'] = 'This setting determines how the markdown file is displayed. Options are:

* Automatic - The best display option is selected automatically
* Embed - The markdown content is displayed within the page
* Force download - The user is prompted to download the markdown file';

$string['displayauto'] = 'Automatic';
$string['displayembed'] = 'Embed';
$string['displaydownload'] = 'Force download';

$string['displaysettings'] = 'Display settings';

$string['nocontent'] = 'No markdown content available.';
$string['nomarkdownfiles'] = 'No markdown files in this course';
$string['clicktodownload'] = 'Click the link below to download the file.';

// Privacy API
$string['privacy:metadata'] = 'The Markdown file resource plugin does not store any personal data.';