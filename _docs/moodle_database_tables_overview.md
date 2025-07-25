# Moodle Database Tables Overview

This document provides a comprehensive overview of all tables in the Moodle 3.5 database schema. Tables are organized alphabetically with descriptions of their purpose, data storage, and relationships to other tables.

## Table Prefix
All Moodle tables use the prefix `mdl_` by default.

## Core Tables Overview

### `mdl_analytics_indicator_calc`
Stores calculated indicator values for Moodle's analytics and machine learning features. Contains metrics used for predictive modeling of student success and engagement.

- **Related to**: `mdl_analytics_models`, `mdl_context`
- **Purpose**: Analytics engine data storage

### `mdl_analytics_models`
Contains definitions of predictive models used in learning analytics. Each model includes target outcomes, indicators, and time-splitting methods.

- **Related to**: `mdl_analytics_predictions`, `mdl_analytics_models_log`
- **Purpose**: Machine learning model configuration

### `mdl_analytics_models_log`

Tracks changes and evaluation history of analytics models including version updates and performance scores.

- **Related to**: `mdl_analytics_models`
- **Purpose**: Model version control and audit trail

### `mdl_analytics_predict_samples`

Records which data samples have been used for predictions to avoid duplicate processing.

- **Related to**: `mdl_analytics_models`, `mdl_analytics_predictions`
- **Purpose**: Prediction processing tracker

### `mdl_analytics_prediction_actions`

Logs user actions taken on predictions (like viewing or dismissing insights).

- **Related to**: `mdl_analytics_predictions`, `mdl_user`
- **Purpose**: Track prediction interactions

### `mdl_analytics_predictions`

Stores actual predictions made by analytics models including confidence scores and calculations.

- **Related to**: `mdl_analytics_models`, `mdl_context`
- **Purpose**: Prediction results storage

### `mdl_analytics_train_samples`

Tracks samples used for training machine learning models.

- **Related to**: `mdl_analytics_models`, `mdl_files`
- **Purpose**: Training data management

### `mdl_analytics_used_analysables`

Records which course/activity items have been analyzed by each model.

- **Related to**: `mdl_analytics_models`
- **Purpose**: Analysis tracking

### `mdl_analytics_used_files`

Tracks files that have been processed for training and predictions.

- **Related to**: `mdl_analytics_models`, `mdl_files`
- **Purpose**: File processing log

### `mdl_assign`

Core assignment activity module table storing assignment configurations and settings.

- **Related to**: `mdl_course`, `mdl_assign_submission`, `mdl_assign_grades`
- **Purpose**: Assignment activity instances

### `mdl_assign_grades`

Stores grades given to assignment submissions including grader information.

- **Related to**: `mdl_assign`, `mdl_user`, `mdl_assign_submission`
- **Purpose**: Assignment grading data

### `mdl_assign_overrides`

Contains date/time overrides for specific users or groups in assignments.

- **Related to**: `mdl_assign`, `mdl_user`, `mdl_groups`
- **Purpose**: Assignment exception handling

### `mdl_assign_plugin_config`

Stores configuration for assignment submission and feedback plugins.

- **Related to**: `mdl_assign`
- **Purpose**: Plugin settings storage

### `mdl_assign_submission`

Records student assignment submissions including status and attempt information.

- **Related to**: `mdl_assign`, `mdl_user`, `mdl_groups`
- **Purpose**: Submission tracking

### `mdl_assign_user_flags`

Tracks per-user assignment flags like locked status, extensions, and workflow states.

- **Related to**: `mdl_assign`, `mdl_user`
- **Purpose**: User-specific assignment states

### `mdl_assign_user_mapping`

Maps assignment-specific anonymous IDs to actual users for blind marking.

- **Related to**: `mdl_assign`, `mdl_user`
- **Purpose**: Anonymous grading support

### `mdl_assignfeedback_comments`

Stores text feedback comments for assignment submissions.

- **Related to**: `mdl_assign`, `mdl_assign_grades`
- **Purpose**: Feedback text storage

### `mdl_assignfeedback_editpdf_annot`

Contains PDF annotations (lines, shapes) added by teachers to student submissions.

- **Related to**: `mdl_assign_grades`
- **Purpose**: PDF markup storage

### `mdl_assignfeedback_editpdf_cmnt`

Stores text comments added to PDF submissions.

- **Related to**: `mdl_assign_grades`
- **Purpose**: PDF comment storage

### `mdl_assignfeedback_editpdf_queue`

Queue for PDF conversion processing of assignment submissions.

- **Related to**: `mdl_assign_submission`
- **Purpose**: PDF processing queue

### `mdl_assignfeedback_editpdf_quick`

Teacher-defined quick comments for reuse in PDF feedback.

- **Related to**: `mdl_user`
- **Purpose**: Comment templates

### `mdl_assignfeedback_file`

Tracks feedback files uploaded by teachers for submissions.

- **Related to**: `mdl_assign`, `mdl_assign_grades`
- **Purpose**: Feedback file tracking

### `mdl_assignment`

Legacy assignment module table (deprecated, replaced by `mdl_assign`).

- **Related to**: `mdl_course`
- **Purpose**: Old assignment format

### `mdl_assignment_submissions`

Legacy assignment submissions table (deprecated).

- **Related to**: `mdl_assignment`, `mdl_user`
- **Purpose**: Old submission format

### `mdl_assignment_upgrade`

Tracks upgrades from old assignment format to new assign module.

- **Related to**: `mdl_assignment`, `mdl_assign`
- **Purpose**: Migration tracking

### `mdl_assignsubmission_file`

Records files submitted by students for assignments.

- **Related to**: `mdl_assign`, `mdl_assign_submission`
- **Purpose**: File submission tracking

### `mdl_assignsubmission_onlinetext`

Stores online text submissions for assignments.

- **Related to**: `mdl_assign`, `mdl_assign_submission`
- **Purpose**: Text submission storage

### `mdl_auth_oauth2_linked_login`

Links external OAuth2 accounts to Moodle user accounts.

- **Related to**: `mdl_user`, `mdl_oauth2_issuer`
- **Purpose**: OAuth2 authentication

### `mdl_backup_controllers`

Stores backup/restore operation controllers and their state.

- **Related to**: `mdl_user`
- **Purpose**: Backup process management

### `mdl_backup_courses`

Tracks automated course backup schedules and status.

- **Related to**: `mdl_course`
- **Purpose**: Scheduled backup tracking

### `mdl_backup_logs`

Detailed logs from backup and restore operations.

- **Related to**: `mdl_backup_controllers`
- **Purpose**: Backup operation logging

### `mdl_badge`

Defines badges that can be awarded to users for achievements.

- **Related to**: `mdl_course`, `mdl_user`, `mdl_badge_issued`
- **Purpose**: Badge definitions

### `mdl_badge_backpack`

Stores connections to external badge backpack services.

- **Related to**: `mdl_user`
- **Purpose**: External badge integration

### `mdl_badge_criteria`

Defines criteria required to earn badges.

- **Related to**: `mdl_badge`
- **Purpose**: Badge earning rules

### `mdl_badge_criteria_met`

Records which badge criteria have been met by users.

- **Related to**: `mdl_badge_criteria`, `mdl_user`, `mdl_badge_issued`
- **Purpose**: Criteria completion tracking

### `mdl_badge_criteria_param`

Parameters for badge criteria (like specific activities or grades).

- **Related to**: `mdl_badge_criteria`
- **Purpose**: Criteria configuration

### `mdl_badge_external`

Settings for displaying external badges from backpacks.

- **Related to**: `mdl_badge_backpack`
- **Purpose**: External badge display

### `mdl_badge_issued`

Records badges awarded to users with issue dates.

- **Related to**: `mdl_badge`, `mdl_user`
- **Purpose**: Badge award tracking

### `mdl_badge_manual_award`

Tracks manual badge awards by authorized users.

- **Related to**: `mdl_badge`, `mdl_user`, `mdl_role`
- **Purpose**: Manual award logging

### `mdl_block`

Registry of all installed block plugins.

- **Related to**: `mdl_block_instances`
- **Purpose**: Block plugin registry

