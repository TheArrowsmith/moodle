import { create } from 'zustand';
import { devtools } from 'zustand/middleware';
import { fetchCategories, updateCategory, deleteCategory } from '../utils/courseManagement/categoryApi';

export const useCategoryStore = create(devtools((set, get) => ({
  // State
  categories: new Map(), // categoryId -> category object
  expandedCategories: new Set(), // expanded category IDs
  selectedCategory: null,
  loadingCategories: new Set(), // categories currently being loaded
  categoryChildren: new Map(), // categoryId -> array of child category IDs

  // Actions
  loadCategory: async (id, forceReload = false) => {
    const state = get();
    
    // Check if already loaded and not forcing reload
    if (!forceReload && state.categories.has(id)) {
      return state.categories.get(id);
    }

    // Check if already loading
    if (state.loadingCategories.has(id)) {
      return;
    }

    set(state => ({
      loadingCategories: new Set([...state.loadingCategories, id])
    }));

    try {
      const categories = await fetchCategories({ parent: id });
      
      set(state => {
        const newCategories = new Map(state.categories);
        const newChildren = new Map(state.categoryChildren);
        
        // Store the parent's children IDs
        newChildren.set(id, categories.map(cat => cat.id));
        
        // Store each category
        categories.forEach(cat => {
          newCategories.set(cat.id, cat);
        });

        const newLoading = new Set(state.loadingCategories);
        newLoading.delete(id);

        return {
          categories: newCategories,
          categoryChildren: newChildren,
          loadingCategories: newLoading
        };
      });

      return categories;
    } catch (error) {
      console.error('Failed to load categories:', error);
      set(state => {
        const newLoading = new Set(state.loadingCategories);
        newLoading.delete(id);
        return { loadingCategories: newLoading };
      });
      throw error;
    }
  },

  toggleExpanded: (id) => {
    set(state => {
      const newExpanded = new Set(state.expandedCategories);
      if (newExpanded.has(id)) {
        newExpanded.delete(id);
      } else {
        newExpanded.add(id);
        // Load children if not already loaded
        if (!state.categoryChildren.has(id)) {
          state.loadCategory(id);
        }
      }
      return { expandedCategories: newExpanded };
    });
  },

  setSelectedCategory: async (categoryOrId) => {
    const id = typeof categoryOrId === 'object' ? categoryOrId.id : categoryOrId;
    const state = get();
    
    let category = state.categories.get(id);
    if (!category && id !== 0) {
      // Load category if not in cache
      const categories = await fetchCategories({ ids: [id] });
      if (categories.length > 0) {
        category = categories[0];
        set(state => {
          const newCategories = new Map(state.categories);
          newCategories.set(id, category);
          return { categories: newCategories };
        });
      }
    }

    set({ selectedCategory: category || { id: 0, name: 'Top' } });
  },

  updateCategory: async (id, data) => {
    try {
      const updated = await updateCategory(id, data);
      
      set(state => {
        const newCategories = new Map(state.categories);
        newCategories.set(id, updated);
        return { categories: newCategories };
      });

      return updated;
    } catch (error) {
      console.error('Failed to update category:', error);
      throw error;
    }
  },

  deleteCategory: async (id, deleteOption) => {
    try {
      await deleteCategory(id, deleteOption);
      
      set(state => {
        const newCategories = new Map(state.categories);
        const newChildren = new Map(state.categoryChildren);
        const newExpanded = new Set(state.expandedCategories);
        
        // Remove the category
        newCategories.delete(id);
        newChildren.delete(id);
        newExpanded.delete(id);
        
        // Remove from parent's children
        newCategories.forEach((cat, catId) => {
          if (newChildren.has(catId)) {
            const children = newChildren.get(catId).filter(childId => childId !== id);
            newChildren.set(catId, children);
          }
        });

        return {
          categories: newCategories,
          categoryChildren: newChildren,
          expandedCategories: newExpanded,
          selectedCategory: state.selectedCategory?.id === id ? null : state.selectedCategory
        };
      });
    } catch (error) {
      console.error('Failed to delete category:', error);
      throw error;
    }
  },

  // Utility methods
  getCategoryPath: (id) => {
    const state = get();
    const path = [];
    let currentId = id;

    while (currentId && currentId !== 0) {
      const category = state.categories.get(currentId);
      if (category) {
        path.unshift(category);
        currentId = category.parent;
      } else {
        break;
      }
    }

    return path;
  },

  getChildCategories: (parentId) => {
    const state = get();
    const childIds = state.categoryChildren.get(parentId) || [];
    return childIds.map(id => state.categories.get(id)).filter(Boolean);
  }
})));