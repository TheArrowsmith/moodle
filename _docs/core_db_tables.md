# Moodle Core DB Tables

  1. `mdl_user` (Users)

  - Central to everything - all user accounts
  - Key columns: id, username, email, firstname, lastname
  - Referenced by almost every other table

  2. `mdl_course` (Courses)

  - All courses in the system
  - Key columns: id, fullname, shortname, category
  - The main organizing unit for learning content

  3. `mdl_context` (Security Contexts)

  - Defines security boundaries and permission scopes
  - Critical for understanding Moodle's permission system
  - Links to courses, activities, users, etc.

  4. `mdl_role` & `mdl_role_assignments` (Roles & Permissions)

  - Defines roles (Student, Teacher, Admin, etc.)
  - `role_assignments` links users to roles in specific contexts
  - Essential for understanding who can do what

  Course Structure Tables

  5. `mdl_course_modules` (Activities/Resources)

  - Links activities (forums, quizzes, assignments) to courses
  - Key junction table between courses and activity types
  - References: course, module (type), instance (specific activity)

  6. `mdl_course_sections` (Course Structure)

  - Organizes course content into topics/weeks
  - Defines the layout and organization within a course

  7. `mdl_modules` (Activity Types)

  - Lists all available activity types (forum, quiz, assign, etc.)
  - Small but crucial reference table

  Enrollment & Groups

  8. `mdl_enrol` & `mdl_user_enrolments` (Enrollments)

  - How users get access to courses
  - enrol = enrollment methods, user_enrolments = actual enrollments

  9. `mdl_groups` & `mdl_groups_members` (Groups)

  - Organizes users within courses into smaller groups
  - Important for collaborative activities

  Key Activity Tables

  10. `mdl_assign` (Assignments)

  - One of the most used activity types
  - Related tables: assign_submission, assign_grades

  11. `mdl_forum` (Forums)

  - Discussion forums
  - Related: forum_discussions, forum_posts

  12. `mdl_quiz` (Quizzes)

  - Assessment tool
  - Related: `quiz_attempts`, `quiz_grades`

  Grading System

  13. `mdl_grade_items` & `mdl_grade_grades` (Gradebook)

  - `grade_items` = what can be graded
  - `grade_grades` = actual grades for users

  Quick Reference Relationships

  User enrolls in Course via Enrollment
    └─> User has Role(s) in Context
    └─> Course contains Course_Modules
        └─> Each links to specific Activity (forum, quiz, etc.)
    └─> Users organized into Groups
    └─> Grades stored in `grade_items`/`grade_grades`
