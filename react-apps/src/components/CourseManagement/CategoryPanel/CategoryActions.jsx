import React, { useState } from 'react';
import { useCategoryStore } from '../../../stores/categoryStore';
import { useUIStore } from '../../../stores/uiStore';
import styles from './CategoryActions.module.css';

const CategoryActions = ({ selectedCategory, bulkMode, selectedItems }) => {
  const { createCategory, deleteCategory } = useCategoryStore();
  const { toggleBulkMode, showSuccess, showError } = useUIStore();
  const [showCreateForm, setShowCreateForm] = useState(false);
  const [creating, setCreating] = useState(false);

  const handleCreateCategory = async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const name = formData.get('name').trim();
    
    if (!name) return;

    setCreating(true);
    try {
      await createCategory({
        name,
        parent: selectedCategory?.id || 0,
        description: formData.get('description') || ''
      });
      
      showSuccess(`Category "${name}" created successfully`);
      setShowCreateForm(false);
      e.target.reset();
    } catch (error) {
      showError(`Failed to create category: ${error.message}`);
    }
    setCreating(false);
  };

  const handleDeleteSelected = async () => {
    if (selectedItems.size === 0) return;
    
    const confirmed = window.confirm(
      `Are you sure you want to delete ${selectedItems.size} categories? This action cannot be undone.`
    );
    
    if (!confirmed) return;

    try {
      for (const categoryId of selectedItems) {
        await deleteCategory(categoryId, { recursive: true });
      }
      showSuccess(`${selectedItems.size} categories deleted successfully`);
    } catch (error) {
      showError(`Failed to delete categories: ${error.message}`);
    }
  };

  return (
    <div className={styles.actions}>
      <div className={styles.primaryActions}>
        <button
          className={styles.createBtn}
          onClick={() => setShowCreateForm(!showCreateForm)}
          title="Create new category"
        >
          + New
        </button>
        
        <button
          className={`${styles.bulkBtn} ${bulkMode ? styles.active : ''}`}
          onClick={toggleBulkMode}
          title="Toggle bulk selection mode"
        >
          Select
        </button>
      </div>

      {bulkMode && selectedItems.size > 0 && (
        <div className={styles.bulkActions}>
          <button
            className={styles.deleteBtn}
            onClick={handleDeleteSelected}
          >
            Delete ({selectedItems.size})
          </button>
        </div>
      )}

      {showCreateForm && (
        <div className={styles.createForm}>
          <form onSubmit={handleCreateCategory}>
            <div className={styles.formGroup}>
              <label htmlFor="category-name">Category name *</label>
              <input
                id="category-name"
                name="name"
                type="text"
                required
                placeholder="Enter category name"
                autoFocus
              />
            </div>
            
            <div className={styles.formGroup}>
              <label htmlFor="category-description">Description</label>
              <textarea
                id="category-description"
                name="description"
                rows="3"
                placeholder="Optional description"
              />
            </div>
            
            <div className={styles.formActions}>
              <button
                type="submit"
                disabled={creating}
                className={styles.submitBtn}
              >
                {creating ? 'Creating...' : 'Create'}
              </button>
              <button
                type="button"
                onClick={() => setShowCreateForm(false)}
                className={styles.cancelBtn}
              >
                Cancel
              </button>
            </div>
          </form>
        </div>
      )}
    </div>
  );
};

export default CategoryActions;