### `mdl_block_community`

Community finder block data.

- **Related to**: `mdl_user`
- **Purpose**: Community course discovery

### `mdl_block_instances`

Instances of blocks placed on pages with configuration.

- **Related to**: `mdl_block`, `mdl_context`
- **Purpose**: Block placement tracking

### `mdl_block_positions`

Stores custom block positions for specific page contexts.

- **Related to**: `mdl_block_instances`, `mdl_context`
- **Purpose**: Block positioning

### `mdl_block_recent_activity`

Caches recent activity data for the recent activity block.

- **Related to**: `mdl_course`, `mdl_user`
- **Purpose**: Activity feed caching

### `mdl_block_rss_client`

RSS feed configurations for the RSS client block.

- **Related to**: `mdl_user`
- **Purpose**: RSS feed management

### `mdl_blog_association`

Links blog entries to courses and activities.

- **Related to**: `mdl_post`, `mdl_course`, `mdl_context`
- **Purpose**: Blog context mapping

### `mdl_blog_external`

External blog feed configurations for importing.

- **Related to**: `mdl_user`
- **Purpose**: External blog integration

### `mdl_book`

Book resource module storing book settings.

- **Related to**: `mdl_course`, `mdl_book_chapters`
- **Purpose**: Book activity instances

### `mdl_book_chapters`

Individual chapters within book resources.

- **Related to**: `mdl_book`
- **Purpose**: Book content storage

### `mdl_cache_filters`

Cached text filter information for performance.

- **Related to**: `mdl_context`
- **Purpose**: Filter caching

### `mdl_cache_flags`

Generic cache invalidation flags.

- **Purpose**: Cache management

### `mdl_capabilities`

Defines all system capabilities (permissions).

- **Related to**: `mdl_role_capabilities`, `mdl_context`
- **Purpose**: Permission definitions

### `mdl_chat`

Chat activity module instances.

- **Related to**: `mdl_course`, `mdl_chat_messages`
- **Purpose**: Chat room configuration

### `mdl_chat_messages`

Messages posted in chat activities.

- **Related to**: `mdl_chat`, `mdl_user`
- **Purpose**: Chat message storage

### `mdl_chat_messages_current`

Currently active chat messages for performance.

- **Related to**: `mdl_chat`, `mdl_user`
- **Purpose**: Live chat caching

### `mdl_chat_users`

Tracks users currently in chat rooms.

- **Related to**: `mdl_chat`, `mdl_user`
- **Purpose**: Chat presence tracking

### `mdl_choice`

Choice (poll) activity module instances.

- **Related to**: `mdl_course`, `mdl_choice_options`, `mdl_choice_answers`
- **Purpose**: Poll configuration

### `mdl_choice_answers`

User responses to choice activities.

- **Related to**: `mdl_choice`, `mdl_choice_options`, `mdl_user`
- **Purpose**: Poll response storage

### `mdl_choice_options`

Available options in choice activities.

- **Related to**: `mdl_choice`
- **Purpose**: Poll option definitions

### `mdl_codesandbox`

Programming sandbox activity instances.

- **Related to**: `mdl_course`, `mdl_codesandbox_submissions`
- **Purpose**: Code exercise configuration

### `mdl_codesandbox_submissions`

Student code submissions for sandbox activities.

- **Related to**: `mdl_codesandbox`, `mdl_user`
- **Purpose**: Code submission storage

### `mdl_cohort`

System-wide or category-wide user cohorts.

- **Related to**: `mdl_context`, `mdl_cohort_members`
- **Purpose**: User grouping

### `mdl_cohort_members`

Membership of users in cohorts.

- **Related to**: `mdl_cohort`, `mdl_user`
- **Purpose**: Cohort membership

### `mdl_comments`

Generic commenting system used across Moodle.

- **Related to**: `mdl_context`, `mdl_user`
- **Purpose**: Universal commenting

### `mdl_competency`

Competency definitions in the competency framework.

- **Related to**: `mdl_competency_framework`
- **Purpose**: Learning outcome definitions

### `mdl_competency_coursecomp`

Links competencies to courses.

- **Related to**: `mdl_competency`, `mdl_course`
- **Purpose**: Course competency mapping

### `mdl_competency_coursecompsetting`

Course-specific competency settings.

- **Related to**: `mdl_course`
- **Purpose**: Competency configuration

### `mdl_competency_evidence`

Evidence of competency achievement.

- **Related to**: `mdl_competency_usercomp`, `mdl_user`
- **Purpose**: Achievement evidence

### `mdl_competency_framework`

Competency framework definitions.

- **Related to**: `mdl_competency`, `mdl_context`
- **Purpose**: Competency structure

### `mdl_competency_modulecomp`

Links competencies to activity modules.

- **Related to**: `mdl_competency`, `mdl_course_modules`
- **Purpose**: Activity competency mapping

### `mdl_competency_plan`

Learning plans for users.

- **Related to**: `mdl_user`, `mdl_competency_template`
- **Purpose**: Personal learning plans

### `mdl_competency_plancomp`

Competencies included in learning plans.

- **Related to**: `mdl_competency_plan`, `mdl_competency`
- **Purpose**: Plan competency mapping

### `mdl_competency_relatedcomp`

Relationships between related competencies.

- **Related to**: `mdl_competency`
- **Purpose**: Competency relationships

### `mdl_competency_template`

Templates for learning plans.

- **Related to**: `mdl_context`, `mdl_competency_templatecomp`
- **Purpose**: Plan templates

### `mdl_competency_templatecohort`

Links templates to cohorts for automatic plan creation.

- **Related to**: `mdl_competency_template`, `mdl_cohort`
- **Purpose**: Template assignment

### `mdl_competency_templatecomp`

Competencies included in templates.

- **Related to**: `mdl_competency_template`, `mdl_competency`
- **Purpose**: Template competencies

### `mdl_competency_usercomp`

User competency achievement status.

- **Related to**: `mdl_user`, `mdl_competency`
- **Purpose**: Competency progress

### `mdl_competency_usercompcourse`

User competency status within specific courses.

- **Related to**: `mdl_competency_usercomp`, `mdl_course`
- **Purpose**: Course competency progress

### `mdl_competency_usercompplan`

User competency status within learning plans.

- **Related to**: `mdl_competency_usercomp`, `mdl_competency_plan`
- **Purpose**: Plan competency progress

### `mdl_competency_userevidence`

User-provided evidence for competencies.

- **Related to**: `mdl_user`, `mdl_competency_userevidencecomp`
- **Purpose**: Evidence storage

### `mdl_competency_userevidencecomp`

Links evidence to specific competencies.

- **Related to**: `mdl_competency_userevidence`, `mdl_competency`
- **Purpose**: Evidence mapping

### `mdl_config`

Core Moodle configuration settings.

- **Purpose**: Global settings storage

### `mdl_config_log`

Audit log of configuration changes.

- **Related to**: `mdl_user`
- **Purpose**: Settings change tracking

### `mdl_config_plugins`

Plugin-specific configuration settings.

- **Purpose**: Plugin settings storage

### `mdl_context`

Context hierarchy (system, category, course, module, block, user).

- **Related to**: All contextual tables
- **Purpose**: Context management

### `mdl_context_temp`

Temporary context table for upgrades.

- **Related to**: `mdl_context`
- **Purpose**: Upgrade processing

### `mdl_course`

Core course information and settings.

- **Related to**: `mdl_course_categories`, `mdl_enrol`, `mdl_course_modules`
- **Purpose**: Course definitions

### `mdl_course_categories`

Course category hierarchy.

- **Related to**: `mdl_course`, `mdl_context`
- **Purpose**: Course organization

### `mdl_course_completion_aggr_methd`

Aggregation methods for course completion criteria.

- **Related to**: `mdl_course`
- **Purpose**: Completion calculation rules

### `mdl_course_completion_crit_compl`

Records user completion of specific criteria.

- **Related to**: `mdl_course_completion_criteria`, `mdl_user`
- **Purpose**: Criteria completion tracking

### `mdl_course_completion_criteria`

Defines course completion requirements.

