import React, { useEffect, useState } from 'react';
import { useCategoryStore } from '../../../stores/categoryStore';
import { useUIStore } from '../../../stores/uiStore';
import CategoryTree from './CategoryTree';
import CategoryActions from './CategoryActions';
import styles from './CategoryPanel.module.css';

const CategoryPanel = ({ selectedCategoryId, onCategorySelect, capabilities }) => {
  const { 
    loadCategory, 
    selectedCategory,
    setSelectedCategory 
  } = useCategoryStore();
  
  const { bulkMode, selectedItems } = useUIStore();
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    // Load root categories on mount
    const loadRootCategories = async () => {
      try {
        setLoading(true);
        await loadCategory(0); // Load top-level categories
        setLoading(false);
      } catch (err) {
        setError(err.message);
        setLoading(false);
      }
    };

    loadRootCategories();
  }, []);

  useEffect(() => {
    // Update selected category when prop changes
    if (selectedCategoryId !== undefined && selectedCategoryId !== selectedCategory?.id) {
      setSelectedCategory(selectedCategoryId);
    }
  }, [selectedCategoryId]);

  const handleCategoryClick = (category) => {
    if (!bulkMode) {
      setSelectedCategory(category);
      onCategorySelect(category.id);
    }
  };

  const canManageCategories = capabilities['moodle/category:manage'] !== false;

  return (
    <div className={styles.panel}>
      <div className={styles.header}>
        <h3 id="category-listing-title">Course categories</h3>
        {canManageCategories && (
          <CategoryActions 
            selectedCategory={selectedCategory}
            bulkMode={bulkMode}
            selectedItems={selectedItems}
          />
        )}
      </div>

      <div className={styles.content}>
        {loading && (
          <div className={styles.loading}>Loading categories...</div>
        )}
        
        {error && (
          <div className={styles.error}>
            Error loading categories: {error}
          </div>
        )}
        
        {!loading && !error && (
          <CategoryTree
            parentId={0}
            selectedCategoryId={selectedCategory?.id}
            onCategoryClick={handleCategoryClick}
            capabilities={capabilities}
            level={0}
          />
        )}
      </div>

      {bulkMode && selectedItems.size > 0 && (
        <div className={styles.bulkActions}>
          <div className={styles.selectedCount}>
            {selectedItems.size} categories selected
          </div>
          <button className={styles.bulkMoveBtn}>
            Move selected
          </button>
          <button className={styles.bulkDeleteBtn}>
            Delete selected
          </button>
        </div>
      )}
    </div>
  );
};

export default CategoryPanel;