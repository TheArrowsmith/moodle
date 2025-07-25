import React from 'react';
import { useCategoryStore } from '../../../stores/categoryStore';
import CategoryNode from './CategoryNode';
import styles from './CategoryTree.module.css';

const CategoryTree = ({ 
  parentId, 
  selectedCategoryId, 
  onCategoryClick, 
  capabilities,
  level = 0 
}) => {
  const { getChildCategories } = useCategoryStore();
  const categories = getChildCategories(parentId);

  if (categories.length === 0 && level > 0) {
    return null;
  }

  return (
    <ul 
      className={styles.tree} 
      role={level === 0 ? "tree" : "group"}
      aria-label={level === 0 ? "Category tree" : undefined}
    >
      {level === 0 && (
        <CategoryNode
          category={{ id: 0, name: 'Top', parent: null }}
          isSelected={selectedCategoryId === 0}
          onClick={() => onCategoryClick({ id: 0, name: 'Top' })}
          capabilities={capabilities}
          level={0}
          isTop={true}
        />
      )}
      
      {categories.map(category => (
        <CategoryNode
          key={category.id}
          category={category}
          isSelected={selectedCategoryId === category.id}
          onClick={() => onCategoryClick(category)}
          capabilities={capabilities}
          level={level}
        />
      ))}
    </ul>
  );
};

export default CategoryTree;