- **Related to**: `mdl_course`
- **Purpose**: Completion requirements

### `mdl_course_completion_defaults`

Default completion settings for activities.

- **Related to**: `mdl_course`, `mdl_modules`
- **Purpose**: Default completion rules

### `mdl_course_completions`

Overall course completion status for users.

- **Related to**: `mdl_course`, `mdl_user`
- **Purpose**: Course completion tracking

### `mdl_course_format_options`

Course format specific settings.

- **Related to**: `mdl_course`
- **Purpose**: Format configuration

### `mdl_course_modules`

Links activities/resources to courses with settings.

- **Related to**: `mdl_course`, `mdl_modules`, `mdl_course_sections`
- **Purpose**: Activity instances

### `mdl_course_modules_completion`

Activity completion status for users.

- **Related to**: `mdl_course_modules`, `mdl_user`
- **Purpose**: Activity completion tracking

### `mdl_course_published`

Information about courses published to hubs.

- **Related to**: `mdl_course`
- **Purpose**: Course sharing

### `mdl_course_request`

Pending course creation requests.

- **Related to**: `mdl_user`, `mdl_course_categories`
- **Purpose**: Course request queue

### `mdl_course_sections`

Course sections/topics organization.

- **Related to**: `mdl_course`
- **Purpose**: Course structure

### `mdl_data`

Database activity module instances.

- **Related to**: `mdl_course`, `mdl_data_fields`, `mdl_data_records`
- **Purpose**: Database activity configuration

### `mdl_data_content`

Field values in database activity records.

- **Related to**: `mdl_data_fields`, `mdl_data_records`
- **Purpose**: Database entry content

### `mdl_data_fields`

Field definitions for database activities.

- **Related to**: `mdl_data`
- **Purpose**: Database schema

### `mdl_data_records`

Records/entries in database activities.

- **Related to**: `mdl_data`, `mdl_user`
- **Purpose**: Database entries

### `mdl_editor_atto_autosave`

Auto-saved content from Atto editor.

- **Related to**: `mdl_user`
- **Purpose**: Draft preservation

### `mdl_enrol`

Enrollment plugin instances in courses.

- **Related to**: `mdl_course`, `mdl_user_enrolments`
- **Purpose**: Enrollment methods

### `mdl_enrol_flatfile`

Flat file enrollment plugin data.

- **Purpose**: Bulk enrollment processing

### `mdl_enrol_lti_lti2_consumer`

LTI 2.0 consumer registrations.

- **Related to**: `mdl_enrol_lti_tools`
- **Purpose**: LTI consumer management

### `mdl_enrol_lti_lti2_context`

LTI 2.0 context mappings.

- **Related to**: `mdl_enrol_lti_lti2_consumer`
- **Purpose**: LTI context mapping

### `mdl_enrol_lti_lti2_nonce`

LTI 2.0 nonce values for security.

- **Related to**: `mdl_enrol_lti_lti2_consumer`
- **Purpose**: LTI security

### `mdl_enrol_lti_lti2_resource_link`

LTI 2.0 resource link configurations.

- **Related to**: `mdl_enrol_lti_lti2_context`
- **Purpose**: LTI resource links

### `mdl_enrol_lti_lti2_share_key`

LTI 2.0 shared keys for resource access.

- **Related to**: `mdl_enrol_lti_lti2_resource_link`
- **Purpose**: LTI authentication

### `mdl_enrol_lti_lti2_tool_proxy`

LTI 2.0 tool proxy registrations.

- **Related to**: `mdl_enrol_lti_lti2_consumer`
- **Purpose**: LTI tool registration

### `mdl_enrol_lti_lti2_user_result`

LTI 2.0 user grade passback data.

- **Related to**: `mdl_enrol_lti_lti2_resource_link`, `mdl_user`
- **Purpose**: LTI grade sync

### `mdl_enrol_lti_tool_consumer_map`

Maps LTI tools to consumer keys.

- **Related to**: `mdl_enrol_lti_tools`
- **Purpose**: LTI tool mapping

### `mdl_enrol_lti_tools`

LTI tool configurations for course access.

- **Related to**: `mdl_course`, `mdl_context`
- **Purpose**: LTI tool setup

### `mdl_enrol_lti_users`

Users enrolled via LTI.

- **Related to**: `mdl_user`, `mdl_enrol_lti_tools`
- **Purpose**: LTI user tracking

### `mdl_enrol_paypal`

PayPal enrollment transaction data.

- **Related to**: `mdl_course`, `mdl_user`
- **Purpose**: Payment processing

### `mdl_event`

Calendar events and scheduling.

- **Related to**: `mdl_user`, `mdl_course`, `mdl_groups`
- **Purpose**: Event management

### `mdl_event_subscriptions`

Calendar subscriptions to external calendars.

- **Related to**: `mdl_user`, `mdl_course`
- **Purpose**: Calendar imports

### `mdl_events_handlers`

Legacy event handler registrations.

- **Purpose**: Event system (deprecated)

### `mdl_events_queue`

Queue for legacy event processing.

- **Related to**: `mdl_events_handlers`
- **Purpose**: Event queue (deprecated)

### `mdl_events_queue_handlers`

Processed event queue status.

- **Related to**: `mdl_events_queue`
- **Purpose**: Queue processing (deprecated)

### `mdl_external_functions`

Web service function definitions.

- **Related to**: `mdl_external_services_functions`
- **Purpose**: API function registry

### `mdl_external_services`

Web service definitions.

- **Related to**: `mdl_external_tokens`
- **Purpose**: API service configuration

### `mdl_external_services_functions`

Links functions to web services.

- **Related to**: `mdl_external_services`, `mdl_external_functions`
- **Purpose**: Service function mapping

### `mdl_external_services_users`

User access to specific web services.

- **Related to**: `mdl_external_services`, `mdl_user`
- **Purpose**: Service authorization

### `mdl_external_tokens`

Authentication tokens for web service access.

- **Related to**: `mdl_user`, `mdl_external_services`
- **Purpose**: API authentication

### `mdl_feedback`

Feedback activity module instances.

- **Related to**: `mdl_course`, `mdl_feedback_item`
- **Purpose**: Survey configuration

### `mdl_feedback_completed`

Completed feedback submissions.

- **Related to**: `mdl_feedback`, `mdl_user`
- **Purpose**: Survey responses

### `mdl_feedback_completedtmp`

Temporary storage for anonymous feedback.

- **Related to**: `mdl_feedback`
- **Purpose**: Anonymous response staging

### `mdl_feedback_item`

Questions/items in feedback activities.

- **Related to**: `mdl_feedback`
- **Purpose**: Survey questions

### `mdl_feedback_sitecourse_map`

Maps site-wide feedback to courses.

- **Related to**: `mdl_feedback`, `mdl_course`
- **Purpose**: Cross-course surveys

### `mdl_feedback_template`

Reusable feedback templates.

- **Purpose**: Survey templates

### `mdl_feedback_value`

Individual answers in feedback submissions.

- **Related to**: `mdl_feedback_completed`, `mdl_feedback_item`
- **Purpose**: Survey answer storage

### `mdl_feedback_valuetmp`

Temporary storage for anonymous answers.

- **Related to**: `mdl_feedback_completedtmp`, `mdl_feedback_item`
- **Purpose**: Anonymous answer staging

### `mdl_file_conversion`

Queue and status for file format conversions.

- **Related to**: `mdl_files`
- **Purpose**: Document conversion

### `mdl_files`

Central file storage with metadata.

- **Related to**: `mdl_context`, `mdl_user`
- **Purpose**: File management

### `mdl_files_reference`

External file references and aliases.

- **Related to**: `mdl_files`
- **Purpose**: External file linking

### `mdl_filter_active`

Active text filters by context.

- **Related to**: `mdl_context`
- **Purpose**: Filter configuration

### `mdl_filter_config`

Text filter settings by context.

- **Related to**: `mdl_context`
- **Purpose**: Filter settings

### `mdl_folder`

Folder resource module instances.

- **Related to**: `mdl_course`
- **Purpose**: File folder resources

### `mdl_forum`

Forum activity module instances.

