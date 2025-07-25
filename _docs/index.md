# Core concepts

Moodle 3.5 is a Learning Management System (LMS) with several major functional domains:

## 1. **Course Management**
The core of Moodle - organizing educational content into courses. Includes course creation, categories, enrollment methods, and course formats (weekly, topics-based, etc.).

**Key Database Tables:**

- `mdl_course` - Core course information
- `mdl_course_categories` - Course category hierarchy
- `mdl_course_sections` - Course sections/topics
- `mdl_course_modules` - Links activities/resources to courses
- `mdl_course_format_options` - Course format settings

**Key Code Locations:**

- `/course/` - Main course management code
- `/course/format/` - Course format plugins
- `/lib/coursecatlib.php` - Course category library
- `/lib/modinfolib.php` - Course module information

## 2. **User Management & Authentication**
Handles user accounts, roles (student, teacher, admin), permissions, and various authentication methods (manual, LDAP, SSO, etc.).

**Key Database Tables:**

- `mdl_user` - User accounts
- `mdl_role` - Role definitions
- `mdl_role_assignments` - User role assignments
- `mdl_role_capabilities` - Permissions for roles
- `mdl_user_preferences` - User preferences
- `mdl_user_lastaccess` - Access tracking

**Key Code Locations:**

- `/user/` - User management pages
- `/auth/` - Authentication plugins
- `/lib/authlib.php` - Authentication library
- `/lib/accesslib.php` - Roles and capabilities
- `/admin/roles/` - Role management

## 3. **Activities & Resources**
- **Activities**: Interactive learning components like assignments, quizzes, forums, wikis, workshops, and SCORM packages
- **Resources**: Static content like files, folders, pages, URLs, and books

**Key Database Tables:**

- `mdl_assign` - Assignment activity
- `mdl_quiz` - Quiz activity
- `mdl_forum` - Forum activity
- `mdl_resource` - File resources
- `mdl_url` - URL resources
- `mdl_book` - Book resources

**Key Code Locations:**

- `/mod/` - All activity and resource modules
- `/mod/assign/` - Assignment module
- `/mod/quiz/` - Quiz module
- `/mod/forum/` - Forum module
- `/lib/modinfolib.php` - Module information library

## 4. **Assessment & Grading**
Comprehensive grading system including gradebook, rubrics, outcomes, competencies, and various grading methods and scales.

**Key Database Tables:**

- `mdl_grade_items` - Gradebook items
- `mdl_grade_grades` - Actual grades
- `mdl_grade_categories` - Grade categories
- `mdl_grading_definitions` - Advanced grading methods
- `mdl_competency` - Competency definitions
- `mdl_competency_usercomp` - User competency records

**Key Code Locations:**

- `/grade/` - Gradebook system
- `/lib/gradelib.php` - Grading library
- `/grade/grading/` - Advanced grading methods
- `/competency/` - Competency framework
- `/lib/grade/` - Grade related classes

## 5. **Communication & Collaboration**
Forums, messaging, chat, comments, notifications, and announcement systems for interaction between users.

**Key Database Tables:**

- `mdl_message` - Messages between users
- `mdl_notifications` - System notifications
- `mdl_forum_posts` - Forum posts
- `mdl_forum_discussions` - Forum discussions
- `mdl_chat` - Chat sessions
- `mdl_comments` - Comments on various items

**Key Code Locations:**

- `/message/` - Messaging system
- `/mod/forum/` - Forum module
- `/mod/chat/` - Chat module
- `/comment/` - Comments system
- `/lib/messagelib.php` - Messaging library

## 6. **Reporting & Analytics**
Activity logs, completion tracking, progress reports, analytics for learning patterns, and custom report generation.

**Key Database Tables:**

- `mdl_logstore_standard_log` - Activity logs
- `mdl_course_completions` - Course completion records
- `mdl_course_modules_completion` - Activity completion
- `mdl_analytics_models` - Analytics models
- `mdl_report_builder` - Custom reports

**Key Code Locations:**

- `/report/` - Report plugins
- `/analytics/` - Analytics subsystem
- `/completion/` - Completion tracking
- `/admin/tool/log/` - Logging system
- `/lib/completionlib.php` - Completion library

## 7. **Groups & Cohorts**
Organizing users into groups within courses or site-wide cohorts for differentiated learning experiences.

**Key Database Tables:**

- `mdl_groups` - Group definitions
- `mdl_groups_members` - Group membership
- `mdl_groupings` - Groupings (groups of groups)
- `mdl_cohort` - System-wide cohorts
- `mdl_cohort_members` - Cohort membership

**Key Code Locations:**

- `/group/` - Group management
- `/cohort/` - Cohort management
- `/lib/grouplib.php` - Group library
- `/lib/cohortlib.php` - Cohort library

## 8. **Badges & Gamification**
Achievement system with badges, completion criteria, and external badge backpack integration.

**Key Database Tables:**

- `mdl_badge` - Badge definitions
- `mdl_badge_issued` - Issued badges
- `mdl_badge_criteria` - Badge criteria
- `mdl_badge_backpack` - External backpack connections

**Key Code Locations:**

- `/badges/` - Badge system
- `/lib/badgeslib.php` - Badges library
- `/badges/criteria/` - Badge criteria types

## 9. **Plugin Architecture**
Extensible system supporting activity modules, blocks, themes, authentication plugins, and more.

**Key Database Tables:**

- `mdl_config_plugins` - Plugin configuration
- `mdl_tool_installaddon_installfrom` - Plugin installation records
- `mdl_upgrade_log` - Plugin upgrade history

**Key Code Locations:**

- `/mod/` - Activity modules
- `/blocks/` - Block plugins
- `/theme/` - Themes
- `/auth/` - Authentication plugins
- `/enrol/` - Enrollment plugins
- `/lib/pluginlib.php` - Plugin management

## 10. **Content Management**
File management, repositories (internal and external), portfolio integration, and backup/restore functionality.

**Key Database Tables:**

- `mdl_files` - File storage
- `mdl_repository` - Repository configurations
- `mdl_repository_instances` - Repository instances
- `mdl_backup_controllers` - Backup operations
- `mdl_portfolio_instance` - Portfolio connections

**Key Code Locations:**

- `/repository/` - Repository plugins
- `/portfolio/` - Portfolio plugins
- `/backup/` - Backup and restore
- `/files/` - File management
- `/lib/filelib.php` - File handling library

These domains work together to create a complete learning environment where instructors can deliver content, assess students, and track progress while learners can access materials, submit work, and collaborate with peers.
