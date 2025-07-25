# Course Management React Components - Full Specification

## Overview

This specification defines the complete functionality for replacing Moodle's course/management.php interface with React components. The goal is to create drop-in replacements that maintain all existing functionality while using the Course API for backend operations.

## Current Implementation Status

### ✅ Completed Features

#### 1. Three-Panel Layout
- **Left Panel**: Category tree navigation with API integration
- **Middle Panel**: Course listing for selected category with API data
- **Right Panel**: Course details panel showing sections and activities

#### 2. API Integration
All components now use the Course Management API with JWT authentication:
- Components receive JWT token as prop
- All API calls go through `/local/courseapi/api/index.php/`
- Proper error handling and loading states

#### 3. Working Components

##### CourseManagementApp (`react-apps/src/components/CourseManagement/CourseManagementApp.jsx`)
**Status**: ✅ Fully implemented
- Manages global state (selected category, selected course, view mode)
- Coordinates communication between panels
- Handles authentication token distribution
- Responsive layout adjusts based on selections

**Current Props**:
```typescript
{
  token: string,              // JWT authentication token
  initialCategoryId?: number, // Starting category ID (defaults to 1)
  initialCourseId?: number,   // Starting course ID from URL
  viewMode?: string,          // View mode (default only for now)
  page?: number,              // Current page
  perPage?: number,           // Items per page
  capabilities: {             // User permissions
    'moodle/category:manage': boolean,
    'moodle/course:create': boolean,
    'moodle/course:update': boolean,
    'moodle/course:visibility': boolean,
    // ... other capabilities
  }
}
```

##### CategoryManagementPanel (`react-apps/src/components/CourseManagement/CategoryManagementPanel.jsx`)
**Status**: ✅ Core functionality implemented
- Fetches categories from `/category/tree` API endpoint
- Displays hierarchical category tree with expand/collapse
- Shows course count per category
- Category selection updates course listing
- Create category button (redirects to Moodle form with parent parameter)

**Working Features**:
- ✅ Tree navigation with expand/collapse
- ✅ Category selection
- ✅ Course count display
- ✅ Create category button with parent context
- ✅ Visual feedback for selected category

**Missing Features**:
- ❌ Category visibility toggle (API endpoint exists but not wired up)
- ❌ Category reordering (needs API endpoints)
- ❌ Inline category editing
- ❌ Category deletion
- ❌ Bulk operations
- ❌ Context menus

##### CourseManagementPanel (`react-apps/src/components/CourseManagement/CourseManagementPanel.jsx`)
**Status**: ✅ Core functionality implemented
- Fetches courses from `/course/list` API endpoint
- Displays course cards with full details
- Shows category name in header
- Create course button (redirects to Moodle form with category pre-selected)
- Pagination controls

**Working Features**:
- ✅ Course listing with details (name, summary, teachers, enrollment)
- ✅ Category name display
- ✅ Course selection
- ✅ Create course button with category context
- ✅ Pagination
- ✅ Loading and error states
- ✅ Visual feedback for selected course

**Missing Features**:
- ❌ Course visibility toggle (handler exists but API endpoint needed)
- ❌ Course reordering
- ❌ Sorting options
- ❌ Search functionality
- ❌ Bulk operations
- ❌ Filter by activity/resource types

##### CourseDetailPanel (`react-apps/src/components/CourseManagement/CourseDetailPanel.jsx`)
**Status**: ✅ Basic implementation
- Fetches course details from `/course/{id}/management_data` API
- Displays course information and structure
- Shows sections with activities

**Working Features**:
- ✅ Course title and details
- ✅ Section listing
- ✅ Activity listing per section
- ✅ Edit course link

**Missing Features**:
- ❌ Activity management (show/hide, move, delete)
- ❌ Section management
- ❌ Quick action buttons
- ❌ Enrollment information
- ❌ Course settings display

### 🔧 API Endpoints Implemented

The following endpoints were added to support the React components:

#### Category Management
- `GET /category/tree` - Returns full category hierarchy
- `GET /category/{id}` - Get single category details
- `POST /category` - Create new category
- `PUT /category/{id}` - Update category
- `DELETE /category/{id}` - Delete category
- `POST /category/{id}/visibility` - Toggle visibility

#### Course Management  
- `GET /course/list` - List courses with pagination and filtering
- `POST /course` - Create new course
- `PUT /course/{id}` - Update course
- `DELETE /course/{id}` - Delete course
- `POST /course/{id}/visibility` - Toggle visibility
- `POST /course/{id}/move` - Move to different category

#### Activity Management
- `GET /activity/list?courseid={id}` - List all activities
- `POST /activity/{id}/visibility` - Toggle visibility
- `POST /activity/{id}/duplicate` - Duplicate activity

#### Section Management
- `POST /section` - Create new section
- `DELETE /section/{id}` - Delete section
- `POST /section/{id}/visibility` - Toggle visibility

### 📁 File Structure

