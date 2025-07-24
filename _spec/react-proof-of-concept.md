### **React Component Specification: AuthenticatedUserDisplay**

#### **1. File Structure and Build Process**

Your React source files cannot be placed directly into Moodle's asset folders. They are JSX and must be compiled. The modern approach uses a centralized React development environment at the root of Moodle that outputs a single, browser-compatible JavaScript bundle.

**File Structure:**

```
moodle-3/
├── react-apps/                 <-- Your React source code lives here.
│   ├── src/
│   │   ├── components/
│   │   │   └── AuthenticatedUserDisplay.jsx
│   │   └── main.jsx            <-- Entry point that registers components.
│   ├── package.json            <-- Defines JS dependencies (React, Vite) and build scripts.
│   └── vite.config.js          <-- Configuration for the Vite build tool.
├── react-dist/                 <-- Production build output
│   ├── moodle-react.iife.js    <-- The SINGLE compiled output file.
│   └── style.css               <-- Compiled styles
└── lib/
    └── react_helper.php        <-- PHP helper for React integration
```

**Build Process:**

1.  **Tooling:** Uses Vite for fast development and optimized production builds.
2.  **Dependencies:** The `react-apps/package.json` includes: `react`, `react-dom`, `@vitejs/plugin-react`, and `vite`.
3.  **Configuration (`vite.config.js`):** Vite is configured to:
    *   Use `src/main.jsx` as the entry point.
    *   Output to `../react-dist/moodle-react.iife.js` in IIFE format.
    *   Bundle all dependencies into a single file.
    *   Support development mode with HMR on port 5173.
4.  **Build Commands:** 
    *   Development: `npm run dev` (from `react-apps/` directory)
    *   Production: `npm run build` (creates `react-dist/moodle-react.iife.js`)

#### **2. API Endpoint Specification**

##### **GET /user/me** (Already Implemented)
*   **Description:** Fetches basic information for the authenticated user.
*   **Method:** `GET`
*   **Authentication:** Requires a valid Bearer Token (JWT).
*   **Endpoint:** `/local/courseapi/api/user/me`
*   **Success Response (200 OK):**
    ```json
    {
      "id": 2,
      "username": "admin",
      "firstname": "Admin",
      "lastname": "User"
    }
    ```
*   **Failure Response (401 Unauthorized):**
    ```json
    { "error": "Invalid or expired token." }
    ```

**Note:** This endpoint is already implemented in `local/courseapi/classes/external.php` as `get_user_info()`.

#### **3. Component Specification: `AuthenticatedUserDisplay.jsx`**

*   **Objective:** To authenticate with the backend using a bootstrapped JWT, fetch the current user's name, and display it. This component proves the entire React-Moodle pipeline is working correctly.
*   **Props:** 
    *   `token` (string, optional): JWT token. If not provided, will look for it in a global location.
    *   `apiUrl` (string, optional): Base API URL. Defaults to `/local/courseapi/api`.
*   **State:**
    *   `isLoading` (boolean): `true` while the API call is in progress. Defaults to `true`.
    *   `userData` (object | null): Stores the user data from the API. Defaults to `null`.
    *   `error` (string | null): Stores any error message. Defaults to `null`.
*   **Lifecycle/Effects (`useEffect`):**
    1.  The component mounts. The `useEffect` hook with an empty dependency array `[]` runs once.
    2.  It reads the JWT from props or a designated global location (e.g., `window.MoodleReact.token`).
    3.  **If no token is found:** It sets `error` to "Authentication token not found." and `isLoading` to `false`.
    4.  **If a token is found:** It makes a `fetch` request to `/local/courseapi/api/user/me`, including the JWT in the `Authorization` header.
    5.  **On API success:** It parses the JSON response, sets `userData` with the result, and sets `isLoading` to `false`.
    6.  **On API failure:** It catches the error, sets `error` to "Failed to fetch user data.", and sets `isLoading` to `false`.
