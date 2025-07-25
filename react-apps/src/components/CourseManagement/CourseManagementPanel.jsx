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
  const [categoryName, setCategoryName] = useState('Loading...');

  useEffect(() => {
    loadCourses();
    loadCategoryName();
  }, [categoryId, currentPage]);

  const loadCategoryName = async () => {
    if (!categoryId || !token) return;
    
    try {
      const baseUrl = window.M?.cfg?.wwwroot || '';
      const response = await fetch(`${baseUrl}/local/courseapi/api/index.php/category/${categoryId}`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
        }
      });

      if (response.ok) {
        const result = await response.json();
        setCategoryName(result.name || 'Unknown Category');
      }
    } catch (err) {
      console.error('Failed to load category name:', err);
      setCategoryName('Category ' + categoryId);
    }
  };

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
      
      // Build query parameters
      const params = new URLSearchParams({
        category: categoryId,
        page: currentPage,
        perpage: perPage
      });
      
      const response = await fetch(`${baseUrl}/local/courseapi/api/index.php/course/list?${params}`, {
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
      console.log('Courses loaded:', result);
      
      setCourses(result.courses || []);
      setTotalCount(result.total || 0);
      
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

  const handleVisibilityToggle = async (courseId, currentlyHidden) => {
    try {
      if (!token) {
        throw new Error('No authentication token provided');
      }
      
      const baseUrl = window.M?.cfg?.wwwroot || '';
      const response = await fetch(`${baseUrl}/local/courseapi/api/index.php/course/${courseId}/visibility`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          visible: currentlyHidden ? 1 : 0
        })
      });

      if (!response.ok) {
        throw new Error(`Failed to toggle visibility: ${response.status}`);
      }

      // Reload courses to show updated state
      await loadCourses();
    } catch (err) {
      console.error('Failed to toggle course visibility:', err);
      setError('Error toggling visibility: ' + err.message);
    }
  };

  const handleCreateCourse = (e) => {
    e.preventDefault();
    e.stopPropagation();
    console.log('Create course clicked for category:', categoryId);
    
    // Redirect to Moodle's course creation page with the current category pre-selected
    const baseUrl = window.M?.cfg?.wwwroot || '';
    const url = `${baseUrl}/course/edit.php?category=${categoryId}`;
    console.log('Redirecting to:', url);
    window.location.href = url;
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
            {course.fullname}
          </h4>
          <div className={styles.courseDetails}>
            <span className={styles.shortname}>{course.shortname}</span>
            {course.idnumber && (
              <span className={styles.idnumber}>ID: {course.idnumber}</span>
            )}
            {course.enrolledcount !== undefined && (
              <span className={styles.enrolled}>{course.enrolledcount} enrolled</span>
            )}
          </div>
          {course.summary && (
            <p className={styles.summary} dangerouslySetInnerHTML={{
              __html: course.summary.substring(0, 150) + (course.summary.length > 150 ? '...' : '')
            }} />
          )}
          {course.teachers && course.teachers.length > 0 && (
            <div className={styles.teachers}>
              Teachers: {course.teachers.join(', ')}
            </div>
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
        <h3>Courses in {categoryName}</h3>
        <div className={styles.headerActions}>
          {capabilities['moodle/course:create'] && (
            <button className={styles.createBtn} onClick={handleCreateCourse}>
              Create course
            </button>
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