- **Related to**: `mdl_course`, `mdl_forum_discussions`
- **Purpose**: Discussion forum configuration

### `mdl_forum_digests`

User email digest preferences for forums.

- **Related to**: `mdl_forum`, `mdl_user`
- **Purpose**: Email preferences

### `mdl_forum_discussion_subs`

User subscriptions to specific discussions.

- **Related to**: `mdl_forum_discussions`, `mdl_user`
- **Purpose**: Discussion following

### `mdl_forum_discussions`

Forum discussion threads.

- **Related to**: `mdl_forum`, `mdl_user`, `mdl_forum_posts`
- **Purpose**: Discussion organization

### `mdl_forum_posts`

Individual posts in forum discussions.

- **Related to**: `mdl_forum_discussions`, `mdl_user`
- **Purpose**: Discussion content

### `mdl_forum_queue`

Queue for forum email notifications.

- **Related to**: `mdl_forum_posts`, `mdl_user`
- **Purpose**: Email queue

### `mdl_forum_read`

Tracks which forum posts users have read.

- **Related to**: `mdl_forum`, `mdl_user`, `mdl_forum_discussions`
- **Purpose**: Read tracking

### `mdl_forum_subscriptions`

User subscriptions to entire forums.

- **Related to**: `mdl_forum`, `mdl_user`
- **Purpose**: Forum following

### `mdl_forum_track_prefs`

User preference to track unread posts.

- **Related to**: `mdl_forum`, `mdl_user`
- **Purpose**: Tracking preferences

### `mdl_glossary`

Glossary activity module instances.

- **Related to**: `mdl_course`, `mdl_glossary_entries`
- **Purpose**: Glossary configuration

### `mdl_glossary_alias`

Alternative terms for glossary entries.

- **Related to**: `mdl_glossary_entries`
- **Purpose**: Term synonyms

### `mdl_glossary_categories`

Categories within glossaries.

- **Related to**: `mdl_glossary`
- **Purpose**: Entry organization

### `mdl_glossary_entries`

Individual glossary term definitions.

- **Related to**: `mdl_glossary`, `mdl_user`
- **Purpose**: Term storage

### `mdl_glossary_entries_categories`

Links entries to categories.

- **Related to**: `mdl_glossary_entries`, `mdl_glossary_categories`
- **Purpose**: Entry categorization

### `mdl_glossary_formats`

Display format plugins for glossaries.

- **Purpose**: Display options

### `mdl_grade_categories`

Gradebook category hierarchy.

- **Related to**: `mdl_course`, `mdl_grade_items`
- **Purpose**: Grade organization

### `mdl_grade_categories_history`

Historical changes to grade categories.

- **Related to**: `mdl_grade_categories`
- **Purpose**: Grade audit trail

### `mdl_grade_grades`

Individual grade records for users.

- **Related to**: `mdl_grade_items`, `mdl_user`
- **Purpose**: Grade storage

### `mdl_grade_grades_history`

Historical grade changes.

- **Related to**: `mdl_grade_grades`
- **Purpose**: Grade history

### `mdl_grade_import_newitem`

Temporary storage for grade imports.

- **Related to**: `mdl_user`
- **Purpose**: Import staging

### `mdl_grade_import_values`

Temporary grade values during import.

- **Related to**: `mdl_grade_import_newitem`
- **Purpose**: Import processing

### `mdl_grade_items`

Gradeable items (activities, manual items, etc).

- **Related to**: `mdl_course`, `mdl_grade_categories`
- **Purpose**: Gradebook structure

### `mdl_grade_items_history`

Historical changes to grade items.

- **Related to**: `mdl_grade_items`
- **Purpose**: Gradebook audit trail

### `mdl_grade_letters`

Custom grade letter boundaries.

- **Related to**: `mdl_context`
- **Purpose**: Grade display mapping

### `mdl_grade_outcomes`

Learning outcome definitions.

- **Related to**: `mdl_course`
- **Purpose**: Outcome definitions

### `mdl_grade_outcomes_courses`

Links outcomes to courses.

- **Related to**: `mdl_grade_outcomes`, `mdl_course`
- **Purpose**: Outcome usage

### `mdl_grade_outcomes_history`

Historical changes to outcomes.

- **Related to**: `mdl_grade_outcomes`
- **Purpose**: Outcome audit trail

### `mdl_grade_settings`

User-specific gradebook preferences.

- **Related to**: `mdl_course`, `mdl_user`
- **Purpose**: Gradebook preferences

### `mdl_grading_areas`

Identifies gradeable areas in activities.

- **Related to**: `mdl_context`
- **Purpose**: Advanced grading locations

### `mdl_grading_definitions`

Advanced grading method definitions (rubrics, guides).

- **Related to**: `mdl_grading_areas`, `mdl_user`
- **Purpose**: Grading method storage

### `mdl_grading_instances`

Filled grading forms for specific submissions.

- **Related to**: `mdl_grading_definitions`
- **Purpose**: Grading form data

### `mdl_gradingform_guide_comments`

Frequently used comments in marking guides.

- **Related to**: `mdl_grading_definitions`
- **Purpose**: Comment bank

### `mdl_gradingform_guide_criteria`

Criteria definitions for marking guides.

- **Related to**: `mdl_grading_definitions`
- **Purpose**: Guide structure

### `mdl_gradingform_guide_fillings`

Completed marking guide assessments.

- **Related to**: `mdl_grading_instances`, `mdl_gradingform_guide_criteria`
- **Purpose**: Guide assessments

### `mdl_gradingform_rubric_criteria`

Rubric criteria definitions.

- **Related to**: `mdl_grading_definitions`
- **Purpose**: Rubric structure

### `mdl_gradingform_rubric_fillings`

Completed rubric assessments.

- **Related to**: `mdl_grading_instances`, `mdl_gradingform_rubric_criteria`
- **Purpose**: Rubric assessments

### `mdl_gradingform_rubric_levels`

Performance level definitions in rubrics.

- **Related to**: `mdl_gradingform_rubric_criteria`
- **Purpose**: Rubric levels

### `mdl_groupings`

Collections of groups within courses.

- **Related to**: `mdl_course`, `mdl_groupings_groups`
- **Purpose**: Group collections

### `mdl_groupings_groups`

Links groups to groupings.

- **Related to**: `mdl_groupings`, `mdl_groups`
- **Purpose**: Grouping membership

### `mdl_groups`

Groups within courses.

- **Related to**: `mdl_course`, `mdl_groups_members`
- **Purpose**: User groups

### `mdl_groups_members`

Group membership records.

- **Related to**: `mdl_groups`, `mdl_user`
- **Purpose**: Group enrollment

### `mdl_imscp`

IMS Content Package resource instances.

- **Related to**: `mdl_course`
- **Purpose**: IMS package resources

### `mdl_label`

Label resource instances (HTML content).

- **Related to**: `mdl_course`
- **Purpose**: Static content display

### `mdl_lesson`

Lesson activity module instances.

- **Related to**: `mdl_course`, `mdl_lesson_pages`
- **Purpose**: Lesson configuration

### `mdl_lesson_answers`

Answer options for lesson questions.

- **Related to**: `mdl_lesson_pages`
- **Purpose**: Question choices

### `mdl_lesson_attempts`

Student attempts at lesson questions.

- **Related to**: `mdl_lesson_pages`, `mdl_user`
- **Purpose**: Response tracking

### `mdl_lesson_branch`

Branch table page visited tracking.

- **Related to**: `mdl_lesson`, `mdl_lesson_pages`, `mdl_user`
- **Purpose**: Navigation tracking

### `mdl_lesson_grades`

Final grades for lesson attempts.

- **Related to**: `mdl_lesson`, `mdl_user`
- **Purpose**: Lesson scoring

### `mdl_lesson_overrides`

User/group overrides for lesson settings.

- **Related to**: `mdl_lesson`, `mdl_user`, `mdl_groups`
- **Purpose**: Exception handling

### `mdl_lesson_pages`

Individual pages/questions in lessons.

- **Related to**: `mdl_lesson`
- **Purpose**: Lesson content

### `mdl_lesson_timer`

