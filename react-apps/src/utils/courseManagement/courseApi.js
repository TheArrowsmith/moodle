/**
 * Course API functions for Moodle web services
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
 * Fetch courses based on criteria
 * @param {Object} params - Query parameters
 * @returns {Promise<Object>} Object with courses array and totalcount
 */
export const fetchCourses = async (params = {}) => {
  // Handle search separately
  if (params.search || params.blocklist || params.modulelist) {
    return searchCourses(params);
  }

  // Use get_courses_by_field for category listing
  if (params.categoryid !== undefined) {
    const response = await callMoodleWebService('core_course_get_courses_by_field', {
      field: 'category',
      value: params.categoryid
    });

    // Apply client-side sorting and pagination
    let courses = response.courses || [];
    
    // Sort
    if (params.sort) {
      courses.sort((a, b) => {
        const aVal = a[params.sort];
        const bVal = b[params.sort];
        const comparison = aVal < bVal ? -1 : aVal > bVal ? 1 : 0;
        return params.order === 'desc' ? -comparison : comparison;
      });
    }

    // Paginate
    const page = params.page || 0;
    const perpage = params.perpage || 20;
    const start = page * perpage;
    const paginatedCourses = courses.slice(start, start + perpage);

    return {
      courses: paginatedCourses,
      totalcount: courses.length
    };
  }

  // Get specific courses by ID
  if (params.ids) {
    const response = await callMoodleWebService('core_course_get_courses', {
      options: {
        ids: params.ids
      }
    });
    
    return {
      courses: response,
      totalcount: response.length
    };
  }

  // Get all courses (be careful with this)
  const response = await callMoodleWebService('core_course_get_courses');
  return {
    courses: response,
    totalcount: response.length
  };
};

/**
 * Search for courses
 * @param {Object} params - Search parameters
 * @returns {Promise<Object>} Search results
 */
const searchCourses = async (params) => {
  const response = await callMoodleWebService('core_course_search_courses', {
    criterianame: 'search',
    criteriavalue: params.search || '',
    page: params.page || 0,
    perpage: params.perpage || 20,
    // Additional filters would need custom implementation
  });

  return {
    courses: response.courses,
    totalcount: response.total
  };
};

/**
 * Get detailed course information
 * @param {number} courseId - Course ID
 * @returns {Promise<Object>} Detailed course data
 */
export const fetchCourseDetails = async (courseId) => {
  // Get course contents and other details
  const [contents, courseInfo] = await Promise.all([
    callMoodleWebService('core_course_get_contents', { courseid: courseId }),
    callMoodleWebService('core_course_get_courses', { 
      options: { ids: [courseId] } 
    })
  ]);

  const course = courseInfo[0];
  
  // Count modules
  const moduleCount = {};
  let totalActivities = 0;
  
  contents.forEach(section => {
    section.modules?.forEach(module => {
      moduleCount[module.modname] = (moduleCount[module.modname] || 0) + 1;
      totalActivities++;
    });
  });

  return {
    ...course,
    sections: contents,
    moduleCount,
    totalActivities,
    sectionCount: contents.length
  };
};

/**
 * Create a new course
 * @param {Object} courseData - Course data
 * @returns {Promise<Object>} Created course
 */
export const createCourse = async (courseData) => {
  const response = await callMoodleWebService('core_course_create_courses', {
    courses: [{
      fullname: courseData.fullname,
      shortname: courseData.shortname,
      categoryid: courseData.categoryid,
      idnumber: courseData.idnumber || '',
      summary: courseData.summary || '',
      summaryformat: 1, // HTML
      format: courseData.format || 'topics',
      showgrades: courseData.showgrades !== false ? 1 : 0,
      newsitems: courseData.newsitems || 5,
      startdate: courseData.startdate || Math.floor(Date.now() / 1000),
      enddate: courseData.enddate || 0,
      numsections: courseData.numsections || 10,
      maxbytes: courseData.maxbytes || 0,
      showreports: courseData.showreports !== false ? 1 : 0,
      visible: courseData.visible !== false ? 1 : 0,
      groupmode: courseData.groupmode || 0,
      groupmodeforce: courseData.groupmodeforce || 0,
      defaultgroupingid: courseData.defaultgroupingid || 0,
      enablecompletion: courseData.enablecompletion || 0,
      completionnotify: courseData.completionnotify || 0,
      lang: courseData.lang || '',
      forcetheme: courseData.forcetheme || ''
    }]
  });

  return response[0];
};

/**
 * Update a course
 * @param {number} id - Course ID
 * @param {Object} updates - Fields to update
 * @returns {Promise<Object>} Updated course
 */
export const updateCourse = async (id, updates) => {
  await callMoodleWebService('core_course_update_courses', {
    courses: [{
      id,
      ...updates
    }]
  });

  // Fetch updated course
  const response = await fetchCourses({ ids: [id] });
  return response.courses[0];
};

/**
 * Delete a course
 * @param {number} id - Course ID
 * @returns {Promise<void>}
 */
export const deleteCourse = async (id) => {
  await callMoodleWebService('core_course_delete_courses', {
    courseids: [id]
  });
};

/**
 * Move courses to a different category
 * @param {number[]} courseIds - Course IDs to move
 * @param {number} categoryId - Target category ID
 * @returns {Promise<void>}
 */
export const moveCourses = async (courseIds, categoryId) => {
  // This uses a custom endpoint
  const response = await fetch('/course/management.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams({
      sesskey: window.M?.cfg?.sesskey || '',
      action: 'bulkaction',
      bulkmovecourses: '1',
      movecoursesto: categoryId,
      ...courseIds.reduce((acc, id, idx) => {
        acc[`bc[${idx}]`] = id;
        return acc;
      }, {})
    })
  });

  if (!response.ok) {
    throw new Error('Failed to move courses');
  }
};

/**
 * Hide or show a course
 * @param {number} id - Course ID
 * @param {boolean} visible - Visibility state
 * @returns {Promise<Object>} Updated course
 */
export const setCourseVisibility = async (id, visible) => {
  return updateCourse(id, { visible: visible ? 1 : 0 });
};

/**
 * Resort courses in a category
 * @param {number} categoryId - Category ID
 * @param {string} sortBy - Sort field
 * @param {string} sortOrder - Sort order
 * @returns {Promise<void>}
 */
export const resortCourses = async (categoryId, sortBy, sortOrder = 'asc') => {
  const response = await fetch('/course/management.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams({
      sesskey: window.M?.cfg?.sesskey || '',
      action: 'resortcourses',
      categoryid: categoryId,
      resort: sortBy + (sortOrder === 'desc' ? 'desc' : '')
    })
  });

  if (!response.ok) {
    throw new Error('Failed to resort courses');
  }
};

/**
 * Duplicate a course
 * @param {number} courseId - Course to duplicate
 * @param {Object} options - Duplication options
 * @returns {Promise<Object>} New course
 */
export const duplicateCourse = async (courseId, options = {}) => {
  const response = await callMoodleWebService('core_course_duplicate_course', {
    courseid: courseId,
    fullname: options.fullname,
    shortname: options.shortname,
    categoryid: options.categoryid,
    visible: options.visible !== false ? 1 : 0,
    options: options.copyOptions || []
  });

  return response;
};

/**
 * Get course enrolment methods
 * @param {number} courseId - Course ID
 * @returns {Promise<Array>} Enrolment methods
 */
export const getCourseEnrolmentMethods = async (courseId) => {
  const response = await callMoodleWebService('core_enrol_get_course_enrolment_methods', {
    courseid: courseId
  });

  return response;
};