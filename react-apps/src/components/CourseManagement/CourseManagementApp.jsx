import React, { useState, useEffect } from 'react';
import { useCategoryStore } from '../../stores/categoryStore';
import { useCourseStore } from '../../stores/courseStore';
import { useUIStore } from '../../stores/uiStore';
import CategoryPanel from './CategoryPanel/CategoryPanel';
import CourseListPanel from './CourseListPanel/CourseListPanel';
import CourseDetailPanel from './CourseDetailPanel/CourseDetailPanel';
import SearchPanel from './SearchPanel/SearchPanel';
import styles from './CourseManagementApp.module.css';

const CourseManagementApp = ({
  userId,
  initialCategoryId = 0,
  initialCourseId = null,
  viewMode: initialViewMode = 'default',
  searchParams = null,
  capabilities = {}
}) => {
  const { selectedCategory, setSelectedCategory } = useCategoryStore();
  const { selectedCourse, setSelectedCourse } = useCourseStore();
  const { viewMode, setViewMode, searchActive, setSearchActive } = useUIStore();
  
  const [searchResults, setSearchResults] = useState(null);

  useEffect(() => {
    // Initialize with provided values
    setViewMode(initialViewMode);
    if (initialCategoryId) {
      setSelectedCategory(initialCategoryId);
    }
    if (initialCourseId) {
      setSelectedCourse(initialCourseId);
    }
    if (searchParams) {
      setSearchActive(true);
      // TODO: Trigger initial search
    }
  }, []);

  const handleCategorySelect = (categoryId) => {
    setSelectedCategory(categoryId);
    setSelectedCourse(null);
    setSearchActive(false);
    setSearchResults(null);
  };

  const handleCourseSelect = (courseId) => {
    setSelectedCourse(courseId);
  };

  const handleSearch = (params) => {
    setSearchActive(true);
    // TODO: Implement search
    console.log('Search params:', params);
  };

  const handleClearSearch = () => {
    setSearchActive(false);
    setSearchResults(null);
  };

  // Determine layout based on view mode
  const getLayoutClass = () => {
    if (viewMode === 'categories') return styles.categoriesOnly;
    if (viewMode === 'courses') return styles.coursesOnly;
    if (selectedCourse && (viewMode === 'default' || viewMode === 'combined')) {
      return styles.threeColumn;
    }
    return styles.twoColumn;
  };

  const showCategories = viewMode === 'default' || viewMode === 'combined' || viewMode === 'categories';
  const showCourses = viewMode === 'default' || viewMode === 'combined' || viewMode === 'courses';
  const showDetails = selectedCourse && (viewMode === 'default' || viewMode === 'courses');

  return (
    <div className={`${styles.container} ${getLayoutClass()}`}>
      {/* View mode selector */}
      <div className={styles.header}>
        <h2>Course and category management</h2>
        <div className={styles.viewModeSelector}>
          <label htmlFor="viewmode">View:</label>
          <select 
            id="viewmode" 
            value={viewMode} 
            onChange={(e) => setViewMode(e.target.value)}
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
            <CategoryPanel
              selectedCategoryId={selectedCategory?.id}
              onCategorySelect={handleCategorySelect}
              capabilities={capabilities}
            />
          </div>
        )}
        
        {showCourses && (
          <div className={styles.coursePanel}>
            <CourseListPanel
              categoryId={selectedCategory?.id || initialCategoryId}
              selectedCourseId={selectedCourse?.id}
              onCourseSelect={handleCourseSelect}
              viewMode={viewMode}
              searchResults={searchResults}
              capabilities={capabilities}
            />
          </div>
        )}
        
        {showDetails && (
          <div className={styles.detailPanel}>
            <CourseDetailPanel
              courseId={selectedCourse?.id}
              onClose={() => setSelectedCourse(null)}
              capabilities={capabilities}
            />
          </div>
        )}
      </div>

      <div className={styles.searchSection}>
        <SearchPanel
          onSearch={handleSearch}
          onClear={handleClearSearch}
          isActive={searchActive}
        />
      </div>
    </div>
  );
};

export default CourseManagementApp;