Tracks time spent on lesson attempts.

- **Related to**: `mdl_lesson`, `mdl_user`
- **Purpose**: Time tracking

### `mdl_license`

Available content licenses.

- **Purpose**: License definitions

### `mdl_lock_db`

Database locking for concurrent access control.

- **Purpose**: Concurrency management

### `mdl_log`

Legacy activity log (deprecated).

- **Related to**: `mdl_user`, `mdl_course`
- **Purpose**: Old logging system

### `mdl_log_display`

Display settings for legacy logs.

- **Related to**: `mdl_modules`
- **Purpose**: Log formatting

### `mdl_log_queries`

Stored database queries for reports.

- **Purpose**: Report queries

### `mdl_logstore_standard_log`

Standard logging storage for events.

- **Related to**: `mdl_user`, `mdl_course`, `mdl_context`
- **Purpose**: Event logging

### `mdl_lti`

LTI (External Tool) activity instances.

- **Related to**: `mdl_course`, `mdl_lti_types`
- **Purpose**: LTI activity configuration

### `mdl_lti_submission`

LTI activity grade submissions.

- **Related to**: `mdl_lti`, `mdl_user`
- **Purpose**: LTI grade storage

### `mdl_lti_tool_proxies`

LTI 2.0 tool proxy configurations.

- **Purpose**: LTI 2.0 tools

### `mdl_lti_tool_settings`

Custom settings for LTI tools.

- **Related to**: `mdl_lti`
- **Purpose**: Tool configuration

### `mdl_lti_types`

Preconfigured LTI tool types.

- **Related to**: `mdl_course`
- **Purpose**: Tool templates

### `mdl_lti_types_config`

Configuration for LTI tool types.

- **Related to**: `mdl_lti_types`
- **Purpose**: Tool type settings

### `mdl_ltiservice_gradebookservices`

LTI gradebook service configurations.

- **Related to**: `mdl_lti`
- **Purpose**: Grade synchronization

### `mdl_message`

Legacy messaging table (deprecated).

- **Related to**: `mdl_user`
- **Purpose**: Old message system

### `mdl_message_airnotifier_devices`

Mobile devices registered for push notifications.

- **Related to**: `mdl_user`
- **Purpose**: Push notification targets

### `mdl_message_contacts`

User messaging contacts and blocked users.

- **Related to**: `mdl_user`
- **Purpose**: Contact management

### `mdl_message_conversation_members`

Participants in messaging conversations.

- **Related to**: `mdl_message_conversations`, `mdl_user`
- **Purpose**: Conversation membership

### `mdl_message_conversations`

Messaging conversation definitions.

- **Purpose**: Conversation management

### `mdl_message_popup`

Unread popup notifications.

- **Related to**: `mdl_messages`
- **Purpose**: Popup queue

### `mdl_message_popup_notifications`

Popup notification display tracking.

- **Related to**: `mdl_notifications`
- **Purpose**: Notification delivery

### `mdl_message_processors`

Installed message output plugins.

- **Purpose**: Message delivery methods

### `mdl_message_providers`

Available message types from components.

- **Purpose**: Message type registry

### `mdl_message_read`

Read/processed messages (legacy).

- **Related to**: `mdl_user`
- **Purpose**: Message history

### `mdl_message_user_actions`

User actions on conversations (mute, delete, etc).

- **Related to**: `mdl_messages`, `mdl_user`
- **Purpose**: Conversation preferences

### `mdl_messageinbound_datakeys`

Validation keys for inbound email processing.

- **Related to**: `mdl_user`
- **Purpose**: Email authentication

### `mdl_messageinbound_handlers`

Handlers for processing inbound emails.

- **Purpose**: Email processing rules

### `mdl_messageinbound_messagelist`

Received emails pending processing.

- **Related to**: `mdl_user`
- **Purpose**: Email queue

### `mdl_messages`

Current messaging system messages.

- **Related to**: `mdl_user`, `mdl_message_conversations`
- **Purpose**: Message storage

### `mdl_mnet_application`

Known Moodle network applications.

- **Purpose**: MNet app registry

### `mdl_mnet_host`

Moodle network peer sites.

- **Related to**: `mdl_mnet_application`
- **Purpose**: Network peer registry

### `mdl_mnet_host2service`

Services available on MNet hosts.

- **Related to**: `mdl_mnet_host`, `mdl_mnet_service`
- **Purpose**: Peer service mapping

### `mdl_mnet_log`

Logs from remote MNet operations.

- **Related to**: `mdl_mnet_host`, `mdl_user`, `mdl_course`
- **Purpose**: Network activity log

### `mdl_mnet_remote_rpc`

Remote procedures available via MNet.

- **Related to**: `mdl_mnet_host`
- **Purpose**: RPC registry

### `mdl_mnet_remote_service2rpc`

Links remote services to RPC methods.

- **Related to**: `mdl_mnet_remote_rpc`, `mdl_mnet_service`
- **Purpose**: Service method mapping

### `mdl_mnet_rpc`

RPC methods available for MNet.

- **Purpose**: Method definitions

### `mdl_mnet_service`

MNet service definitions.

- **Purpose**: Service registry

### `mdl_mnet_service2rpc`

Links services to their RPC methods.

- **Related to**: `mdl_mnet_service`, `mdl_mnet_rpc`
- **Purpose**: Service composition

### `mdl_mnet_session`

Active SSO sessions via MNet.

- **Related to**: `mdl_user`, `mdl_mnet_host`
- **Purpose**: SSO tracking

### `mdl_mnet_sso_access_control`

Access control for MNet SSO.

- **Related to**: `mdl_user`, `mdl_mnet_host`
- **Purpose**: SSO permissions

### `mdl_mnetservice_enrol_courses`

Courses available for remote enrollment.

- **Related to**: `mdl_course`, `mdl_mnet_host`
- **Purpose**: Remote course catalog

### `mdl_mnetservice_enrol_enrolments`

Remote user enrollments via MNet.

- **Related to**: `mdl_user`, `mdl_mnet_host`
- **Purpose**: Remote enrollment tracking

### `mdl_modules`

Installed activity module types.

- **Related to**: `mdl_course_modules`
- **Purpose**: Module registry

### `mdl_my_pages`

User dashboard page configurations.

- **Related to**: `mdl_user`
- **Purpose**: Dashboard customization

### `mdl_notifications`

System notifications for users.

- **Related to**: `mdl_user`
- **Purpose**: Notification queue

### `mdl_oauth2_endpoint`

OAuth2 provider endpoint URLs.

- **Related to**: `mdl_oauth2_issuer`
- **Purpose**: OAuth2 endpoints

### `mdl_oauth2_issuer`

OAuth2 identity provider configurations.

- **Purpose**: OAuth2 providers

### `mdl_oauth2_system_account`

System-level OAuth2 authenticated accounts.

- **Related to**: `mdl_oauth2_issuer`
- **Purpose**: OAuth2 system auth

### `mdl_oauth2_user_field_mapping`

Maps OAuth2 claims to user profile fields.

- **Related to**: `mdl_oauth2_issuer`
- **Purpose**: Profile mapping

### `mdl_page`

Page resource module instances.

- **Related to**: `mdl_course`
- **Purpose**: Static page resources

### `mdl_portfolio_instance`

Configured portfolio plugins.

- **Purpose**: Portfolio destinations

### `mdl_portfolio_instance_config`

Settings for portfolio instances.

- **Related to**: `mdl_portfolio_instance`
- **Purpose**: Portfolio configuration

### `mdl_portfolio_instance_user`

User preferences for portfolio instances.

- **Related to**: `mdl_portfolio_instance`, `mdl_user`
- **Purpose**: Portfolio preferences

### `mdl_portfolio_log`

Portfolio export transaction log.

- **Related to**: `mdl_user`
- **Purpose**: Export tracking

### `mdl_portfolio_mahara_queue`

Queue for Mahara portfolio transfers.

- **Related to**: `mdl_user`
- **Purpose**: Mahara integration

### `mdl_portfolio_tempdata`

Temporary data during portfolio exports.

- **Related to**: `mdl_user`
- **Purpose**: Export staging

