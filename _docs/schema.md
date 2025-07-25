```sql
CREATE TABLE `mdl_analytics_indicator_calc` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `starttime` bigint NOT NULL,
  `endtime` bigint NOT NULL,
  `contextid` bigint NOT NULL,
  `sampleorigin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `sampleid` bigint NOT NULL,
  `indicator` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `value` decimal(10,2) DEFAULT NULL,
  `timecreated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_analindicalc_staendcon_ix` (`starttime`,`endtime`,`contextid`),
  KEY `mdl_analindicalc_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stored indicator calculations';


CREATE TABLE `mdl_analytics_models` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `trained` tinyint(1) NOT NULL DEFAULT '0',
  `target` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `indicators` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `timesplitting` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `version` bigint NOT NULL,
  `timecreated` bigint DEFAULT NULL,
  `timemodified` bigint NOT NULL,
  `usermodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_analmode_enatra_ix` (`enabled`,`trained`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Analytic models.';


CREATE TABLE `mdl_analytics_models_log` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `modelid` bigint NOT NULL,
  `version` bigint NOT NULL,
  `target` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `indicators` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `timesplitting` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `score` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `dir` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `timecreated` bigint NOT NULL,
  `usermodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_analmodelog_mod_ix` (`modelid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Analytic models changes during evaluation.';


