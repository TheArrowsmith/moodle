import React, { useState, useEffect } from 'react';
import styles from './CourseDetailPanel.module.css';

/**
 * React component to show detailed course information
 * This component will be mounted when a course is selected
 */
const CourseDetailPanel = ({ 
  token,
  courseId,
  onClose,
  capabilities = {}
}) => {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [courseData, setCourseData] = useState(null);

  useEffect(() => {
    if (courseId) {
      loadCourseDetails();
    }
  }, [courseId]);

  const loadCourseDetails = async () => {
    try {
      setLoading(true);
      setError(null);
      
      if (!token) {
        throw new Error('No authentication token provided');
      }
      
      const baseUrl = window.M?.cfg?.wwwroot || '';
      const response = await fetch(`${baseUrl}/local/courseapi/api/index.php/course/${courseId}/management_data`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
        }
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      setCourseData(data);
    } catch (err) {
      console.error('Failed to load course details:', err);
      setError(err);
    } finally {
      setLoading(false);
    }
  };

  const formatDate = (timestamp) => {
    if (!timestamp) return 'Not set';
    return new Date(timestamp * 1000).toLocaleDateString();
  };

  const countModules = () => {
    let total = 0;
    const moduleTypes = {};
    
    if (courseData?.sections) {
      courseData.sections.forEach(section => {
        if (section.activities) {
          section.activities.forEach(activity => {
            total++;
            moduleTypes[activity.modname] = (moduleTypes[activity.modname] || 0) + 1;
          });
        }
      });
    }
    
    return { total, types: moduleTypes };
  };

  if (loading) {
    return (
      <div className={styles.panel}>
        <div className={styles.loading}>Loading course details...</div>
      </div>
    );
  }

  if (error || !courseData) {
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
              <label>Course name:</label>
              <span>{courseData.course_name}</span>
            </div>
            <div className={styles.infoItem}>
              <label>Course ID:</label>
              <span>{courseId}</span>
            </div>
          </div>
        </div>

        {/* Course Content */}
        <div className={styles.section}>
          <h4>Course Content</h4>
          <div className={styles.infoGrid}>
            <div className={styles.infoItem}>
              <label>Sections:</label>
              <span>{courseData.sections?.length || 0}</span>
            </div>
            <div className={styles.infoItem}>
              <label>Activities:</label>
              <span>{moduleStats.total}</span>
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

        {/* Sections and Activities */}
        <div className={styles.section}>
          <h4>Sections</h4>
          {courseData.sections?.map((section, index) => (
            <div key={section.id} className={styles.sectionItem}>
              <h5>{section.name || `Section ${index + 1}`}</h5>
              {section.summary && (
                <div className={styles.sectionSummary} dangerouslySetInnerHTML={{ __html: section.summary }} />
              )}
              {section.activities && section.activities.length > 0 && (
                <div className={styles.activities}>
                  {section.activities.map(activity => (
                    <div key={activity.id} className={styles.activity}>
                      <img src={activity.modicon} alt={activity.modname} className={styles.activityIcon} />
                      <span className={styles.activityName}>{activity.name}</span>
                      <span className={`${styles.visibility} ${activity.visible ? '' : styles.hidden}`}>
                        {activity.visible ? '' : '(hidden)'}
                      </span>
                    </div>
                  ))}
                </div>
              )}
            </div>
          ))}
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