### `mdl_post`

Blog posts and comments.

- **Related to**: `mdl_user`
- **Purpose**: Blog content

### `mdl_profiling`

Performance profiling data.

- **Purpose**: Performance analysis

### `mdl_qtype_ddimageortext`

Drag-drop onto image question type.

- **Related to**: `mdl_question`
- **Purpose**: Question subtype

### `mdl_qtype_ddimageortext_drags`

Draggable items for image questions.

- **Related to**: `mdl_qtype_ddimageortext`
- **Purpose**: Drag items

### `mdl_qtype_ddimageortext_drops`

Drop zones for image questions.

- **Related to**: `mdl_qtype_ddimageortext`
- **Purpose**: Drop targets

### `mdl_qtype_ddmarker`

Drag markers question type.

- **Related to**: `mdl_question`
- **Purpose**: Question subtype

### `mdl_qtype_ddmarker_drags`

Draggable markers.

- **Related to**: `mdl_qtype_ddmarker`
- **Purpose**: Marker items

### `mdl_qtype_ddmarker_drops`

Drop zones for markers.

- **Related to**: `mdl_qtype_ddmarker`
- **Purpose**: Marker targets

### `mdl_qtype_essay_options`

Essay question type settings.

- **Related to**: `mdl_question`
- **Purpose**: Essay configuration

### `mdl_qtype_match_options`

Matching question type settings.

- **Related to**: `mdl_question`
- **Purpose**: Match configuration

### `mdl_qtype_match_subquestions`

Sub-questions for matching questions.

- **Related to**: `mdl_question`
- **Purpose**: Match items

### `mdl_qtype_multichoice_options`

Multiple choice question settings.

- **Related to**: `mdl_question`
- **Purpose**: MCQ configuration

### `mdl_qtype_randomsamatch_options`

Random short-answer matching settings.

- **Related to**: `mdl_question`
- **Purpose**: Random match config

### `mdl_qtype_shortanswer_options`

Short answer question settings.

- **Related to**: `mdl_question`
- **Purpose**: Short answer config

### `mdl_question`

Question bank questions.

- **Related to**: `mdl_question_categories`
- **Purpose**: Question storage

### `mdl_question_answers`

Answer options for questions.

- **Related to**: `mdl_question`
- **Purpose**: Answer choices

### `mdl_question_attempt_step_data`

Data for each step in a question attempt.

- **Related to**: `mdl_question_attempt_steps`
- **Purpose**: Step data storage

### `mdl_question_attempt_steps`

Steps/states in question attempts.

- **Related to**: `mdl_question_attempts`
- **Purpose**: Attempt progression

### `mdl_question_attempts`

Individual question attempt instances.

- **Related to**: `mdl_question`, `mdl_question_usages`
- **Purpose**: Attempt tracking

### `mdl_question_calculated`

Calculated question type data.

- **Related to**: `mdl_question`
- **Purpose**: Calculated questions

### `mdl_question_calculated_options`

Settings for calculated questions.

- **Related to**: `mdl_question_calculated`
- **Purpose**: Calculation settings

### `mdl_question_categories`

Question bank categories.

- **Related to**: `mdl_context`
- **Purpose**: Question organization

### `mdl_question_dataset_definitions`

Dataset definitions for calculated questions.

- **Related to**: `mdl_question_categories`
- **Purpose**: Variable definitions

### `mdl_question_dataset_items`

Dataset values for calculated questions.

- **Related to**: `mdl_question_dataset_definitions`
- **Purpose**: Variable values

### `mdl_question_datasets`

Links questions to datasets.

- **Related to**: `mdl_question`, `mdl_question_dataset_definitions`
- **Purpose**: Dataset usage

### `mdl_question_ddwtos`

Drag-drop into text questions.

- **Related to**: `mdl_question`
- **Purpose**: Drag-drop text

### `mdl_question_gapselect`

Gap select question type.

- **Related to**: `mdl_question`
- **Purpose**: Gap selection

### `mdl_question_hints`

Hints for questions.

- **Related to**: `mdl_question`
- **Purpose**: Question hints

### `mdl_question_multianswer`

Embedded answers (Cloze) questions.

- **Related to**: `mdl_question`
- **Purpose**: Cloze questions

### `mdl_question_numerical`

Numerical question type data.

- **Related to**: `mdl_question`
- **Purpose**: Numeric questions

### `mdl_question_numerical_options`

Settings for numerical questions.

- **Related to**: `mdl_question_numerical`
- **Purpose**: Number settings

### `mdl_question_numerical_units`

Units for numerical questions.

- **Related to**: `mdl_question_numerical`
- **Purpose**: Unit definitions

### `mdl_question_response_analysis`

Cached question response analysis.

- **Related to**: `mdl_question`
- **Purpose**: Response statistics

### `mdl_question_response_count`

Response frequency analysis cache.

- **Related to**: `mdl_question`
- **Purpose**: Response counts

### `mdl_question_statistics`

Question performance statistics.

- **Related to**: `mdl_question`
- **Purpose**: Question analytics

### `mdl_question_truefalse`

True/false question type data.

- **Related to**: `mdl_question`
- **Purpose**: T/F questions

### `mdl_question_usages`

Groups of question attempts (e.g., in a quiz).

- **Related to**: `mdl_context`
- **Purpose**: Attempt grouping

### `mdl_quiz`

Quiz activity module instances.

- **Related to**: `mdl_course`, `mdl_quiz_slots`
- **Purpose**: Quiz configuration

### `mdl_quiz_attempts`

Student quiz attempt records.

- **Related to**: `mdl_quiz`, `mdl_user`, `mdl_question_usages`
- **Purpose**: Quiz attempts

### `mdl_quiz_feedback`

Feedback messages for score ranges.

- **Related to**: `mdl_quiz`
- **Purpose**: Grade feedback

### `mdl_quiz_grades`

Final quiz grades for users.

- **Related to**: `mdl_quiz`, `mdl_user`
- **Purpose**: Quiz scores

### `mdl_quiz_overrides`

User/group quiz setting overrides.

- **Related to**: `mdl_quiz`, `mdl_user`, `mdl_groups`
- **Purpose**: Quiz exceptions

### `mdl_quiz_overview_regrades`

Tracks questions needing regrading.

- **Related to**: `mdl_quiz`, `mdl_question`
- **Purpose**: Regrade queue

### `mdl_quiz_reports`

Installed quiz report plugins.

- **Purpose**: Report registry

### `mdl_quiz_sections`

Quiz page/section organization.

- **Related to**: `mdl_quiz`
- **Purpose**: Quiz structure

### `mdl_quiz_slot_tags`

Tags required for random questions.

- **Related to**: `mdl_quiz_slots`
- **Purpose**: Random criteria

### `mdl_quiz_slots`

Questions included in quizzes.

- **Related to**: `mdl_quiz`, `mdl_question`
- **Purpose**: Quiz composition

### `mdl_quiz_statistics`

Cached quiz performance statistics.

- **Related to**: `mdl_quiz`
- **Purpose**: Quiz analytics

### `mdl_rating`

Generic rating/star system.

- **Related to**: `mdl_context`, `mdl_user`
- **Purpose**: Content rating

### `mdl_registration_hubs`

Registered Moodle hub connections.

- **Purpose**: Hub registry

### `mdl_repository`

Installed repository plugins.

- **Purpose**: Repository types

### `mdl_repository_instance_config`

Settings for repository instances.

- **Related to**: `mdl_repository_instances`
- **Purpose**: Repository config

### `mdl_repository_instances`

Configured repository connections.

- **Related to**: `mdl_repository`, `mdl_context`
- **Purpose**: Repository setup

### `mdl_repository_onedrive_access`

OneDrive access tokens.

- **Related to**: `mdl_user`
- **Purpose**: OneDrive auth

### `mdl_resource`

File resource module instances.

- **Related to**: `mdl_course`
- **Purpose**: File resources

### `mdl_resource_old`

Legacy resource module data.

- **Related to**: `mdl_course`
- **Purpose**: Old resources

### `mdl_role`

Role definitions (Student, Teacher, etc).

