import React, { useState, useEffect } from 'react';
import { useMoodleAjax } from '../../hooks/useMoodleAjax';
import styles from './CourseDetailPanel.module.css';

/**
 * React component to show detailed course information
 * This component will be mounted when a course is selected
 */
const CourseDetailPanel = ({ 
  courseId,
  onClose,
  capabilities = {}
}) => {
  const { data: courseDetails, loading, error, callService } = useMoodleAjax();
  const [course, setCourse] = useState(null);
  const [sections, setSections] = useState([]);

  useEffect(() => {
    if (courseId) {
      loadCourseDetails();
    }
  }, [courseId]);

  const loadCourseDetails = async () => {
    try {
      // Load course info and contents in parallel
      const [courseInfo, courseContents] = await Promise.all([
        callService('core_course_get_courses', { 
          options: { ids: [courseId] } 
        }),
        callService('core_course_get_contents', { 
          courseid: courseId 
        })
      ]);

      if (courseInfo && courseInfo.length > 0) {
        setCourse(courseInfo[0]);
      }
      
      if (courseContents) {
        setSections(courseContents);
      }
    } catch (err) {
      console.error('Failed to load course details:', err);
    }
  };

  const formatDate = (timestamp) => {
    if (!timestamp) return 'Not set';
    return new Date(timestamp * 1000).toLocaleDateString();
  };

  const countModules = () => {
    let total = 0;
    const moduleTypes = {};
    
    sections.forEach(section => {
      if (section.modules) {
        section.modules.forEach(module => {
          total++;
          moduleTypes[module.modname] = (moduleTypes[module.modname] || 0) + 1;
        });
      }
    });
    
    return { total, types: moduleTypes };
  };

  if (loading) {
    return (
      <div className={styles.panel}>
        <div className={styles.loading}>Loading course details...</div>
      </div>
    );
  }

  if (error || !course) {
    return (
      <div className={styles.panel}>
        <div className={styles.error}>
          {error ? `Error: ${error.message}` : 'Course not found'}
        </div>
      </div>
    );
  }

  const moduleStats = countModules();

  return (
    <div className={styles.panel}>
      <div className={styles.header}>
        <h3>Course details</h3>
        {onClose && (
          <button className={styles.closeBtn} onClick={onClose}>
            ✕
          </button>
        )}
      </div>

      <div className={styles.content}>
        {/* Basic Information */}
        <div className={styles.section}>
          <h4>Basic Information</h4>
          <div className={styles.infoGrid}>
            <div className={styles.infoItem}>
              <label>Full name:</label>
              <span>{course.fullname}</span>
            </div>
            <div className={styles.infoItem}>
              <label>Short name:</label>
              <span>{course.shortname}</span>
            </div>
            {course.idnumber && (
              <div className={styles.infoItem}>
                <label>ID number:</label>
                <span>{course.idnumber}</span>
              </div>
            )}
            <div className={styles.infoItem}>
              <label>Category:</label>
              <span>{course.categoryname || 'Unknown'}</span>
            </div>
            <div className={styles.infoItem}>
              <label>Visible:</label>
              <span className={course.visible ? styles.visible : styles.hidden}>
                {course.visible ? 'Yes' : 'No'}
              </span>
            </div>
          </div>
        </div>

        {/* Course Dates */}
        <div className={styles.section}>
          <h4>Course Dates</h4>
          <div className={styles.infoGrid}>
            <div className={styles.infoItem}>
              <label>Start date:</label>
              <span>{formatDate(course.startdate)}</span>
            </div>
            <div className={styles.infoItem}>
              <label>End date:</label>
              <span>{formatDate(course.enddate)}</span>
            </div>
            <div className={styles.infoItem}>
              <label>Created:</label>
              <span>{formatDate(course.timecreated)}</span>
            </div>
            <div className={styles.infoItem}>
              <label>Modified:</label>
              <span>{formatDate(course.timemodified)}</span>
            </div>
          </div>
        </div>

        {/* Course Content */}
        <div className={styles.section}>
          <h4>Course Content</h4>
          <div className={styles.infoGrid}>
            <div className={styles.infoItem}>
              <label>Sections:</label>
              <span>{sections.length}</span>
            </div>
            <div className={styles.infoItem}>
              <label>Activities:</label>
              <span>{moduleStats.total}</span>
            </div>
            <div className={styles.infoItem}>
              <label>Format:</label>
              <span>{course.format || 'topics'}</span>
            </div>
          </div>
          
          {Object.keys(moduleStats.types).length > 0 && (
            <div className={styles.moduleTypes}>
              <h5>Activity types:</h5>
              <div className={styles.moduleList}>
                {Object.entries(moduleStats.types).map(([type, count]) => (
                  <span key={type} className={styles.moduleTag}>
                    {type}: {count}
                  </span>
                ))}
              </div>
            </div>
          )}
        </div>

        {/* Course Settings */}
        <div className={styles.section}>
          <h4>Settings</h4>
          <div className={styles.infoGrid}>
            <div className={styles.infoItem}>
              <label>Group mode:</label>
              <span>
                {course.groupmode === 0 ? 'No groups' :
                 course.groupmode === 1 ? 'Separate groups' : 'Visible groups'}
              </span>
            </div>
            <div className={styles.infoItem}>
              <label>Completion:</label>
              <span>{course.enablecompletion ? 'Enabled' : 'Disabled'}</span>
            </div>
            <div className={styles.infoItem}>
              <label>Show grades:</label>
              <span>{course.showgrades ? 'Yes' : 'No'}</span>
            </div>
          </div>
        </div>

        {/* Quick Actions */}
        {capabilities && (
          <div className={styles.section}>
            <h4>Quick Actions</h4>
            <div className={styles.actionButtons}>
              <a 
                href={`/course/view.php?id=${courseId}`}
                className={styles.actionLink}
              >
                View course
              </a>
              {capabilities['moodle/course:update'] && (
                <a 
                  href={`/course/edit.php?id=${courseId}`}
                  className={styles.actionLink}
                >
                  Edit settings
                </a>
              )}
              <a 
                href={`/grade/report/grader/index.php?id=${courseId}`}
                className={styles.actionLink}
              >
                Gradebook
              </a>
              <a 
                href={`/enrol/instances.php?id=${courseId}`}
                className={styles.actionLink}
              >
                Enrolments
              </a>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default CourseDetailPanel;