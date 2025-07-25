import React, { useState, useEffect } from 'react';
import styles from './CategoryManagementPanel.module.css';

/**
 * React component to replace the category listing panel in course/management.php
 * This component will be mounted to replace the existing category listing HTML
 */
const CategoryManagementPanel = ({ 
  initialCategoryId = 0,
  selectedCategoryId,
  onCategorySelect,
  capabilities = {},
  token // JWT token for API authentication
}) => {
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [expandedCategories, setExpandedCategories] = useState(new Set());
  const [selectedCategory, setSelectedCategory] = useState(null);

  useEffect(() => {
    // Load initial categories
    loadCategories(0);
  }, []);

  useEffect(() => {
    // Debug: log that component mounted
    console.log('CategoryManagementPanel mounted', { initialCategoryId, capabilities });
  }, []);

  useEffect(() => {
    if (selectedCategoryId !== undefined) {
      setSelectedCategory(selectedCategoryId);
    }
  }, [selectedCategoryId]);

  const loadCategories = async (parentId) => {
    try {
      setLoading(true);
      setError(null);
      console.log('Loading categories - using mock data for now');
      
      // The Course API doesn't have category endpoints, so we'll use mock data for now
      // In a real implementation, you'd need to add category endpoints to the API
      const mockCategories = [
        {
          id: 1,
          name: 'Miscellaneous',
          parent: 0,
          visible: true,
          coursecount: 2,
          childrencount: 0,
          description: 'Default category for courses',
          path: '/1'
        },
        {
          id: 2,
          name: 'Programming Courses',
          parent: 0,
          visible: true,
          coursecount: 5,
          childrencount: 2,
          description: 'Courses related to programming',
          path: '/2'
        }
      ];
      
      // Simulate API delay
      await new Promise(resolve => setTimeout(resolve, 500));
      
      setCategories(mockCategories);
      
    } catch (err) {
      console.error('Failed to load categories:', err);
      setError('Error loading categories: ' + err.message);
    } finally {
      setLoading(false);
    }
  };

  const handleCategoryClick = (category) => {
    setSelectedCategory(category.id);
    if (onCategorySelect) {
      onCategorySelect(category);
    }
  };

  const toggleExpanded = async (categoryId) => {
    const newExpanded = new Set(expandedCategories);
    if (newExpanded.has(categoryId)) {
      newExpanded.delete(categoryId);
    } else {
      newExpanded.add(categoryId);
      // Load children if not already loaded
      await loadCategories(categoryId);
    }
    setExpandedCategories(newExpanded);
  };

  const CategoryNode = ({ category, level = 0, isSelected, onClick }) => {
    const isExpanded = expandedCategories.has(category.id);
    const hasChildren = category.coursecount > 0 || category.childrencount > 0;

    return (
      <li className={styles.categoryNode} style={{ marginLeft: `${level * 20}px` }}>
        <div 
          className={`${styles.categoryItem} ${isSelected ? styles.selected : ''}`}
          onClick={onClick}
        >
          {hasChildren && (
            <button
              className={styles.expandButton}
              onClick={(e) => {
                e.stopPropagation();
                toggleExpanded(category.id);
              }}
            >
              {isExpanded ? '▼' : '▶'}
            </button>
          )}
          
          {!hasChildren && <span className={styles.noExpand}></span>}
          
          <span className={styles.categoryName}>
            {category.name}
            {category.coursecount > 0 && (
              <span className={styles.courseCount}>({category.coursecount})</span>
            )}
          </span>
        </div>
        
        {isExpanded && (
          <ul className={styles.subcategories}>
            {/* TODO: Load and render subcategories */}
          </ul>
        )}
      </li>
    );
  };

  if (loading) {
    return <div className={styles.loading}>Loading categories...</div>;
  }

  if (error) {
    return <div className={styles.error}>Error loading categories: {error.message}</div>;
  }

  return (
    <div className={styles.panel}>
      <h3>Course categories</h3>
      
      {capabilities['moodle/category:manage'] && (
        <div className={styles.actions}>
          <button className={styles.createBtn}>Create category</button>
        </div>
      )}
      
      <ul className={styles.categoryTree}>
        {/* Top level category */}
        <CategoryNode 
          category={{ id: 0, name: 'Top', coursecount: 0 }} 
          isSelected={selectedCategory === 0}
          onClick={() => handleCategoryClick({ id: 0, name: 'Top' })}
        />
        
        {/* Child categories */}
        {categories.map(category => (
          <CategoryNode 
            key={category.id} 
            category={category} 
            level={1}
            isSelected={selectedCategory === category.id}
            onClick={() => handleCategoryClick(category)}
          />
        ))}
      </ul>
    </div>
  );
};

export default CategoryManagementPanel;