```
react-apps/
├── src/
│   ├── components/
│   │   └── CourseManagement/
│   │       ├── CourseManagementApp.jsx
│   │       ├── CourseManagementApp.module.css
│   │       ├── CategoryManagementPanel.jsx
│   │       ├── CategoryManagementPanel.module.css
│   │       ├── CourseManagementPanel.jsx
│   │       ├── CourseManagementPanel.module.css
│   │       ├── CourseDetailPanel.jsx
│   │       └── CourseDetailPanel.module.css
│   └── main.jsx (registers components in global MoodleReact)
├── package.json
└── vite.config.js

course/
├── management_react.php (test page for full React interface)
└── test_react.php (original test page for individual components)

react-dist/ (build output)
├── moodle-react.iife.js
└── style.css
```

## 🚀 Desired Additional Functionality

### Phase 1: Complete Core Features (Priority)

#### 1. Wire Up Existing API Endpoints
Many API endpoints exist but aren't connected to the UI:
- Course visibility toggle
- Category visibility toggle  
- Category management operations
- Activity visibility toggle

#### 2. Search and Filtering
- Add search box to course panel
- Implement course search API
- Add activity/resource type filters
- Search across all categories option

#### 3. Sorting Options
- Sort courses by:
  - Name (A-Z, Z-A)
  - Creation date
  - Last modified
  - Enrollment count
- Remember user's sort preference

#### 4. Bulk Operations
- Select multiple courses/categories
- Bulk move to different category
- Bulk visibility toggle
- Bulk delete with confirmation

### Phase 2: Enhanced UX

#### 1. Inline Editing
- Edit category names inline
- Quick edit course names
- Edit section names directly

#### 2. Drag and Drop
- Reorder categories by dragging
- Move courses between categories
- Reorder activities within sections
- Visual drop zones

#### 3. Context Menus
- Right-click on categories/courses
- Quick access to common actions
- Keyboard shortcuts

#### 4. Real-time Updates
- WebSocket integration for live updates
- Show when other users make changes
- Conflict resolution for concurrent edits

### Phase 3: Advanced Features

#### 1. Advanced Search
- Full-text search across course content
- Filter by multiple criteria
- Save search queries
- Search history

#### 2. Batch Import/Export
- Import courses from CSV
- Export course lists
- Bulk course creation templates

#### 3. Analytics Integration
- Show course activity graphs
- Enrollment trends
- Completion rates inline

#### 4. Mobile Optimization
- Touch-friendly interface
- Swipe gestures
- Responsive panels that stack on mobile

## 🔧 Technical Improvements Needed

### State Management
Currently using local component state. Should migrate to:
- Zustand for global state management
- React Query for API cache management
- Optimistic updates for better UX

### Performance Optimizations
- Virtual scrolling for large lists
- Lazy loading for course details
- Debounced search
- Prefetch on hover

### Testing
- Unit tests for all components
- Integration tests for API calls
- E2E tests for critical workflows
- Accessibility testing

### Error Handling
- Better error messages
- Retry mechanisms
- Offline support
- Session timeout handling

## 📋 Implementation Checklist

### Immediate Tasks
- [ ] Connect course visibility toggle to API
- [ ] Connect category visibility toggle to API  
- [ ] Add course search functionality
- [ ] Add sorting dropdown to course panel
- [ ] Implement bulk selection UI
- [ ] Add loading skeletons

### Short Term (1-2 weeks)
- [ ] Add Zustand for state management
- [ ] Implement React Query for caching
- [ ] Add inline editing for names
- [ ] Create context menus
- [ ] Add keyboard navigation
- [ ] Write component tests

### Medium Term (3-4 weeks)
- [ ] Drag and drop support
- [ ] Advanced search interface
- [ ] Mobile responsive design
- [ ] WebSocket integration
- [ ] Performance optimizations
- [ ] E2E test suite

## 🎯 Success Metrics

### Current Achievement
- ✅ All panels load and display data from API
- ✅ Basic CRUD operations work through Moodle forms
- ✅ Navigation between categories and courses works
- ✅ JWT authentication properly integrated
- ✅ Error handling shows user-friendly messages

### Target Metrics
- Page load time < 2 seconds
- API response time < 500ms
- 100% feature parity with legacy interface
- Zero data loss during operations
- WCAG 2.1 AA accessibility compliance
- 80%+ test coverage

## 🚦 Migration Strategy

### Current Status
- Test pages available at `/course/management_react.php`
- Components can be embedded individually
- API backend fully supports both old and new interfaces

### Next Steps
1. Complete missing UI features
2. Add feature flag to enable React UI
3. Beta test with selected users
4. Gradual rollout with fallback option
5. Full deployment once stable

## 📝 Developer Notes

### Running Locally
```bash
cd react-apps
npm install
npm run dev    # Development with hot reload
npm run build  # Production build
```

### Testing API
```bash
# Get auth token
curl -X POST http://localhost:8888/local/courseapi/api/index.php/auth/token \
  -d "username=admin&password=password"

# Test category endpoint
curl http://localhost:8888/local/courseapi/api/index.php/category/tree \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Adding New Features
1. Check if API endpoint exists in `/local/courseapi/`
2. Update component to use the endpoint
3. Add error handling and loading states
4. Update this spec with the new feature
5. Test with different user roles

## Conclusion

The React course management interface has successfully replaced the core functionality of Moodle's legacy interface. All major components are working with API integration. The next phase is to add the remaining features for complete feature parity and then enhance the UX beyond what the legacy interface provides.