CREATE TABLE `mdl_analytics_predict_samples` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `modelid` bigint NOT NULL,
  `analysableid` bigint NOT NULL,
  `timesplitting` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `rangeindex` bigint NOT NULL,
  `sampleids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_analpredsamp_modanatimr_ix` (`modelid`,`analysableid`,`timesplitting`,`rangeindex`),
  KEY `mdl_analpredsamp_mod_ix` (`modelid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Samples already used for predictions.';


CREATE TABLE `mdl_analytics_prediction_actions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `predictionid` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `actionname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timecreated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_analpredacti_preuseact_ix` (`predictionid`,`userid`,`actionname`),
  KEY `mdl_analpredacti_pre_ix` (`predictionid`),
  KEY `mdl_analpredacti_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Register of user actions over predictions.';


CREATE TABLE `mdl_analytics_predictions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `modelid` bigint NOT NULL,
  `contextid` bigint NOT NULL,
  `sampleid` bigint NOT NULL,
  `rangeindex` mediumint NOT NULL,
  `prediction` decimal(10,2) NOT NULL,
  `predictionscore` decimal(10,5) NOT NULL,
  `calculations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timestart` bigint DEFAULT NULL,
  `timeend` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_analpred_modcon_ix` (`modelid`,`contextid`),
  KEY `mdl_analpred_mod_ix` (`modelid`),
  KEY `mdl_analpred_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Predictions';


CREATE TABLE `mdl_analytics_train_samples` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `modelid` bigint NOT NULL,
  `analysableid` bigint NOT NULL,
  `timesplitting` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `fileid` bigint NOT NULL,
  `sampleids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `timecreated` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_analtraisamp_modanatim_ix` (`modelid`,`analysableid`,`timesplitting`),
  KEY `mdl_analtraisamp_mod_ix` (`modelid`),
  KEY `mdl_analtraisamp_fil_ix` (`fileid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Samples used for training';


CREATE TABLE `mdl_analytics_used_analysables` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `modelid` bigint NOT NULL,
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `analysableid` bigint NOT NULL,
  `timeanalysed` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_analusedanal_modact_ix` (`modelid`,`action`),
  KEY `mdl_analusedanal_mod_ix` (`modelid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='List of analysables used by each model';


CREATE TABLE `mdl_analytics_used_files` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `modelid` bigint NOT NULL DEFAULT '0',
  `fileid` bigint NOT NULL DEFAULT '0',
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `time` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_analusedfile_modactfil_ix` (`modelid`,`action`,`fileid`),
  KEY `mdl_analusedfile_mod_ix` (`modelid`),
  KEY `mdl_analusedfile_fil_ix` (`fileid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Files that have already been used for training and predictio';


CREATE TABLE `mdl_assign` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `introformat` smallint NOT NULL DEFAULT '0',
  `alwaysshowdescription` tinyint NOT NULL DEFAULT '0',
  `nosubmissions` tinyint NOT NULL DEFAULT '0',
  `submissiondrafts` tinyint NOT NULL DEFAULT '0',
  `sendnotifications` tinyint NOT NULL DEFAULT '0',
  `sendlatenotifications` tinyint NOT NULL DEFAULT '0',
  `duedate` bigint NOT NULL DEFAULT '0',
  `allowsubmissionsfromdate` bigint NOT NULL DEFAULT '0',
  `grade` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `requiresubmissionstatement` tinyint NOT NULL DEFAULT '0',
  `completionsubmit` tinyint NOT NULL DEFAULT '0',
  `cutoffdate` bigint NOT NULL DEFAULT '0',
  `gradingduedate` bigint NOT NULL DEFAULT '0',
  `teamsubmission` tinyint NOT NULL DEFAULT '0',
  `requireallteammemberssubmit` tinyint NOT NULL DEFAULT '0',
  `teamsubmissiongroupingid` bigint NOT NULL DEFAULT '0',
  `blindmarking` tinyint NOT NULL DEFAULT '0',
  `revealidentities` tinyint NOT NULL DEFAULT '0',
  `attemptreopenmethod` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'none',
  `maxattempts` mediumint NOT NULL DEFAULT '-1',
  `markingworkflow` tinyint NOT NULL DEFAULT '0',
  `markingallocation` tinyint NOT NULL DEFAULT '0',
  `sendstudentnotifications` tinyint NOT NULL DEFAULT '1',
  `preventsubmissionnotingroup` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assi_cou_ix` (`course`),
  KEY `mdl_assi_tea_ix` (`teamsubmissiongroupingid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table saves information about an instance of mod_assign';


CREATE TABLE `mdl_assign_grades` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `assignment` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `grader` bigint NOT NULL DEFAULT '0',
  `grade` decimal(10,5) DEFAULT '0.00000',
  `attemptnumber` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_assigrad_assuseatt_uix` (`assignment`,`userid`,`attemptnumber`),
  KEY `mdl_assigrad_use_ix` (`userid`),
  KEY `mdl_assigrad_att_ix` (`attemptnumber`),
  KEY `mdl_assigrad_ass_ix` (`assignment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Grading information about a single assignment submission.';


CREATE TABLE `mdl_assign_overrides` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `assignid` bigint NOT NULL DEFAULT '0',
  `groupid` bigint DEFAULT NULL,
  `userid` bigint DEFAULT NULL,
  `sortorder` bigint DEFAULT NULL,
  `allowsubmissionsfromdate` bigint DEFAULT NULL,
  `duedate` bigint DEFAULT NULL,
  `cutoffdate` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_assiover_ass_ix` (`assignid`),
  KEY `mdl_assiover_gro_ix` (`groupid`),
  KEY `mdl_assiover_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='The overrides to assign settings.';


CREATE TABLE `mdl_assign_plugin_config` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `assignment` bigint NOT NULL DEFAULT '0',
  `plugin` varchar(28) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `subtype` varchar(28) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `name` varchar(28) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  KEY `mdl_assiplugconf_plu_ix` (`plugin`),
  KEY `mdl_assiplugconf_sub_ix` (`subtype`),
  KEY `mdl_assiplugconf_nam_ix` (`name`),
  KEY `mdl_assiplugconf_ass_ix` (`assignment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Config data for an instance of a plugin in an assignment.';


CREATE TABLE `mdl_assign_submission` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `assignment` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `status` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `groupid` bigint NOT NULL DEFAULT '0',
  `attemptnumber` bigint NOT NULL DEFAULT '0',
  `latest` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_assisubm_assusegroatt_uix` (`assignment`,`userid`,`groupid`,`attemptnumber`),
  KEY `mdl_assisubm_use_ix` (`userid`),
  KEY `mdl_assisubm_att_ix` (`attemptnumber`),
  KEY `mdl_assisubm_assusegrolat_ix` (`assignment`,`userid`,`groupid`,`latest`),
  KEY `mdl_assisubm_ass_ix` (`assignment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table keeps information about student interactions with';


CREATE TABLE `mdl_assign_user_flags` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `assignment` bigint NOT NULL DEFAULT '0',
  `locked` bigint NOT NULL DEFAULT '0',
  `mailed` smallint NOT NULL DEFAULT '0',
  `extensionduedate` bigint NOT NULL DEFAULT '0',
  `workflowstate` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `allocatedmarker` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assiuserflag_mai_ix` (`mailed`),
  KEY `mdl_assiuserflag_use_ix` (`userid`),
  KEY `mdl_assiuserflag_ass_ix` (`assignment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='List of flags that can be set for a single user in a single ';


CREATE TABLE `mdl_assign_user_mapping` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `assignment` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assiusermapp_ass_ix` (`assignment`),
  KEY `mdl_assiusermapp_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Map an assignment specific id number to a user';


CREATE TABLE `mdl_assignfeedback_comments` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `assignment` bigint NOT NULL DEFAULT '0',
  `grade` bigint NOT NULL DEFAULT '0',
  `commenttext` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `commentformat` smallint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assicomm_ass_ix` (`assignment`),
  KEY `mdl_assicomm_gra_ix` (`grade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Text feedback for submitted assignments';


CREATE TABLE `mdl_assignfeedback_editpdf_annot` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `gradeid` bigint NOT NULL DEFAULT '0',
  `pageno` bigint NOT NULL DEFAULT '0',
  `x` bigint DEFAULT '0',
  `y` bigint DEFAULT '0',
  `endx` bigint DEFAULT '0',
  `endy` bigint DEFAULT '0',
  `path` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'line',
  `colour` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'black',
  `draft` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `mdl_assieditanno_grapag_ix` (`gradeid`,`pageno`),
  KEY `mdl_assieditanno_gra_ix` (`gradeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='stores annotations added to pdfs submitted by students';


CREATE TABLE `mdl_assignfeedback_editpdf_cmnt` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `gradeid` bigint NOT NULL DEFAULT '0',
  `x` bigint DEFAULT '0',
  `y` bigint DEFAULT '0',
  `width` bigint DEFAULT '120',
  `rawtext` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `pageno` bigint NOT NULL DEFAULT '0',
  `colour` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'black',
  `draft` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `mdl_assieditcmnt_grapag_ix` (`gradeid`,`pageno`),
  KEY `mdl_assieditcmnt_gra_ix` (`gradeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores comments added to pdfs';


CREATE TABLE `mdl_assignfeedback_editpdf_queue` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `submissionid` bigint NOT NULL,
  `submissionattempt` bigint NOT NULL,
  `attemptedconversions` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_assieditqueu_subsub_uix` (`submissionid`,`submissionattempt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Queue for processing.';


CREATE TABLE `mdl_assignfeedback_editpdf_quick` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `rawtext` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `width` bigint NOT NULL DEFAULT '120',
  `colour` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'yellow',
  PRIMARY KEY (`id`),
  KEY `mdl_assieditquic_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores teacher specified quicklist comments';


CREATE TABLE `mdl_assignfeedback_file` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `assignment` bigint NOT NULL DEFAULT '0',
  `grade` bigint NOT NULL DEFAULT '0',
  `numfiles` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assifile_ass2_ix` (`assignment`),
  KEY `mdl_assifile_gra_ix` (`grade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores info about the number of files submitted by a student';


CREATE TABLE `mdl_assignment` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `introformat` smallint NOT NULL DEFAULT '0',
  `assignmenttype` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `resubmit` tinyint NOT NULL DEFAULT '0',
  `preventlate` tinyint NOT NULL DEFAULT '0',
  `emailteachers` tinyint NOT NULL DEFAULT '0',
  `var1` bigint DEFAULT '0',
  `var2` bigint DEFAULT '0',
  `var3` bigint DEFAULT '0',
  `var4` bigint DEFAULT '0',
  `var5` bigint DEFAULT '0',
  `maxbytes` bigint NOT NULL DEFAULT '100000',
  `timedue` bigint NOT NULL DEFAULT '0',
  `timeavailable` bigint NOT NULL DEFAULT '0',
  `grade` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assi_cou2_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines assignments';


CREATE TABLE `mdl_assignment_submissions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `assignment` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `numfiles` bigint NOT NULL DEFAULT '0',
  `data1` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `data2` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `grade` bigint NOT NULL DEFAULT '0',
  `submissioncomment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `format` smallint NOT NULL DEFAULT '0',
  `teacher` bigint NOT NULL DEFAULT '0',
  `timemarked` bigint NOT NULL DEFAULT '0',
  `mailed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assisubm_use2_ix` (`userid`),
  KEY `mdl_assisubm_mai_ix` (`mailed`),
  KEY `mdl_assisubm_tim_ix` (`timemarked`),
  KEY `mdl_assisubm_ass2_ix` (`assignment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Info about submitted assignments';


CREATE TABLE `mdl_assignment_upgrade` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `oldcmid` bigint NOT NULL DEFAULT '0',
  `oldinstance` bigint NOT NULL DEFAULT '0',
  `newcmid` bigint NOT NULL DEFAULT '0',
  `newinstance` bigint NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assiupgr_old_ix` (`oldcmid`),
  KEY `mdl_assiupgr_old2_ix` (`oldinstance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Info about upgraded assignments';


CREATE TABLE `mdl_assignsubmission_file` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `assignment` bigint NOT NULL DEFAULT '0',
  `submission` bigint NOT NULL DEFAULT '0',
  `numfiles` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assifile_ass_ix` (`assignment`),
  KEY `mdl_assifile_sub_ix` (`submission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Info about file submissions for assignments';


CREATE TABLE `mdl_assignsubmission_onlinetext` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `assignment` bigint NOT NULL DEFAULT '0',
  `submission` bigint NOT NULL DEFAULT '0',
  `onlinetext` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `onlineformat` smallint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assionli_ass_ix` (`assignment`),
  KEY `mdl_assionli_sub_ix` (`submission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Info about onlinetext submission';


CREATE TABLE `mdl_auth_oauth2_linked_login` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `usermodified` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `issuerid` bigint NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `email` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `confirmtoken` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `confirmtokenexpires` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_authoautlinklogi_useis_uix` (`userid`,`issuerid`,`username`),
  KEY `mdl_authoautlinklogi_issuse_ix` (`issuerid`,`username`),
  KEY `mdl_authoautlinklogi_use_ix` (`usermodified`),
  KEY `mdl_authoautlinklogi_use2_ix` (`userid`),
  KEY `mdl_authoautlinklogi_iss_ix` (`issuerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Accounts linked to a users Moodle account.';


CREATE TABLE `mdl_backup_controllers` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `backupid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `operation` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'backup',
  `type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `itemid` bigint NOT NULL,
  `format` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `interactive` smallint NOT NULL,
  `purpose` smallint NOT NULL,
  `userid` bigint NOT NULL,
  `status` smallint NOT NULL,
  `execution` smallint NOT NULL,
  `executiontime` bigint NOT NULL,
  `checksum` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `controller` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_backcont_bac_uix` (`backupid`),
  KEY `mdl_backcont_typite_ix` (`type`,`itemid`),
  KEY `mdl_backcont_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='To store the backup_controllers as they are used';


CREATE TABLE `mdl_backup_courses` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL DEFAULT '0',
  `laststarttime` bigint NOT NULL DEFAULT '0',
  `lastendtime` bigint NOT NULL DEFAULT '0',
  `laststatus` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '5',
  `nextstarttime` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_backcour_cou_uix` (`courseid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='To store every course backup status';


CREATE TABLE `mdl_backup_logs` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `backupid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `loglevel` smallint NOT NULL,
  `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `timecreated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_backlogs_bacid_uix` (`backupid`,`id`),
  KEY `mdl_backlogs_bac_ix` (`backupid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='To store all the logs from backup and restore operations (by';


CREATE TABLE `mdl_badge` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `usercreated` bigint NOT NULL,
  `usermodified` bigint NOT NULL,
  `issuername` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `issuerurl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `issuercontact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `expiredate` bigint DEFAULT NULL,
  `expireperiod` bigint DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `courseid` bigint DEFAULT NULL,
  `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `messagesubject` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `attachment` tinyint(1) NOT NULL DEFAULT '1',
  `notification` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `nextcron` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_badg_typ_ix` (`type`),
  KEY `mdl_badg_cou_ix` (`courseid`),
  KEY `mdl_badg_use_ix` (`usermodified`),
  KEY `mdl_badg_use2_ix` (`usercreated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines badge';


CREATE TABLE `mdl_badge_backpack` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `backpackurl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `backpackuid` bigint NOT NULL,
  `autosync` tinyint(1) NOT NULL DEFAULT '0',
  `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_badgback_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines settings for connecting external backpack';


CREATE TABLE `mdl_badge_criteria` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `badgeid` bigint NOT NULL DEFAULT '0',
  `criteriatype` bigint DEFAULT NULL,
  `method` tinyint(1) NOT NULL DEFAULT '1',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_badgcrit_badcri_uix` (`badgeid`,`criteriatype`),
  KEY `mdl_badgcrit_cri_ix` (`criteriatype`),
  KEY `mdl_badgcrit_bad_ix` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines criteria for issuing badges';


CREATE TABLE `mdl_badge_criteria_met` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `issuedid` bigint DEFAULT NULL,
  `critid` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `datemet` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_badgcritmet_cri_ix` (`critid`),
  KEY `mdl_badgcritmet_use_ix` (`userid`),
  KEY `mdl_badgcritmet_iss_ix` (`issuedid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines criteria that were met for an issued badge';


CREATE TABLE `mdl_badge_criteria_param` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `critid` bigint NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_badgcritpara_cri_ix` (`critid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines parameters for badges criteria';


CREATE TABLE `mdl_badge_external` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `backpackid` bigint NOT NULL,
  `collectionid` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_badgexte_bac_ix` (`backpackid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Setting for external badges display';


CREATE TABLE `mdl_badge_issued` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `badgeid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `uniquehash` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `dateissued` bigint NOT NULL DEFAULT '0',
  `dateexpire` bigint DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `issuernotified` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_badgissu_baduse_uix` (`badgeid`,`userid`),
  KEY `mdl_badgissu_bad_ix` (`badgeid`),
  KEY `mdl_badgissu_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines issued badges';


CREATE TABLE `mdl_badge_manual_award` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `badgeid` bigint NOT NULL,
  `recipientid` bigint NOT NULL,
  `issuerid` bigint NOT NULL,
  `issuerrole` bigint NOT NULL,
  `datemet` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_badgmanuawar_bad_ix` (`badgeid`),
  KEY `mdl_badgmanuawar_rec_ix` (`recipientid`),
  KEY `mdl_badgmanuawar_iss_ix` (`issuerid`),
  KEY `mdl_badgmanuawar_iss2_ix` (`issuerrole`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Track manual award criteria for badges';


CREATE TABLE `mdl_block` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `cron` bigint NOT NULL DEFAULT '0',
  `lastcron` bigint NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_bloc_nam_uix` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='contains all installed blocks';


CREATE TABLE `mdl_block_community` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL,
  `coursename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `coursedescription` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `courseurl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `imageurl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Community block';


CREATE TABLE `mdl_block_instances` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `blockname` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `parentcontextid` bigint NOT NULL,
  `showinsubcontexts` smallint NOT NULL,
  `requiredbytheme` smallint NOT NULL DEFAULT '0',
  `pagetypepattern` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `subpagepattern` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `defaultregion` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `defaultweight` bigint NOT NULL,
  `configdata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_blocinst_parshopagsub_ix` (`parentcontextid`,`showinsubcontexts`,`pagetypepattern`,`subpagepattern`),
  KEY `mdl_blocinst_tim_ix` (`timemodified`),
  KEY `mdl_blocinst_par_ix` (`parentcontextid`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table stores block instances. The type of block this is';


CREATE TABLE `mdl_block_positions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `blockinstanceid` bigint NOT NULL,
  `contextid` bigint NOT NULL,
  `pagetype` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `subpage` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `visible` smallint NOT NULL,
  `region` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `weight` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_blocposi_bloconpagsub_uix` (`blockinstanceid`,`contextid`,`pagetype`,`subpage`),
  KEY `mdl_blocposi_blo_ix` (`blockinstanceid`),
  KEY `mdl_blocposi_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores the position of a sticky block_instance on a another ';


CREATE TABLE `mdl_block_recent_activity` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL,
  `cmid` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `action` tinyint(1) NOT NULL,
  `modname` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_blocreceacti_coutim_ix` (`courseid`,`timecreated`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Recent activity block';


CREATE TABLE `mdl_block_rss_client` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `preferredtitle` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `shared` tinyint NOT NULL DEFAULT '0',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `skiptime` bigint NOT NULL DEFAULT '0',
  `skipuntil` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Remote news feed information. Contains the news feed id, the';


CREATE TABLE `mdl_blog_association` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `contextid` bigint NOT NULL,
  `blogid` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_blogasso_con_ix` (`contextid`),
  KEY `mdl_blogasso_blo_ix` (`blogid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Associations of blog entries with courses and module instanc';


CREATE TABLE `mdl_blog_external` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `url` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `filtertags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `failedlastsync` tinyint(1) NOT NULL DEFAULT '0',
  `timemodified` bigint DEFAULT NULL,
  `timefetched` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_blogexte_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='External blog links used for RSS copying of blog entries to ';


CREATE TABLE `mdl_book` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `introformat` smallint NOT NULL DEFAULT '0',
  `numbering` smallint NOT NULL DEFAULT '0',
  `navstyle` smallint NOT NULL DEFAULT '1',
  `customtitles` tinyint NOT NULL DEFAULT '0',
  `revision` bigint NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines book';


CREATE TABLE `mdl_book_chapters` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `bookid` bigint NOT NULL DEFAULT '0',
  `pagenum` bigint NOT NULL DEFAULT '0',
  `subchapter` bigint NOT NULL DEFAULT '0',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `contentformat` smallint NOT NULL DEFAULT '0',
  `hidden` tinyint NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `importsrc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines book_chapters';


CREATE TABLE `mdl_cache_filters` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `filter` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `version` bigint NOT NULL DEFAULT '0',
  `md5key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `rawtext` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_cachfilt_filmd5_ix` (`filter`,`md5key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='For keeping information about cached data';


CREATE TABLE `mdl_cache_flags` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `flagtype` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `expiry` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_cachflag_fla_ix` (`flagtype`),
  KEY `mdl_cachflag_nam_ix` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Cache of time-sensitive flags';


CREATE TABLE `mdl_capabilities` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `captype` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `contextlevel` bigint NOT NULL DEFAULT '0',
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `riskbitmask` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_capa_nam_uix` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=610 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='this defines all capabilities';


CREATE TABLE `mdl_chat` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `introformat` smallint NOT NULL DEFAULT '0',
  `keepdays` bigint NOT NULL DEFAULT '0',
  `studentlogs` smallint NOT NULL DEFAULT '0',
  `chattime` bigint NOT NULL DEFAULT '0',
  `schedule` smallint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_chat_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Each of these is a chat room';


CREATE TABLE `mdl_chat_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `chatid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `groupid` bigint NOT NULL DEFAULT '0',
  `issystem` tinyint(1) NOT NULL DEFAULT '0',
  `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `timestamp` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_chatmess_use_ix` (`userid`),
  KEY `mdl_chatmess_gro_ix` (`groupid`),
  KEY `mdl_chatmess_timcha_ix` (`timestamp`,`chatid`),
  KEY `mdl_chatmess_cha_ix` (`chatid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores all the actual chat messages';


CREATE TABLE `mdl_chat_messages_current` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `chatid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `groupid` bigint NOT NULL DEFAULT '0',
  `issystem` tinyint(1) NOT NULL DEFAULT '0',
  `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `timestamp` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_chatmesscurr_use_ix` (`userid`),
  KEY `mdl_chatmesscurr_gro_ix` (`groupid`),
  KEY `mdl_chatmesscurr_timcha_ix` (`timestamp`,`chatid`),
  KEY `mdl_chatmesscurr_cha_ix` (`chatid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores current session';


CREATE TABLE `mdl_chat_users` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `chatid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `groupid` bigint NOT NULL DEFAULT '0',
  `version` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `firstping` bigint NOT NULL DEFAULT '0',
  `lastping` bigint NOT NULL DEFAULT '0',
  `lastmessageping` bigint NOT NULL DEFAULT '0',
  `sid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `course` bigint NOT NULL DEFAULT '0',
  `lang` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_chatuser_use_ix` (`userid`),
  KEY `mdl_chatuser_las_ix` (`lastping`),
  KEY `mdl_chatuser_gro_ix` (`groupid`),
  KEY `mdl_chatuser_cha_ix` (`chatid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Keeps track of which users are in which chat rooms';


CREATE TABLE `mdl_choice` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `introformat` smallint NOT NULL DEFAULT '0',
  `publish` tinyint NOT NULL DEFAULT '0',
  `showresults` tinyint NOT NULL DEFAULT '0',
  `display` smallint NOT NULL DEFAULT '0',
  `allowupdate` tinyint NOT NULL DEFAULT '0',
  `allowmultiple` tinyint NOT NULL DEFAULT '0',
  `showunanswered` tinyint NOT NULL DEFAULT '0',
  `includeinactive` tinyint NOT NULL DEFAULT '1',
  `limitanswers` tinyint NOT NULL DEFAULT '0',
  `timeopen` bigint NOT NULL DEFAULT '0',
  `timeclose` bigint NOT NULL DEFAULT '0',
  `showpreview` tinyint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `completionsubmit` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_choi_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Available choices are stored here';


CREATE TABLE `mdl_choice_answers` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `choiceid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `optionid` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_choiansw_use_ix` (`userid`),
  KEY `mdl_choiansw_cho_ix` (`choiceid`),
  KEY `mdl_choiansw_opt_ix` (`optionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='choices performed by users';


CREATE TABLE `mdl_choice_options` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `choiceid` bigint NOT NULL DEFAULT '0',
  `text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `maxanswers` bigint DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_choiopti_cho_ix` (`choiceid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='available options to choice';


CREATE TABLE `mdl_codesandbox` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `introformat` smallint NOT NULL DEFAULT '0',
  `starter_code` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `test_suite_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `is_gradable` tinyint(1) NOT NULL DEFAULT '0',
  `grade_max` decimal(10,5) NOT NULL DEFAULT '100.00000',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_code_cou_ix` (`course`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Code sandbox instances';


CREATE TABLE `mdl_codesandbox_submissions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `codesandboxid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `code` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `score` decimal(5,4) DEFAULT NULL,
  `feedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timesubmitted` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_codesubm_coduse_ix` (`codesandboxid`,`userid`),
  KEY `mdl_codesubm_cod_ix` (`codesandboxid`),
  KEY `mdl_codesubm_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Student code submissions';


CREATE TABLE `mdl_cohort` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `contextid` bigint NOT NULL,
  `name` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `idnumber` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` tinyint NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `theme` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_coho_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Each record represents one cohort (aka site-wide group).';


CREATE TABLE `mdl_cohort_members` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `cohortid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `timeadded` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_cohomemb_cohuse_uix` (`cohortid`,`userid`),
  KEY `mdl_cohomemb_coh_ix` (`cohortid`),
  KEY `mdl_cohomemb_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Link a user to a cohort.';


CREATE TABLE `mdl_comments` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `contextid` bigint NOT NULL,
  `component` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `commentarea` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `itemid` bigint NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `format` tinyint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_comm_concomite_ix` (`contextid`,`commentarea`,`itemid`),
  KEY `mdl_comm_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='moodle comments module';


CREATE TABLE `mdl_competency` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `shortname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` smallint NOT NULL DEFAULT '0',
  `idnumber` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `competencyframeworkid` bigint NOT NULL,
  `parentid` bigint NOT NULL DEFAULT '0',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `sortorder` bigint NOT NULL,
  `ruletype` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ruleoutcome` tinyint NOT NULL DEFAULT '0',
  `ruleconfig` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `scaleid` bigint DEFAULT NULL,
  `scaleconfiguration` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `usermodified` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_comp_comidn_uix` (`competencyframeworkid`,`idnumber`),
  KEY `mdl_comp_rul_ix` (`ruleoutcome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table contains the master record of each competency in ';


CREATE TABLE `mdl_competency_coursecomp` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL,
  `competencyid` bigint NOT NULL,
  `ruleoutcome` tinyint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `usermodified` bigint NOT NULL,
  `sortorder` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_compcour_coucom_uix` (`courseid`,`competencyid`),
  KEY `mdl_compcour_courul_ix` (`courseid`,`ruleoutcome`),
  KEY `mdl_compcour_cou2_ix` (`courseid`),
  KEY `mdl_compcour_com_ix` (`competencyid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Link a competency to a course.';


CREATE TABLE `mdl_competency_coursecompsetting` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL,
  `pushratingstouserplans` tinyint DEFAULT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `usermodified` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_compcour_cou_uix` (`courseid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table contains the course specific settings for compete';


CREATE TABLE `mdl_competency_evidence` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `usercompetencyid` bigint NOT NULL,
  `contextid` bigint NOT NULL,
  `action` tinyint NOT NULL,
  `actionuserid` bigint DEFAULT NULL,
  `descidentifier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `desccomponent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `desca` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `grade` bigint DEFAULT NULL,
  `note` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `usermodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_compevid_use_ix` (`usercompetencyid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='The evidence linked to a user competency';


CREATE TABLE `mdl_competency_framework` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `shortname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `contextid` bigint NOT NULL,
  `idnumber` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` smallint NOT NULL DEFAULT '0',
  `scaleid` bigint DEFAULT NULL,
  `scaleconfiguration` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `visible` tinyint NOT NULL DEFAULT '1',
  `taxonomies` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `usermodified` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_compfram_idn_uix` (`idnumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='List of competency frameworks.';


CREATE TABLE `mdl_competency_modulecomp` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `cmid` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `usermodified` bigint NOT NULL,
  `sortorder` bigint NOT NULL,
  `competencyid` bigint NOT NULL,
  `ruleoutcome` tinyint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_compmodu_cmicom_uix` (`cmid`,`competencyid`),
  KEY `mdl_compmodu_cmirul_ix` (`cmid`,`ruleoutcome`),
  KEY `mdl_compmodu_cmi_ix` (`cmid`),
  KEY `mdl_compmodu_com_ix` (`competencyid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Link a competency to a module.';


CREATE TABLE `mdl_competency_plan` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` smallint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL,
  `templateid` bigint DEFAULT NULL,
  `origtemplateid` bigint DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `duedate` bigint DEFAULT '0',
  `reviewerid` bigint DEFAULT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL DEFAULT '0',
  `usermodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_compplan_usesta_ix` (`userid`,`status`),
  KEY `mdl_compplan_tem_ix` (`templateid`),
  KEY `mdl_compplan_stadue_ix` (`status`,`duedate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Learning plans';


CREATE TABLE `mdl_competency_plancomp` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `planid` bigint NOT NULL,
  `competencyid` bigint NOT NULL,
  `sortorder` bigint DEFAULT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint DEFAULT NULL,
  `usermodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_compplan_placom_uix` (`planid`,`competencyid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Plan competencies';


CREATE TABLE `mdl_competency_relatedcomp` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `competencyid` bigint NOT NULL,
  `relatedcompetencyid` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint DEFAULT NULL,
  `usermodified` bigint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Related competencies';


CREATE TABLE `mdl_competency_template` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `shortname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `contextid` bigint NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` smallint NOT NULL DEFAULT '0',
  `visible` tinyint NOT NULL DEFAULT '1',
  `duedate` bigint DEFAULT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `usermodified` bigint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Learning plan templates.';


CREATE TABLE `mdl_competency_templatecohort` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `templateid` bigint NOT NULL,
  `cohortid` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `usermodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_comptemp_temcoh_uix` (`templateid`,`cohortid`),
  KEY `mdl_comptemp_tem2_ix` (`templateid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Default comment for the table, please edit me';


CREATE TABLE `mdl_competency_templatecomp` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `templateid` bigint NOT NULL,
  `competencyid` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `usermodified` bigint NOT NULL,
  `sortorder` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_comptemp_tem_ix` (`templateid`),
  KEY `mdl_comptemp_com_ix` (`competencyid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Link a competency to a learning plan template.';


CREATE TABLE `mdl_competency_usercomp` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL,
  `competencyid` bigint NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `reviewerid` bigint DEFAULT NULL,
  `proficiency` tinyint DEFAULT NULL,
  `grade` bigint DEFAULT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint DEFAULT NULL,
  `usermodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_compuser_usecom_uix` (`userid`,`competencyid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='User competencies';


CREATE TABLE `mdl_competency_usercompcourse` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL,
  `courseid` bigint NOT NULL,
  `competencyid` bigint NOT NULL,
  `proficiency` tinyint DEFAULT NULL,
  `grade` bigint DEFAULT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint DEFAULT NULL,
  `usermodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_compuser_usecoucom_uix` (`userid`,`courseid`,`competencyid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='User competencies in a course';


CREATE TABLE `mdl_competency_usercompplan` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL,
  `competencyid` bigint NOT NULL,
  `planid` bigint NOT NULL,
  `proficiency` tinyint DEFAULT NULL,
  `grade` bigint DEFAULT NULL,
  `sortorder` bigint DEFAULT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint DEFAULT NULL,
  `usermodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_compuser_usecompla_uix` (`userid`,`competencyid`,`planid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='User competencies plans';


CREATE TABLE `mdl_competency_userevidence` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `descriptionformat` tinyint(1) NOT NULL,
  `url` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `usermodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_compuser_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='The evidence of prior learning';


CREATE TABLE `mdl_competency_userevidencecomp` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userevidenceid` bigint NOT NULL,
  `competencyid` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `usermodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_compuser_usecom2_uix` (`userevidenceid`,`competencyid`),
  KEY `mdl_compuser_use2_ix` (`userevidenceid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Relationship between user evidence and competencies';


CREATE TABLE `mdl_config` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_conf_nam_uix` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=471 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Moodle configuration variables';


CREATE TABLE `mdl_config_log` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `plugin` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `oldvalue` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  KEY `mdl_conflog_tim_ix` (`timemodified`),
  KEY `mdl_conflog_use_ix` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=1590 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Changes done in server configuration through admin UI';


CREATE TABLE `mdl_config_plugins` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `plugin` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'core',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_confplug_plunam_uix` (`plugin`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1868 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Moodle modules and plugins configuration variables';


CREATE TABLE `mdl_context` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `contextlevel` bigint NOT NULL DEFAULT '0',
  `instanceid` bigint NOT NULL DEFAULT '0',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `depth` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_cont_conins_uix` (`contextlevel`,`instanceid`),
  KEY `mdl_cont_ins_ix` (`instanceid`),
  KEY `mdl_cont_pat_ix` (`path`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='one of these must be set';


CREATE TABLE `mdl_context_temp` (
  `id` bigint NOT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `depth` tinyint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Used by build_context_path() in upgrade and cron to keep con';


CREATE TABLE `mdl_course` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `category` bigint NOT NULL DEFAULT '0',
  `sortorder` bigint NOT NULL DEFAULT '0',
  `fullname` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `shortname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `idnumber` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `summaryformat` tinyint NOT NULL DEFAULT '0',
  `format` varchar(21) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'topics',
  `showgrades` tinyint NOT NULL DEFAULT '1',
  `newsitems` mediumint NOT NULL DEFAULT '1',
  `startdate` bigint NOT NULL DEFAULT '0',
  `enddate` bigint NOT NULL DEFAULT '0',
  `marker` bigint NOT NULL DEFAULT '0',
  `maxbytes` bigint NOT NULL DEFAULT '0',
  `legacyfiles` smallint NOT NULL DEFAULT '0',
  `showreports` smallint NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `visibleold` tinyint(1) NOT NULL DEFAULT '1',
  `groupmode` smallint NOT NULL DEFAULT '0',
  `groupmodeforce` smallint NOT NULL DEFAULT '0',
  `defaultgroupingid` bigint NOT NULL DEFAULT '0',
  `lang` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `calendartype` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `theme` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `requested` tinyint(1) NOT NULL DEFAULT '0',
  `enablecompletion` tinyint(1) NOT NULL DEFAULT '0',
  `completionnotify` tinyint(1) NOT NULL DEFAULT '0',
  `cacherev` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_cour_cat_ix` (`category`),
  KEY `mdl_cour_idn_ix` (`idnumber`),
  KEY `mdl_cour_sho_ix` (`shortname`),
  KEY `mdl_cour_sor_ix` (`sortorder`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Central course table';


CREATE TABLE `mdl_course_categories` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `idnumber` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` tinyint NOT NULL DEFAULT '0',
  `parent` bigint NOT NULL DEFAULT '0',
  `sortorder` bigint NOT NULL DEFAULT '0',
  `coursecount` bigint NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `visibleold` tinyint(1) NOT NULL DEFAULT '1',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `depth` bigint NOT NULL DEFAULT '0',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `theme` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_courcate_par_ix` (`parent`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Course categories';


CREATE TABLE `mdl_course_completion_aggr_methd` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `criteriatype` bigint DEFAULT NULL,
  `method` tinyint(1) NOT NULL DEFAULT '0',
  `value` decimal(10,5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_courcompaggrmeth_coucr_uix` (`course`,`criteriatype`),
  KEY `mdl_courcompaggrmeth_cou_ix` (`course`),
  KEY `mdl_courcompaggrmeth_cri_ix` (`criteriatype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Course completion aggregation methods for criteria';


CREATE TABLE `mdl_course_completion_crit_compl` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `course` bigint NOT NULL DEFAULT '0',
  `criteriaid` bigint NOT NULL DEFAULT '0',
  `gradefinal` decimal(10,5) DEFAULT NULL,
  `unenroled` bigint DEFAULT NULL,
  `timecompleted` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_courcompcritcomp_useco_uix` (`userid`,`course`,`criteriaid`),
  KEY `mdl_courcompcritcomp_use_ix` (`userid`),
  KEY `mdl_courcompcritcomp_cou_ix` (`course`),
  KEY `mdl_courcompcritcomp_cri_ix` (`criteriaid`),
  KEY `mdl_courcompcritcomp_tim_ix` (`timecompleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Course completion user records';


CREATE TABLE `mdl_course_completion_criteria` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `criteriatype` bigint NOT NULL DEFAULT '0',
  `module` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `moduleinstance` bigint DEFAULT NULL,
  `courseinstance` bigint DEFAULT NULL,
  `enrolperiod` bigint DEFAULT NULL,
  `timeend` bigint DEFAULT NULL,
  `gradepass` decimal(10,5) DEFAULT NULL,
  `role` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_courcompcrit_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Course completion criteria';


CREATE TABLE `mdl_course_completion_defaults` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL,
  `module` bigint NOT NULL,
  `completion` tinyint(1) NOT NULL DEFAULT '0',
  `completionview` tinyint(1) NOT NULL DEFAULT '0',
  `completionusegrade` tinyint(1) NOT NULL DEFAULT '0',
  `completionexpected` bigint NOT NULL DEFAULT '0',
  `customrules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_courcompdefa_coumod_uix` (`course`,`module`),
  KEY `mdl_courcompdefa_mod_ix` (`module`),
  KEY `mdl_courcompdefa_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Default settings for activities completion';


CREATE TABLE `mdl_course_completions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `course` bigint NOT NULL DEFAULT '0',
  `timeenrolled` bigint NOT NULL DEFAULT '0',
  `timestarted` bigint NOT NULL DEFAULT '0',
  `timecompleted` bigint DEFAULT NULL,
  `reaggregate` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_courcomp_usecou_uix` (`userid`,`course`),
  KEY `mdl_courcomp_use_ix` (`userid`),
  KEY `mdl_courcomp_cou_ix` (`course`),
  KEY `mdl_courcomp_tim_ix` (`timecompleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Course completion records';


CREATE TABLE `mdl_course_format_options` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL,
  `format` varchar(21) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `sectionid` bigint NOT NULL DEFAULT '0',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_courformopti_couforsec_uix` (`courseid`,`format`,`sectionid`,`name`),
  KEY `mdl_courformopti_cou_ix` (`courseid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores format-specific options for the course or course sect';


CREATE TABLE `mdl_course_modules` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `module` bigint NOT NULL DEFAULT '0',
  `instance` bigint NOT NULL DEFAULT '0',
  `section` bigint NOT NULL DEFAULT '0',
  `idnumber` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `added` bigint NOT NULL DEFAULT '0',
  `score` smallint NOT NULL DEFAULT '0',
  `indent` mediumint NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `visibleoncoursepage` tinyint(1) NOT NULL DEFAULT '1',
  `visibleold` tinyint(1) NOT NULL DEFAULT '1',
  `groupmode` smallint NOT NULL DEFAULT '0',
  `groupingid` bigint NOT NULL DEFAULT '0',
  `completion` tinyint(1) NOT NULL DEFAULT '0',
  `completiongradeitemnumber` bigint DEFAULT NULL,
  `completionview` tinyint(1) NOT NULL DEFAULT '0',
  `completionexpected` bigint NOT NULL DEFAULT '0',
  `showdescription` tinyint(1) NOT NULL DEFAULT '0',
  `availability` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `deletioninprogress` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_courmodu_vis_ix` (`visible`),
  KEY `mdl_courmodu_cou_ix` (`course`),
  KEY `mdl_courmodu_mod_ix` (`module`),
  KEY `mdl_courmodu_ins_ix` (`instance`),
  KEY `mdl_courmodu_idncou_ix` (`idnumber`,`course`),
  KEY `mdl_courmodu_gro_ix` (`groupingid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='course_modules table retrofitted from MySQL';


CREATE TABLE `mdl_course_modules_completion` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `coursemoduleid` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `completionstate` tinyint(1) NOT NULL,
  `viewed` tinyint(1) DEFAULT NULL,
  `overrideby` bigint DEFAULT NULL,
  `timemodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_courmoducomp_usecou_uix` (`userid`,`coursemoduleid`),
  KEY `mdl_courmoducomp_cou_ix` (`coursemoduleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores the completion state (completed or not completed, etc';


CREATE TABLE `mdl_course_published` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `huburl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `courseid` bigint NOT NULL,
  `timepublished` bigint NOT NULL,
  `enrollable` tinyint(1) NOT NULL DEFAULT '1',
  `hubcourseid` bigint NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `timechecked` bigint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Information about how and when an local courses were publish';


CREATE TABLE `mdl_course_request` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `fullname` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `shortname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `summaryformat` tinyint NOT NULL DEFAULT '0',
  `category` bigint NOT NULL DEFAULT '0',
  `reason` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `requester` bigint NOT NULL DEFAULT '0',
  `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_courrequ_sho_ix` (`shortname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='course requests';


CREATE TABLE `mdl_course_sections` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `section` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `summaryformat` tinyint NOT NULL DEFAULT '0',
  `sequence` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `availability` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_coursect_cousec_uix` (`course`,`section`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='to define the sections for each course';


CREATE TABLE `mdl_data` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `introformat` smallint NOT NULL DEFAULT '0',
  `comments` smallint NOT NULL DEFAULT '0',
  `timeavailablefrom` bigint NOT NULL DEFAULT '0',
  `timeavailableto` bigint NOT NULL DEFAULT '0',
  `timeviewfrom` bigint NOT NULL DEFAULT '0',
  `timeviewto` bigint NOT NULL DEFAULT '0',
  `requiredentries` int NOT NULL DEFAULT '0',
  `requiredentriestoview` int NOT NULL DEFAULT '0',
  `maxentries` int NOT NULL DEFAULT '0',
  `rssarticles` smallint NOT NULL DEFAULT '0',
  `singletemplate` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `listtemplate` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `listtemplateheader` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `listtemplatefooter` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `addtemplate` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `rsstemplate` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `rsstitletemplate` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `csstemplate` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `jstemplate` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `asearchtemplate` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `approval` smallint NOT NULL DEFAULT '0',
  `manageapproved` smallint NOT NULL DEFAULT '1',
  `scale` bigint NOT NULL DEFAULT '0',
  `assessed` bigint NOT NULL DEFAULT '0',
  `assesstimestart` bigint NOT NULL DEFAULT '0',
  `assesstimefinish` bigint NOT NULL DEFAULT '0',
  `defaultsort` bigint NOT NULL DEFAULT '0',
  `defaultsortdir` smallint NOT NULL DEFAULT '0',
  `editany` smallint NOT NULL DEFAULT '0',
  `notification` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `completionentries` bigint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_data_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='all database activities';


CREATE TABLE `mdl_data_content` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `fieldid` bigint NOT NULL DEFAULT '0',
  `recordid` bigint NOT NULL DEFAULT '0',
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `content1` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `content2` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `content3` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `content4` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  KEY `mdl_datacont_rec_ix` (`recordid`),
  KEY `mdl_datacont_fie_ix` (`fieldid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='the content introduced in each record/fields';


CREATE TABLE `mdl_data_fields` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `dataid` bigint NOT NULL DEFAULT '0',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `param1` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `param2` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `param3` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `param4` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `param5` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `param6` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `param7` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `param8` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `param9` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `param10` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  KEY `mdl_datafiel_typdat_ix` (`type`,`dataid`),
  KEY `mdl_datafiel_dat_ix` (`dataid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='every field available';


CREATE TABLE `mdl_data_records` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `groupid` bigint NOT NULL DEFAULT '0',
  `dataid` bigint NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `approved` smallint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_datareco_dat_ix` (`dataid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='every record introduced';


CREATE TABLE `mdl_editor_atto_autosave` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `elementid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `contextid` bigint NOT NULL,
  `pagehash` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `userid` bigint NOT NULL,
  `drafttext` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `draftid` bigint DEFAULT NULL,
  `pageinstance` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_editattoauto_eleconuse_uix` (`elementid`,`contextid`,`userid`,`pagehash`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Draft text that is auto-saved every 5 seconds while an edito';


CREATE TABLE `mdl_enrol` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `enrol` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `status` bigint NOT NULL DEFAULT '0',
  `courseid` bigint NOT NULL,
  `sortorder` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `enrolperiod` bigint DEFAULT '0',
  `enrolstartdate` bigint DEFAULT '0',
  `enrolenddate` bigint DEFAULT '0',
  `expirynotify` tinyint(1) DEFAULT '0',
  `expirythreshold` bigint DEFAULT '0',
  `notifyall` tinyint(1) DEFAULT '0',
  `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `cost` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `roleid` bigint DEFAULT '0',
  `customint1` bigint DEFAULT NULL,
  `customint2` bigint DEFAULT NULL,
  `customint3` bigint DEFAULT NULL,
  `customint4` bigint DEFAULT NULL,
  `customint5` bigint DEFAULT NULL,
  `customint6` bigint DEFAULT NULL,
  `customint7` bigint DEFAULT NULL,
  `customint8` bigint DEFAULT NULL,
  `customchar1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `customchar2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `customchar3` varchar(1333) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `customdec1` decimal(12,7) DEFAULT NULL,
  `customdec2` decimal(12,7) DEFAULT NULL,
  `customtext1` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `customtext2` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `customtext3` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `customtext4` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_enro_enr_ix` (`enrol`),
  KEY `mdl_enro_cou_ix` (`courseid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Instances of enrolment plugins used in courses, fields marke';


CREATE TABLE `mdl_enrol_flatfile` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `action` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `roleid` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `courseid` bigint NOT NULL,
  `timestart` bigint NOT NULL DEFAULT '0',
  `timeend` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_enroflat_cou_ix` (`courseid`),
  KEY `mdl_enroflat_use_ix` (`userid`),
  KEY `mdl_enroflat_rol_ix` (`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='enrol_flatfile table retrofitted from MySQL';


CREATE TABLE `mdl_enrol_lti_lti2_consumer` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `consumerkey256` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `consumerkey` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `secret` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `ltiversion` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `consumername` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `consumerversion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `consumerguid` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `profile` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `toolproxy` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `protected` tinyint(1) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `enablefrom` bigint DEFAULT NULL,
  `enableuntil` bigint DEFAULT NULL,
  `lastaccess` bigint DEFAULT NULL,
  `created` bigint NOT NULL,
  `updated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_enroltilti2cons_con_uix` (`consumerkey256`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='LTI consumers interacting with moodle';


CREATE TABLE `mdl_enrol_lti_lti2_context` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `consumerid` bigint NOT NULL,
  `lticontextkey` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `created` bigint NOT NULL,
  `updated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_enroltilti2cont_con_ix` (`consumerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Information about a specific LTI contexts from the consumers';


CREATE TABLE `mdl_enrol_lti_lti2_nonce` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `consumerid` bigint NOT NULL,
  `value` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `expires` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_enroltilti2nonc_con_ix` (`consumerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Nonce used for authentication between moodle and a consumer';


CREATE TABLE `mdl_enrol_lti_lti2_resource_link` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `contextid` bigint DEFAULT NULL,
  `consumerid` bigint DEFAULT NULL,
  `ltiresourcelinkkey` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `primaryresourcelinkid` bigint DEFAULT NULL,
  `shareapproved` tinyint(1) DEFAULT NULL,
  `created` bigint NOT NULL,
  `updated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_enroltilti2resolink_con_ix` (`contextid`),
  KEY `mdl_enroltilti2resolink_pri_ix` (`primaryresourcelinkid`),
  KEY `mdl_enroltilti2resolink_co2_ix` (`consumerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Link from the consumer to the tool';


CREATE TABLE `mdl_enrol_lti_lti2_share_key` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `sharekey` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `resourcelinkid` bigint NOT NULL,
  `autoapprove` tinyint(1) NOT NULL,
  `expires` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_enroltilti2sharkey_sha_uix` (`sharekey`),
  UNIQUE KEY `mdl_enroltilti2sharkey_res_uix` (`resourcelinkid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Resource link share key';


CREATE TABLE `mdl_enrol_lti_lti2_tool_proxy` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `toolproxykey` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `consumerid` bigint NOT NULL,
  `toolproxy` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created` bigint NOT NULL,
  `updated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_enroltilti2toolprox_to_uix` (`toolproxykey`),
  KEY `mdl_enroltilti2toolprox_con_ix` (`consumerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='A tool proxy between moodle and a consumer';


CREATE TABLE `mdl_enrol_lti_lti2_user_result` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `resourcelinkid` bigint NOT NULL,
  `ltiuserkey` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `ltiresultsourcedid` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `created` bigint NOT NULL,
  `updated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_enroltilti2userresu_res_ix` (`resourcelinkid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Results for each user for each resource link';


CREATE TABLE `mdl_enrol_lti_tool_consumer_map` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `toolid` bigint NOT NULL,
  `consumerid` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_enroltitoolconsmap_too_ix` (`toolid`),
  KEY `mdl_enroltitoolconsmap_con_ix` (`consumerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Table that maps the published tool to tool consumers.';


CREATE TABLE `mdl_enrol_lti_tools` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `enrolid` bigint NOT NULL,
  `contextid` bigint NOT NULL,
  `institution` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `lang` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'en',
  `timezone` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '99',
  `maxenrolled` bigint NOT NULL DEFAULT '0',
  `maildisplay` tinyint NOT NULL DEFAULT '2',
  `city` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `country` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `gradesync` tinyint(1) NOT NULL DEFAULT '0',
  `gradesynccompletion` tinyint(1) NOT NULL DEFAULT '0',
  `membersync` tinyint(1) NOT NULL DEFAULT '0',
  `membersyncmode` tinyint(1) NOT NULL DEFAULT '0',
  `roleinstructor` bigint NOT NULL,
  `rolelearner` bigint NOT NULL,
  `secret` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_enroltitool_enr_ix` (`enrolid`),
  KEY `mdl_enroltitool_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='List of tools provided to the remote system';


CREATE TABLE `mdl_enrol_lti_users` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL,
  `toolid` bigint NOT NULL,
  `serviceurl` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `sourceid` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `consumerkey` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `consumersecret` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `membershipsurl` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `membershipsid` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `lastgrade` decimal(10,5) DEFAULT NULL,
  `lastaccess` bigint DEFAULT NULL,
  `timecreated` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_enroltiuser_use_ix` (`userid`),
  KEY `mdl_enroltiuser_too_ix` (`toolid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='User access log and gradeback data';


CREATE TABLE `mdl_enrol_paypal` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `business` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `receiver_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `receiver_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `courseid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `instanceid` bigint NOT NULL DEFAULT '0',
  `memo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `tax` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `option_name1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `option_selection1_x` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `option_name2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `option_selection2_x` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `payment_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `pending_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `reason_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `txn_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `parent_txn_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `payment_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timeupdated` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_enropayp_bus_ix` (`business`),
  KEY `mdl_enropayp_rec_ix` (`receiver_email`),
  KEY `mdl_enropayp_cou_ix` (`courseid`),
  KEY `mdl_enropayp_use_ix` (`userid`),
  KEY `mdl_enropayp_ins_ix` (`instanceid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Holds all known information about PayPal transactions';


CREATE TABLE `mdl_event` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `format` smallint NOT NULL DEFAULT '0',
  `categoryid` bigint NOT NULL DEFAULT '0',
  `courseid` bigint NOT NULL DEFAULT '0',
  `groupid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `repeatid` bigint NOT NULL DEFAULT '0',
  `modulename` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `instance` bigint NOT NULL DEFAULT '0',
  `type` smallint NOT NULL DEFAULT '0',
  `eventtype` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timestart` bigint NOT NULL DEFAULT '0',
  `timeduration` bigint NOT NULL DEFAULT '0',
  `timesort` bigint DEFAULT NULL,
  `visible` smallint NOT NULL DEFAULT '1',
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `sequence` bigint NOT NULL DEFAULT '1',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `subscriptionid` bigint DEFAULT NULL,
  `priority` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_even_cou_ix` (`courseid`),
  KEY `mdl_even_use_ix` (`userid`),
  KEY `mdl_even_tim_ix` (`timestart`),
  KEY `mdl_even_tim2_ix` (`timeduration`),
  KEY `mdl_even_uui_ix` (`uuid`),
  KEY `mdl_even_typtim_ix` (`type`,`timesort`),
  KEY `mdl_even_grocoucatvisuse_ix` (`groupid`,`courseid`,`categoryid`,`visible`,`userid`),
  KEY `mdl_even_cat_ix` (`categoryid`),
  KEY `mdl_even_sub_ix` (`subscriptionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='For everything with a time associated to it';


CREATE TABLE `mdl_event_subscriptions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `categoryid` bigint NOT NULL DEFAULT '0',
  `courseid` bigint NOT NULL DEFAULT '0',
  `groupid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `eventtype` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `pollinterval` bigint NOT NULL DEFAULT '0',
  `lastupdated` bigint DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Tracks subscriptions to remote calendars.';


CREATE TABLE `mdl_events_handlers` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `eventname` varchar(166) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `component` varchar(166) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `handlerfile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `handlerfunction` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `schedule` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `status` bigint NOT NULL DEFAULT '0',
  `internal` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_evenhand_evecom_uix` (`eventname`,`component`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table is for storing which components requests what typ';


CREATE TABLE `mdl_events_queue` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `eventdata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `stackdump` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `userid` bigint DEFAULT NULL,
  `timecreated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_evenqueu_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table is for storing queued events. It stores only one ';


CREATE TABLE `mdl_events_queue_handlers` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `queuedeventid` bigint NOT NULL,
  `handlerid` bigint NOT NULL,
  `status` bigint DEFAULT NULL,
  `errormessage` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timemodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_evenqueuhand_que_ix` (`queuedeventid`),
  KEY `mdl_evenqueuhand_han_ix` (`handlerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This is the list of queued handlers for processing. The even';


CREATE TABLE `mdl_external_functions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `classname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `methodname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `classpath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `capabilities` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `services` varchar(1333) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_extefunc_nam_uix` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=504 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='list of all external functions';


CREATE TABLE `mdl_external_services` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL,
  `requiredcapability` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `restrictedusers` tinyint(1) NOT NULL,
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint DEFAULT NULL,
  `shortname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `downloadfiles` tinyint(1) NOT NULL DEFAULT '0',
  `uploadfiles` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_exteserv_nam_uix` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='built in and custom external services';


CREATE TABLE `mdl_external_services_functions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `externalserviceid` bigint NOT NULL,
  `functionname` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_exteservfunc_ext_ix` (`externalserviceid`)
) ENGINE=InnoDB AUTO_INCREMENT=300 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='lists functions available in each service group';


CREATE TABLE `mdl_external_services_users` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `externalserviceid` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `iprestriction` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `validuntil` bigint DEFAULT NULL,
  `timecreated` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_exteservuser_ext_ix` (`externalserviceid`),
  KEY `mdl_exteservuser_use_ix` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='users allowed to use services with restricted users flag';


CREATE TABLE `mdl_external_tokens` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `token` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `privatetoken` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `tokentype` smallint NOT NULL,
  `userid` bigint NOT NULL,
  `externalserviceid` bigint NOT NULL,
  `sid` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `contextid` bigint NOT NULL,
  `creatorid` bigint NOT NULL DEFAULT '1',
  `iprestriction` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `validuntil` bigint DEFAULT NULL,
  `timecreated` bigint NOT NULL,
  `lastaccess` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_extetoke_use_ix` (`userid`),
  KEY `mdl_extetoke_ext_ix` (`externalserviceid`),
  KEY `mdl_extetoke_con_ix` (`contextid`),
  KEY `mdl_extetoke_cre_ix` (`creatorid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Security tokens for accessing of external services';


CREATE TABLE `mdl_feedback` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `introformat` smallint NOT NULL DEFAULT '0',
  `anonymous` tinyint(1) NOT NULL DEFAULT '1',
  `email_notification` tinyint(1) NOT NULL DEFAULT '1',
  `multiple_submit` tinyint(1) NOT NULL DEFAULT '1',
  `autonumbering` tinyint(1) NOT NULL DEFAULT '1',
  `site_after_submit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `page_after_submit` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `page_after_submitformat` tinyint NOT NULL DEFAULT '0',
  `publish_stats` tinyint(1) NOT NULL DEFAULT '0',
  `timeopen` bigint NOT NULL DEFAULT '0',
  `timeclose` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `completionsubmit` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_feed_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='all feedbacks';


CREATE TABLE `mdl_feedback_completed` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `feedback` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `random_response` bigint NOT NULL DEFAULT '0',
  `anonymous_response` tinyint(1) NOT NULL DEFAULT '0',
  `courseid` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_feedcomp_use_ix` (`userid`),
  KEY `mdl_feedcomp_fee_ix` (`feedback`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='filled out feedback';


CREATE TABLE `mdl_feedback_completedtmp` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `feedback` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `guestid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `random_response` bigint NOT NULL DEFAULT '0',
  `anonymous_response` tinyint(1) NOT NULL DEFAULT '0',
  `courseid` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_feedcomp_use2_ix` (`userid`),
  KEY `mdl_feedcomp_fee2_ix` (`feedback`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='filled out feedback';


CREATE TABLE `mdl_feedback_item` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `feedback` bigint NOT NULL DEFAULT '0',
  `template` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `presentation` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `typ` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `hasvalue` tinyint(1) NOT NULL DEFAULT '0',
  `position` smallint NOT NULL DEFAULT '0',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `dependitem` bigint NOT NULL DEFAULT '0',
  `dependvalue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `options` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_feeditem_fee_ix` (`feedback`),
  KEY `mdl_feeditem_tem_ix` (`template`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='feedback_items';


CREATE TABLE `mdl_feedback_sitecourse_map` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `feedbackid` bigint NOT NULL DEFAULT '0',
  `courseid` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_feedsitemap_cou_ix` (`courseid`),
  KEY `mdl_feedsitemap_fee_ix` (`feedbackid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='feedback sitecourse map';


CREATE TABLE `mdl_feedback_template` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `ispublic` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_feedtemp_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='templates of feedbackstructures';


CREATE TABLE `mdl_feedback_value` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course_id` bigint NOT NULL DEFAULT '0',
  `item` bigint NOT NULL DEFAULT '0',
  `completed` bigint NOT NULL DEFAULT '0',
  `tmp_completed` bigint NOT NULL DEFAULT '0',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_feedvalu_comitecou_uix` (`completed`,`item`,`course_id`),
  KEY `mdl_feedvalu_cou_ix` (`course_id`),
  KEY `mdl_feedvalu_ite_ix` (`item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='values of the completeds';


CREATE TABLE `mdl_feedback_valuetmp` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course_id` bigint NOT NULL DEFAULT '0',
  `item` bigint NOT NULL DEFAULT '0',
  `completed` bigint NOT NULL DEFAULT '0',
  `tmp_completed` bigint NOT NULL DEFAULT '0',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_feedvalu_comitecou2_uix` (`completed`,`item`,`course_id`),
  KEY `mdl_feedvalu_cou2_ix` (`course_id`),
  KEY `mdl_feedvalu_ite2_ix` (`item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='values of the completedstmp';


CREATE TABLE `mdl_file_conversion` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `usermodified` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `sourcefileid` bigint NOT NULL,
  `targetformat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `status` bigint DEFAULT '0',
  `statusmessage` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `converter` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `destfileid` bigint DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  KEY `mdl_fileconv_sou_ix` (`sourcefileid`),
  KEY `mdl_fileconv_des_ix` (`destfileid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Table to track file conversions.';


CREATE TABLE `mdl_files` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `contenthash` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `pathnamehash` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `contextid` bigint NOT NULL,
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `filearea` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `itemid` bigint NOT NULL,
  `filepath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `userid` bigint DEFAULT NULL,
  `filesize` bigint NOT NULL,
  `mimetype` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `status` bigint NOT NULL DEFAULT '0',
  `source` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `license` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `sortorder` bigint NOT NULL DEFAULT '0',
  `referencefileid` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_file_pat_uix` (`pathnamehash`),
  KEY `mdl_file_comfilconite_ix` (`component`,`filearea`,`contextid`,`itemid`),
  KEY `mdl_file_con_ix` (`contenthash`),
  KEY `mdl_file_con2_ix` (`contextid`),
  KEY `mdl_file_use_ix` (`userid`),
  KEY `mdl_file_ref_ix` (`referencefileid`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='description of files, content is stored in sha1 file pool';


CREATE TABLE `mdl_files_reference` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `repositoryid` bigint NOT NULL,
  `lastsync` bigint DEFAULT NULL,
  `reference` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `referencehash` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_filerefe_refrep_uix` (`referencehash`,`repositoryid`),
  KEY `mdl_filerefe_rep_ix` (`repositoryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Store files references';


CREATE TABLE `mdl_filter_active` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `filter` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `contextid` bigint NOT NULL,
  `active` smallint NOT NULL,
  `sortorder` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_filtacti_confil_uix` (`contextid`,`filter`),
  KEY `mdl_filtacti_con_ix` (`contextid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores information about which filters are active in which c';


CREATE TABLE `mdl_filter_config` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `filter` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `contextid` bigint NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_filtconf_confilnam_uix` (`contextid`,`filter`,`name`),
  KEY `mdl_filtconf_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores per-context configuration settings for filters which ';


CREATE TABLE `mdl_folder` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `introformat` smallint NOT NULL DEFAULT '0',
  `revision` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `display` smallint NOT NULL DEFAULT '0',
  `showexpanded` tinyint(1) NOT NULL DEFAULT '1',
  `showdownloadfolder` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `mdl_fold_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='each record is one folder resource';


CREATE TABLE `mdl_forum` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'general',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `introformat` smallint NOT NULL DEFAULT '0',
  `assessed` bigint NOT NULL DEFAULT '0',
  `assesstimestart` bigint NOT NULL DEFAULT '0',
  `assesstimefinish` bigint NOT NULL DEFAULT '0',
  `scale` bigint NOT NULL DEFAULT '0',
  `maxbytes` bigint NOT NULL DEFAULT '0',
  `maxattachments` bigint NOT NULL DEFAULT '1',
  `forcesubscribe` tinyint(1) NOT NULL DEFAULT '0',
  `trackingtype` tinyint NOT NULL DEFAULT '1',
  `rsstype` tinyint NOT NULL DEFAULT '0',
  `rssarticles` tinyint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `warnafter` bigint NOT NULL DEFAULT '0',
  `blockafter` bigint NOT NULL DEFAULT '0',
  `blockperiod` bigint NOT NULL DEFAULT '0',
  `completiondiscussions` int NOT NULL DEFAULT '0',
  `completionreplies` int NOT NULL DEFAULT '0',
  `completionposts` int NOT NULL DEFAULT '0',
  `displaywordcount` tinyint(1) NOT NULL DEFAULT '0',
  `lockdiscussionafter` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_foru_cou_ix` (`course`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Forums contain and structure discussion';


CREATE TABLE `mdl_forum_digests` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL,
  `forum` bigint NOT NULL,
  `maildigest` tinyint(1) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_forudige_forusemai_uix` (`forum`,`userid`,`maildigest`),
  KEY `mdl_forudige_use_ix` (`userid`),
  KEY `mdl_forudige_for_ix` (`forum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Keeps track of user mail delivery preferences for each forum';


CREATE TABLE `mdl_forum_discussion_subs` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `forum` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `discussion` bigint NOT NULL,
  `preference` bigint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_forudiscsubs_usedis_uix` (`userid`,`discussion`),
  KEY `mdl_forudiscsubs_for_ix` (`forum`),
  KEY `mdl_forudiscsubs_use_ix` (`userid`),
  KEY `mdl_forudiscsubs_dis_ix` (`discussion`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Users may choose to subscribe and unsubscribe from specific ';


CREATE TABLE `mdl_forum_discussions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `forum` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `firstpost` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `groupid` bigint NOT NULL DEFAULT '-1',
  `assessed` tinyint(1) NOT NULL DEFAULT '1',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `usermodified` bigint NOT NULL DEFAULT '0',
  `timestart` bigint NOT NULL DEFAULT '0',
  `timeend` bigint NOT NULL DEFAULT '0',
  `pinned` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_forudisc_use_ix` (`userid`),
  KEY `mdl_forudisc_cou_ix` (`course`),
  KEY `mdl_forudisc_for_ix` (`forum`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Forums are composed of discussions';


CREATE TABLE `mdl_forum_posts` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `discussion` bigint NOT NULL DEFAULT '0',
  `parent` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `created` bigint NOT NULL DEFAULT '0',
  `modified` bigint NOT NULL DEFAULT '0',
  `mailed` tinyint NOT NULL DEFAULT '0',
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `messageformat` tinyint NOT NULL DEFAULT '0',
  `messagetrust` tinyint NOT NULL DEFAULT '0',
  `attachment` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `totalscore` smallint NOT NULL DEFAULT '0',
  `mailnow` bigint NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_forupost_use_ix` (`userid`),
  KEY `mdl_forupost_cre_ix` (`created`),
  KEY `mdl_forupost_mai_ix` (`mailed`),
  KEY `mdl_forupost_dis_ix` (`discussion`),
  KEY `mdl_forupost_par_ix` (`parent`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='All posts are stored in this table';


CREATE TABLE `mdl_forum_queue` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `discussionid` bigint NOT NULL DEFAULT '0',
  `postid` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_foruqueu_use_ix` (`userid`),
  KEY `mdl_foruqueu_dis_ix` (`discussionid`),
  KEY `mdl_foruqueu_pos_ix` (`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='For keeping track of posts that will be mailed in digest for';


CREATE TABLE `mdl_forum_read` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `forumid` bigint NOT NULL DEFAULT '0',
  `discussionid` bigint NOT NULL DEFAULT '0',
  `postid` bigint NOT NULL DEFAULT '0',
  `firstread` bigint NOT NULL DEFAULT '0',
  `lastread` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_foruread_usefor_ix` (`userid`,`forumid`),
  KEY `mdl_foruread_usedis_ix` (`userid`,`discussionid`),
  KEY `mdl_foruread_posuse_ix` (`postid`,`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Tracks each users read posts';


CREATE TABLE `mdl_forum_subscriptions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `forum` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_forusubs_usefor_uix` (`userid`,`forum`),
  KEY `mdl_forusubs_use_ix` (`userid`),
  KEY `mdl_forusubs_for_ix` (`forum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Keeps track of who is subscribed to what forum';


CREATE TABLE `mdl_forum_track_prefs` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `forumid` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_forutracpref_usefor_ix` (`userid`,`forumid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Tracks each users untracked forums';


CREATE TABLE `mdl_glossary` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `introformat` smallint NOT NULL DEFAULT '0',
  `allowduplicatedentries` tinyint NOT NULL DEFAULT '0',
  `displayformat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'dictionary',
  `mainglossary` tinyint NOT NULL DEFAULT '0',
  `showspecial` tinyint NOT NULL DEFAULT '1',
  `showalphabet` tinyint NOT NULL DEFAULT '1',
  `showall` tinyint NOT NULL DEFAULT '1',
  `allowcomments` tinyint NOT NULL DEFAULT '0',
  `allowprintview` tinyint NOT NULL DEFAULT '1',
  `usedynalink` tinyint NOT NULL DEFAULT '1',
  `defaultapproval` tinyint NOT NULL DEFAULT '1',
  `approvaldisplayformat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'default',
  `globalglossary` tinyint NOT NULL DEFAULT '0',
  `entbypage` smallint NOT NULL DEFAULT '10',
  `editalways` tinyint NOT NULL DEFAULT '0',
  `rsstype` tinyint NOT NULL DEFAULT '0',
  `rssarticles` tinyint NOT NULL DEFAULT '0',
  `assessed` bigint NOT NULL DEFAULT '0',
  `assesstimestart` bigint NOT NULL DEFAULT '0',
  `assesstimefinish` bigint NOT NULL DEFAULT '0',
  `scale` bigint NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `completionentries` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_glos_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='all glossaries';


CREATE TABLE `mdl_glossary_alias` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `entryid` bigint NOT NULL DEFAULT '0',
  `alias` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_glosalia_ent_ix` (`entryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='entries alias';


CREATE TABLE `mdl_glossary_categories` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `glossaryid` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `usedynalink` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `mdl_gloscate_glo_ix` (`glossaryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='all categories for glossary entries';


CREATE TABLE `mdl_glossary_entries` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `glossaryid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `concept` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `definition` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `definitionformat` tinyint NOT NULL DEFAULT '0',
  `definitiontrust` tinyint NOT NULL DEFAULT '0',
  `attachment` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `teacherentry` tinyint NOT NULL DEFAULT '0',
  `sourceglossaryid` bigint NOT NULL DEFAULT '0',
  `usedynalink` tinyint NOT NULL DEFAULT '1',
  `casesensitive` tinyint NOT NULL DEFAULT '0',
  `fullmatch` tinyint NOT NULL DEFAULT '1',
  `approved` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `mdl_glosentr_use_ix` (`userid`),
  KEY `mdl_glosentr_con_ix` (`concept`),
  KEY `mdl_glosentr_glo_ix` (`glossaryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='all glossary entries';


CREATE TABLE `mdl_glossary_entries_categories` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `categoryid` bigint NOT NULL DEFAULT '0',
  `entryid` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_glosentrcate_cat_ix` (`categoryid`),
  KEY `mdl_glosentrcate_ent_ix` (`entryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='categories of each glossary entry';


CREATE TABLE `mdl_glossary_formats` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `popupformatname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `visible` tinyint NOT NULL DEFAULT '1',
  `showgroup` tinyint NOT NULL DEFAULT '1',
  `showtabs` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `defaultmode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `defaulthook` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `sortkey` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `sortorder` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Setting of the display formats';


CREATE TABLE `mdl_grade_categories` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL,
  `parent` bigint DEFAULT NULL,
  `depth` bigint NOT NULL DEFAULT '0',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `aggregation` bigint NOT NULL DEFAULT '0',
  `keephigh` bigint NOT NULL DEFAULT '0',
  `droplow` bigint NOT NULL DEFAULT '0',
  `aggregateonlygraded` tinyint(1) NOT NULL DEFAULT '0',
  `aggregateoutcomes` tinyint(1) NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_gradcate_cou_ix` (`courseid`),
  KEY `mdl_gradcate_par_ix` (`parent`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table keeps information about categories, used for grou';


CREATE TABLE `mdl_grade_categories_history` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `action` bigint NOT NULL DEFAULT '0',
  `oldid` bigint NOT NULL,
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `timemodified` bigint DEFAULT NULL,
  `loggeduser` bigint DEFAULT NULL,
  `courseid` bigint NOT NULL,
  `parent` bigint DEFAULT NULL,
  `depth` bigint NOT NULL DEFAULT '0',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `aggregation` bigint NOT NULL DEFAULT '0',
  `keephigh` bigint NOT NULL DEFAULT '0',
  `droplow` bigint NOT NULL DEFAULT '0',
  `aggregateonlygraded` tinyint(1) NOT NULL DEFAULT '0',
  `aggregateoutcomes` tinyint(1) NOT NULL DEFAULT '0',
  `aggregatesubcats` tinyint(1) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_gradcatehist_act_ix` (`action`),
  KEY `mdl_gradcatehist_tim_ix` (`timemodified`),
  KEY `mdl_gradcatehist_old_ix` (`oldid`),
  KEY `mdl_gradcatehist_cou_ix` (`courseid`),
  KEY `mdl_gradcatehist_par_ix` (`parent`),
  KEY `mdl_gradcatehist_log_ix` (`loggeduser`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='History of grade_categories';


CREATE TABLE `mdl_grade_grades` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `itemid` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `rawgrade` decimal(10,5) DEFAULT NULL,
  `rawgrademax` decimal(10,5) NOT NULL DEFAULT '100.00000',
  `rawgrademin` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `rawscaleid` bigint DEFAULT NULL,
  `usermodified` bigint DEFAULT NULL,
  `finalgrade` decimal(10,5) DEFAULT NULL,
  `hidden` bigint NOT NULL DEFAULT '0',
  `locked` bigint NOT NULL DEFAULT '0',
  `locktime` bigint NOT NULL DEFAULT '0',
  `exported` bigint NOT NULL DEFAULT '0',
  `overridden` bigint NOT NULL DEFAULT '0',
  `excluded` bigint NOT NULL DEFAULT '0',
  `feedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `feedbackformat` bigint NOT NULL DEFAULT '0',
  `information` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `informationformat` bigint NOT NULL DEFAULT '0',
  `timecreated` bigint DEFAULT NULL,
  `timemodified` bigint DEFAULT NULL,
  `aggregationstatus` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'unknown',
  `aggregationweight` decimal(10,5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_gradgrad_useite_uix` (`userid`,`itemid`),
  KEY `mdl_gradgrad_locloc_ix` (`locked`,`locktime`),
  KEY `mdl_gradgrad_ite_ix` (`itemid`),
  KEY `mdl_gradgrad_use_ix` (`userid`),
  KEY `mdl_gradgrad_raw_ix` (`rawscaleid`),
  KEY `mdl_gradgrad_use2_ix` (`usermodified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='grade_grades  This table keeps individual grades for each us';


CREATE TABLE `mdl_grade_grades_history` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `action` bigint NOT NULL DEFAULT '0',
  `oldid` bigint NOT NULL,
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `timemodified` bigint DEFAULT NULL,
  `loggeduser` bigint DEFAULT NULL,
  `itemid` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `rawgrade` decimal(10,5) DEFAULT NULL,
  `rawgrademax` decimal(10,5) NOT NULL DEFAULT '100.00000',
  `rawgrademin` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `rawscaleid` bigint DEFAULT NULL,
  `usermodified` bigint DEFAULT NULL,
  `finalgrade` decimal(10,5) DEFAULT NULL,
  `hidden` bigint NOT NULL DEFAULT '0',
  `locked` bigint NOT NULL DEFAULT '0',
  `locktime` bigint NOT NULL DEFAULT '0',
  `exported` bigint NOT NULL DEFAULT '0',
  `overridden` bigint NOT NULL DEFAULT '0',
  `excluded` bigint NOT NULL DEFAULT '0',
  `feedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `feedbackformat` bigint NOT NULL DEFAULT '0',
  `information` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `informationformat` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_gradgradhist_act_ix` (`action`),
  KEY `mdl_gradgradhist_tim_ix` (`timemodified`),
  KEY `mdl_gradgradhist_useitetim_ix` (`userid`,`itemid`,`timemodified`),
  KEY `mdl_gradgradhist_old_ix` (`oldid`),
  KEY `mdl_gradgradhist_ite_ix` (`itemid`),
  KEY `mdl_gradgradhist_use_ix` (`userid`),
  KEY `mdl_gradgradhist_raw_ix` (`rawscaleid`),
  KEY `mdl_gradgradhist_use2_ix` (`usermodified`),
  KEY `mdl_gradgradhist_log_ix` (`loggeduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='History table';


CREATE TABLE `mdl_grade_import_newitem` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `itemname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `importcode` bigint NOT NULL,
  `importer` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_gradimponewi_imp_ix` (`importer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='temporary table for storing new grade_item names from grade ';


CREATE TABLE `mdl_grade_import_values` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `itemid` bigint DEFAULT NULL,
  `newgradeitem` bigint DEFAULT NULL,
  `userid` bigint NOT NULL,
  `finalgrade` decimal(10,5) DEFAULT NULL,
  `feedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `importcode` bigint NOT NULL,
  `importer` bigint DEFAULT NULL,
  `importonlyfeedback` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_gradimpovalu_ite_ix` (`itemid`),
  KEY `mdl_gradimpovalu_new_ix` (`newgradeitem`),
  KEY `mdl_gradimpovalu_imp_ix` (`importer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Temporary table for importing grades';


CREATE TABLE `mdl_grade_items` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint DEFAULT NULL,
  `categoryid` bigint DEFAULT NULL,
  `itemname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `itemtype` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `itemmodule` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `iteminstance` bigint DEFAULT NULL,
  `itemnumber` bigint DEFAULT NULL,
  `iteminfo` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `idnumber` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `calculation` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `gradetype` smallint NOT NULL DEFAULT '1',
  `grademax` decimal(10,5) NOT NULL DEFAULT '100.00000',
  `grademin` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `scaleid` bigint DEFAULT NULL,
  `outcomeid` bigint DEFAULT NULL,
  `gradepass` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `multfactor` decimal(10,5) NOT NULL DEFAULT '1.00000',
  `plusfactor` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `aggregationcoef` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `aggregationcoef2` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `sortorder` bigint NOT NULL DEFAULT '0',
  `display` bigint NOT NULL DEFAULT '0',
  `decimals` tinyint(1) DEFAULT NULL,
  `hidden` bigint NOT NULL DEFAULT '0',
  `locked` bigint NOT NULL DEFAULT '0',
  `locktime` bigint NOT NULL DEFAULT '0',
  `needsupdate` bigint NOT NULL DEFAULT '0',
  `weightoverride` tinyint(1) NOT NULL DEFAULT '0',
  `timecreated` bigint DEFAULT NULL,
  `timemodified` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_graditem_locloc_ix` (`locked`,`locktime`),
  KEY `mdl_graditem_itenee_ix` (`itemtype`,`needsupdate`),
  KEY `mdl_graditem_gra_ix` (`gradetype`),
  KEY `mdl_graditem_idncou_ix` (`idnumber`,`courseid`),
  KEY `mdl_graditem_cou_ix` (`courseid`),
  KEY `mdl_graditem_cat_ix` (`categoryid`),
  KEY `mdl_graditem_sca_ix` (`scaleid`),
  KEY `mdl_graditem_out_ix` (`outcomeid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table keeps information about gradeable items (ie colum';


CREATE TABLE `mdl_grade_items_history` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `action` bigint NOT NULL DEFAULT '0',
  `oldid` bigint NOT NULL,
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `timemodified` bigint DEFAULT NULL,
  `loggeduser` bigint DEFAULT NULL,
  `courseid` bigint DEFAULT NULL,
  `categoryid` bigint DEFAULT NULL,
  `itemname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `itemtype` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `itemmodule` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `iteminstance` bigint DEFAULT NULL,
  `itemnumber` bigint DEFAULT NULL,
  `iteminfo` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `idnumber` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `calculation` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `gradetype` smallint NOT NULL DEFAULT '1',
  `grademax` decimal(10,5) NOT NULL DEFAULT '100.00000',
  `grademin` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `scaleid` bigint DEFAULT NULL,
  `outcomeid` bigint DEFAULT NULL,
  `gradepass` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `multfactor` decimal(10,5) NOT NULL DEFAULT '1.00000',
  `plusfactor` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `aggregationcoef` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `aggregationcoef2` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `sortorder` bigint NOT NULL DEFAULT '0',
  `hidden` bigint NOT NULL DEFAULT '0',
  `locked` bigint NOT NULL DEFAULT '0',
  `locktime` bigint NOT NULL DEFAULT '0',
  `needsupdate` bigint NOT NULL DEFAULT '0',
  `display` bigint NOT NULL DEFAULT '0',
  `decimals` tinyint(1) DEFAULT NULL,
  `weightoverride` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_graditemhist_act_ix` (`action`),
  KEY `mdl_graditemhist_tim_ix` (`timemodified`),
  KEY `mdl_graditemhist_old_ix` (`oldid`),
  KEY `mdl_graditemhist_cou_ix` (`courseid`),
  KEY `mdl_graditemhist_cat_ix` (`categoryid`),
  KEY `mdl_graditemhist_sca_ix` (`scaleid`),
  KEY `mdl_graditemhist_out_ix` (`outcomeid`),
  KEY `mdl_graditemhist_log_ix` (`loggeduser`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='History of grade_items';


CREATE TABLE `mdl_grade_letters` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `contextid` bigint NOT NULL,
  `lowerboundary` decimal(10,5) NOT NULL,
  `letter` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_gradlett_conlowlet_uix` (`contextid`,`lowerboundary`,`letter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Repository for grade letters, for courses and other moodle e';


CREATE TABLE `mdl_grade_outcomes` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint DEFAULT NULL,
  `shortname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `fullname` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `scaleid` bigint DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` tinyint NOT NULL DEFAULT '0',
  `timecreated` bigint DEFAULT NULL,
  `timemodified` bigint DEFAULT NULL,
  `usermodified` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_gradoutc_cousho_uix` (`courseid`,`shortname`),
  KEY `mdl_gradoutc_cou_ix` (`courseid`),
  KEY `mdl_gradoutc_sca_ix` (`scaleid`),
  KEY `mdl_gradoutc_use_ix` (`usermodified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table describes the outcomes used in the system. An out';


CREATE TABLE `mdl_grade_outcomes_courses` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL,
  `outcomeid` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_gradoutccour_couout_uix` (`courseid`,`outcomeid`),
  KEY `mdl_gradoutccour_cou_ix` (`courseid`),
  KEY `mdl_gradoutccour_out_ix` (`outcomeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='stores what outcomes are used in what courses.';


CREATE TABLE `mdl_grade_outcomes_history` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `action` bigint NOT NULL DEFAULT '0',
  `oldid` bigint NOT NULL,
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `timemodified` bigint DEFAULT NULL,
  `loggeduser` bigint DEFAULT NULL,
  `courseid` bigint DEFAULT NULL,
  `shortname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `fullname` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `scaleid` bigint DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_gradoutchist_act_ix` (`action`),
  KEY `mdl_gradoutchist_tim_ix` (`timemodified`),
  KEY `mdl_gradoutchist_old_ix` (`oldid`),
  KEY `mdl_gradoutchist_cou_ix` (`courseid`),
  KEY `mdl_gradoutchist_sca_ix` (`scaleid`),
  KEY `mdl_gradoutchist_log_ix` (`loggeduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='History table';


CREATE TABLE `mdl_grade_settings` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_gradsett_counam_uix` (`courseid`,`name`),
  KEY `mdl_gradsett_cou_ix` (`courseid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='gradebook settings';


CREATE TABLE `mdl_grading_areas` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `contextid` bigint NOT NULL,
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `areaname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `activemethod` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_gradarea_concomare_uix` (`contextid`,`component`,`areaname`),
  KEY `mdl_gradarea_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Identifies gradable areas where advanced grading can happen.';


CREATE TABLE `mdl_grading_definitions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `areaid` bigint NOT NULL,
  `method` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` tinyint DEFAULT NULL,
  `status` bigint NOT NULL DEFAULT '0',
  `copiedfromid` bigint DEFAULT NULL,
  `timecreated` bigint NOT NULL,
  `usercreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `usermodified` bigint NOT NULL,
  `timecopied` bigint DEFAULT '0',
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_graddefi_aremet_uix` (`areaid`,`method`),
  KEY `mdl_graddefi_are_ix` (`areaid`),
  KEY `mdl_graddefi_use_ix` (`usermodified`),
  KEY `mdl_graddefi_use2_ix` (`usercreated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Contains the basic information about an advanced grading for';


CREATE TABLE `mdl_grading_instances` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `definitionid` bigint NOT NULL,
  `raterid` bigint NOT NULL,
  `itemid` bigint DEFAULT NULL,
  `rawgrade` decimal(10,5) DEFAULT NULL,
  `status` bigint NOT NULL DEFAULT '0',
  `feedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `feedbackformat` tinyint DEFAULT NULL,
  `timemodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_gradinst_def_ix` (`definitionid`),
  KEY `mdl_gradinst_rat_ix` (`raterid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Grading form instance is an assessment record for one gradab';


CREATE TABLE `mdl_gradingform_guide_comments` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `definitionid` bigint NOT NULL,
  `sortorder` bigint NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_gradguidcomm_def_ix` (`definitionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='frequently used comments used in marking guide';


CREATE TABLE `mdl_gradingform_guide_criteria` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `definitionid` bigint NOT NULL,
  `sortorder` bigint NOT NULL,
  `shortname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` tinyint DEFAULT NULL,
  `descriptionmarkers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionmarkersformat` tinyint DEFAULT NULL,
  `maxscore` decimal(10,5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_gradguidcrit_def_ix` (`definitionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores the rows of the criteria grid.';


CREATE TABLE `mdl_gradingform_guide_fillings` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `instanceid` bigint NOT NULL,
  `criterionid` bigint NOT NULL,
  `remark` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `remarkformat` tinyint DEFAULT NULL,
  `score` decimal(10,5) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_gradguidfill_inscri_uix` (`instanceid`,`criterionid`),
  KEY `mdl_gradguidfill_ins_ix` (`instanceid`),
  KEY `mdl_gradguidfill_cri_ix` (`criterionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores the data of how the guide is filled by a particular r';


CREATE TABLE `mdl_gradingform_rubric_criteria` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `definitionid` bigint NOT NULL,
  `sortorder` bigint NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_gradrubrcrit_def_ix` (`definitionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores the rows of the rubric grid.';


CREATE TABLE `mdl_gradingform_rubric_fillings` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `instanceid` bigint NOT NULL,
  `criterionid` bigint NOT NULL,
  `levelid` bigint DEFAULT NULL,
  `remark` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `remarkformat` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_gradrubrfill_inscri_uix` (`instanceid`,`criterionid`),
  KEY `mdl_gradrubrfill_lev_ix` (`levelid`),
  KEY `mdl_gradrubrfill_ins_ix` (`instanceid`),
  KEY `mdl_gradrubrfill_cri_ix` (`criterionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores the data of how the rubric is filled by a particular ';


CREATE TABLE `mdl_gradingform_rubric_levels` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `criterionid` bigint NOT NULL,
  `score` decimal(10,5) NOT NULL,
  `definition` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `definitionformat` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_gradrubrleve_cri_ix` (`criterionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores the columns of the rubric grid.';


CREATE TABLE `mdl_groupings` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `idnumber` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` tinyint NOT NULL DEFAULT '0',
  `configdata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_grou_idn2_ix` (`idnumber`),
  KEY `mdl_grou_cou2_ix` (`courseid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='A grouping is a collection of groups. WAS: groups_groupings';


CREATE TABLE `mdl_groupings_groups` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `groupingid` bigint NOT NULL DEFAULT '0',
  `groupid` bigint NOT NULL DEFAULT '0',
  `timeadded` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_grougrou_gro_ix` (`groupingid`),
  KEY `mdl_grougrou_gro2_ix` (`groupid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Link a grouping to a group (note, groups can be in multiple ';


CREATE TABLE `mdl_groups` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL,
  `idnumber` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `name` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` tinyint NOT NULL DEFAULT '0',
  `enrolmentkey` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `picture` bigint NOT NULL DEFAULT '0',
  `hidepicture` tinyint(1) NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_grou_idn_ix` (`idnumber`),
  KEY `mdl_grou_cou_ix` (`courseid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Each record represents a group.';


CREATE TABLE `mdl_groups_members` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `groupid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `timeadded` bigint NOT NULL DEFAULT '0',
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `itemid` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_groumemb_usegro_uix` (`userid`,`groupid`),
  KEY `mdl_groumemb_gro_ix` (`groupid`),
  KEY `mdl_groumemb_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Link a user to a group.';


CREATE TABLE `mdl_imscp` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `introformat` smallint NOT NULL DEFAULT '0',
  `revision` bigint NOT NULL DEFAULT '0',
  `keepold` bigint NOT NULL DEFAULT '-1',
  `structure` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_imsc_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='each record is one imscp resource';


CREATE TABLE `mdl_label` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `introformat` smallint DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_labe_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines labels';


CREATE TABLE `mdl_lesson` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `introformat` smallint NOT NULL DEFAULT '0',
  `practice` smallint NOT NULL DEFAULT '0',
  `modattempts` smallint NOT NULL DEFAULT '0',
  `usepassword` smallint NOT NULL DEFAULT '0',
  `password` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `dependency` bigint NOT NULL DEFAULT '0',
  `conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `grade` bigint NOT NULL DEFAULT '0',
  `custom` smallint NOT NULL DEFAULT '0',
  `ongoing` smallint NOT NULL DEFAULT '0',
  `usemaxgrade` smallint NOT NULL DEFAULT '0',
  `maxanswers` smallint NOT NULL DEFAULT '4',
  `maxattempts` smallint NOT NULL DEFAULT '5',
  `review` smallint NOT NULL DEFAULT '0',
  `nextpagedefault` smallint NOT NULL DEFAULT '0',
  `feedback` smallint NOT NULL DEFAULT '1',
  `minquestions` smallint NOT NULL DEFAULT '0',
  `maxpages` smallint NOT NULL DEFAULT '0',
  `timelimit` bigint NOT NULL DEFAULT '0',
  `retake` smallint NOT NULL DEFAULT '1',
  `activitylink` bigint NOT NULL DEFAULT '0',
  `mediafile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `mediaheight` bigint NOT NULL DEFAULT '100',
  `mediawidth` bigint NOT NULL DEFAULT '650',
  `mediaclose` smallint NOT NULL DEFAULT '0',
  `slideshow` smallint NOT NULL DEFAULT '0',
  `width` bigint NOT NULL DEFAULT '640',
  `height` bigint NOT NULL DEFAULT '480',
  `bgcolor` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '#FFFFFF',
  `displayleft` smallint NOT NULL DEFAULT '0',
  `displayleftif` smallint NOT NULL DEFAULT '0',
  `progressbar` smallint NOT NULL DEFAULT '0',
  `available` bigint NOT NULL DEFAULT '0',
  `deadline` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `completionendreached` tinyint(1) DEFAULT '0',
  `completiontimespent` bigint DEFAULT '0',
  `allowofflineattempts` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_less_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines lesson';


CREATE TABLE `mdl_lesson_answers` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `lessonid` bigint NOT NULL DEFAULT '0',
  `pageid` bigint NOT NULL DEFAULT '0',
  `jumpto` bigint NOT NULL DEFAULT '0',
  `grade` smallint NOT NULL DEFAULT '0',
  `score` bigint NOT NULL DEFAULT '0',
  `flags` smallint NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `answer` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `answerformat` tinyint NOT NULL DEFAULT '0',
  `response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `responseformat` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_lessansw_les_ix` (`lessonid`),
  KEY `mdl_lessansw_pag_ix` (`pageid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines lesson_answers';


CREATE TABLE `mdl_lesson_attempts` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `lessonid` bigint NOT NULL DEFAULT '0',
  `pageid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `answerid` bigint NOT NULL DEFAULT '0',
  `retry` smallint NOT NULL DEFAULT '0',
  `correct` bigint NOT NULL DEFAULT '0',
  `useranswer` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timeseen` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_lessatte_use_ix` (`userid`),
  KEY `mdl_lessatte_les_ix` (`lessonid`),
  KEY `mdl_lessatte_pag_ix` (`pageid`),
  KEY `mdl_lessatte_ans_ix` (`answerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines lesson_attempts';


CREATE TABLE `mdl_lesson_branch` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `lessonid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `pageid` bigint NOT NULL DEFAULT '0',
  `retry` bigint NOT NULL DEFAULT '0',
  `flag` smallint NOT NULL DEFAULT '0',
  `timeseen` bigint NOT NULL DEFAULT '0',
  `nextpageid` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_lessbran_use_ix` (`userid`),
  KEY `mdl_lessbran_les_ix` (`lessonid`),
  KEY `mdl_lessbran_pag_ix` (`pageid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='branches for each lesson/user';


CREATE TABLE `mdl_lesson_grades` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `lessonid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `grade` double NOT NULL DEFAULT '0',
  `late` smallint NOT NULL DEFAULT '0',
  `completed` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_lessgrad_use_ix` (`userid`),
  KEY `mdl_lessgrad_les_ix` (`lessonid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines lesson_grades';


CREATE TABLE `mdl_lesson_overrides` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `lessonid` bigint NOT NULL DEFAULT '0',
  `groupid` bigint DEFAULT NULL,
  `userid` bigint DEFAULT NULL,
  `available` bigint DEFAULT NULL,
  `deadline` bigint DEFAULT NULL,
  `timelimit` bigint DEFAULT NULL,
  `review` smallint DEFAULT NULL,
  `maxattempts` smallint DEFAULT NULL,
  `retake` smallint DEFAULT NULL,
  `password` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_lessover_les_ix` (`lessonid`),
  KEY `mdl_lessover_gro_ix` (`groupid`),
  KEY `mdl_lessover_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='The overrides to lesson settings.';


CREATE TABLE `mdl_lesson_pages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `lessonid` bigint NOT NULL DEFAULT '0',
  `prevpageid` bigint NOT NULL DEFAULT '0',
  `nextpageid` bigint NOT NULL DEFAULT '0',
  `qtype` smallint NOT NULL DEFAULT '0',
  `qoption` smallint NOT NULL DEFAULT '0',
  `layout` smallint NOT NULL DEFAULT '1',
  `display` smallint NOT NULL DEFAULT '1',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `contents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `contentsformat` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_lesspage_les_ix` (`lessonid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines lesson_pages';


CREATE TABLE `mdl_lesson_timer` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `lessonid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `starttime` bigint NOT NULL DEFAULT '0',
  `lessontime` bigint NOT NULL DEFAULT '0',
  `completed` tinyint(1) DEFAULT '0',
  `timemodifiedoffline` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_lesstime_use_ix` (`userid`),
  KEY `mdl_lesstime_les_ix` (`lessonid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='lesson timer for each lesson';


CREATE TABLE `mdl_license` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `shortname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `fullname` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `version` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='store licenses used by moodle';


CREATE TABLE `mdl_lock_db` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `resourcekey` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `expires` bigint DEFAULT NULL,
  `owner` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_lockdb_res_uix` (`resourcekey`),
  KEY `mdl_lockdb_exp_ix` (`expires`),
  KEY `mdl_lockdb_own_ix` (`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores active and inactive lock types for db locking method.';


CREATE TABLE `mdl_log` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `time` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `course` bigint NOT NULL DEFAULT '0',
  `module` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `cmid` bigint NOT NULL DEFAULT '0',
  `action` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_log_coumodact_ix` (`course`,`module`,`action`),
  KEY `mdl_log_tim_ix` (`time`),
  KEY `mdl_log_act_ix` (`action`),
  KEY `mdl_log_usecou_ix` (`userid`,`course`),
  KEY `mdl_log_cmi_ix` (`cmid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Every action is logged as far as possible';


CREATE TABLE `mdl_log_display` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `module` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `action` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `mtable` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `field` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_logdisp_modact_uix` (`module`,`action`)
) ENGINE=InnoDB AUTO_INCREMENT=190 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='For a particular module/action, specifies a moodle table/fie';


CREATE TABLE `mdl_log_queries` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `qtype` mediumint NOT NULL,
  `sqltext` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `sqlparams` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `error` mediumint NOT NULL DEFAULT '0',
  `info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `backtrace` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `exectime` decimal(10,5) NOT NULL,
  `timelogged` bigint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Logged database queries.';


CREATE TABLE `mdl_logstore_standard_log` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `eventname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `action` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `target` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `objecttable` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `objectid` bigint DEFAULT NULL,
  `crud` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `edulevel` tinyint(1) NOT NULL,
  `contextid` bigint NOT NULL,
  `contextlevel` bigint NOT NULL,
  `contextinstanceid` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `courseid` bigint DEFAULT NULL,
  `relateduserid` bigint DEFAULT NULL,
  `anonymous` tinyint(1) NOT NULL DEFAULT '0',
  `other` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timecreated` bigint NOT NULL,
  `origin` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `realuserid` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_logsstanlog_tim_ix` (`timecreated`),
  KEY `mdl_logsstanlog_couanotim_ix` (`courseid`,`anonymous`,`timecreated`),
  KEY `mdl_logsstanlog_useconconcr_ix` (`userid`,`contextlevel`,`contextinstanceid`,`crud`,`edulevel`,`timecreated`),
  KEY `mdl_logsstanlog_con_ix` (`contextid`)
) ENGINE=InnoDB AUTO_INCREMENT=1164 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Standard log table';


CREATE TABLE `mdl_lti` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `introformat` smallint DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `typeid` bigint DEFAULT NULL,
  `toolurl` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `securetoolurl` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `instructorchoicesendname` tinyint(1) DEFAULT NULL,
  `instructorchoicesendemailaddr` tinyint(1) DEFAULT NULL,
  `instructorchoiceallowroster` tinyint(1) DEFAULT NULL,
  `instructorchoiceallowsetting` tinyint(1) DEFAULT NULL,
  `instructorcustomparameters` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `instructorchoiceacceptgrades` tinyint(1) DEFAULT NULL,
  `grade` bigint NOT NULL DEFAULT '100',
  `launchcontainer` tinyint NOT NULL DEFAULT '1',
  `resourcekey` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `debuglaunch` tinyint(1) NOT NULL DEFAULT '0',
  `showtitlelaunch` tinyint(1) NOT NULL DEFAULT '0',
  `showdescriptionlaunch` tinyint(1) NOT NULL DEFAULT '0',
  `servicesalt` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `icon` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `secureicon` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  KEY `mdl_lti_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table contains Basic LTI activities instances';


CREATE TABLE `mdl_lti_submission` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `ltiid` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `datesubmitted` bigint NOT NULL,
  `dateupdated` bigint NOT NULL,
  `gradepercent` decimal(10,5) NOT NULL,
  `originalgrade` decimal(10,5) NOT NULL,
  `launchid` bigint NOT NULL,
  `state` tinyint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_ltisubm_lti_ix` (`ltiid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Keeps track of individual submissions for LTI activities.';


CREATE TABLE `mdl_lti_tool_proxies` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Tool Provider',
  `regurl` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `state` tinyint NOT NULL DEFAULT '1',
  `guid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `vendorcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `capabilityoffered` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `serviceoffered` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `toolproxy` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `createdby` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_ltitoolprox_gui_uix` (`guid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='LTI tool proxy registrations';


CREATE TABLE `mdl_lti_tool_settings` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `toolproxyid` bigint NOT NULL,
  `course` bigint DEFAULT NULL,
  `coursemoduleid` bigint DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_ltitoolsett_too_ix` (`toolproxyid`),
  KEY `mdl_ltitoolsett_cou_ix` (`course`),
  KEY `mdl_ltitoolsett_cou2_ix` (`coursemoduleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='LTI tool setting values';


CREATE TABLE `mdl_lti_types` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'basiclti Activity',
  `baseurl` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tooldomain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `state` tinyint NOT NULL DEFAULT '2',
  `course` bigint NOT NULL,
  `coursevisible` tinyint(1) NOT NULL DEFAULT '0',
  `toolproxyid` bigint DEFAULT NULL,
  `enabledcapability` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `parameter` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `icon` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `secureicon` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `createdby` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  KEY `mdl_ltitype_cou_ix` (`course`),
  KEY `mdl_ltitype_too_ix` (`tooldomain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Basic LTI pre-configured activities';


CREATE TABLE `mdl_lti_types_config` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `typeid` bigint NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_ltitypeconf_typ_ix` (`typeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Basic LTI types configuration';


CREATE TABLE `mdl_ltiservice_gradebookservices` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `gradeitemid` bigint NOT NULL,
  `courseid` bigint NOT NULL,
  `toolproxyid` bigint DEFAULT NULL,
  `typeid` bigint DEFAULT NULL,
  `baseurl` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `ltilinkid` bigint DEFAULT NULL,
  `tag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_ltisgrad_lti_ix` (`ltilinkid`),
  KEY `mdl_ltisgrad_gracou_ix` (`gradeitemid`,`courseid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This file records the grade items created by the LTI Gradebo';


CREATE TABLE `mdl_message` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `useridfrom` bigint NOT NULL DEFAULT '0',
  `useridto` bigint NOT NULL DEFAULT '0',
  `subject` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `fullmessage` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `fullmessageformat` smallint DEFAULT '0',
  `fullmessagehtml` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `smallmessage` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `notification` tinyint(1) DEFAULT '0',
  `contexturl` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `contexturlname` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timeuserfromdeleted` bigint NOT NULL DEFAULT '0',
  `timeusertodeleted` bigint NOT NULL DEFAULT '0',
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `eventtype` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_mess_useusetimtim_ix` (`useridfrom`,`useridto`,`timeuserfromdeleted`,`timeusertodeleted`),
  KEY `mdl_mess_usetimnot_ix` (`useridfrom`,`timeuserfromdeleted`,`notification`),
  KEY `mdl_mess_usetimnot2_ix` (`useridto`,`timeusertodeleted`,`notification`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores all unread messages';


CREATE TABLE `mdl_message_airnotifier_devices` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userdeviceid` bigint NOT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_messairndevi_use_uix` (`userdeviceid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Store information about the devices registered in Airnotifie';


CREATE TABLE `mdl_message_contacts` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `contactid` bigint NOT NULL DEFAULT '0',
  `blocked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_messcont_usecon_uix` (`userid`,`contactid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Maintains lists of relationships between users';


CREATE TABLE `mdl_message_conversation_members` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `conversationid` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_messconvmemb_con_ix` (`conversationid`),
  KEY `mdl_messconvmemb_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores all members in a conversations';


CREATE TABLE `mdl_message_conversations` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `convhash` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timecreated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_messconv_con_uix` (`convhash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores all message conversations';


CREATE TABLE `mdl_message_popup` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `messageid` bigint NOT NULL,
  `isread` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_messpopu_mesisr_uix` (`messageid`,`isread`),
  KEY `mdl_messpopu_isr_ix` (`isread`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Keep state of notifications for the popup message processor';


CREATE TABLE `mdl_message_popup_notifications` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `notificationid` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_messpopunoti_not_ix` (`notificationid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='List of notifications to display in the message output popup';


CREATE TABLE `mdl_message_processors` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(166) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='List of message output plugins';


CREATE TABLE `mdl_message_providers` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `component` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `capability` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_messprov_comnam_uix` (`component`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table stores the message providers (modules and core sy';


CREATE TABLE `mdl_message_read` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `useridfrom` bigint NOT NULL DEFAULT '0',
  `useridto` bigint NOT NULL DEFAULT '0',
  `subject` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `fullmessage` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `fullmessageformat` smallint DEFAULT '0',
  `fullmessagehtml` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `smallmessage` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `notification` tinyint(1) DEFAULT '0',
  `contexturl` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `contexturlname` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timeread` bigint NOT NULL DEFAULT '0',
  `timeuserfromdeleted` bigint NOT NULL DEFAULT '0',
  `timeusertodeleted` bigint NOT NULL DEFAULT '0',
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `eventtype` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_messread_useusetimtim_ix` (`useridfrom`,`useridto`,`timeuserfromdeleted`,`timeusertodeleted`),
  KEY `mdl_messread_nottim_ix` (`notification`,`timeread`),
  KEY `mdl_messread_usetimnot_ix` (`useridfrom`,`timeuserfromdeleted`,`notification`),
  KEY `mdl_messread_usetimnot2_ix` (`useridto`,`timeusertodeleted`,`notification`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores all messages that have been read';


CREATE TABLE `mdl_message_user_actions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL,
  `messageid` bigint NOT NULL,
  `action` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_messuseracti_usemesact_uix` (`userid`,`messageid`,`action`),
  KEY `mdl_messuseracti_use_ix` (`userid`),
  KEY `mdl_messuseracti_mes_ix` (`messageid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores all per-user actions on individual messages';


CREATE TABLE `mdl_messageinbound_datakeys` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `handler` bigint NOT NULL,
  `datavalue` bigint NOT NULL,
  `datakey` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `timecreated` bigint NOT NULL,
  `expires` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_messdata_handat_uix` (`handler`,`datavalue`),
  KEY `mdl_messdata_han_ix` (`handler`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Inbound Message data item secret keys.';


CREATE TABLE `mdl_messageinbound_handlers` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `classname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `defaultexpiration` bigint NOT NULL DEFAULT '86400',
  `validateaddress` tinyint(1) NOT NULL DEFAULT '1',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_messhand_cla_uix` (`classname`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Inbound Message Handler definitions.';


CREATE TABLE `mdl_messageinbound_messagelist` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `messageid` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `userid` bigint NOT NULL,
  `address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `timecreated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_messmess_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='A list of message IDs for existing replies';


CREATE TABLE `mdl_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `useridfrom` bigint NOT NULL,
  `conversationid` bigint NOT NULL,
  `subject` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `fullmessage` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `fullmessageformat` tinyint(1) NOT NULL DEFAULT '0',
  `fullmessagehtml` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `smallmessage` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timecreated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_mess_contim_ix` (`conversationid`,`timecreated`),
  KEY `mdl_mess_use_ix` (`useridfrom`),
  KEY `mdl_mess_con_ix` (`conversationid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores all messages';


CREATE TABLE `mdl_mnet_application` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `display_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `xmlrpc_server_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `sso_land_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `sso_jump_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Information about applications on remote hosts';


CREATE TABLE `mdl_mnet_host` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `wwwroot` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `name` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `public_key` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `public_key_expires` bigint NOT NULL DEFAULT '0',
  `transport` tinyint NOT NULL DEFAULT '0',
  `portno` mediumint NOT NULL DEFAULT '0',
  `last_connect_time` bigint NOT NULL DEFAULT '0',
  `last_log_id` bigint NOT NULL DEFAULT '0',
  `force_theme` tinyint(1) NOT NULL DEFAULT '0',
  `theme` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `applicationid` bigint NOT NULL DEFAULT '1',
  `sslverification` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_mnethost_app_ix` (`applicationid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Information about the local and remote hosts for RPC';


CREATE TABLE `mdl_mnet_host2service` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `hostid` bigint NOT NULL DEFAULT '0',
  `serviceid` bigint NOT NULL DEFAULT '0',
  `publish` tinyint(1) NOT NULL DEFAULT '0',
  `subscribe` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_mnethost_hosser_uix` (`hostid`,`serviceid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Information about the services for a given host';


CREATE TABLE `mdl_mnet_log` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `hostid` bigint NOT NULL DEFAULT '0',
  `remoteid` bigint NOT NULL DEFAULT '0',
  `time` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `course` bigint NOT NULL DEFAULT '0',
  `coursename` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `module` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `cmid` bigint NOT NULL DEFAULT '0',
  `action` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_mnetlog_hosusecou_ix` (`hostid`,`userid`,`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Store session data from users migrating to other sites';


CREATE TABLE `mdl_mnet_remote_rpc` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `functionname` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `xmlrpcpath` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `plugintype` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `pluginname` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table describes functions that might be called remotely';


CREATE TABLE `mdl_mnet_remote_service2rpc` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `serviceid` bigint NOT NULL DEFAULT '0',
  `rpcid` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_mnetremoserv_rpcser_uix` (`rpcid`,`serviceid`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Group functions or methods under a service';


CREATE TABLE `mdl_mnet_rpc` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `functionname` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `xmlrpcpath` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `plugintype` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `pluginname` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `help` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `profile` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `filename` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `classname` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `static` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_mnetrpc_enaxml_ix` (`enabled`,`xmlrpcpath`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Functions or methods that we may publish or subscribe to';


CREATE TABLE `mdl_mnet_service` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `description` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `apiversion` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `offer` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='A service is a group of functions';


CREATE TABLE `mdl_mnet_service2rpc` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `serviceid` bigint NOT NULL DEFAULT '0',
  `rpcid` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_mnetserv_rpcser_uix` (`rpcid`,`serviceid`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Group functions or methods under a service';


CREATE TABLE `mdl_mnet_session` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `token` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `mnethostid` bigint NOT NULL DEFAULT '0',
  `useragent` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `confirm_timeout` bigint NOT NULL DEFAULT '0',
  `session_id` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `expires` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_mnetsess_tok_uix` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Store session data from users migrating to other sites';


CREATE TABLE `mdl_mnet_sso_access_control` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `mnet_host_id` bigint NOT NULL DEFAULT '0',
  `accessctrl` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'allow',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_mnetssoaccecont_mneuse_uix` (`mnet_host_id`,`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Users by host permitted (or not) to login from a remote prov';


CREATE TABLE `mdl_mnetservice_enrol_courses` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `hostid` bigint NOT NULL,
  `remoteid` bigint NOT NULL,
  `categoryid` bigint NOT NULL,
  `categoryname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `sortorder` bigint NOT NULL DEFAULT '0',
  `fullname` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `shortname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `idnumber` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `summaryformat` smallint DEFAULT '0',
  `startdate` bigint NOT NULL,
  `roleid` bigint NOT NULL,
  `rolename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_mnetenrocour_hosrem_uix` (`hostid`,`remoteid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Caches the information fetched via XML-RPC about courses on ';


CREATE TABLE `mdl_mnetservice_enrol_enrolments` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `hostid` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `remotecourseid` bigint NOT NULL,
  `rolename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `enroltime` bigint NOT NULL DEFAULT '0',
  `enroltype` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_mnetenroenro_use_ix` (`userid`),
  KEY `mdl_mnetenroenro_hos_ix` (`hostid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Caches the information about enrolments of our local users i';


CREATE TABLE `mdl_modules` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `cron` bigint NOT NULL DEFAULT '0',
  `lastcron` bigint NOT NULL DEFAULT '0',
  `search` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `mdl_modu_nam_ix` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='modules available in the site';


CREATE TABLE `mdl_my_pages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint DEFAULT '0',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `private` tinyint(1) NOT NULL DEFAULT '1',
  `sortorder` mediumint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_mypage_usepri_ix` (`userid`,`private`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Extra user pages for the My Moodle system';


CREATE TABLE `mdl_notifications` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `useridfrom` bigint NOT NULL,
  `useridto` bigint NOT NULL,
  `subject` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `fullmessage` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `fullmessageformat` tinyint(1) NOT NULL DEFAULT '0',
  `fullmessagehtml` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `smallmessage` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `eventtype` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `contexturl` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `contexturlname` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timeread` bigint DEFAULT NULL,
  `timecreated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_noti_use_ix` (`useridfrom`),
  KEY `mdl_noti_use2_ix` (`useridto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores all notifications';


CREATE TABLE `mdl_oauth2_endpoint` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `usermodified` bigint NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `url` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `issuerid` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_oautendp_iss_ix` (`issuerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Describes the named endpoint for an oauth2 service.';


CREATE TABLE `mdl_oauth2_issuer` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `usermodified` bigint NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `baseurl` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `clientid` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `clientsecret` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `loginscopes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `loginscopesoffline` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `loginparams` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `loginparamsoffline` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `alloweddomains` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `scopessupported` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `enabled` tinyint NOT NULL DEFAULT '1',
  `showonloginpage` tinyint NOT NULL DEFAULT '1',
  `basicauth` tinyint NOT NULL DEFAULT '0',
  `sortorder` bigint NOT NULL,
  `requireconfirmation` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Details for an oauth 2 connect identity issuer.';


CREATE TABLE `mdl_oauth2_system_account` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `usermodified` bigint NOT NULL,
  `issuerid` bigint NOT NULL,
  `refreshtoken` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `grantedscopes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `username` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_oautsystacco_iss_uix` (`issuerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stored details used to get an access token as a system user ';


CREATE TABLE `mdl_oauth2_user_field_mapping` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `timemodified` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `usermodified` bigint NOT NULL,
  `issuerid` bigint NOT NULL,
  `externalfield` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `internalfield` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_oautuserfielmapp_issin_uix` (`issuerid`,`internalfield`),
  KEY `mdl_oautuserfielmapp_iss_ix` (`issuerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Mapping of oauth user fields to moodle fields.';


CREATE TABLE `mdl_page` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `introformat` smallint NOT NULL DEFAULT '0',
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `contentformat` smallint NOT NULL DEFAULT '0',
  `legacyfiles` smallint NOT NULL DEFAULT '0',
  `legacyfileslast` bigint DEFAULT NULL,
  `display` smallint NOT NULL DEFAULT '0',
  `displayoptions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `revision` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_page_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Each record is one page and its config data';


CREATE TABLE `mdl_portfolio_instance` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `plugin` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='base table (not including config data) for instances of port';


CREATE TABLE `mdl_portfolio_instance_config` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `instance` bigint NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  KEY `mdl_portinstconf_nam_ix` (`name`),
  KEY `mdl_portinstconf_ins_ix` (`instance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='config for portfolio plugin instances';


CREATE TABLE `mdl_portfolio_instance_user` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `instance` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  KEY `mdl_portinstuser_ins_ix` (`instance`),
  KEY `mdl_portinstuser_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='user data for portfolio instances.';


CREATE TABLE `mdl_portfolio_log` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL,
  `time` bigint NOT NULL,
  `portfolio` bigint NOT NULL,
  `caller_class` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `caller_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `caller_component` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `caller_sha1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `tempdataid` bigint NOT NULL DEFAULT '0',
  `returnurl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `continueurl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_portlog_use_ix` (`userid`),
  KEY `mdl_portlog_por_ix` (`portfolio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='log of portfolio transfers (used to later check for duplicat';


CREATE TABLE `mdl_portfolio_mahara_queue` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `transferid` bigint NOT NULL,
  `token` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_portmahaqueu_tok_ix` (`token`),
  KEY `mdl_portmahaqueu_tra_ix` (`transferid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='maps mahara tokens to transfer ids';


CREATE TABLE `mdl_portfolio_tempdata` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `expirytime` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `instance` bigint DEFAULT '0',
  `queued` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_porttemp_use_ix` (`userid`),
  KEY `mdl_porttemp_ins_ix` (`instance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='stores temporary data for portfolio exports. the id of this ';


CREATE TABLE `mdl_post` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `module` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `userid` bigint NOT NULL DEFAULT '0',
  `courseid` bigint NOT NULL DEFAULT '0',
  `groupid` bigint NOT NULL DEFAULT '0',
  `moduleid` bigint NOT NULL DEFAULT '0',
  `coursemoduleid` bigint NOT NULL DEFAULT '0',
  `subject` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `uniquehash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `rating` bigint NOT NULL DEFAULT '0',
  `format` bigint NOT NULL DEFAULT '0',
  `summaryformat` tinyint NOT NULL DEFAULT '0',
  `attachment` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `publishstate` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'draft',
  `lastmodified` bigint NOT NULL DEFAULT '0',
  `created` bigint NOT NULL DEFAULT '0',
  `usermodified` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_post_iduse_uix` (`id`,`userid`),
  KEY `mdl_post_las_ix` (`lastmodified`),
  KEY `mdl_post_mod_ix` (`module`),
  KEY `mdl_post_sub_ix` (`subject`),
  KEY `mdl_post_use_ix` (`usermodified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Generic post table to hold data blog entries etc in differen';


CREATE TABLE `mdl_profiling` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `runid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `totalexecutiontime` bigint NOT NULL,
  `totalcputime` bigint NOT NULL,
  `totalcalls` bigint NOT NULL,
  `totalmemory` bigint NOT NULL,
  `runreference` tinyint NOT NULL DEFAULT '0',
  `runcomment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timecreated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_prof_run_uix` (`runid`),
  KEY `mdl_prof_urlrun_ix` (`url`,`runreference`),
  KEY `mdl_prof_timrun_ix` (`timecreated`,`runreference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores the results of all the profiling runs';


CREATE TABLE `mdl_qtype_ddimageortext` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `questionid` bigint NOT NULL DEFAULT '0',
  `shuffleanswers` smallint NOT NULL DEFAULT '1',
  `correctfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `correctfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `partiallycorrectfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `partiallycorrectfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `incorrectfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `incorrectfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `shownumcorrect` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_qtypddim_que_ix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines drag and drop (text or images onto a background imag';


CREATE TABLE `mdl_qtype_ddimageortext_drags` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `questionid` bigint NOT NULL DEFAULT '0',
  `no` bigint NOT NULL DEFAULT '0',
  `draggroup` bigint NOT NULL DEFAULT '0',
  `infinite` smallint NOT NULL DEFAULT '0',
  `label` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_qtypddimdrag_que_ix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Images to drag. Actual file names are not stored here we use';


CREATE TABLE `mdl_qtype_ddimageortext_drops` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `questionid` bigint NOT NULL DEFAULT '0',
  `no` bigint NOT NULL DEFAULT '0',
  `xleft` bigint NOT NULL DEFAULT '0',
  `ytop` bigint NOT NULL DEFAULT '0',
  `choice` bigint NOT NULL DEFAULT '0',
  `label` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_qtypddimdrop_que_ix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Drop boxes';


CREATE TABLE `mdl_qtype_ddmarker` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `questionid` bigint NOT NULL DEFAULT '0',
  `shuffleanswers` smallint NOT NULL DEFAULT '1',
  `correctfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `correctfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `partiallycorrectfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `partiallycorrectfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `incorrectfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `incorrectfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `shownumcorrect` tinyint NOT NULL DEFAULT '0',
  `showmisplaced` smallint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_qtypddma_que_ix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines drag and drop (text or images onto a background imag';


CREATE TABLE `mdl_qtype_ddmarker_drags` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `questionid` bigint NOT NULL DEFAULT '0',
  `no` bigint NOT NULL DEFAULT '0',
  `label` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `infinite` smallint NOT NULL DEFAULT '0',
  `noofdrags` bigint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `mdl_qtypddmadrag_que_ix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Labels for markers to drag.';


CREATE TABLE `mdl_qtype_ddmarker_drops` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `questionid` bigint NOT NULL DEFAULT '0',
  `no` bigint NOT NULL DEFAULT '0',
  `shape` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `coords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `choice` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_qtypddmadrop_que_ix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='drop regions';


CREATE TABLE `mdl_qtype_essay_options` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `questionid` bigint NOT NULL,
  `responseformat` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'editor',
  `responserequired` tinyint NOT NULL DEFAULT '1',
  `responsefieldlines` smallint NOT NULL DEFAULT '15',
  `attachments` smallint NOT NULL DEFAULT '0',
  `attachmentsrequired` smallint NOT NULL DEFAULT '0',
  `graderinfo` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `graderinfoformat` smallint NOT NULL DEFAULT '0',
  `responsetemplate` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `responsetemplateformat` smallint NOT NULL DEFAULT '0',
  `filetypeslist` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_qtypessaopti_que_uix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Extra options for essay questions.';


CREATE TABLE `mdl_qtype_match_options` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `questionid` bigint NOT NULL DEFAULT '0',
  `shuffleanswers` smallint NOT NULL DEFAULT '1',
  `correctfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `correctfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `partiallycorrectfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `partiallycorrectfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `incorrectfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `incorrectfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `shownumcorrect` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_qtypmatcopti_que_uix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines the question-type specific options for matching ques';


CREATE TABLE `mdl_qtype_match_subquestions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `questionid` bigint NOT NULL DEFAULT '0',
  `questiontext` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `questiontextformat` tinyint NOT NULL DEFAULT '0',
  `answertext` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_qtypmatcsubq_que_ix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='The subquestions that make up a matching question';


CREATE TABLE `mdl_qtype_multichoice_options` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `questionid` bigint NOT NULL DEFAULT '0',
  `layout` smallint NOT NULL DEFAULT '0',
  `single` smallint NOT NULL DEFAULT '0',
  `shuffleanswers` smallint NOT NULL DEFAULT '1',
  `correctfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `correctfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `partiallycorrectfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `partiallycorrectfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `incorrectfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `incorrectfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `answernumbering` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'abc',
  `shownumcorrect` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_qtypmultopti_que_uix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Options for multiple choice questions';


CREATE TABLE `mdl_qtype_randomsamatch_options` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `questionid` bigint NOT NULL DEFAULT '0',
  `choose` bigint NOT NULL DEFAULT '4',
  `subcats` tinyint NOT NULL DEFAULT '1',
  `correctfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `correctfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `partiallycorrectfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `partiallycorrectfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `incorrectfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `incorrectfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `shownumcorrect` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_qtyprandopti_que_uix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Info about a random short-answer matching question';


CREATE TABLE `mdl_qtype_shortanswer_options` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `questionid` bigint NOT NULL DEFAULT '0',
  `usecase` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_qtypshoropti_que_uix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Options for short answer questions';


CREATE TABLE `mdl_question` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `category` bigint NOT NULL DEFAULT '0',
  `parent` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `questiontext` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `questiontextformat` tinyint NOT NULL DEFAULT '0',
  `generalfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `generalfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `defaultmark` decimal(12,7) NOT NULL DEFAULT '1.0000000',
  `penalty` decimal(12,7) NOT NULL DEFAULT '0.3333333',
  `qtype` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `length` bigint NOT NULL DEFAULT '1',
  `stamp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `createdby` bigint DEFAULT NULL,
  `modifiedby` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_ques_qty_ix` (`qtype`),
  KEY `mdl_ques_cat_ix` (`category`),
  KEY `mdl_ques_par_ix` (`parent`),
  KEY `mdl_ques_cre_ix` (`createdby`),
  KEY `mdl_ques_mod_ix` (`modifiedby`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='The questions themselves';


CREATE TABLE `mdl_question_answers` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `question` bigint NOT NULL DEFAULT '0',
  `answer` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `answerformat` tinyint NOT NULL DEFAULT '0',
  `fraction` decimal(12,7) NOT NULL DEFAULT '0.0000000',
  `feedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `feedbackformat` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_quesansw_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Answers, with a fractional grade (0-1) and feedback';


CREATE TABLE `mdl_question_attempt_step_data` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `attemptstepid` bigint NOT NULL,
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  KEY `mdl_quesattestepdata_att_ix` (`attemptstepid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Each question_attempt_step has an associative array of the d';


CREATE TABLE `mdl_question_attempt_steps` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `questionattemptid` bigint NOT NULL,
  `sequencenumber` bigint NOT NULL,
  `state` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `fraction` decimal(12,7) DEFAULT NULL,
  `timecreated` bigint NOT NULL,
  `userid` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_quesattestep_queseq_uix` (`questionattemptid`,`sequencenumber`),
  KEY `mdl_quesattestep_que_ix` (`questionattemptid`),
  KEY `mdl_quesattestep_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores one step in in a question attempt. As well as the dat';


CREATE TABLE `mdl_question_attempts` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `questionusageid` bigint NOT NULL,
  `slot` bigint NOT NULL,
  `behaviour` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `questionid` bigint NOT NULL,
  `variant` bigint NOT NULL DEFAULT '1',
  `maxmark` decimal(12,7) NOT NULL,
  `minfraction` decimal(12,7) NOT NULL,
  `maxfraction` decimal(12,7) NOT NULL DEFAULT '1.0000000',
  `flagged` tinyint(1) NOT NULL DEFAULT '0',
  `questionsummary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `rightanswer` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `responsesummary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timemodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_quesatte_queslo_uix` (`questionusageid`,`slot`),
  KEY `mdl_quesatte_beh_ix` (`behaviour`),
  KEY `mdl_quesatte_que_ix` (`questionid`),
  KEY `mdl_quesatte_que2_ix` (`questionusageid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Each row here corresponds to an attempt at one question, as ';


CREATE TABLE `mdl_question_calculated` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `question` bigint NOT NULL DEFAULT '0',
  `answer` bigint NOT NULL DEFAULT '0',
  `tolerance` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '0.0',
  `tolerancetype` bigint NOT NULL DEFAULT '1',
  `correctanswerlength` bigint NOT NULL DEFAULT '2',
  `correctanswerformat` bigint NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `mdl_quescalc_ans_ix` (`answer`),
  KEY `mdl_quescalc_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Options for questions of type calculated';


CREATE TABLE `mdl_question_calculated_options` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `question` bigint NOT NULL DEFAULT '0',
  `synchronize` tinyint NOT NULL DEFAULT '0',
  `single` smallint NOT NULL DEFAULT '0',
  `shuffleanswers` smallint NOT NULL DEFAULT '0',
  `correctfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `correctfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `partiallycorrectfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `partiallycorrectfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `incorrectfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `incorrectfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `answernumbering` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'abc',
  `shownumcorrect` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_quescalcopti_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Options for questions of type calculated';


CREATE TABLE `mdl_question_categories` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `contextid` bigint NOT NULL DEFAULT '0',
  `info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `infoformat` tinyint NOT NULL DEFAULT '0',
  `stamp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `parent` bigint NOT NULL DEFAULT '0',
  `sortorder` bigint NOT NULL DEFAULT '999',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_quescate_consta_uix` (`contextid`,`stamp`),
  KEY `mdl_quescate_con_ix` (`contextid`),
  KEY `mdl_quescate_par_ix` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Categories are for grouping questions';


CREATE TABLE `mdl_question_dataset_definitions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `category` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `type` bigint NOT NULL DEFAULT '0',
  `options` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `itemcount` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_quesdatadefi_cat_ix` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Organises and stores properties for dataset items';


CREATE TABLE `mdl_question_dataset_items` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `definition` bigint NOT NULL DEFAULT '0',
  `itemnumber` bigint NOT NULL DEFAULT '0',
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_quesdataitem_def_ix` (`definition`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Individual dataset items';


CREATE TABLE `mdl_question_datasets` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `question` bigint NOT NULL DEFAULT '0',
  `datasetdefinition` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_quesdata_quedat_ix` (`question`,`datasetdefinition`),
  KEY `mdl_quesdata_que_ix` (`question`),
  KEY `mdl_quesdata_dat_ix` (`datasetdefinition`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Many-many relation between questions and dataset definitions';


CREATE TABLE `mdl_question_ddwtos` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `questionid` bigint NOT NULL DEFAULT '0',
  `shuffleanswers` smallint NOT NULL DEFAULT '1',
  `correctfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `correctfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `partiallycorrectfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `partiallycorrectfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `incorrectfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `incorrectfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `shownumcorrect` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_quesddwt_que_ix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines drag and drop (words into sentences) questions';


CREATE TABLE `mdl_question_gapselect` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `questionid` bigint NOT NULL DEFAULT '0',
  `shuffleanswers` smallint NOT NULL DEFAULT '1',
  `correctfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `correctfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `partiallycorrectfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `partiallycorrectfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `incorrectfeedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `incorrectfeedbackformat` tinyint NOT NULL DEFAULT '0',
  `shownumcorrect` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_quesgaps_que_ix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines select missing words questions';


CREATE TABLE `mdl_question_hints` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `questionid` bigint NOT NULL,
  `hint` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `hintformat` smallint NOT NULL DEFAULT '0',
  `shownumcorrect` tinyint(1) DEFAULT NULL,
  `clearwrong` tinyint(1) DEFAULT NULL,
  `options` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_queshint_que_ix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores the the part of the question definition that gives di';


CREATE TABLE `mdl_question_multianswer` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `question` bigint NOT NULL DEFAULT '0',
  `sequence` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_quesmult_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Options for multianswer questions';


CREATE TABLE `mdl_question_numerical` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `question` bigint NOT NULL DEFAULT '0',
  `answer` bigint NOT NULL DEFAULT '0',
  `tolerance` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '0.0',
  PRIMARY KEY (`id`),
  KEY `mdl_quesnume_ans_ix` (`answer`),
  KEY `mdl_quesnume_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Options for numerical questions.';


CREATE TABLE `mdl_question_numerical_options` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `question` bigint NOT NULL DEFAULT '0',
  `showunits` smallint NOT NULL DEFAULT '0',
  `unitsleft` smallint NOT NULL DEFAULT '0',
  `unitgradingtype` smallint NOT NULL DEFAULT '0',
  `unitpenalty` decimal(12,7) NOT NULL DEFAULT '0.1000000',
  PRIMARY KEY (`id`),
  KEY `mdl_quesnumeopti_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Options for questions of type numerical This table is also u';


CREATE TABLE `mdl_question_numerical_units` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `question` bigint NOT NULL DEFAULT '0',
  `multiplier` decimal(38,19) NOT NULL DEFAULT '1.0000000000000000000',
  `unit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_quesnumeunit_queuni_uix` (`question`,`unit`),
  KEY `mdl_quesnumeunit_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Optional unit options for numerical questions. This table is';


CREATE TABLE `mdl_question_response_analysis` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `hashcode` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `whichtries` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timemodified` bigint NOT NULL,
  `questionid` bigint NOT NULL,
  `variant` bigint DEFAULT NULL,
  `subqid` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `aid` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `credit` decimal(15,5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Analysis of student responses given to questions.';


CREATE TABLE `mdl_question_response_count` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `analysisid` bigint NOT NULL,
  `try` bigint NOT NULL,
  `rcount` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_quesrespcoun_ana_ix` (`analysisid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Count for each responses for each try at a question.';


CREATE TABLE `mdl_question_statistics` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `hashcode` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timemodified` bigint NOT NULL,
  `questionid` bigint NOT NULL,
  `slot` bigint DEFAULT NULL,
  `subquestion` smallint NOT NULL,
  `variant` bigint DEFAULT NULL,
  `s` bigint NOT NULL DEFAULT '0',
  `effectiveweight` decimal(15,5) DEFAULT NULL,
  `negcovar` tinyint NOT NULL DEFAULT '0',
  `discriminationindex` decimal(15,5) DEFAULT NULL,
  `discriminativeefficiency` decimal(15,5) DEFAULT NULL,
  `sd` decimal(15,10) DEFAULT NULL,
  `facility` decimal(15,10) DEFAULT NULL,
  `subquestions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `maxmark` decimal(12,7) DEFAULT NULL,
  `positions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `randomguessscore` decimal(12,7) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Statistics for individual questions used in an activity.';


CREATE TABLE `mdl_question_truefalse` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `question` bigint NOT NULL DEFAULT '0',
  `trueanswer` bigint NOT NULL DEFAULT '0',
  `falseanswer` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_questrue_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Options for True-False questions';


CREATE TABLE `mdl_question_usages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `contextid` bigint NOT NULL,
  `component` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `preferredbehaviour` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_quesusag_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table''s main purpose it to assign a unique id to each a';


CREATE TABLE `mdl_quiz` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `introformat` smallint NOT NULL DEFAULT '0',
  `timeopen` bigint NOT NULL DEFAULT '0',
  `timeclose` bigint NOT NULL DEFAULT '0',
  `timelimit` bigint NOT NULL DEFAULT '0',
  `overduehandling` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'autoabandon',
  `graceperiod` bigint NOT NULL DEFAULT '0',
  `preferredbehaviour` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `canredoquestions` smallint NOT NULL DEFAULT '0',
  `attempts` mediumint NOT NULL DEFAULT '0',
  `attemptonlast` smallint NOT NULL DEFAULT '0',
  `grademethod` smallint NOT NULL DEFAULT '1',
  `decimalpoints` smallint NOT NULL DEFAULT '2',
  `questiondecimalpoints` smallint NOT NULL DEFAULT '-1',
  `reviewattempt` mediumint NOT NULL DEFAULT '0',
  `reviewcorrectness` mediumint NOT NULL DEFAULT '0',
  `reviewmarks` mediumint NOT NULL DEFAULT '0',
  `reviewspecificfeedback` mediumint NOT NULL DEFAULT '0',
  `reviewgeneralfeedback` mediumint NOT NULL DEFAULT '0',
  `reviewrightanswer` mediumint NOT NULL DEFAULT '0',
  `reviewoverallfeedback` mediumint NOT NULL DEFAULT '0',
  `questionsperpage` bigint NOT NULL DEFAULT '0',
  `navmethod` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'free',
  `shuffleanswers` smallint NOT NULL DEFAULT '0',
  `sumgrades` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `grade` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `subnet` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `browsersecurity` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `delay1` bigint NOT NULL DEFAULT '0',
  `delay2` bigint NOT NULL DEFAULT '0',
  `showuserpicture` smallint NOT NULL DEFAULT '0',
  `showblocks` smallint NOT NULL DEFAULT '0',
  `completionattemptsexhausted` tinyint(1) DEFAULT '0',
  `completionpass` tinyint(1) DEFAULT '0',
  `allowofflineattempts` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_quiz_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='The settings for each quiz.';


CREATE TABLE `mdl_quiz_attempts` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `quiz` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `attempt` mediumint NOT NULL DEFAULT '0',
  `uniqueid` bigint NOT NULL DEFAULT '0',
  `layout` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `currentpage` bigint NOT NULL DEFAULT '0',
  `preview` smallint NOT NULL DEFAULT '0',
  `state` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'inprogress',
  `timestart` bigint NOT NULL DEFAULT '0',
  `timefinish` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `timemodifiedoffline` bigint NOT NULL DEFAULT '0',
  `timecheckstate` bigint DEFAULT '0',
  `sumgrades` decimal(10,5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_quizatte_quiuseatt_uix` (`quiz`,`userid`,`attempt`),
  UNIQUE KEY `mdl_quizatte_uni_uix` (`uniqueid`),
  KEY `mdl_quizatte_statim_ix` (`state`,`timecheckstate`),
  KEY `mdl_quizatte_qui_ix` (`quiz`),
  KEY `mdl_quizatte_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores users attempts at quizzes.';


CREATE TABLE `mdl_quiz_feedback` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `quizid` bigint NOT NULL DEFAULT '0',
  `feedbacktext` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `feedbacktextformat` tinyint NOT NULL DEFAULT '0',
  `mingrade` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `maxgrade` decimal(10,5) NOT NULL DEFAULT '0.00000',
  PRIMARY KEY (`id`),
  KEY `mdl_quizfeed_qui_ix` (`quizid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Feedback given to students based on which grade band their o';


CREATE TABLE `mdl_quiz_grades` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `quiz` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `grade` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_quizgrad_use_ix` (`userid`),
  KEY `mdl_quizgrad_qui_ix` (`quiz`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores the overall grade for each user on the quiz, based on';


CREATE TABLE `mdl_quiz_overrides` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `quiz` bigint NOT NULL DEFAULT '0',
  `groupid` bigint DEFAULT NULL,
  `userid` bigint DEFAULT NULL,
  `timeopen` bigint DEFAULT NULL,
  `timeclose` bigint DEFAULT NULL,
  `timelimit` bigint DEFAULT NULL,
  `attempts` mediumint DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_quizover_qui_ix` (`quiz`),
  KEY `mdl_quizover_gro_ix` (`groupid`),
  KEY `mdl_quizover_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='The overrides to quiz settings on a per-user and per-group b';


CREATE TABLE `mdl_quiz_overview_regrades` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `questionusageid` bigint NOT NULL,
  `slot` bigint NOT NULL,
  `newfraction` decimal(12,7) DEFAULT NULL,
  `oldfraction` decimal(12,7) DEFAULT NULL,
  `regraded` smallint NOT NULL,
  `timemodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_quizoverregr_queslo_ix` (`questionusageid`,`slot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table records which question attempts need regrading an';


CREATE TABLE `mdl_quiz_reports` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `displayorder` bigint NOT NULL,
  `capability` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_quizrepo_nam_uix` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Lists all the installed quiz reports and their display order';


CREATE TABLE `mdl_quiz_sections` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `quizid` bigint NOT NULL,
  `firstslot` bigint NOT NULL,
  `heading` varchar(1333) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `shufflequestions` smallint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_quizsect_quifir_uix` (`quizid`,`firstslot`),
  KEY `mdl_quizsect_qui_ix` (`quizid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores sections of a quiz with section name (heading), from ';


CREATE TABLE `mdl_quiz_slot_tags` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `slotid` bigint DEFAULT NULL,
  `tagid` bigint DEFAULT NULL,
  `tagname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_quizslottags_slo_ix` (`slotid`),
  KEY `mdl_quizslottags_tag_ix` (`tagid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores data about the tags that a question must have so that';


CREATE TABLE `mdl_quiz_slots` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `slot` bigint NOT NULL,
  `quizid` bigint NOT NULL DEFAULT '0',
  `page` bigint NOT NULL,
  `requireprevious` smallint NOT NULL DEFAULT '0',
  `questionid` bigint NOT NULL DEFAULT '0',
  `questioncategoryid` bigint DEFAULT NULL,
  `includingsubcategories` smallint DEFAULT NULL,
  `maxmark` decimal(12,7) NOT NULL DEFAULT '0.0000000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_quizslot_quislo_uix` (`quizid`,`slot`),
  KEY `mdl_quizslot_qui_ix` (`quizid`),
  KEY `mdl_quizslot_que_ix` (`questionid`),
  KEY `mdl_quizslot_que2_ix` (`questioncategoryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores the question used in a quiz, with the order, and for ';


CREATE TABLE `mdl_quiz_statistics` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `hashcode` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `whichattempts` smallint NOT NULL,
  `timemodified` bigint NOT NULL,
  `firstattemptscount` bigint NOT NULL,
  `highestattemptscount` bigint NOT NULL,
  `lastattemptscount` bigint NOT NULL,
  `allattemptscount` bigint NOT NULL,
  `firstattemptsavg` decimal(15,5) DEFAULT NULL,
  `highestattemptsavg` decimal(15,5) DEFAULT NULL,
  `lastattemptsavg` decimal(15,5) DEFAULT NULL,
  `allattemptsavg` decimal(15,5) DEFAULT NULL,
  `median` decimal(15,5) DEFAULT NULL,
  `standarddeviation` decimal(15,5) DEFAULT NULL,
  `skewness` decimal(15,10) DEFAULT NULL,
  `kurtosis` decimal(15,5) DEFAULT NULL,
  `cic` decimal(15,10) DEFAULT NULL,
  `errorratio` decimal(15,10) DEFAULT NULL,
  `standarderror` decimal(15,10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='table to cache results from analysis done in statistics repo';


CREATE TABLE `mdl_rating` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `contextid` bigint NOT NULL,
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `ratingarea` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `itemid` bigint NOT NULL,
  `scaleid` bigint NOT NULL,
  `rating` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_rati_comratconite_ix` (`component`,`ratingarea`,`contextid`,`itemid`),
  KEY `mdl_rati_con_ix` (`contextid`),
  KEY `mdl_rati_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='moodle ratings';


CREATE TABLE `mdl_registration_hubs` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `hubname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `huburl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='hub where the site is registered on with their associated to';


CREATE TABLE `mdl_repository` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `visible` tinyint(1) DEFAULT '1',
  `sortorder` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table contains one entry for every configured external ';


CREATE TABLE `mdl_repository_instance_config` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `instanceid` bigint NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='The config for intances';


CREATE TABLE `mdl_repository_instances` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `typeid` bigint NOT NULL,
  `userid` bigint NOT NULL DEFAULT '0',
  `contextid` bigint NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `timecreated` bigint DEFAULT NULL,
  `timemodified` bigint DEFAULT NULL,
  `readonly` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table contains one entry for every configured external ';


CREATE TABLE `mdl_repository_onedrive_access` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `timemodified` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `usermodified` bigint NOT NULL,
  `permissionid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `itemid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_repoonedacce_use_ix` (`usermodified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='List of temporary access grants.';


CREATE TABLE `mdl_resource` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `introformat` smallint NOT NULL DEFAULT '0',
  `tobemigrated` smallint NOT NULL DEFAULT '0',
  `legacyfiles` smallint NOT NULL DEFAULT '0',
  `legacyfileslast` bigint DEFAULT NULL,
  `display` smallint NOT NULL DEFAULT '0',
  `displayoptions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `filterfiles` smallint NOT NULL DEFAULT '0',
  `revision` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_reso_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Each record is one resource and its config data';


CREATE TABLE `mdl_resource_old` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `introformat` smallint NOT NULL DEFAULT '0',
  `alltext` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `popup` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `options` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `oldid` bigint NOT NULL,
  `cmid` bigint DEFAULT NULL,
  `newmodule` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `newid` bigint DEFAULT NULL,
  `migrated` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_resoold_old_uix` (`oldid`),
  KEY `mdl_resoold_cmi_ix` (`cmid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='backup of all old resource instances from 1.9';


CREATE TABLE `mdl_role` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `shortname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `sortorder` bigint NOT NULL DEFAULT '0',
  `archetype` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_role_sor_uix` (`sortorder`),
  UNIQUE KEY `mdl_role_sho_uix` (`shortname`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='moodle roles';


CREATE TABLE `mdl_role_allow_assign` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `roleid` bigint NOT NULL DEFAULT '0',
  `allowassign` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_rolealloassi_rolall_uix` (`roleid`,`allowassign`),
  KEY `mdl_rolealloassi_rol_ix` (`roleid`),
  KEY `mdl_rolealloassi_all_ix` (`allowassign`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='this defines what role can assign what role';


CREATE TABLE `mdl_role_allow_override` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `roleid` bigint NOT NULL DEFAULT '0',
  `allowoverride` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_rolealloover_rolall_uix` (`roleid`,`allowoverride`),
  KEY `mdl_rolealloover_rol_ix` (`roleid`),
  KEY `mdl_rolealloover_all_ix` (`allowoverride`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='this defines what role can override what role';


CREATE TABLE `mdl_role_allow_switch` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `roleid` bigint NOT NULL,
  `allowswitch` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_rolealloswit_rolall_uix` (`roleid`,`allowswitch`),
  KEY `mdl_rolealloswit_rol_ix` (`roleid`),
  KEY `mdl_rolealloswit_all_ix` (`allowswitch`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table stores which which other roles a user is allowed ';


CREATE TABLE `mdl_role_allow_view` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `roleid` bigint NOT NULL,
  `allowview` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_rolealloview_rolall_uix` (`roleid`,`allowview`),
  KEY `mdl_rolealloview_rol_ix` (`roleid`),
  KEY `mdl_rolealloview_all_ix` (`allowview`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table stores which which other roles a user is allowed ';


CREATE TABLE `mdl_role_assignments` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `roleid` bigint NOT NULL DEFAULT '0',
  `contextid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `modifierid` bigint NOT NULL DEFAULT '0',
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `itemid` bigint NOT NULL DEFAULT '0',
  `sortorder` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_roleassi_sor_ix` (`sortorder`),
  KEY `mdl_roleassi_rolcon_ix` (`roleid`,`contextid`),
  KEY `mdl_roleassi_useconrol_ix` (`userid`,`contextid`,`roleid`),
  KEY `mdl_roleassi_comiteuse_ix` (`component`,`itemid`,`userid`),
  KEY `mdl_roleassi_rol_ix` (`roleid`),
  KEY `mdl_roleassi_con_ix` (`contextid`),
  KEY `mdl_roleassi_use_ix` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='assigning roles in different context';


CREATE TABLE `mdl_role_capabilities` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `contextid` bigint NOT NULL DEFAULT '0',
  `roleid` bigint NOT NULL DEFAULT '0',
  `capability` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `permission` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `modifierid` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_rolecapa_rolconcap_uix` (`roleid`,`contextid`,`capability`),
  KEY `mdl_rolecapa_rol_ix` (`roleid`),
  KEY `mdl_rolecapa_con_ix` (`contextid`),
  KEY `mdl_rolecapa_mod_ix` (`modifierid`),
  KEY `mdl_rolecapa_cap_ix` (`capability`)
) ENGINE=InnoDB AUTO_INCREMENT=1303 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='permission has to be signed, overriding a capability for a p';


CREATE TABLE `mdl_role_context_levels` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `roleid` bigint NOT NULL,
  `contextlevel` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_rolecontleve_conrol_uix` (`contextlevel`,`roleid`),
  KEY `mdl_rolecontleve_rol_ix` (`roleid`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Lists which roles can be assigned at which context levels. T';


CREATE TABLE `mdl_role_names` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `roleid` bigint NOT NULL DEFAULT '0',
  `contextid` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_rolename_rolcon_uix` (`roleid`,`contextid`),
  KEY `mdl_rolename_rol_ix` (`roleid`),
  KEY `mdl_rolename_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='role names in native strings';


CREATE TABLE `mdl_role_sortorder` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL,
  `roleid` bigint NOT NULL,
  `contextid` bigint NOT NULL,
  `sortoder` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_rolesort_userolcon_uix` (`userid`,`roleid`,`contextid`),
  KEY `mdl_rolesort_use_ix` (`userid`),
  KEY `mdl_rolesort_rol_ix` (`roleid`),
  KEY `mdl_rolesort_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='sort order of course managers in a course';


CREATE TABLE `mdl_scale` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `scale` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `descriptionformat` tinyint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_scal_cou_ix` (`courseid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines grading scales';


CREATE TABLE `mdl_scale_history` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `action` bigint NOT NULL DEFAULT '0',
  `oldid` bigint NOT NULL,
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `timemodified` bigint DEFAULT NULL,
  `loggeduser` bigint DEFAULT NULL,
  `courseid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `scale` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_scalhist_act_ix` (`action`),
  KEY `mdl_scalhist_tim_ix` (`timemodified`),
  KEY `mdl_scalhist_old_ix` (`oldid`),
  KEY `mdl_scalhist_cou_ix` (`courseid`),
  KEY `mdl_scalhist_log_ix` (`loggeduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='History table';


CREATE TABLE `mdl_scorm` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `scormtype` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'local',
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `introformat` smallint NOT NULL DEFAULT '0',
  `version` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `maxgrade` double NOT NULL DEFAULT '0',
  `grademethod` tinyint NOT NULL DEFAULT '0',
  `whatgrade` bigint NOT NULL DEFAULT '0',
  `maxattempt` bigint NOT NULL DEFAULT '1',
  `forcecompleted` tinyint(1) NOT NULL DEFAULT '0',
  `forcenewattempt` tinyint(1) NOT NULL DEFAULT '0',
  `lastattemptlock` tinyint(1) NOT NULL DEFAULT '0',
  `masteryoverride` tinyint(1) NOT NULL DEFAULT '1',
  `displayattemptstatus` tinyint(1) NOT NULL DEFAULT '1',
  `displaycoursestructure` tinyint(1) NOT NULL DEFAULT '0',
  `updatefreq` tinyint(1) NOT NULL DEFAULT '0',
  `sha1hash` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `md5hash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `revision` bigint NOT NULL DEFAULT '0',
  `launch` bigint NOT NULL DEFAULT '0',
  `skipview` tinyint(1) NOT NULL DEFAULT '1',
  `hidebrowse` tinyint(1) NOT NULL DEFAULT '0',
  `hidetoc` tinyint(1) NOT NULL DEFAULT '0',
  `nav` tinyint(1) NOT NULL DEFAULT '1',
  `navpositionleft` bigint DEFAULT '-100',
  `navpositiontop` bigint DEFAULT '-100',
  `auto` tinyint(1) NOT NULL DEFAULT '0',
  `popup` tinyint(1) NOT NULL DEFAULT '0',
  `options` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `width` bigint NOT NULL DEFAULT '100',
  `height` bigint NOT NULL DEFAULT '600',
  `timeopen` bigint NOT NULL DEFAULT '0',
  `timeclose` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `completionstatusrequired` tinyint(1) DEFAULT NULL,
  `completionscorerequired` bigint DEFAULT NULL,
  `completionstatusallscos` tinyint(1) DEFAULT NULL,
  `displayactivityname` smallint NOT NULL DEFAULT '1',
  `autocommit` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_scor_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='each table is one SCORM module and its configuration';


CREATE TABLE `mdl_scorm_aicc_session` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `scormid` bigint NOT NULL DEFAULT '0',
  `hacpsession` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `scoid` bigint DEFAULT '0',
  `scormmode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `scormstatus` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `attempt` bigint DEFAULT NULL,
  `lessonstatus` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `sessiontime` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_scoraiccsess_sco_ix` (`scormid`),
  KEY `mdl_scoraiccsess_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Used by AICC HACP to store session information';


CREATE TABLE `mdl_scorm_scoes` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `scorm` bigint NOT NULL DEFAULT '0',
  `manifest` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `organization` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `parent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `identifier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `launch` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `scormtype` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `sortorder` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_scorscoe_sco_ix` (`scorm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='each SCO part of the SCORM module';


CREATE TABLE `mdl_scorm_scoes_data` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `scoid` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_scorscoedata_sco_ix` (`scoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Contains variable data get from packages';


CREATE TABLE `mdl_scorm_scoes_track` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `scormid` bigint NOT NULL DEFAULT '0',
  `scoid` bigint NOT NULL DEFAULT '0',
  `attempt` bigint NOT NULL DEFAULT '1',
  `element` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_scorscoetrac_usescosco_uix` (`userid`,`scormid`,`scoid`,`attempt`,`element`),
  KEY `mdl_scorscoetrac_use_ix` (`userid`),
  KEY `mdl_scorscoetrac_ele_ix` (`element`),
  KEY `mdl_scorscoetrac_sco_ix` (`scormid`),
  KEY `mdl_scorscoetrac_sco2_ix` (`scoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='to track SCOes';


CREATE TABLE `mdl_scorm_seq_mapinfo` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `scoid` bigint NOT NULL DEFAULT '0',
  `objectiveid` bigint NOT NULL DEFAULT '0',
  `targetobjectiveid` bigint NOT NULL DEFAULT '0',
  `readsatisfiedstatus` tinyint(1) NOT NULL DEFAULT '1',
  `readnormalizedmeasure` tinyint(1) NOT NULL DEFAULT '1',
  `writesatisfiedstatus` tinyint(1) NOT NULL DEFAULT '0',
  `writenormalizedmeasure` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_scorseqmapi_scoidobj_uix` (`scoid`,`id`,`objectiveid`),
  KEY `mdl_scorseqmapi_sco_ix` (`scoid`),
  KEY `mdl_scorseqmapi_obj_ix` (`objectiveid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='SCORM2004 objective mapinfo description';


CREATE TABLE `mdl_scorm_seq_objective` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `scoid` bigint NOT NULL DEFAULT '0',
  `primaryobj` tinyint(1) NOT NULL DEFAULT '0',
  `objectiveid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `satisfiedbymeasure` tinyint(1) NOT NULL DEFAULT '1',
  `minnormalizedmeasure` float(11,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_scorseqobje_scoid_uix` (`scoid`,`id`),
  KEY `mdl_scorseqobje_sco_ix` (`scoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='SCORM2004 objective description';


CREATE TABLE `mdl_scorm_seq_rolluprule` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `scoid` bigint NOT NULL DEFAULT '0',
  `childactivityset` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `minimumcount` bigint NOT NULL DEFAULT '0',
  `minimumpercent` float(11,4) NOT NULL DEFAULT '0.0000',
  `conditioncombination` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'all',
  `action` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_scorseqroll_scoid_uix` (`scoid`,`id`),
  KEY `mdl_scorseqroll_sco_ix` (`scoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='SCORM2004 sequencing rule';


CREATE TABLE `mdl_scorm_seq_rolluprulecond` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `scoid` bigint NOT NULL DEFAULT '0',
  `rollupruleid` bigint NOT NULL DEFAULT '0',
  `operator` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'noOp',
  `cond` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_scorseqroll_scorolid_uix` (`scoid`,`rollupruleid`,`id`),
  KEY `mdl_scorseqroll_sco2_ix` (`scoid`),
  KEY `mdl_scorseqroll_rol_ix` (`rollupruleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='SCORM2004 sequencing rule';


CREATE TABLE `mdl_scorm_seq_rulecond` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `scoid` bigint NOT NULL DEFAULT '0',
  `ruleconditionsid` bigint NOT NULL DEFAULT '0',
  `refrencedobjective` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `measurethreshold` float(11,4) NOT NULL DEFAULT '0.0000',
  `operator` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'noOp',
  `cond` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'always',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_scorseqrule_idscorul_uix` (`id`,`scoid`,`ruleconditionsid`),
  KEY `mdl_scorseqrule_sco2_ix` (`scoid`),
  KEY `mdl_scorseqrule_rul_ix` (`ruleconditionsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='SCORM2004 rule condition';


CREATE TABLE `mdl_scorm_seq_ruleconds` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `scoid` bigint NOT NULL DEFAULT '0',
  `conditioncombination` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'all',
  `ruletype` tinyint NOT NULL DEFAULT '0',
  `action` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_scorseqrule_scoid_uix` (`scoid`,`id`),
  KEY `mdl_scorseqrule_sco_ix` (`scoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='SCORM2004 rule conditions';


CREATE TABLE `mdl_search_index_requests` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `contextid` bigint NOT NULL,
  `searcharea` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timerequested` bigint NOT NULL,
  `partialarea` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `partialtime` bigint NOT NULL,
  `indexpriority` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_searinderequ_indtim_ix` (`indexpriority`,`timerequested`),
  KEY `mdl_searinderequ_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Records requests for (re)indexing of specific contexts. Entr';


CREATE TABLE `mdl_search_simpledb_index` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `docid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `itemid` bigint NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `contextid` bigint NOT NULL,
  `areaid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL,
  `courseid` bigint NOT NULL,
  `owneruserid` bigint DEFAULT NULL,
  `modified` bigint NOT NULL,
  `userid` bigint DEFAULT NULL,
  `description1` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `description2` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_searsimpinde_doc_uix` (`docid`),
  KEY `mdl_searsimpinde_owncon_ix` (`owneruserid`,`contextid`),
  FULLTEXT KEY `mdl_search_simpledb_index_index` (`title`,`content`,`description1`,`description2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='search_simpledb table containing the index data.';


CREATE TABLE `mdl_sessions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `state` bigint NOT NULL DEFAULT '0',
  `sid` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `userid` bigint NOT NULL,
  `sessdata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `firstip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `lastip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_sess_sid_uix` (`sid`),
  KEY `mdl_sess_sta_ix` (`state`),
  KEY `mdl_sess_tim_ix` (`timecreated`),
  KEY `mdl_sess_tim2_ix` (`timemodified`),
  KEY `mdl_sess_use_ix` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Database based session storage - now recommended';


CREATE TABLE `mdl_stats_daily` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL DEFAULT '0',
  `timeend` bigint NOT NULL DEFAULT '0',
  `roleid` bigint NOT NULL DEFAULT '0',
  `stattype` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'activity',
  `stat1` bigint NOT NULL DEFAULT '0',
  `stat2` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_statdail_cou_ix` (`courseid`),
  KEY `mdl_statdail_tim_ix` (`timeend`),
  KEY `mdl_statdail_rol_ix` (`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='to accumulate daily stats';


CREATE TABLE `mdl_stats_monthly` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL DEFAULT '0',
  `timeend` bigint NOT NULL DEFAULT '0',
  `roleid` bigint NOT NULL DEFAULT '0',
  `stattype` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'activity',
  `stat1` bigint NOT NULL DEFAULT '0',
  `stat2` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_statmont_cou_ix` (`courseid`),
  KEY `mdl_statmont_tim_ix` (`timeend`),
  KEY `mdl_statmont_rol_ix` (`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='To accumulate monthly stats';


CREATE TABLE `mdl_stats_user_daily` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `roleid` bigint NOT NULL DEFAULT '0',
  `timeend` bigint NOT NULL DEFAULT '0',
  `statsreads` bigint NOT NULL DEFAULT '0',
  `statswrites` bigint NOT NULL DEFAULT '0',
  `stattype` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_statuserdail_cou_ix` (`courseid`),
  KEY `mdl_statuserdail_use_ix` (`userid`),
  KEY `mdl_statuserdail_rol_ix` (`roleid`),
  KEY `mdl_statuserdail_tim_ix` (`timeend`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='To accumulate daily stats per course/user';


CREATE TABLE `mdl_stats_user_monthly` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `roleid` bigint NOT NULL DEFAULT '0',
  `timeend` bigint NOT NULL DEFAULT '0',
  `statsreads` bigint NOT NULL DEFAULT '0',
  `statswrites` bigint NOT NULL DEFAULT '0',
  `stattype` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_statusermont_cou_ix` (`courseid`),
  KEY `mdl_statusermont_use_ix` (`userid`),
  KEY `mdl_statusermont_rol_ix` (`roleid`),
  KEY `mdl_statusermont_tim_ix` (`timeend`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='To accumulate monthly stats per course/user';


CREATE TABLE `mdl_stats_user_weekly` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `roleid` bigint NOT NULL DEFAULT '0',
  `timeend` bigint NOT NULL DEFAULT '0',
  `statsreads` bigint NOT NULL DEFAULT '0',
  `statswrites` bigint NOT NULL DEFAULT '0',
  `stattype` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_statuserweek_cou_ix` (`courseid`),
  KEY `mdl_statuserweek_use_ix` (`userid`),
  KEY `mdl_statuserweek_rol_ix` (`roleid`),
  KEY `mdl_statuserweek_tim_ix` (`timeend`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='To accumulate weekly stats per course/user';


CREATE TABLE `mdl_stats_weekly` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL DEFAULT '0',
  `timeend` bigint NOT NULL DEFAULT '0',
  `roleid` bigint NOT NULL DEFAULT '0',
  `stattype` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'activity',
  `stat1` bigint NOT NULL DEFAULT '0',
  `stat2` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_statweek_cou_ix` (`courseid`),
  KEY `mdl_statweek_tim_ix` (`timeend`),
  KEY `mdl_statweek_rol_ix` (`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='To accumulate weekly stats';


CREATE TABLE `mdl_survey` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `template` bigint NOT NULL DEFAULT '0',
  `days` mediumint NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `introformat` smallint NOT NULL DEFAULT '0',
  `questions` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `completionsubmit` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_surv_cou_ix` (`course`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Each record is one SURVEY module with its configuration';


CREATE TABLE `mdl_survey_analysis` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `survey` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `notes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_survanal_use_ix` (`userid`),
  KEY `mdl_survanal_sur_ix` (`survey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='text about each survey submission';


CREATE TABLE `mdl_survey_answers` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `survey` bigint NOT NULL DEFAULT '0',
  `question` bigint NOT NULL DEFAULT '0',
  `time` bigint NOT NULL DEFAULT '0',
  `answer1` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `answer2` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_survansw_use_ix` (`userid`),
  KEY `mdl_survansw_sur_ix` (`survey`),
  KEY `mdl_survansw_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='the answers to each questions filled by the users';


CREATE TABLE `mdl_survey_questions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `shorttext` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `multi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `type` smallint NOT NULL DEFAULT '0',
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='the questions conforming one survey';


CREATE TABLE `mdl_tag` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL,
  `tagcollid` bigint NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `rawname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `isstandard` tinyint(1) NOT NULL DEFAULT '0',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` tinyint NOT NULL DEFAULT '0',
  `flag` smallint DEFAULT '0',
  `timemodified` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_tag_tagnam_uix` (`tagcollid`,`name`),
  KEY `mdl_tag_tagiss_ix` (`tagcollid`,`isstandard`),
  KEY `mdl_tag_use_ix` (`userid`),
  KEY `mdl_tag_tag_ix` (`tagcollid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Tag table - this generic table will replace the old "tags" t';


CREATE TABLE `mdl_tag_area` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `itemtype` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `tagcollid` bigint NOT NULL,
  `callback` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `callbackfile` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `showstandard` tinyint(1) NOT NULL DEFAULT '0',
  `multiplecontexts` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_tagarea_comite_uix` (`component`,`itemtype`),
  KEY `mdl_tagarea_tag_ix` (`tagcollid`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines various tag areas, one area is identified by compone';


CREATE TABLE `mdl_tag_coll` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `isdefault` tinyint NOT NULL DEFAULT '0',
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `sortorder` mediumint NOT NULL DEFAULT '0',
  `searchable` tinyint NOT NULL DEFAULT '1',
  `customurl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Defines different set of tags';


CREATE TABLE `mdl_tag_correlation` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `tagid` bigint NOT NULL,
  `correlatedtags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_tagcorr_tag_ix` (`tagid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='The rationale for the ''tag_correlation'' table is performance';


CREATE TABLE `mdl_tag_instance` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `tagid` bigint NOT NULL,
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `itemtype` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `itemid` bigint NOT NULL,
  `contextid` bigint DEFAULT NULL,
  `tiuserid` bigint NOT NULL DEFAULT '0',
  `ordering` bigint DEFAULT NULL,
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_taginst_comiteiteconti_uix` (`component`,`itemtype`,`itemid`,`contextid`,`tiuserid`,`tagid`),
  KEY `mdl_taginst_itecomtagcon_ix` (`itemtype`,`component`,`tagid`,`contextid`),
  KEY `mdl_taginst_tag_ix` (`tagid`),
  KEY `mdl_taginst_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='tag_instance table holds the information of associations bet';


CREATE TABLE `mdl_task_adhoc` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `component` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `classname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `nextruntime` bigint NOT NULL,
  `faildelay` bigint DEFAULT NULL,
  `customdata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `userid` bigint DEFAULT NULL,
  `blocking` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_taskadho_nex_ix` (`nextruntime`),
  KEY `mdl_taskadho_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='List of adhoc tasks waiting to run.';


CREATE TABLE `mdl_task_scheduled` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `component` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `classname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `lastruntime` bigint DEFAULT NULL,
  `nextruntime` bigint DEFAULT NULL,
  `blocking` tinyint NOT NULL DEFAULT '0',
  `minute` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `hour` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `day` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `month` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `dayofweek` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `faildelay` bigint DEFAULT NULL,
  `customised` tinyint NOT NULL DEFAULT '0',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_tasksche_cla_uix` (`classname`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='List of scheduled tasks to be run by cron.';


CREATE TABLE `mdl_tool_cohortroles` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `cohortid` bigint NOT NULL,
  `roleid` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `usermodified` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_toolcoho_cohroluse_uix` (`cohortid`,`roleid`,`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Mapping of users to cohort role assignments.';


CREATE TABLE `mdl_tool_customlang` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `lang` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `componentid` bigint NOT NULL,
  `stringid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `original` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `master` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `local` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timemodified` bigint NOT NULL,
  `timecustomized` bigint DEFAULT NULL,
  `outdated` smallint DEFAULT '0',
  `modified` smallint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_toolcust_lancomstr_uix` (`lang`,`componentid`,`stringid`),
  KEY `mdl_toolcust_com_ix` (`componentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Contains the working checkout of all strings and their custo';


CREATE TABLE `mdl_tool_customlang_components` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Contains the list of all installed plugins that provide thei';


CREATE TABLE `mdl_tool_dataprivacy_category` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` tinyint(1) DEFAULT NULL,
  `usermodified` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Data categories';


CREATE TABLE `mdl_tool_dataprivacy_contextlist` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `component` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='List of contexts for a component';


CREATE TABLE `mdl_tool_dataprivacy_ctxexpired` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `contextid` bigint NOT NULL,
  `unexpiredroles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `expiredroles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `defaultexpired` tinyint(1) NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `usermodified` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_tooldatactxe_con_uix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Default comment for the table, please edit me';


CREATE TABLE `mdl_tool_dataprivacy_ctxinstance` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `contextid` bigint NOT NULL,
  `purposeid` bigint DEFAULT NULL,
  `categoryid` bigint DEFAULT NULL,
  `usermodified` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_tooldatactxi_con_uix` (`contextid`),
  KEY `mdl_tooldatactxi_pur_ix` (`purposeid`),
  KEY `mdl_tooldatactxi_cat_ix` (`categoryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Default comment for the table, please edit me';


CREATE TABLE `mdl_tool_dataprivacy_ctxlevel` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `contextlevel` smallint NOT NULL,
  `purposeid` bigint DEFAULT NULL,
  `categoryid` bigint DEFAULT NULL,
  `usermodified` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_tooldatactxl_con_uix` (`contextlevel`),
  KEY `mdl_tooldatactxl_cat_ix` (`categoryid`),
  KEY `mdl_tooldatactxl_pur_ix` (`purposeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Default comment for the table, please edit me';


CREATE TABLE `mdl_tool_dataprivacy_ctxlst_ctx` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `contextid` bigint NOT NULL,
  `contextlistid` bigint NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_tooldatactxlctx_con_ix` (`contextlistid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='A contextlist context item';


CREATE TABLE `mdl_tool_dataprivacy_purpose` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` tinyint(1) DEFAULT NULL,
  `lawfulbases` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `sensitivedatareasons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `retentionperiod` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `protected` tinyint(1) DEFAULT NULL,
  `usermodified` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Data purposes';


CREATE TABLE `mdl_tool_dataprivacy_purposerole` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `purposeid` bigint NOT NULL,
  `roleid` bigint NOT NULL,
  `lawfulbases` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `sensitivedatareasons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `retentionperiod` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `protected` tinyint(1) DEFAULT NULL,
  `usermodified` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_tooldatapurp_purrol_uix` (`purposeid`,`roleid`),
  KEY `mdl_tooldatapurp_pur_ix` (`purposeid`),
  KEY `mdl_tooldatapurp_rol_ix` (`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Data purpose overrides for a specific role';


CREATE TABLE `mdl_tool_dataprivacy_request` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `type` bigint NOT NULL DEFAULT '0',
  `comments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `commentsformat` tinyint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `requestedby` bigint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '0',
  `dpo` bigint DEFAULT '0',
  `dpocomment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `dpocommentformat` tinyint NOT NULL DEFAULT '0',
  `usermodified` bigint NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `creationmethod` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_tooldatarequ_use_ix` (`userid`),
  KEY `mdl_tooldatarequ_req_ix` (`requestedby`),
  KEY `mdl_tooldatarequ_dpo_ix` (`dpo`),
  KEY `mdl_tooldatarequ_use2_ix` (`usermodified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Table for data requests';


CREATE TABLE `mdl_tool_dataprivacy_rqst_ctxlst` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `requestid` bigint NOT NULL,
  `contextlistid` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_tooldatarqstctxl_reqco_uix` (`requestid`,`contextlistid`),
  KEY `mdl_tooldatarqstctxl_req_ix` (`requestid`),
  KEY `mdl_tooldatarqstctxl_con_ix` (`contextlistid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Association table joining requests and contextlists';


CREATE TABLE `mdl_tool_monitor_events` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `eventname` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `contextid` bigint NOT NULL,
  `contextlevel` bigint NOT NULL,
  `contextinstanceid` bigint NOT NULL,
  `link` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `courseid` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='A table that keeps a log of events related to subscriptions';


CREATE TABLE `mdl_tool_monitor_history` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `sid` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `timesent` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_toolmonihist_sidusetim_uix` (`sid`,`userid`,`timesent`),
  KEY `mdl_toolmonihist_sid_ix` (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Table to store history of message notifications sent';


CREATE TABLE `mdl_tool_monitor_rules` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` tinyint(1) NOT NULL,
  `name` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `userid` bigint NOT NULL,
  `courseid` bigint NOT NULL,
  `plugin` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `eventname` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `template` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `templateformat` tinyint(1) NOT NULL,
  `frequency` smallint NOT NULL,
  `timewindow` mediumint NOT NULL,
  `timemodified` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_toolmonirule_couuse_ix` (`courseid`,`userid`),
  KEY `mdl_toolmonirule_eve_ix` (`eventname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Table to store rules';


CREATE TABLE `mdl_tool_monitor_subscriptions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL,
  `ruleid` bigint NOT NULL,
  `cmid` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `lastnotificationsent` bigint NOT NULL DEFAULT '0',
  `inactivedate` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_toolmonisubs_couuse_ix` (`courseid`,`userid`),
  KEY `mdl_toolmonisubs_rul_ix` (`ruleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Table to store user subscriptions to various rules';


CREATE TABLE `mdl_tool_policy` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `sortorder` mediumint NOT NULL DEFAULT '999',
  `currentversionid` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_toolpoli_cur_ix` (`currentversionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Contains the list of policy documents defined on the site.';


CREATE TABLE `mdl_tool_policy_acceptances` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `policyversionid` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `lang` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `usermodified` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `note` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_toolpoliacce_poluse_uix` (`policyversionid`,`userid`),
  KEY `mdl_toolpoliacce_pol_ix` (`policyversionid`),
  KEY `mdl_toolpoliacce_use_ix` (`userid`),
  KEY `mdl_toolpoliacce_use2_ix` (`usermodified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Tracks users accepting the policy versions';


CREATE TABLE `mdl_tool_policy_versions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(1333) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `type` smallint NOT NULL DEFAULT '0',
  `audience` smallint NOT NULL DEFAULT '0',
  `archived` smallint NOT NULL DEFAULT '0',
  `usermodified` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `policyid` bigint NOT NULL,
  `revision` varchar(1333) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `summaryformat` smallint NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `contentformat` smallint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_toolpolivers_use_ix` (`usermodified`),
  KEY `mdl_toolpolivers_pol_ix` (`policyid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Holds versions of the policy documents';


CREATE TABLE `mdl_tool_recyclebin_category` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `categoryid` bigint NOT NULL,
  `shortname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timecreated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_toolrecycate_tim_ix` (`timecreated`),
  KEY `mdl_toolrecycate_cat_ix` (`categoryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='A list of items in the category recycle bin';


CREATE TABLE `mdl_tool_recyclebin_course` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `courseid` bigint NOT NULL,
  `section` bigint NOT NULL,
  `module` bigint NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `timecreated` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_toolrecycour_tim_ix` (`timecreated`),
  KEY `mdl_toolrecycour_cou_ix` (`courseid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='A list of items in the course recycle bin';


CREATE TABLE `mdl_tool_usertours_steps` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `tourid` bigint NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `targettype` tinyint NOT NULL,
  `targetvalue` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `sortorder` bigint NOT NULL DEFAULT '0',
  `configdata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_tooluserstep_tousor_ix` (`tourid`,`sortorder`),
  KEY `mdl_tooluserstep_tou_ix` (`tourid`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Steps in an tour';


CREATE TABLE `mdl_tool_usertours_tours` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `pathmatch` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `sortorder` bigint NOT NULL DEFAULT '0',
  `configdata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='List of tours';


CREATE TABLE `mdl_upgrade_log` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `type` bigint NOT NULL,
  `plugin` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `version` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `targetversion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `backtrace` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `userid` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_upgrlog_tim_ix` (`timemodified`),
  KEY `mdl_upgrlog_typtim_ix` (`type`,`timemodified`),
  KEY `mdl_upgrlog_use_ix` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=1194 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Upgrade logging';


CREATE TABLE `mdl_url` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `introformat` smallint NOT NULL DEFAULT '0',
  `externalurl` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `display` smallint NOT NULL DEFAULT '0',
  `displayoptions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `parameters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_url_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='each record is one url resource';


CREATE TABLE `mdl_user` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `auth` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'manual',
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `policyagreed` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `suspended` tinyint(1) NOT NULL DEFAULT '0',
  `mnethostid` bigint NOT NULL DEFAULT '0',
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `idnumber` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `firstname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `lastname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `emailstop` tinyint(1) NOT NULL DEFAULT '0',
  `icq` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `skype` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `yahoo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `aim` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `msn` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `phone1` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `phone2` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `institution` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `city` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `country` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `lang` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'en',
  `calendartype` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'gregorian',
  `theme` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timezone` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '99',
  `firstaccess` bigint NOT NULL DEFAULT '0',
  `lastaccess` bigint NOT NULL DEFAULT '0',
  `lastlogin` bigint NOT NULL DEFAULT '0',
  `currentlogin` bigint NOT NULL DEFAULT '0',
  `lastip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `secret` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `picture` bigint NOT NULL DEFAULT '0',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` tinyint NOT NULL DEFAULT '1',
  `mailformat` tinyint(1) NOT NULL DEFAULT '1',
  `maildigest` tinyint(1) NOT NULL DEFAULT '0',
  `maildisplay` tinyint NOT NULL DEFAULT '2',
  `autosubscribe` tinyint(1) NOT NULL DEFAULT '1',
  `trackforums` tinyint(1) NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `trustbitmask` bigint NOT NULL DEFAULT '0',
  `imagealt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `lastnamephonetic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `firstnamephonetic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `middlename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `alternatename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_user_mneuse_uix` (`mnethostid`,`username`),
  KEY `mdl_user_del_ix` (`deleted`),
  KEY `mdl_user_con_ix` (`confirmed`),
  KEY `mdl_user_fir_ix` (`firstname`),
  KEY `mdl_user_las_ix` (`lastname`),
  KEY `mdl_user_cit_ix` (`city`),
  KEY `mdl_user_cou_ix` (`country`),
  KEY `mdl_user_las2_ix` (`lastaccess`),
  KEY `mdl_user_ema_ix` (`email`),
  KEY `mdl_user_aut_ix` (`auth`),
  KEY `mdl_user_idn_ix` (`idnumber`),
  KEY `mdl_user_fir2_ix` (`firstnamephonetic`),
  KEY `mdl_user_las3_ix` (`lastnamephonetic`),
  KEY `mdl_user_mid_ix` (`middlename`),
  KEY `mdl_user_alt_ix` (`alternatename`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='One record for each person';


CREATE TABLE `mdl_user_devices` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `appid` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `model` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `platform` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `version` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `pushid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_userdevi_pususe_uix` (`pushid`,`userid`),
  KEY `mdl_userdevi_uuiuse_ix` (`uuid`,`userid`),
  KEY `mdl_userdevi_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table stores user''s mobile devices information in order';


CREATE TABLE `mdl_user_enrolments` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `status` bigint NOT NULL DEFAULT '0',
  `enrolid` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `timestart` bigint NOT NULL DEFAULT '0',
  `timeend` bigint NOT NULL DEFAULT '2147483647',
  `modifierid` bigint NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_userenro_enruse_uix` (`enrolid`,`userid`),
  KEY `mdl_userenro_enr_ix` (`enrolid`),
  KEY `mdl_userenro_use_ix` (`userid`),
  KEY `mdl_userenro_mod_ix` (`modifierid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Users participating in courses (aka enrolled users) - everyb';


CREATE TABLE `mdl_user_info_category` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `sortorder` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Customisable fields categories';


CREATE TABLE `mdl_user_info_data` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `fieldid` bigint NOT NULL DEFAULT '0',
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `dataformat` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_userinfodata_usefie_uix` (`userid`,`fieldid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Data for the customisable user fields';


CREATE TABLE `mdl_user_info_field` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `shortname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'shortname',
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `datatype` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` tinyint NOT NULL DEFAULT '0',
  `categoryid` bigint NOT NULL DEFAULT '0',
  `sortorder` bigint NOT NULL DEFAULT '0',
  `required` tinyint NOT NULL DEFAULT '0',
  `locked` tinyint NOT NULL DEFAULT '0',
  `visible` smallint NOT NULL DEFAULT '0',
  `forceunique` tinyint NOT NULL DEFAULT '0',
  `signup` tinyint NOT NULL DEFAULT '0',
  `defaultdata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `defaultdataformat` tinyint NOT NULL DEFAULT '0',
  `param1` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `param2` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `param3` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `param4` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `param5` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Customisable user profile fields';


CREATE TABLE `mdl_user_lastaccess` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `courseid` bigint NOT NULL DEFAULT '0',
  `timeaccess` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_userlast_usecou_uix` (`userid`,`courseid`),
  KEY `mdl_userlast_use_ix` (`userid`),
  KEY `mdl_userlast_cou_ix` (`courseid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='To keep track of course page access times, used in online pa';


CREATE TABLE `mdl_user_password_history` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL,
  `hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `timecreated` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_userpasshist_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='A rotating log of hashes of previously used passwords for ea';


CREATE TABLE `mdl_user_password_resets` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL,
  `timerequested` bigint NOT NULL,
  `timererequested` bigint NOT NULL DEFAULT '0',
  `token` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_userpassrese_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='table tracking password reset confirmation tokens';


CREATE TABLE `mdl_user_preferences` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `value` varchar(1333) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_userpref_usenam_uix` (`userid`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Allows modules to store arbitrary user preferences';


CREATE TABLE `mdl_user_private_key` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `script` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `value` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `userid` bigint NOT NULL,
  `instance` bigint DEFAULT NULL,
  `iprestriction` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `validuntil` bigint DEFAULT NULL,
  `timecreated` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_userprivkey_scrval_ix` (`script`,`value`),
  KEY `mdl_userprivkey_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='access keys used in cookieless scripts - rss, etc.';


CREATE TABLE `mdl_wiki` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Wiki',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `introformat` smallint NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `firstpagetitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'First Page',
  `wikimode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'collaborative',
  `defaultformat` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'creole',
  `forceformat` tinyint(1) NOT NULL DEFAULT '1',
  `editbegin` bigint NOT NULL DEFAULT '0',
  `editend` bigint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_wiki_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores Wiki activity configuration';


CREATE TABLE `mdl_wiki_links` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `subwikiid` bigint NOT NULL DEFAULT '0',
  `frompageid` bigint NOT NULL DEFAULT '0',
  `topageid` bigint NOT NULL DEFAULT '0',
  `tomissingpage` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_wikilink_fro_ix` (`frompageid`),
  KEY `mdl_wikilink_sub_ix` (`subwikiid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Page wiki links';


CREATE TABLE `mdl_wiki_locks` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `pageid` bigint NOT NULL DEFAULT '0',
  `sectionname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `userid` bigint NOT NULL DEFAULT '0',
  `lockedat` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Manages page locks';


CREATE TABLE `mdl_wiki_pages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `subwikiid` bigint NOT NULL DEFAULT '0',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'title',
  `cachedcontent` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `timecreated` bigint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL DEFAULT '0',
  `timerendered` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  `pageviews` bigint NOT NULL DEFAULT '0',
  `readonly` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_wikipage_subtituse_uix` (`subwikiid`,`title`,`userid`),
  KEY `mdl_wikipage_sub_ix` (`subwikiid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores wiki pages';


CREATE TABLE `mdl_wiki_subwikis` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `wikiid` bigint NOT NULL DEFAULT '0',
  `groupid` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_wikisubw_wikgrouse_uix` (`wikiid`,`groupid`,`userid`),
  KEY `mdl_wikisubw_wik_ix` (`wikiid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores subwiki instances';


CREATE TABLE `mdl_wiki_synonyms` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `subwikiid` bigint NOT NULL DEFAULT '0',
  `pageid` bigint NOT NULL DEFAULT '0',
  `pagesynonym` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Pagesynonym',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_wikisyno_pagpag_uix` (`pageid`,`pagesynonym`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores wiki pages synonyms';


CREATE TABLE `mdl_wiki_versions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `pageid` bigint NOT NULL DEFAULT '0',
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `contentformat` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'creole',
  `version` mediumint NOT NULL DEFAULT '0',
  `timecreated` bigint NOT NULL DEFAULT '0',
  `userid` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_wikivers_pag_ix` (`pageid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores wiki page history';


CREATE TABLE `mdl_workshop` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `course` bigint NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `introformat` smallint NOT NULL DEFAULT '0',
  `instructauthors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `instructauthorsformat` smallint NOT NULL DEFAULT '0',
  `instructreviewers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `instructreviewersformat` smallint NOT NULL DEFAULT '0',
  `timemodified` bigint NOT NULL,
  `phase` smallint DEFAULT '0',
  `useexamples` tinyint DEFAULT '0',
  `usepeerassessment` tinyint DEFAULT '0',
  `useselfassessment` tinyint DEFAULT '0',
  `grade` decimal(10,5) DEFAULT '80.00000',
  `gradinggrade` decimal(10,5) DEFAULT '20.00000',
  `strategy` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `evaluation` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `gradedecimals` smallint DEFAULT '0',
  `nattachments` smallint DEFAULT '0',
  `submissionfiletypes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `latesubmissions` tinyint DEFAULT '0',
  `maxbytes` bigint DEFAULT '100000',
  `examplesmode` smallint DEFAULT '0',
  `submissionstart` bigint DEFAULT '0',
  `submissionend` bigint DEFAULT '0',
  `assessmentstart` bigint DEFAULT '0',
  `assessmentend` bigint DEFAULT '0',
  `phaseswitchassessment` tinyint NOT NULL DEFAULT '0',
  `conclusion` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `conclusionformat` smallint NOT NULL DEFAULT '1',
  `overallfeedbackmode` smallint DEFAULT '1',
  `overallfeedbackfiles` smallint DEFAULT '0',
  `overallfeedbackfiletypes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `overallfeedbackmaxbytes` bigint DEFAULT '100000',
  PRIMARY KEY (`id`),
  KEY `mdl_work_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This table keeps information about the module instances and ';


CREATE TABLE `mdl_workshop_aggregations` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `workshopid` bigint NOT NULL,
  `userid` bigint NOT NULL,
  `gradinggrade` decimal(10,5) DEFAULT NULL,
  `timegraded` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_workaggr_woruse_uix` (`workshopid`,`userid`),
  KEY `mdl_workaggr_wor_ix` (`workshopid`),
  KEY `mdl_workaggr_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Aggregated grades for assessment are stored here. The aggreg';


CREATE TABLE `mdl_workshop_assessments` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `submissionid` bigint NOT NULL,
  `reviewerid` bigint NOT NULL,
  `weight` bigint NOT NULL DEFAULT '1',
  `timecreated` bigint DEFAULT '0',
  `timemodified` bigint DEFAULT '0',
  `grade` decimal(10,5) DEFAULT NULL,
  `gradinggrade` decimal(10,5) DEFAULT NULL,
  `gradinggradeover` decimal(10,5) DEFAULT NULL,
  `gradinggradeoverby` bigint DEFAULT NULL,
  `feedbackauthor` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `feedbackauthorformat` smallint DEFAULT '0',
  `feedbackauthorattachment` smallint DEFAULT '0',
  `feedbackreviewer` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `feedbackreviewerformat` smallint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_workasse_sub_ix` (`submissionid`),
  KEY `mdl_workasse_gra_ix` (`gradinggradeoverby`),
  KEY `mdl_workasse_rev_ix` (`reviewerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Info about the made assessment and automatically calculated ';


CREATE TABLE `mdl_workshop_grades` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `assessmentid` bigint NOT NULL,
  `strategy` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `dimensionid` bigint NOT NULL,
  `grade` decimal(10,5) NOT NULL,
  `peercomment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `peercommentformat` smallint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_workgrad_assstrdim_uix` (`assessmentid`,`strategy`,`dimensionid`),
  KEY `mdl_workgrad_ass_ix` (`assessmentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='How the reviewers filled-up the grading forms, given grades ';


CREATE TABLE `mdl_workshop_submissions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `workshopid` bigint NOT NULL,
  `example` tinyint DEFAULT '0',
  `authorid` bigint NOT NULL,
  `timecreated` bigint NOT NULL,
  `timemodified` bigint NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `contentformat` smallint NOT NULL DEFAULT '0',
  `contenttrust` smallint NOT NULL DEFAULT '0',
  `attachment` tinyint DEFAULT '0',
  `grade` decimal(10,5) DEFAULT NULL,
  `gradeover` decimal(10,5) DEFAULT NULL,
  `gradeoverby` bigint DEFAULT NULL,
  `feedbackauthor` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `feedbackauthorformat` smallint DEFAULT '0',
  `timegraded` bigint DEFAULT NULL,
  `published` tinyint DEFAULT '0',
  `late` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_worksubm_wor_ix` (`workshopid`),
  KEY `mdl_worksubm_gra_ix` (`gradeoverby`),
  KEY `mdl_worksubm_aut_ix` (`authorid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Info about the submission and the aggregation of the grade f';


CREATE TABLE `mdl_workshopallocation_scheduled` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `workshopid` bigint NOT NULL,
  `enabled` tinyint NOT NULL DEFAULT '0',
  `submissionend` bigint NOT NULL,
  `timeallocated` bigint DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `resultstatus` bigint DEFAULT NULL,
  `resultmessage` varchar(1333) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `resultlog` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_worksche_wor_uix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Stores the allocation settings for the scheduled allocator';


CREATE TABLE `mdl_workshopeval_best_settings` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `workshopid` bigint NOT NULL,
  `comparison` smallint DEFAULT '5',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_workbestsett_wor_uix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Settings for the grading evaluation subplugin Comparison wit';


CREATE TABLE `mdl_workshopform_accumulative` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `workshopid` bigint NOT NULL,
  `sort` bigint DEFAULT '0',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` smallint DEFAULT '0',
  `grade` bigint NOT NULL,
  `weight` mediumint DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `mdl_workaccu_wor_ix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='The assessment dimensions definitions of Accumulative gradin';


CREATE TABLE `mdl_workshopform_comments` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `workshopid` bigint NOT NULL,
  `sort` bigint DEFAULT '0',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` smallint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_workcomm_wor_ix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='The assessment dimensions definitions of Comments strategy f';


CREATE TABLE `mdl_workshopform_numerrors` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `workshopid` bigint NOT NULL,
  `sort` bigint DEFAULT '0',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` smallint DEFAULT '0',
  `descriptiontrust` bigint DEFAULT NULL,
  `grade0` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `grade1` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `weight` mediumint DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `mdl_worknume_wor_ix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='The assessment dimensions definitions of Number of errors gr';


CREATE TABLE `mdl_workshopform_numerrors_map` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `workshopid` bigint NOT NULL,
  `nonegative` bigint NOT NULL,
  `grade` decimal(10,5) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_worknumemap_wornon_uix` (`workshopid`,`nonegative`),
  KEY `mdl_worknumemap_wor_ix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='This maps the number of errors to a percentual grade for sub';


CREATE TABLE `mdl_workshopform_rubric` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `workshopid` bigint NOT NULL,
  `sort` bigint DEFAULT '0',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionformat` smallint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_workrubr_wor_ix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='The assessment dimensions definitions of Rubric grading stra';


CREATE TABLE `mdl_workshopform_rubric_config` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `workshopid` bigint NOT NULL,
  `layout` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'list',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_workrubrconf_wor_uix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='Configuration table for the Rubric grading strategy';


CREATE TABLE `mdl_workshopform_rubric_levels` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `dimensionid` bigint NOT NULL,
  `grade` decimal(10,5) NOT NULL,
  `definition` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `definitionformat` smallint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_workrubrleve_dim_ix` (`dimensionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=COMPRESSED COMMENT='The definition of rubric rating scales';
```