- **Related to**: `mdl_role_assignments`, `mdl_role_capabilities`
- **Purpose**: Permission roles

### `mdl_role_allow_assign`

Which roles can assign other roles.

- **Related to**: `mdl_role`
- **Purpose**: Role management

### `mdl_role_allow_override`

Which roles can override other roles.

- **Related to**: `mdl_role`
- **Purpose**: Permission override

### `mdl_role_allow_switch`

Which roles can switch to other roles.

- **Related to**: `mdl_role`
- **Purpose**: Role switching

### `mdl_role_allow_view`

Which roles can view other roles.

- **Related to**: `mdl_role`
- **Purpose**: Role visibility

### `mdl_role_assignments`

Assigns roles to users in contexts.

- **Related to**: `mdl_role`, `mdl_user`, `mdl_context`
- **Purpose**: Role grants

### `mdl_role_capabilities`

Permission overrides for roles.

- **Related to**: `mdl_role`, `mdl_capabilities`, `mdl_context`
- **Purpose**: Permission customization

### `mdl_role_context_levels`

Valid contexts for each role.

- **Related to**: `mdl_role`
- **Purpose**: Role scope

### `mdl_role_names`

Custom role names by context.

- **Related to**: `mdl_role`, `mdl_context`
- **Purpose**: Role naming

### `mdl_role_sortorder`

Display order for roles.

- **Related to**: `mdl_role`, `mdl_context`
- **Purpose**: Role ordering

### `mdl_scale`

Custom grading scales.

- **Related to**: `mdl_course`
- **Purpose**: Grade scales

### `mdl_scale_history`

Historical changes to scales.

- **Related to**: `mdl_scale`
- **Purpose**: Scale audit trail

### `mdl_scorm`

SCORM package activity instances.

- **Related to**: `mdl_course`, `mdl_scorm_scoes`
- **Purpose**: SCORM configuration

### `mdl_scorm_aicc_session`

AICC session data for SCORM.

- **Related to**: `mdl_scorm`, `mdl_user`
- **Purpose**: AICC protocol

### `mdl_scorm_scoes`

Individual SCOs within SCORM packages.

- **Related to**: `mdl_scorm`
- **Purpose**: SCORM structure

### `mdl_scorm_scoes_data`

Static data for SCORM SCOs.

- **Related to**: `mdl_scorm_scoes`
- **Purpose**: SCO content

### `mdl_scorm_scoes_track`

User progress tracking in SCORM.

- **Related to**: `mdl_scorm_scoes`, `mdl_user`
- **Purpose**: SCORM tracking

### `mdl_scorm_seq_mapinfo`

SCORM 2004 sequencing map info.

- **Related to**: `mdl_scorm_scoes`
- **Purpose**: Sequencing data

### `mdl_scorm_seq_objective`

SCORM 2004 objectives.

- **Related to**: `mdl_scorm_scoes`
- **Purpose**: Learning objectives

### `mdl_scorm_seq_rolluprule`

SCORM 2004 rollup rules.

- **Related to**: `mdl_scorm_scoes`
- **Purpose**: Progress rules

### `mdl_scorm_seq_rolluprulecond`

Conditions for rollup rules.

- **Related to**: `mdl_scorm_seq_rolluprule`
- **Purpose**: Rule conditions

### `mdl_scorm_seq_rulecond`

SCORM 2004 rule conditions.

- **Related to**: `mdl_scorm_scoes`
- **Purpose**: Sequencing conditions

### `mdl_scorm_seq_ruleconds`

SCORM 2004 rule condition sets.

- **Related to**: `mdl_scorm_scoes`
- **Purpose**: Condition groups

### `mdl_search_index_requests`

Queued items for search indexing.

- **Related to**: `mdl_context`
- **Purpose**: Search queue

### `mdl_search_simpledb_index`

Simple database search index.

- **Purpose**: Search data

### `mdl_sessions`

Active user sessions.

- **Related to**: `mdl_user`
- **Purpose**: Session management

### `mdl_stats_daily`

Daily statistics snapshots.

- **Related to**: `mdl_course`, `mdl_user`
- **Purpose**: Daily analytics

### `mdl_stats_monthly`

Monthly statistics aggregations.

- **Related to**: `mdl_course`
- **Purpose**: Monthly analytics

### `mdl_stats_user_daily`

Daily per-user statistics.

- **Related to**: `mdl_user`, `mdl_course`
- **Purpose**: User daily stats

### `mdl_stats_user_monthly`

Monthly per-user statistics.

- **Related to**: `mdl_user`, `mdl_course`
- **Purpose**: User monthly stats

### `mdl_stats_user_weekly`

Weekly per-user statistics.

- **Related to**: `mdl_user`, `mdl_course`
- **Purpose**: User weekly stats

### `mdl_stats_weekly`

Weekly statistics aggregations.

- **Related to**: `mdl_course`
- **Purpose**: Weekly analytics

### `mdl_survey`

Survey activity module instances.

- **Related to**: `mdl_course`
- **Purpose**: Survey configuration

### `mdl_survey_analysis`

Analyzed survey responses.

- **Related to**: `mdl_survey`, `mdl_user`
- **Purpose**: Survey analysis

### `mdl_survey_answers`

Individual survey question responses.

- **Related to**: `mdl_survey`, `mdl_survey_questions`, `mdl_user`
- **Purpose**: Survey responses

### `mdl_survey_questions`

Pre-defined survey questions.

- **Purpose**: Question bank

### `mdl_tag`

Tag definitions.

- **Related to**: `mdl_tag_instance`
- **Purpose**: Tag vocabulary

### `mdl_tag_area`

Taggable areas in Moodle.

- **Related to**: `mdl_context`
- **Purpose**: Tag contexts

### `mdl_tag_coll`

Tag collections.

- **Purpose**: Tag grouping

### `mdl_tag_correlation`

Related tag calculations.

- **Related to**: `mdl_tag`
- **Purpose**: Tag relationships

### `mdl_tag_instance`

Items tagged with tags.

- **Related to**: `mdl_tag`, `mdl_context`
- **Purpose**: Tag assignments

### `mdl_task_adhoc`

Queued one-time background tasks.

- **Purpose**: Task queue

### `mdl_task_scheduled`

Recurring scheduled tasks.

- **Purpose**: Cron tasks

### `mdl_tool_cohortroles`

Assigns roles to cohorts.

- **Related to**: `mdl_cohort`, `mdl_role`
- **Purpose**: Bulk role assignment

### `mdl_tool_customlang`

Custom language string modifications.

- **Purpose**: Language customization

### `mdl_tool_customlang_components`

Language components with customizations.

- **Purpose**: Component tracking

### `mdl_tool_dataprivacy_category`

Data privacy categories for retention.

- **Related to**: `mdl_context`
- **Purpose**: Privacy categories

### `mdl_tool_dataprivacy_contextlist`

Contexts included in privacy requests.

- **Related to**: `mdl_tool_dataprivacy_request`
- **Purpose**: Request scope

### `mdl_tool_dataprivacy_ctxexpired`

Contexts with expired retention periods.

- **Related to**: `mdl_context`
- **Purpose**: Retention tracking

### `mdl_tool_dataprivacy_ctxinstance`

Context-level privacy settings.

- **Related to**: `mdl_context`
- **Purpose**: Privacy configuration

### `mdl_tool_dataprivacy_ctxlevel`

Context level privacy defaults.

- **Purpose**: Privacy defaults

### `mdl_tool_dataprivacy_ctxlst_ctx`

Individual contexts in privacy requests.

- **Related to**: `mdl_tool_dataprivacy_contextlist`, `mdl_context`
- **Purpose**: Request contexts

### `mdl_tool_dataprivacy_purpose`

Data retention purposes.

- **Purpose**: Retention reasons

### `mdl_tool_dataprivacy_purposerole`

Maps purposes to roles for defaults.

- **Related to**: `mdl_tool_dataprivacy_purpose`, `mdl_role`
- **Purpose**: Purpose assignment

### `mdl_tool_dataprivacy_request`

User data privacy requests.

