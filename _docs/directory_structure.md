# Directory structure

| Directory     | Purpose | Contents |
|---------------|---------|----------|
| `admin/`      | Handles site administration interfaces and tools. | Administration pages, settings forms, CLI scripts, and third-party libraries used in admin. |
| `analytics/`  | Provides the analytics API for machine learning models and predictions. | Classes for analytics models, targets, indicators, and machine learning backends like PHP-ML or Python. |
| `auth/`       | Manages user authentication methods. | Authentication plugins (e.g., manual, LDAP, OAuth), each in subdirectories like auth/manual/, with plugin code for login processes. |
| `availability/` | Controls conditional availability of activities and resources. | Condition plugins (e.g., availability_date, availability_grade), with classes defining restrictions. |
| `backup/`     | Supports course backup and restore functionality. | Backup controllers, converters, utilities, and Moodle Backup Format (MBZ) handling code. |
| `badges/`     | Integrates Open Badges for awarding achievements. | Badge classes, backends (e.g., Backpack), criteria, and issuance logic. |
| `blocks/`     | Contains sidebar blocks for additional functionality. | Block plugins (e.g., blocks/html, blocks/navigation), each with block classes, settings, and templates. |
| `blog/`       | Manages the blogging system. | Blog entry handling, viewing, and editing code, including RSS integration. |
| `cache/`      | Implements the caching framework to improve performance. | Cache stores, definitions, loaders, and admin interfaces for cache management. |
| `calendar/`   | Handles the calendar and events system. | Event classes, exporters, subscriptions, and display logic. |
| `cohort/`     | Manages site-wide user cohorts (groups). | Cohort API, enrolment sync, and management interfaces. |
| `comment/`    | Provides commenting functionality across Moodle. | Comment API, lib, and JavaScript for inline comments. |
| `competency/` | Supports competency frameworks and learning plans. | Competency API, frameworks, templates, and evidence handling. |
| `completion/` | Tracks activity and course completion. | Completion criteria, progress tracking, and bulk editing tools. |
| `course/`     | Core course management and format handling. | Course formats (e.g., topics, weeks), management pages, and AJAX handlers. |
| `dataformat/` | Defines formats for data export. | Data format plugins (e.g., CSV, JSON), with writers for exporting tables. |
| `enrol/`      | Manages enrolment methods for courses. | Enrolment plugins (e.g., manual, self, cohort), each with enrolment logic and settings. |
| `error/`      | Handles error reporting and display. | Error pages, exception handlers, and debugging tools. |
| `files/`      | Implements the File API for handling files. | File browsers, converters, storage, and MIME type handling. |
| `filter/`     | Applies text filters during content display. | Filter plugins (e.g., multimedia, mathjax), with filtering logic. |
| `grade/`      | Manages grading and gradebook. | Grade items, categories, reports, export/import, and aggregation methods. |
| `group/`      | Handles course groups and groupings. | Group API, management interfaces, and selectors. |
| `install/`    | Contains installation and upgrade scripts. | Installer code, database setup, and language packs for installation. |
| `iplookup/`   | Provides IP address lookup tools. | GeoIP integration and IP mapping functions. |
| `lang/`       | Stores language packs. | Language strings in PHP files for different languages (e.g., lang/en/). |
| `lib/`        | Core libraries and APIs. | Essential classes, components, databases, testing, and third-party libs like TCPDF, GraphQL. |
| `local/`      | For local custom plugins. | Site-specific plugins that don't fit standard types, each in subdirs. |
| `login/`      | Handles login pages and processes. | Login forms, password reset, and signup code. |
| `media/`      | Manages media players. | Media player plugins (e.g., YouTube, Vimeo), with embedding code. |
| `message/`    | Handles messaging and notifications. | Message processors (e.g., email, popup), outputs, and preferences. |
| `mnet/`       | Supports Moodle Networking (MNet) for SSO and sharing. | MNet API, peers, services, and XML-RPC handling. |
| `mod/`        | Contains activity and resource modules. | Module plugins (e.g., mod/forum, mod/quiz), with activity logic, forms, and backups. |
| `my/`         | Manages the "My Moodle" dashboard. | Dashboard pages, course overviews, and customization. |
| `notes/`      | Handles user notes. | Notes API and interfaces for adding notes to users. |
| `pix/`        | Stores core images and icons. | PNG, SVG icons used in Moodle interface. |
| `plagiarism/` | Integrates plagiarism detection tools. | Plagiarism plugins (e.g., Turnitin), with API hooks. |
| `portfolio/`  | Supports portfolio export. | Portfolio plugins (e.g., Mahara, Google Docs), with exporters. |
| `privacy/`    | Handles data privacy and GDPR compliance. | Privacy API, metadata providers, and export/request handling. |
| `question/`   | Manages question bank and types. | Question engine, types (e.g., multiple choice), behaviours, and formats. |
| `rating/`     | Provides rating functionality. | Rating API and AJAX handlers for stars/ratings. |
| `report/`     | Contains report plugins. | Report plugins (e.g., report/log, report/stats), with viewers and builders. |
| `repository/` | Manages file repositories. | Repository plugins (e.g., Dropbox, filesystem), with connectors. |
| `rss/`        | Handles RSS feeds. | RSS client and generation code. |
| `search/`     | Implements global search. | Search engine plugins (e.g., Solr), indexes, and queries. |
| `tag/`        | Manages tags. | Tag API, areas, collections, and cloud display. |
| `theme/`      | Contains themes for site appearance. | Theme plugins (e.g., theme/clean, theme/boost), with layouts, styles, and settings. |
| `user/`       | Handles user profiles and management. | User fields, selectors, profile pages, and pix handling. |
| `userpix/`    | Deprecated; used for user pictures. | Legacy user image storage; mostly handled in files/ now. |
| `webservice/` | Supports web services and APIs. | Web service protocols (e.g., REST, SOAP), tokens, and functions. 