*   **Render Logic:**
    *   If `isLoading` is `true`, render a simple "Loading..." message or a spinner.
    *   If `error` is not `null`, render the error message (e.g., "Error: Authentication token not found.").
    *   If `userData` is not `null`, render a welcome message (e.g., `<div>Welcome, {userData.firstname} {userData.lastname}!</div>`).

#### **4. Integration with a Moodle Page**

This demonstrates how to place the component on any Moodle page using the modern React helper. We will use the main dashboard (`my/index.php`) as the example target.

1.  **Use the React Helper:** The easiest way to integrate React components is using the `react_helper.php`:

    ```php
    // In your PHP page (e.g., my/index.php)
    require_once($CFG->libdir . '/react_helper.php');
    require_once($CFG->dirroot . '/local/courseapi/classes/jwt.php');
    
    // Generate JWT token for the current user
    $token = local_courseapi\jwt::create_token($USER->id);
    
    // Render the React component
    render_react_component('AuthenticatedUserDisplay', 'auth-user-display', [
        'token' => $token,
        'apiUrl' => $CFG->wwwroot . '/local/courseapi/api'
    ]);
    ```

2.  **Alternative Manual Integration:** If you need more control:

    ```php
    // 1. Add a target div to your page template
    echo '<div id="react-user-display-root"></div>';
    
    // 2. Load the React bundle
    echo '<script src="' . $CFG->wwwroot . '/react-dist/moodle-react.iife.js"></script>';
    
    // 3. Mount the component with JavaScript
    echo '<script>
    (function() {
        var token = "' . $token . '";
        if (window.MoodleReact && window.MoodleReact.mount) {
            window.MoodleReact.mount(
                "AuthenticatedUserDisplay",
                "#react-user-display-root",
                { token: token }
            );
        }
    })();
    </script>';
    ```

#### **5. Component Registration**

The component must be registered in `react-apps/src/main.jsx`:

```jsx
import AuthenticatedUserDisplay from './components/AuthenticatedUserDisplay';

// Add to the global MoodleReact API
window.MoodleReact = {
  components: {
    HelloMoodle,
    MarkdownRenderer,
    AuthenticatedUserDisplay  // Register your component here
  },
  mount: function(componentName, element, props = {}) {
    // ... existing mount logic
  },
  // ... rest of API
};
```

#### **6. Example Component Implementation**

```jsx
// react-apps/src/components/AuthenticatedUserDisplay.jsx
import React, { useState, useEffect } from 'react';
import styles from './AuthenticatedUserDisplay.module.css';

const AuthenticatedUserDisplay = ({ token, apiUrl = '/local/courseapi/api' }) => {
  const [isLoading, setIsLoading] = useState(true);
  const [userData, setUserData] = useState(null);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchUserData = async () => {
      // Check for token in props or global location
      const authToken = token || window.MoodleReact?.token;
      
      if (!authToken) {
        setError('Authentication token not found.');
        setIsLoading(false);
        return;
      }

      try {
        const response = await fetch(`${apiUrl}/user/me`, {
          headers: {
            'Authorization': `Bearer ${authToken}`,
            'Content-Type': 'application/json'
          }
        });

        if (!response.ok) {
          throw new Error('Failed to fetch user data');
        }

        const data = await response.json();
        setUserData(data);
      } catch (err) {
        setError('Failed to fetch user data.');
      } finally {
        setIsLoading(false);
      }
    };

    fetchUserData();
  }, [token, apiUrl]);

  if (isLoading) {
    return <div className={styles.loading}>Loading...</div>;
  }

  if (error) {
    return <div className={styles.error}>Error: {error}</div>;
  }

  if (userData) {
    return (
      <div className={styles.welcome}>
        Welcome, {userData.firstname} {userData.lastname}!
      </div>
    );
  }

  return null;
};

export default AuthenticatedUserDisplay;
```

This complete specification provides a robust and scalable foundation. By successfully implementing this simple component, you will have validated:
- The modern React build process
- JWT-based API authentication
- The Moodle-React integration strategy using the react_helper.php
- The global MoodleReact API for component management

This paves the way for more complex components like the course management UI.
