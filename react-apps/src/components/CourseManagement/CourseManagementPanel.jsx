import React, { useState, useEffect } from 'react';
import styles from './CourseManagementPanel.module.css';

/**
 * React component to replace the course listing panel in course/management.php
 * This component will be mounted to replace the existing course listing HTML
 */
const CourseManagementPanel = ({ 
  categoryId = 0,
  selectedCourseId,
  onCourseSelect,
  viewMode = 'default',
  page = 0,
  perPage = 20,
  capabilities = {},
  token // JWT token for API authentication
}) => {
  const [courses, setCourses] = useState([]);
  const [totalCount, setTotalCount] = useState(0);
  const [currentPage, setCurrentPage] = useState(page);
  const [selectedCourse, setSelectedCourse] = useState(selectedCourseId);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  useEffect(() => {
    loadCourses();
  }, [categoryId, currentPage]);

  useEffect(() => {
    if (selectedCourseId !== undefined) {
      setSelectedCourse(selectedCourseId);
    }
  }, [selectedCourseId]);

  const loadCourses = async () => {
    try {
      setLoading(true);
      setError(null);
      console.log('Loading courses for category:', categoryId);
      
      if (!token) {
        throw new Error('No authentication token provided');
      }
      
      // Get the base URL from Moodle's configuration
      const baseUrl = window.M?.cfg?.wwwroot || '';
      const courseId = 2; // TODO: Get this dynamically
      const response = await fetch(`${baseUrl}/local/courseapi/api/index.php/course/${courseId}/management_data`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
        }
      });

      if (!response.ok) {
        const errorText = await response.text();
        console.error('API Response:', response.status, errorText);
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json();
      console.log('Course management data loaded:', result);
      
      // Transform the data to match our component's expected format
      const allCourses = [];
      if (result.sections) {
        // For now, just display section info as "courses" to test
        result.sections.forEach(section => {
          if (section.activities) {
            section.activities.forEach(activity => {
              allCourses.push({
                id: activity.id,
                fullname: activity.name,
                shortname: activity.modname,
                visible: activity.visible ? 1 : 0,
                summary: `Activity in ${section.name}`,
                idnumber: '',
                categoryid: categoryId
              });
            });
          }
        });
      }
      
      setCourses(allCourses);
      setTotalCount(allCourses.length);
      
    } catch (err) {
      console.error('Failed to load courses:', err);
      setError('Error loading course data: ' + err.message);
    } finally {
      setLoading(false);
    }
  };

  const handleCourseClick = (course) => {
    setSelectedCourse(course.id);
    if (onCourseSelect) {
      onCourseSelect(course);
    }
  };

  const handleVisibilityToggle = async (courseId, visible) => {
    // TODO: Implement visibility toggle using custom AJAX endpoint
    console.log('Visibility toggle not yet implemented', { courseId, visible });
    // For now, just reload to show the functionality works
    await loadCourses();
  };

  const CourseCard = ({ course }) => {
    const isSelected = selectedCourse === course.id;
    const isHidden = course.visible === 0;

    return (
      <div 
        className={`${styles.courseCard} ${isSelected ? styles.selected : ''} ${isHidden ? styles.hidden : ''}`}
        onClick={() => handleCourseClick(course)}
      >
        <div className={styles.courseInfo}>
          <h4 className={styles.courseName}>
            {course.displayname || course.fullname}
          </h4>
          <div className={styles.courseDetails}>
            <span className={styles.shortname}>{course.shortname}</span>
            {course.idnumber && (
              <span className={styles.idnumber}>ID: {course.idnumber}</span>
            )}
          </div>
          {course.summary && (
            <p className={styles.summary}>
              {course.summary.replace(/<[^>]*>/g, '').substring(0, 100)}...
            </p>
          )}
        </div>
        
        {capabilities['moodle/course:visibility'] && (
          <div className={styles.actions}>
            <button
              className={styles.actionBtn}
              onClick={(e) => {
                e.stopPropagation();
                handleVisibilityToggle(course.id, isHidden);
              }}
              title={isHidden ? 'Show course' : 'Hide course'}
            >
              {isHidden ? '👁' : '👁‍🗨'}
            </button>
          </div>
        )}
      </div>
    );
  };

  if (loading) {
    return <div className={styles.loading}>Loading courses...</div>;
  }

  if (error) {
    return <div className={styles.error}>Error loading courses: {error.message}</div>;
  }

  return (
    <div className={styles.panel}>
      <div className={styles.header}>
        <h3>Courses in this category</h3>
        <div className={styles.headerActions}>
          {capabilities['moodle/course:create'] && (
            <button className={styles.createBtn}>Create course</button>
          )}
          <span className={styles.courseCount}>
            {totalCount} course{totalCount !== 1 ? 's' : ''}
          </span>
        </div>
      </div>
      
      <div className={styles.courseList}>
        {courses.length === 0 ? (
          <div className={styles.empty}>
            No courses found in this category.
          </div>
        ) : (
          courses.map(course => (
            <CourseCard key={course.id} course={course} />
          ))
        )}
      </div>
      
      {totalCount > perPage && (
        <div className={styles.pagination}>
          <button 
            disabled={currentPage === 0}
            onClick={() => setCurrentPage(currentPage - 1)}
          >
            Previous
          </button>
          <span>
            Page {currentPage + 1} of {Math.ceil(totalCount / perPage)}
          </span>
          <button 
            disabled={(currentPage + 1) * perPage >= totalCount}
            onClick={() => setCurrentPage(currentPage + 1)}
          >
            Next
          </button>
        </div>
      )}
    </div>
  );
};

export default CourseManagementPanel;