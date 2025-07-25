import React, { useState, useEffect } from 'react';
import CategoryManagementPanel from './CategoryManagementPanel';
import CourseManagementPanel from './CourseManagementPanel';
import CourseDetailPanel from './CourseDetailPanel';
import styles from './CourseManagementApp.module.css';

const CourseManagementApp = ({
  token,
  initialCategoryId = 0,
  initialCourseId = null,
  viewMode: initialViewMode = 'default',
  page = 0,
  perPage = 20,
  search = '',
  capabilities = {}
}) => {
  const [selectedCategoryId, setSelectedCategoryId] = useState(initialCategoryId);
  const [selectedCourseId, setSelectedCourseId] = useState(initialCourseId);
  const [currentViewMode, setCurrentViewMode] = useState(initialViewMode);

  // Sync URL params
  useEffect(() => {
    if (window.history.replaceState) {
      const url = new URL(window.location);
      url.searchParams.set('categoryid', selectedCategoryId);
      if (selectedCourseId) {
        url.searchParams.set('courseid', selectedCourseId);
      } else {
        url.searchParams.delete('courseid');
      }
      window.history.replaceState({}, '', url);
    }
  }, [selectedCategoryId, selectedCourseId]);

  const handleCategorySelect = (category) => {
    setSelectedCategoryId(category.id);
    setSelectedCourseId(null); // Clear course selection when category changes
  };

  const handleCourseSelect = (course) => {
    setSelectedCourseId(course.id);
  };

  // Determine layout based on view mode
  const getLayoutClass = () => {
    if (currentViewMode === 'categories') return styles.categoriesOnly;
    if (currentViewMode === 'courses') return styles.coursesOnly;
    if (selectedCourseId && (currentViewMode === 'default' || currentViewMode === 'combined')) {
      return styles.threeColumn;
    }
    return styles.twoColumn;
  };

  const showCategories = currentViewMode === 'default' || currentViewMode === 'combined' || currentViewMode === 'categories';
  const showCourses = currentViewMode === 'default' || currentViewMode === 'combined' || currentViewMode === 'courses';
  const showDetails = selectedCourseId && (currentViewMode === 'default' || currentViewMode === 'courses');

  return (
    <div className={`${styles.container} ${getLayoutClass()}`}>
      {/* View mode selector */}
      <div className={styles.header}>
        <h2>Course and category management</h2>
        <div className={styles.viewModeSelector}>
          <label htmlFor="viewmode">View:</label>
          <select 
            id="viewmode" 
            value={currentViewMode} 
            onChange={(e) => setCurrentViewMode(e.target.value)}
          >
            <option value="default">Default</option>
            <option value="combined">Combined</option>
            <option value="courses">Courses only</option>
            <option value="categories">Categories only</option>
          </select>
        </div>
      </div>

      <div className={styles.panels}>
        {showCategories && (
          <div className={styles.categoryPanel}>
            <CategoryManagementPanel
              token={token}
              selectedCategoryId={selectedCategoryId}
              onCategorySelect={handleCategorySelect}
              capabilities={capabilities}
            />
          </div>
        )}
        
        {showCourses && (
          <div className={styles.coursePanel}>
            <CourseManagementPanel
              token={token}
              categoryId={selectedCategoryId}
              selectedCourseId={selectedCourseId}
              onCourseSelect={handleCourseSelect}
              viewMode={currentViewMode}
              page={page}
              perPage={perPage}
              capabilities={capabilities}
            />
          </div>
        )}
        
        {showDetails && (
          <div className={styles.detailPanel}>
            <CourseDetailPanel
              token={token}
              courseId={selectedCourseId}
              onClose={() => setSelectedCourseId(null)}
              capabilities={capabilities}
            />
          </div>
        )}
      </div>
    </div>
  );
};

export default CourseManagementApp;