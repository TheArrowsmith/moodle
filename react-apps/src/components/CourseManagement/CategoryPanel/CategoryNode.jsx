import React, { useState } from 'react';
import { useCategoryStore } from '../../../stores/categoryStore';
import { useUIStore } from '../../../stores/uiStore';
import CategoryTree from './CategoryTree';
import styles from './CategoryNode.module.css';

const CategoryNode = ({ 
  category, 
  isSelected, 
  onClick, 
  capabilities, 
  level,
  isTop = false 
}) => {
  const { 
    expandedCategories, 
    toggleExpanded, 
    loadCategory,
    getChildCategories,
    setCategoryVisibility 
  } = useCategoryStore();
  
  const { 
    bulkMode, 
    isSelected: isItemSelected, 
    toggleItemSelection 
  } = useUIStore();

  const [loading, setLoading] = useState(false);
  const [showActions, setShowActions] = useState(false);

  const isExpanded = expandedCategories.has(category.id);
  const hasChildren = category.coursecount > 0 || category.subcategories > 0 || isTop;
  const children = getChildCategories(category.id);
  const isHidden = category.visible === 0 || category.visible === false;

  const handleToggleExpand = async (e) => {
    e.stopPropagation();
    
    if (!isExpanded && children.length === 0 && hasChildren) {
      setLoading(true);
      try {
        await loadCategory(category.id);
      } catch (error) {
        console.error('Failed to load subcategories:', error);
      }
      setLoading(false);
    }
    
    toggleExpanded(category.id);
  };

  const handleClick = (e) => {
    if (bulkMode) {
      e.preventDefault();
      toggleItemSelection(category.id, 'category');
    } else {
      onClick();
    }
  };

  const handleVisibilityToggle = async (e) => {
    e.stopPropagation();
    try {
      await setCategoryVisibility(category.id, isHidden);
    } catch (error) {
      console.error('Failed to toggle visibility:', error);
    }
  };

  const canManage = capabilities['moodle/category:manage'] !== false;
  const canViewHidden = capabilities['moodle/category:viewhiddencategories'] !== false;

  // Don't show hidden categories if user can't view them
  if (isHidden && !canViewHidden && !isTop) {
    return null;
  }

  return (
    <li 
      className={styles.node}
      role="treeitem"
      aria-expanded={hasChildren ? isExpanded : undefined}
      aria-selected={isSelected}
      aria-level={level + 1}
    >
      <div 
        className={`${styles.nodeContent} ${isSelected ? styles.selected : ''} ${isHidden ? styles.hidden : ''}`}
        onClick={handleClick}
        onMouseEnter={() => setShowActions(true)}
        onMouseLeave={() => setShowActions(false)}
      >
        {bulkMode && (
          <input
            type="checkbox"
            className={styles.checkbox}
            checked={isItemSelected(category.id)}
            onChange={() => {}}
            onClick={(e) => e.stopPropagation()}
            aria-label={`Select ${category.name}`}
          />
        )}
        
        {hasChildren && (
          <button
            className={styles.expandButton}
            onClick={handleToggleExpand}
            aria-label={isExpanded ? 'Collapse' : 'Expand'}
          >
            {loading ? (
              <span className={styles.spinner}>⟳</span>
            ) : (
              <span className={isExpanded ? styles.expanded : styles.collapsed}>
                {isExpanded ? '▼' : '▶'}
              </span>
            )}
          </button>
        )}
        
        {!hasChildren && (
          <span className={styles.noExpand}></span>
        )}
        
        <span className={styles.name}>
          {category.name}
          {category.coursecount > 0 && (
            <span className={styles.courseCount}>({category.coursecount})</span>
          )}
        </span>
        
        {canManage && showActions && !bulkMode && (
          <div className={styles.actions}>
            {!isTop && (
              <>
                <button
                  className={styles.actionBtn}
                  onClick={handleVisibilityToggle}
                  title={isHidden ? 'Show category' : 'Hide category'}
                >
                  {isHidden ? '👁' : '👁‍🗨'}
                </button>
                <button
                  className={styles.actionBtn}
                  onClick={(e) => {
                    e.stopPropagation();
                    console.log('Edit category:', category.id);
                  }}
                  title="Edit category"
                >
                  ✏️
                </button>
              </>
            )}
            <button
              className={styles.actionBtn}
              onClick={(e) => {
                e.stopPropagation();
                console.log('Create subcategory in:', category.id);
              }}
              title="Create subcategory"
            >
              ➕
            </button>
          </div>
        )}
      </div>
      
      {isExpanded && (
        <CategoryTree
          parentId={category.id}
          selectedCategoryId={isSelected ? category.id : null}
          onCategoryClick={onClick}
          capabilities={capabilities}
          level={level + 1}
        />
      )}
    </li>
  );
};

export default CategoryNode;