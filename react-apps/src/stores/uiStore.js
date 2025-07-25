import { create } from 'zustand';
import { devtools } from 'zustand/middleware';

export const useUIStore = create(devtools((set, get) => ({
  // State
  viewMode: 'default', // 'default', 'combined', 'courses', 'categories'
  bulkMode: false,
  selectedItems: new Set(), // Selected items for bulk operations
  itemType: null, // 'course' or 'category'
  searchActive: false,
  notifications: [], // Toast notifications
  draggedItem: null, // Currently dragged item
  dropTarget: null, // Current drop target

  // Actions
  setViewMode: (mode) => {
    set({ viewMode: mode });
  },

  toggleBulkMode: () => {
    set(state => ({
      bulkMode: !state.bulkMode,
      selectedItems: state.bulkMode ? new Set() : state.selectedItems,
      itemType: state.bulkMode ? null : state.itemType
    }));
  },

  toggleItemSelection: (id, type) => {
    set(state => {
      const newSelected = new Set(state.selectedItems);
      
      // If switching item types, clear selection
      if (state.itemType && state.itemType !== type) {
        return {
          selectedItems: new Set([id]),
          itemType: type
        };
      }

      if (newSelected.has(id)) {
        newSelected.delete(id);
      } else {
        newSelected.add(id);
      }

      return {
        selectedItems: newSelected,
        itemType: newSelected.size > 0 ? type : null
      };
    });
  },

  selectAll: (ids, type) => {
    set({
      selectedItems: new Set(ids),
      itemType: type
    });
  },

  clearSelection: () => {
    set({
      selectedItems: new Set(),
      itemType: null
    });
  },

  setSearchActive: (active) => {
    set({ searchActive: active });
  },

  // Notification system
  showNotification: (message, type = 'info', duration = 5000) => {
    const id = Date.now();
    const notification = { id, message, type };
    
    set(state => ({
      notifications: [...state.notifications, notification]
    }));

    if (duration > 0) {
      setTimeout(() => {
        set(state => ({
          notifications: state.notifications.filter(n => n.id !== id)
        }));
      }, duration);
    }

    return id;
  },

  dismissNotification: (id) => {
    set(state => ({
      notifications: state.notifications.filter(n => n.id !== id)
    }));
  },

  // Drag and drop
  setDraggedItem: (item) => {
    set({ draggedItem: item });
  },

  setDropTarget: (target) => {
    set({ dropTarget: target });
  },

  clearDragState: () => {
    set({ draggedItem: null, dropTarget: null });
  },

  // Utility methods
  getSelectedCount: () => {
    return get().selectedItems.size;
  },

  isSelected: (id) => {
    return get().selectedItems.has(id);
  },

  showSuccess: (message) => {
    return get().showNotification(message, 'success');
  },

  showError: (message) => {
    return get().showNotification(message, 'error', 10000);
  },

  showWarning: (message) => {
    return get().showNotification(message, 'warning', 7000);
  }
})));