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
      console.log('Loading categories from API');
      
      if (!token) {
        throw new Error('No authentication token provided');
      }
      
      // Get the base URL from Moodle's configuration
      const baseUrl = window.M?.cfg?.wwwroot || '';
      
      const response = await fetch(`${baseUrl}/local/courseapi/api/index.php/category/tree`, {
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
      console.log('Categories loaded:', result);
      
      setCategories(result.categories || []);
      
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

  const handleCreateCategory = (e) => {
    e.preventDefault();
    e.stopPropagation();
    console.log('Create category clicked, current selected:', selectedCategory);
    
    // Use the currently selected category as parent, or default to 1 if none selected
    const parentId = selectedCategory || 1;
    
    // Redirect to Moodle's category creation page with parent parameter
    const baseUrl = window.M?.cfg?.wwwroot || '';
    const url = `${baseUrl}/course/editcategory.php?parent=${parentId}`;
    console.log('Redirecting to:', url);
    window.location.href = url;
  };

  const toggleExpanded = async (categoryId) => {
    const newExpanded = new Set(expandedCategories);
    if (newExpanded.has(categoryId)) {
      newExpanded.delete(categoryId);
    } else {
      newExpanded.add(categoryId);
    }
    setExpandedCategories(newExpanded);
  };

  const CategoryNode = ({ category, level = 0, isSelected, onClick }) => {
    const isExpanded = expandedCategories.has(category.id);
    const hasChildren = category.children && category.children.length > 0;

    return (
      <li className={styles.categoryNode} style={{ marginLeft: `${level * 20}px` }}>
        <div 
          className={`${styles.categoryItem} ${isSelected ? styles.selected : ''} ${!category.visible ? styles.hidden : ''}`}
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
          
          {capabilities['moodle/category:manage'] && category.id !== 0 && (
            <div className={styles.categoryActions}>
              <button 
                className={styles.actionBtn} 
                title={category.visible ? 'Hide category' : 'Show category'}
                onClick={(e) => {
                  e.stopPropagation();
                  // TODO: Implement visibility toggle
                }}
              >
                {category.visible ? '👁' : '👁‍🗨'}
              </button>
            </div>
          )}
        </div>
        
        {isExpanded && hasChildren && (
          <ul className={styles.subcategories}>
            {category.children.map(child => (
              <CategoryNode 
                key={child.id}
                category={child}
                level={level + 1}
                isSelected={selectedCategory === child.id}
                onClick={() => handleCategoryClick(child)}
              />
            ))}
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
          <button className={styles.createBtn} onClick={handleCreateCategory}>
            Create category
          </button>
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