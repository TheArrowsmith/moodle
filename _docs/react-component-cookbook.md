# React Component Cookbook for Moodle

This cookbook provides ready-to-use patterns and examples for common Moodle UI components built with React.

## Table of Contents

1. [File Upload Component](#file-upload-component)
2. [Course Card Grid](#course-card-grid)
3. [Real-time Notifications](#real-time-notifications)
4. [Activity Completion Tracker](#activity-completion-tracker)
5. [User Avatar with Presence](#user-avatar-with-presence)
6. [Forum Post Editor](#forum-post-editor)
7. [Grade Display Component](#grade-display-component)
8. [Search Autocomplete](#search-autocomplete)

## File Upload Component

A drag-and-drop file uploader that integrates with Moodle's file API.

### Component Code

```jsx
// react-apps/src/components/FileUploader.jsx
import React, { useState, useCallback } from 'react';
import styles from './FileUploader.module.css';

const FileUploader = ({ 
  contextId, 
  component = 'user', 
  fileArea = 'draft', 
  itemId = 0,
  acceptedTypes = '*',
  maxSize = 10485760, // 10MB default
  onUploadComplete 
}) => {
  const [isDragging, setIsDragging] = useState(false);
  const [files, setFiles] = useState([]);
  const [uploading, setUploading] = useState(false);
  const [progress, setProgress] = useState({});

  const handleDrop = useCallback((e) => {
    e.preventDefault();
    setIsDragging(false);
    
    const droppedFiles = Array.from(e.dataTransfer.files);
    handleFiles(droppedFiles);
  }, []);

  const handleFiles = async (fileList) => {
    const validFiles = fileList.filter(file => {
      if (file.size > maxSize) {
        alert(`${file.name} is too large. Max size: ${maxSize / 1048576}MB`);
        return false;
      }
      return true;
    });

    setFiles(prev => [...prev, ...validFiles]);
    
    // Upload each file
    for (const file of validFiles) {
      await uploadFile(file);
    }
  };

  const uploadFile = async (file) => {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('contextid', contextId);
    formData.append('component', component);
    formData.append('filearea', fileArea);
    formData.append('itemid', itemId);
    formData.append('filepath', '/');
    formData.append('filename', file.name);
    formData.append('sesskey', window.M.cfg.sesskey);

    try {
      setUploading(true);
      setProgress(prev => ({ ...prev, [file.name]: 0 }));

      const xhr = new XMLHttpRequest();
      
      xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
          const percentComplete = (e.loaded / e.total) * 100;
          setProgress(prev => ({ ...prev, [file.name]: percentComplete }));
        }
      });

      xhr.onload = function() {
        if (xhr.status === 200) {
          const response = JSON.parse(xhr.responseText);
          if (onUploadComplete) {
            onUploadComplete(response);
          }
        }
      };

      xhr.open('POST', '/repository/repository_ajax.php?action=upload');
      xhr.send(formData);
      
    } catch (error) {
      console.error('Upload failed:', error);
    } finally {
      setUploading(false);
    }
  };

  return (
    <div 
      className={`${styles.uploader} ${isDragging ? styles.dragging : ''}`}
      onDragOver={(e) => { e.preventDefault(); setIsDragging(true); }}
      onDragLeave={() => setIsDragging(false)}
      onDrop={handleDrop}
    >
      <div className={styles.dropZone}>
        <svg className={styles.icon} fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} 
                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
        <p>Drag files here or click to browse</p>
        <input 
          type="file" 
          multiple 
          onChange={(e) => handleFiles(Array.from(e.target.files))}
          className={styles.input}
          accept={acceptedTypes}
        />
      </div>
      
      {files.length > 0 && (
        <div className={styles.fileList}>
          {files.map((file, index) => (
            <div key={index} className={styles.fileItem}>
              <span>{file.name}</span>
              {progress[file.name] !== undefined && (
                <div className={styles.progressBar}>
                  <div 
                    className={styles.progressFill} 
                    style={{ width: `${progress[file.name]}%` }}
                  />
                </div>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default FileUploader;
```

### Usage in Moodle

```php
render_react_component('FileUploader', 'file-upload-area', [
    'contextId' => $context->id,
    'component' => 'mod_assign',
    'fileArea' => 'submissions',
    'itemId' => $submission->id,
    'maxSize' => $maxbytes,
    'acceptedTypes' => '.pdf,.doc,.docx'
]);
```

## Course Card Grid

Modern course cards with progress indicators and quick actions.

### Component Code

```jsx
// react-apps/src/components/CourseGrid.jsx
import React, { useState, useEffect } from 'react';
import styles from './CourseGrid.module.css';

const CourseGrid = ({ userId, categoryId = 0 }) => {
  const [courses, setCourses] = useState([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState('all');

  useEffect(() => {
    fetchCourses();
  }, [userId, categoryId]);

  const fetchCourses = async () => {
    try {
      const response = await fetch('/lib/ajax/service.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          methodname: 'core_course_get_enrolled_courses_by_timeline_classification',
          args: {
            userid: userId,
            classification: 'all',
            limit: 0
          },
          sesskey: window.M.cfg.sesskey
        })
      });
      
      const data = await response.json();
      setCourses(data.courses || []);
    } catch (error) {
      console.error('Failed to fetch courses:', error);
    } finally {
      setLoading(false);
    }
  };

  const CourseCard = ({ course }) => {
    const progress = course.progress || 0;
    const imageUrl = course.courseimage || '/theme/image.php/boost/core/1/course';
    
    return (
      <div className={styles.card}>
        <div 
          className={styles.cardImage} 
          style={{ backgroundImage: `url(${imageUrl})` }}
        >
          <div className={styles.cardOverlay}>
            <a 
              href={`/course/view.php?id=${course.id}`} 
              className={styles.viewButton}
            >
              View Course
            </a>
          </div>
        </div>
        
        <div className={styles.cardContent}>
          <h3 className={styles.cardTitle}>
            <a href={`/course/view.php?id=${course.id}`}>
              {course.fullname}
            </a>
          </h3>
          
          {course.summary && (
            <p className={styles.cardSummary}>
              {course.summary.replace(/<[^>]*>/g, '').substring(0, 100)}...
            </p>
          )}
          
          <div className={styles.progressContainer}>
            <div className={styles.progressBar}>
              <div 
                className={styles.progressFill} 
                style={{ width: `${progress}%` }}
              />
            </div>
            <span className={styles.progressText}>{progress}% complete</span>
          </div>
          
          <div className={styles.cardActions}>
            <a href={`/grade/report/user/index.php?id=${course.id}`}>
              Grades
            </a>
            <a href={`/course/view.php?id=${course.id}#section-0`}>
              Content
            </a>
            <a href={`/mod/forum/index.php?id=${course.id}`}>
              Forums
            </a>
          </div>
        </div>
      </div>
    );
  };

  if (loading) {
    return <div className={styles.loading}>Loading courses...</div>;
  }

  const filteredCourses = courses.filter(course => {
    if (filter === 'all') return true;
    if (filter === 'inprogress') return course.progress > 0 && course.progress < 100;
    if (filter === 'completed') return course.progress === 100;
    return true;
  });

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <h2>My Courses</h2>
        <div className={styles.filters}>
          <button 
            className={filter === 'all' ? styles.active : ''}
            onClick={() => setFilter('all')}
          >
            All ({courses.length})
          </button>
          <button 
            className={filter === 'inprogress' ? styles.active : ''}
            onClick={() => setFilter('inprogress')}
          >
            In Progress
          </button>
          <button 
            className={filter === 'completed' ? styles.active : ''}
            onClick={() => setFilter('completed')}
          >
            Completed
          </button>
        </div>
      </div>
      
      <div className={styles.grid}>
        {filteredCourses.map(course => (
          <CourseCard key={course.id} course={course} />
        ))}
      </div>
    </div>
  );
};

export default CourseGrid;
```

## Real-time Notifications

WebSocket-based notifications with toast UI.

### Component Code

```jsx
// react-apps/src/components/NotificationCenter.jsx
import React, { useState, useEffect, useRef } from 'react';
import styles from './NotificationCenter.module.css';

const NotificationCenter = ({ userId, wsUrl }) => {
  const [notifications, setNotifications] = useState([]);
  const [unreadCount, setUnreadCount] = useState(0);
  const [isOpen, setIsOpen] = useState(false);
  const ws = useRef(null);

  useEffect(() => {
    // Fetch existing notifications
    fetchNotifications();
    
    // Setup WebSocket for real-time updates
    connectWebSocket();
    
    return () => {
      if (ws.current) {
        ws.current.close();
      }
    };
  }, [userId]);

  const fetchNotifications = async () => {
    try {
      const response = await fetch('/lib/ajax/service.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          methodname: 'message_popup_get_popup_notifications',
          args: { userid: userId, limit: 20 },
          sesskey: window.M.cfg.sesskey
        })
      });
      
      const data = await response.json();
      setNotifications(data.notifications || []);
      setUnreadCount(data.unreadcount || 0);
    } catch (error) {
      console.error('Failed to fetch notifications:', error);
    }
  };

  const connectWebSocket = () => {
    const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
    ws.current = new WebSocket(`${protocol}//${window.location.host}/ws`);
    
    ws.current.onmessage = (event) => {
      const data = JSON.parse(event.data);
      if (data.type === 'notification') {
        showToast(data.notification);
        setNotifications(prev => [data.notification, ...prev]);
        setUnreadCount(prev => prev + 1);
      }
    };
  };

  const showToast = (notification) => {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = styles.toast;
    toast.innerHTML = `
      <div class="${styles.toastContent}">
        <strong>${notification.subject}</strong>
        <p>${notification.fullmessage}</p>
      </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
      toast.classList.add(styles.show);
    }, 100);
    
    setTimeout(() => {
      toast.classList.remove(styles.show);
      setTimeout(() => document.body.removeChild(toast), 300);
    }, 5000);
  };

  const markAsRead = async (notificationId) => {
    try {
      await fetch('/lib/ajax/service.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          methodname: 'core_message_mark_notification_read',
          args: { notificationid: notificationId },
          sesskey: window.M.cfg.sesskey
        })
      });
      
      setNotifications(prev => 
        prev.map(n => n.id === notificationId ? { ...n, read: true } : n)
      );
      setUnreadCount(prev => Math.max(0, prev - 1));
    } catch (error) {
      console.error('Failed to mark as read:', error);
    }
  };

  return (
    <div className={styles.notificationCenter}>
      <button 
        className={styles.trigger}
        onClick={() => setIsOpen(!isOpen)}
      >
        <svg className={styles.icon} fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} 
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        {unreadCount > 0 && (
          <span className={styles.badge}>{unreadCount}</span>
        )}
      </button>
      
      {isOpen && (
        <div className={styles.dropdown}>
          <div className={styles.header}>
            <h3>Notifications</h3>
            <button onClick={() => setIsOpen(false)}>×</button>
          </div>
          
          <div className={styles.list}>
            {notifications.length === 0 ? (
              <p className={styles.empty}>No notifications</p>
            ) : (
              notifications.map(notification => (
                <div 
                  key={notification.id} 
                  className={`${styles.item} ${!notification.read ? styles.unread : ''}`}
                  onClick={() => !notification.read && markAsRead(notification.id)}
                >
                  <div className={styles.itemContent}>
                    <strong>{notification.subject}</strong>
                    <p>{notification.fullmessage}</p>
                    <time>{new Date(notification.timecreated * 1000).toRelativeTime()}</time>
                  </div>
                </div>
              ))
            )}
          </div>
          
          <div className={styles.footer}>
            <a href="/message/index.php">View all messages</a>
          </div>
        </div>
      )}
    </div>
  );
};

export default NotificationCenter;
```

## Quick Start Templates

### 1. Basic Component Template

```jsx
// react-apps/src/components/YourComponent.jsx
import React, { useState, useEffect } from 'react';
import styles from './YourComponent.module.css';

const YourComponent = ({ prop1, prop2 }) => {
  const [state, setState] = useState(null);
  
  useEffect(() => {
    // Component initialization
  }, []);
  
  return (
    <div className={styles.container}>
      {/* Your component content */}
    </div>
  );
};

export default YourComponent;
```

### 2. Component with Moodle API

```jsx
// react-apps/src/components/MoodleApiComponent.jsx
import React, { useState, useEffect } from 'react';
import { useMoodleAjax } from '../hooks/useMoodleAjax';

const MoodleApiComponent = ({ courseId }) => {
  const { data, loading, error, callService } = useMoodleAjax();
  
  useEffect(() => {
    callService('core_course_get_contents', { courseid: courseId });
  }, [courseId]);
  
  if (loading) return <div>Loading...</div>;
  if (error) return <div>Error: {error.message}</div>;
  
  return (
    <div>
      {data && data.map(section => (
        <div key={section.id}>{section.name}</div>
      ))}
    </div>
  );
};
```

### 3. Custom Hook for Moodle AJAX

```javascript
// react-apps/src/hooks/useMoodleAjax.js
import { useState, useCallback } from 'react';

export const useMoodleAjax = () => {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  
  const callService = useCallback(async (methodname, args = {}) => {
    setLoading(true);
    setError(null);
    
    try {
      const response = await fetch('/lib/ajax/service.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          methodname,
          args,
          sesskey: window.M.cfg.sesskey
        })
      });
      
      const result = await response.json();
      
      if (result.error) {
        throw new Error(result.error);
      }
      
      setData(result);
    } catch (err) {
      setError(err);
    } finally {
      setLoading(false);
    }
  }, []);
  
  return { data, loading, error, callService };
};
```

## Testing Components

### Setting Up Tests

```bash
cd react-apps
npm install --save-dev vitest @testing-library/react @testing-library/user-event
```

### Example Test

```javascript
// react-apps/src/components/__tests__/HelloMoodle.test.jsx
import { render, screen, fireEvent } from '@testing-library/react';
import HelloMoodle from '../HelloMoodle';

describe('HelloMoodle', () => {
  it('renders with props', () => {
    render(<HelloMoodle userName="Test User" courseName="Test Course" />);
    
    expect(screen.getByText(/Test User/)).toBeInTheDocument();
    expect(screen.getByText(/Test Course/)).toBeInTheDocument();
  });
  
  it('increments counter on click', () => {
    render(<HelloMoodle />);
    
    const button = screen.getByText(/Clicked 0 times/);
    fireEvent.click(button);
    
    expect(screen.getByText(/Clicked 1 times/)).toBeInTheDocument();
  });
});
```

## Deployment Checklist

Before deploying new components:

- [ ] Component tested in standalone mode (localhost:5173)
- [ ] Component tested in Moodle integration
- [ ] Props documented with PropTypes or TypeScript
- [ ] CSS modules used for styling (no global styles)
- [ ] Error boundaries implemented for robustness
- [ ] Loading states handled properly
- [ ] Accessibility checked (ARIA labels, keyboard nav)
- [ ] Mobile responsive design verified
- [ ] Bundle size checked after build
- [ ] No console errors in production mode

## Tips and Tricks

1. **Use Moodle's Language Strings**
   ```javascript
   // In component
   const [strings, setStrings] = useState({});
   
   useEffect(() => {
     if (window.M && window.M.str) {
       window.M.str.get_strings([
         { key: 'save', component: 'moodle' },
         { key: 'cancel', component: 'moodle' }
       ]).then(setStrings);
     }
   }, []);
   ```

2. **Respect Moodle's Theme**
   ```css
   /* Use CSS variables when possible */
   .button {
     background-color: var(--primary, #0f6fc5);
     color: var(--white, #fff);
   }
   ```

3. **Handle Moodle's Page Lifecycle**
   ```javascript
   useEffect(() => {
     // Signal to Moodle that JS is loading
     if (window.M && window.M.util && window.M.util.js_pending) {
       window.M.util.js_pending('my-component');
       
       // Do your initialization
       
       // Signal completion
       window.M.util.js_complete('my-component');
     }
   }, []);
   ```

Remember: Always test in both development and production modes!