- **Related to**: `mdl_user`
- **Purpose**: Privacy requests

### `mdl_tool_dataprivacy_rqst_ctxlst`

Links requests to context lists.

- **Related to**: `mdl_tool_dataprivacy_request`, `mdl_tool_dataprivacy_contextlist`
- **Purpose**: Request mapping

### `mdl_tool_monitor_events`

Event monitoring subscriptions.

- **Related to**: `mdl_user`
- **Purpose**: Event alerts

### `mdl_tool_monitor_history`

Event monitoring trigger history.

- **Related to**: `mdl_tool_monitor_events`
- **Purpose**: Alert history

### `mdl_tool_monitor_rules`

Event monitoring rule definitions.

- **Related to**: `mdl_course`, `mdl_user`
- **Purpose**: Alert rules

### `mdl_tool_monitor_subscriptions`

User subscriptions to monitoring rules.

- **Related to**: `mdl_tool_monitor_rules`, `mdl_user`
- **Purpose**: Alert subscriptions

### `mdl_tool_policy`

Site policy documents.

- **Purpose**: Policy management

### `mdl_tool_policy_acceptances`

User policy acceptance records.

- **Related to**: `mdl_tool_policy_versions`, `mdl_user`
- **Purpose**: Consent tracking

### `mdl_tool_policy_versions`

Versions of policy documents.

- **Related to**: `mdl_tool_policy`
- **Purpose**: Policy versioning

### `mdl_tool_recyclebin_category`

Deleted items from course categories.

- **Related to**: `mdl_course_categories`
- **Purpose**: Category recycle bin

### `mdl_tool_recyclebin_course`

Deleted items from courses.

- **Related to**: `mdl_course`
- **Purpose**: Course recycle bin

### `mdl_tool_usertours_steps`

Steps in user interface tours.

- **Related to**: `mdl_tool_usertours_tours`
- **Purpose**: Tour content

### `mdl_tool_usertours_tours`

User interface tour definitions.

- **Purpose**: UI tours

### `mdl_upgrade_log`

Detailed upgrade process logging.

- **Purpose**: Upgrade tracking

### `mdl_url`

URL resource module instances.

- **Related to**: `mdl_course`
- **Purpose**: External links

### `mdl_user`

Core user accounts table.

- **Related to**: Almost all user-related tables
- **Purpose**: User accounts

### `mdl_user_devices`

User mobile devices for push notifications.

- **Related to**: `mdl_user`
- **Purpose**: Device registration

### `mdl_user_enrolments`

Links users to enrollment instances.

- **Related to**: `mdl_user`, `mdl_enrol`
- **Purpose**: Course enrollment

### `mdl_user_info_category`

Custom user profile field categories.

- **Purpose**: Profile organization

### `mdl_user_info_data`

Custom user profile field values.

- **Related to**: `mdl_user`, `mdl_user_info_field`
- **Purpose**: Profile data

### `mdl_user_info_field`

Custom user profile field definitions.

- **Related to**: `mdl_user_info_category`
- **Purpose**: Profile fields

### `mdl_user_lastaccess`

Tracks last course access times.

- **Related to**: `mdl_user`, `mdl_course`
- **Purpose**: Access tracking

### `mdl_user_password_history`

Previous password hashes for reuse prevention.

- **Related to**: `mdl_user`
- **Purpose**: Password history

### `mdl_user_password_resets`

Pending password reset requests.

- **Related to**: `mdl_user`
- **Purpose**: Reset tokens

### `mdl_user_preferences`

User preference key-value storage.

- **Related to**: `mdl_user`
- **Purpose**: User settings

### `mdl_user_private_key`

User API keys for external access.

- **Related to**: `mdl_user`
- **Purpose**: API authentication

### `mdl_wiki`

Wiki activity module instances.

- **Related to**: `mdl_course`, `mdl_wiki_subwikis`
- **Purpose**: Wiki configuration

### `mdl_wiki_links`

Links between wiki pages.

- **Related to**: `mdl_wiki_subwikis`
- **Purpose**: Page connections

### `mdl_wiki_locks`

Page locking for concurrent editing.

- **Related to**: `mdl_wiki_pages`, `mdl_user`
- **Purpose**: Edit locking

### `mdl_wiki_pages`

Individual wiki pages.

- **Related to**: `mdl_wiki_subwikis`
- **Purpose**: Wiki content

### `mdl_wiki_subwikis`

Wiki instances for users/groups.

- **Related to**: `mdl_wiki`, `mdl_user`, `mdl_groups`
- **Purpose**: Wiki instances

### `mdl_wiki_synonyms`

Page title synonyms for linking.

- **Related to**: `mdl_wiki_pages`
- **Purpose**: Page aliases

### `mdl_wiki_versions`

Wiki page version history.

- **Related to**: `mdl_wiki_pages`, `mdl_user`
- **Purpose**: Page versioning

### `mdl_workshop`

Workshop (peer assessment) activity instances.

- **Related to**: `mdl_course`, `mdl_workshop_submissions`
- **Purpose**: Workshop configuration

### `mdl_workshop_aggregations`

Aggregated peer assessment grades.

- **Related to**: `mdl_workshop`, `mdl_user`
- **Purpose**: Grade aggregation

### `mdl_workshop_assessments`

Individual peer assessments.

- **Related to**: `mdl_workshop_submissions`, `mdl_user`
- **Purpose**: Peer reviews

### `mdl_workshop_grades`

Assessment dimension grades.

- **Related to**: `mdl_workshop_assessments`
- **Purpose**: Detailed scoring

### `mdl_workshop_submissions`

Workshop submissions by participants.

- **Related to**: `mdl_workshop`, `mdl_user`
- **Purpose**: Work submissions

### `mdl_workshopallocation_scheduled`

Scheduled peer review allocations.

- **Related to**: `mdl_workshop`
- **Purpose**: Review scheduling

### `mdl_workshopeval_best_settings`

Best assessment evaluation method settings.

- **Related to**: `mdl_workshop`
- **Purpose**: Evaluation config

### `mdl_workshopform_accumulative`

Accumulative grading strategy settings.

- **Related to**: `mdl_workshop`
- **Purpose**: Grading method

### `mdl_workshopform_comments`

Comments grading strategy settings.

- **Related to**: `mdl_workshop`
- **Purpose**: Comment method

### `mdl_workshopform_numerrors`

Number of errors grading strategy.

- **Related to**: `mdl_workshop`
- **Purpose**: Error counting

### `mdl_workshopform_numerrors_map`

Grade mappings for error counts.

- **Related to**: `mdl_workshop`
- **Purpose**: Grade mapping

### `mdl_workshopform_rubric`

Rubric grading strategy settings.

- **Related to**: `mdl_workshop`
- **Purpose**: Rubric method

### `mdl_workshopform_rubric_config`

Rubric configuration for workshops.

- **Related to**: `mdl_workshop`
- **Purpose**: Rubric setup

### `mdl_workshopform_rubric_levels`

Rubric level definitions.

- **Related to**: `mdl_workshopform_rubric`
- **Purpose**: Rubric criteria

## Key Relationships Summary

The Moodle database is built around several core concepts:

1. **Users** (`mdl_user`) - Central to almost all functionality
2. **Courses** (`mdl_course`) - Container for all learning activities  
3. **Context** (`mdl_context`) - Hierarchical permission system
4. **Roles** (`mdl_role`) - Define user capabilities
5. **Activities/Modules** - Various learning activity types (quiz, forum, assignment, etc.)
6. **Enrollment** (`mdl_enrol`, `mdl_user_enrolments`) - Course access control
7. **Grades** (`mdl_grade_*`) - Assessment and gradebook system
8. **Groups** (`mdl_groups`) - Organize users within courses
9. **Files** (`mdl_files`) - Centralized file management

Each activity module typically has:
- A main configuration table (e.g., `mdl_quiz`)
- Related data tables (e.g., `mdl_quiz_attempts`, `mdl_quiz_slots`)
- User interaction tracking tables
- Grade/feedback storage tables

The system uses contexts extensively to determine permissions and scope for various operations, making the `mdl_context` table crucial for understanding data relationships.
