import React from 'react'
import ReactDOM from 'react-dom/client'
import HelloMoodle from './HelloMoodle'
import CategoryManagementPanel from './components/CourseManagement/CategoryManagementPanel'
import CourseManagementPanel from './components/CourseManagement/CourseManagementPanel'
// import CourseDetailPanel from './components/CourseManagement/CourseDetailPanel'

// Create global MoodleReact API
window.MoodleReact = {
  components: {
    HelloMoodle,
    CategoryManagementPanel,
    CourseManagementPanel,
    // CourseDetailPanel  // Temporarily disabled - uses web services
  },
  
  /**
   * Mount a React component to a DOM element
   * @param {string} componentName - Name of the component to mount
   * @param {string|HTMLElement} element - DOM element or selector
   * @param {Object} props - Props to pass to the component
   * @returns {Object} - React root instance
   */
  mount: function(componentName, element, props = {}) {
    const Component = this.components[componentName]
    if (!Component) {
      console.error(`Component ${componentName} not found in MoodleReact.components`)
      return null
    }
    
    const targetElement = typeof element === 'string' 
      ? document.querySelector(element)
      : element
      
    if (!targetElement) {
      console.error(`Target element not found: ${element}`)
      return null
    }
    
    const root = ReactDOM.createRoot(targetElement)
    root.render(React.createElement(Component, props))
    return root
  },
  
  /**
   * Unmount a React component from a DOM element
   * @param {Object} root - React root instance returned from mount
   */
  unmount: function(root) {
    if (root && root.unmount) {
      root.unmount()
    }
  },
  
  /**
   * Register a new component
   * @param {string} name - Component name
   * @param {React.Component} component - React component
   */
  register: function(name, component) {
    this.components[name] = component
  }
}

// Export for AMD compatibility
export default window.MoodleReact