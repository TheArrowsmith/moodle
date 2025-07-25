/**
 * Category API functions for Moodle web services
 */

const callMoodleWebService = async (methodname, args) => {
  const response = await fetch('/lib/ajax/service.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      methodname,
      args,
      sesskey: window.M?.cfg?.sesskey || ''
    })
  });

  if (!response.ok) {
    throw new Error(`API call failed: ${response.statusText}`);
  }

  const data = await response.json();
  
  if (data.error) {
    throw new Error(data.error);
  }

  return data;
};

/**
 * Fetch categories based on criteria
 * @param {Object} params - Query parameters
 * @param {number} params.parent - Parent category ID
 * @param {number[]} params.ids - Specific category IDs to fetch
 * @returns {Promise<Array>} Array of category objects
 */
export const fetchCategories = async (params = {}) => {
  const args = {};
  
  if (params.parent !== undefined) {
    args.criteria = [{
      key: 'parent',
      value: params.parent
    }];
  } else if (params.ids) {
    args.criteria = params.ids.map(id => ({
      key: 'id',
      value: id
    }));
  }

  args.addsubcategories = params.includeSubcategories || 0;

  const response = await callMoodleWebService('core_course_get_categories', args);
  return response;
};

/**
 * Create a new category
 * @param {Object} category - Category data
 * @returns {Promise<Object>} Created category
 */
export const createCategory = async (category) => {
  const response = await callMoodleWebService('core_course_create_categories', {
    categories: [{
      name: category.name,
      parent: category.parent || 0,
      idnumber: category.idnumber || '',
      description: category.description || '',
      descriptionformat: 1, // HTML format
      theme: category.theme || ''
    }]
  });

  return response[0];
};

/**
 * Update a category
 * @param {number} id - Category ID
 * @param {Object} updates - Fields to update
 * @returns {Promise<Object>} Updated category
 */
export const updateCategory = async (id, updates) => {
  const response = await callMoodleWebService('core_course_update_categories', {
    categories: [{
      id,
      ...updates
    }]
  });

  // Fetch the updated category to return full object
  const updated = await fetchCategories({ ids: [id] });
  return updated[0];
};

/**
 * Delete a category
 * @param {number} id - Category ID
 * @param {Object} options - Deletion options
 * @param {number} options.newparent - New parent for content (if moving)
 * @param {boolean} options.recursive - Delete all content recursively
 * @returns {Promise<void>}
 */
export const deleteCategory = async (id, options = {}) => {
  await callMoodleWebService('core_course_delete_categories', {
    categories: [{
      id,
      newparent: options.newparent,
      recursive: options.recursive ? 1 : 0
    }]
  });
};

/**
 * Move a category to a new parent
 * @param {number} categoryId - Category to move
 * @param {number} newParentId - New parent category ID
 * @returns {Promise<void>}
 */
export const moveCategory = async (categoryId, newParentId) => {
  // This uses a custom AJAX endpoint
  const response = await fetch('/course/management.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams({
      sesskey: window.M?.cfg?.sesskey || '',
      action: 'movecategory',
      categoryid: categoryId,
      newparent: newParentId
    })
  });

  if (!response.ok) {
    throw new Error('Failed to move category');
  }
};

/**
 * Resort categories
 * @param {number} parentId - Parent category ID
 * @param {string} sortBy - Sort field (name, idnumber, etc.)
 * @param {string} sortOrder - Sort order (asc, desc)
 * @returns {Promise<void>}
 */
export const resortCategories = async (parentId, sortBy, sortOrder = 'asc') => {
  // This uses a custom AJAX endpoint
  const response = await fetch('/course/management.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams({
      sesskey: window.M?.cfg?.sesskey || '',
      action: 'resortcategories',
      categoryid: parentId,
      resort: sortBy + (sortOrder === 'desc' ? 'desc' : '')
    })
  });

  if (!response.ok) {
    throw new Error('Failed to resort categories');
  }
};

/**
 * Hide or show a category
 * @param {number} id - Category ID
 * @param {boolean} visible - Visibility state
 * @returns {Promise<Object>} Updated category
 */
export const setCategoryVisibility = async (id, visible) => {
  const action = visible ? 'showcategory' : 'hidecategory';
  
  const response = await fetch('/course/management.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams({
      sesskey: window.M?.cfg?.sesskey || '',
      action,
      categoryid: id
    })
  });

  if (!response.ok) {
    throw new Error(`Failed to ${visible ? 'show' : 'hide'} category`);
  }

  // Return updated category
  const updated = await fetchCategories({ ids: [id] });
  return updated[0];
};

/**
 * Get category permissions for current user
 * @param {number} categoryId - Category ID
 * @returns {Promise<Object>} Object with capability flags
 */
export const getCategoryPermissions = async (categoryId) => {
  // This would need a custom web service or we check capabilities client-side
  // For now, return based on what we know from PHP
  return {
    canManage: true, // Would check 'moodle/category:manage'
    canViewHidden: true, // Would check 'moodle/category:viewhiddencategories'
    canCreate: true, // Would check 'moodle/course:create'
    canDelete: true, // Complex logic based on category content
    canMove: true // Would check parent permissions
  };
};