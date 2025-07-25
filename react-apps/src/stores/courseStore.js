import { create } from 'zustand';
import { devtools } from 'zustand/middleware';
import { 
  fetchCourses, 
  fetchCourseDetails,
  updateCourse, 
  deleteCourse,
  moveCourses 
} from '../utils/courseManagement/courseApi';

export const useCourseStore = create(devtools((set, get) => ({
  // State
  courses: new Map(), // courseId -> course object
  selectedCourse: null,
  courseLists: new Map(), // categoryId -> { courses: [], total: 0, page: 0 }
  loadingCourses: new Set(), // categories currently loading courses
  courseDetails: new Map(), // courseId -> detailed course info

  // Actions
  loadCourses: async (categoryId, page = 0, perPage = 20, sortBy = 'fullname', sortOrder = 'asc') => {
    const state = get();
    
    // Check if already loading
    if (state.loadingCourses.has(categoryId)) {
      return;
    }

    set(state => ({
      loadingCourses: new Set([...state.loadingCourses, categoryId])
    }));

    try {
      const result = await fetchCourses({
        categoryid: categoryId,
        page,
        perpage: perPage,
        sort: sortBy,
        order: sortOrder
      });

      set(state => {
        const newCourses = new Map(state.courses);
        const newCourseLists = new Map(state.courseLists);
        const newLoading = new Set(state.loadingCourses);
        
        // Store each course
        result.courses.forEach(course => {
          newCourses.set(course.id, course);
        });

        // Store the list info
        newCourseLists.set(categoryId, {
          courses: result.courses.map(c => c.id),
          total: result.totalcount,
          page,
          perPage,
          sortBy,
          sortOrder
        });

        newLoading.delete(categoryId);

        return {
          courses: newCourses,
          courseLists: newCourseLists,
          loadingCourses: newLoading
        };
      });

      return result;
    } catch (error) {
      console.error('Failed to load courses:', error);
      set(state => {
        const newLoading = new Set(state.loadingCourses);
        newLoading.delete(categoryId);
        return { loadingCourses: newLoading };
      });
      throw error;
    }
  },

  setSelectedCourse: async (courseOrId) => {
    if (!courseOrId) {
      set({ selectedCourse: null });
      return;
    }

    const id = typeof courseOrId === 'object' ? courseOrId.id : courseOrId;
    const state = get();
    
    let course = state.courses.get(id);
    if (!course) {
      // Load basic course info if not in cache
      const courses = await fetchCourses({ ids: [id] });
      if (courses.courses.length > 0) {
        course = courses.courses[0];
        set(state => {
          const newCourses = new Map(state.courses);
          newCourses.set(id, course);
          return { courses: newCourses };
        });
      }
    }

    set({ selectedCourse: course });
  },

  loadCourseDetails: async (courseId) => {
    const state = get();
    
    // Check if already cached
    if (state.courseDetails.has(courseId)) {
      return state.courseDetails.get(courseId);
    }

    try {
      const details = await fetchCourseDetails(courseId);
      
      set(state => {
        const newDetails = new Map(state.courseDetails);
        newDetails.set(courseId, details);
        return { courseDetails: newDetails };
      });

      return details;
    } catch (error) {
      console.error('Failed to load course details:', error);
      throw error;
    }
  },

  updateCourse: async (id, data) => {
    try {
      const updated = await updateCourse(id, data);
      
      set(state => {
        const newCourses = new Map(state.courses);
        const newDetails = new Map(state.courseDetails);
        
        newCourses.set(id, updated);
        // Clear cached details to force reload
        newDetails.delete(id);
        
        return { 
          courses: newCourses,
          courseDetails: newDetails,
          selectedCourse: state.selectedCourse?.id === id ? updated : state.selectedCourse
        };
      });

      return updated;
    } catch (error) {
      console.error('Failed to update course:', error);
      throw error;
    }
  },

  deleteCourse: async (id) => {
    try {
      await deleteCourse(id);
      
      set(state => {
        const newCourses = new Map(state.courses);
        const newDetails = new Map(state.courseDetails);
        const newCourseLists = new Map(state.courseLists);
        
        // Remove the course
        newCourses.delete(id);
        newDetails.delete(id);
        
        // Remove from category lists
        newCourseLists.forEach((list, categoryId) => {
          list.courses = list.courses.filter(courseId => courseId !== id);
          list.total = Math.max(0, list.total - 1);
        });

        return {
          courses: newCourses,
          courseDetails: newDetails,
          courseLists: newCourseLists,
          selectedCourse: state.selectedCourse?.id === id ? null : state.selectedCourse
        };
      });
    } catch (error) {
      console.error('Failed to delete course:', error);
      throw error;
    }
  },

  moveCourses: async (courseIds, targetCategoryId) => {
    try {
      await moveCourses(courseIds, targetCategoryId);
      
      // Clear course lists to force reload
      set(state => ({
        courseLists: new Map()
      }));
      
      // Reload the target category
      await get().loadCourses(targetCategoryId);
    } catch (error) {
      console.error('Failed to move courses:', error);
      throw error;
    }
  },

  // Utility methods
  getCoursesForCategory: (categoryId) => {
    const state = get();
    const listInfo = state.courseLists.get(categoryId);
    
    if (!listInfo) {
      return { courses: [], total: 0, loaded: false };
    }

    const courses = listInfo.courses
      .map(id => state.courses.get(id))
      .filter(Boolean);

    return {
      courses,
      total: listInfo.total,
      page: listInfo.page,
      perPage: listInfo.perPage,
      loaded: true
    };
  },

  searchCourses: async (searchParams) => {
    try {
      const results = await fetchCourses({
        search: searchParams.text,
        blocklist: searchParams.blockId,
        modulelist: searchParams.moduleType,
        ...searchParams
      });

      // Store search results
      set(state => {
        const newCourses = new Map(state.courses);
        
        results.courses.forEach(course => {
          newCourses.set(course.id, course);
        });

        return { courses: newCourses };
      });

      return results;
    } catch (error) {
      console.error('Failed to search courses:', error);
      throw error;
    }
  }
})));