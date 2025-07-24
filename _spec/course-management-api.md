### **API Specification: Moodle Course Management**

#### **1. General Principles**

*   **Base Path:** All endpoints will be prefixed with `/api`.
*   **Implementation:** This API will be implemented as a new **Local Plugin** (`local_courseapi`) in Moodle. The API logic will reside in the plugin's `externallib.php`, exposed via Moodle's built-in web services routing.
*   **Data Format:** All requests and responses will use the `application/json` content type.
*   **Authentication:** All endpoints are protected and require a valid Bearer Token (JWT), as detailed below.
*   **Error Handling:** Failed requests will return standard HTTP status codes and a JSON body with an error message:
    ```json
    {
      "error": "A human-readable error message."
    }
    ```
    *   **401 Unauthorized:** The provided JWT is missing, invalid, or expired.
    *   **403 Forbidden:** The user (identified by the JWT) does not have permission to perform the action (e.g., not an instructor in the course).
    *   **404 Not Found:** The requested resource (course, section, activity) does not exist.
    *   **422 Unprocessable Entity:** The request body is malformed or missing required fields.

#### **2. Authentication**

The API uses a temporary JWT bootstrapped from the user's primary PHP session. This is a secure method for bridging the server-rendered page and the client-side React application.

1.  **Login:** The user logs into Moodle normally, creating a standard PHP session.
2.  **Token Generation (PHP):** On the `course/management.php` page, the PHP code verifies the user's session. If valid, it generates a short-lived (e.g., 60-minute expiry) JWT. The token's payload must include `user_id`, `course_id`, and the expiration timestamp (`exp`).
3.  **Token Injection:** The generated JWT is injected into the HTML as a data attribute on the React component mount point:
    ```html
    <div id="course-management-app" 
         data-token="your.generated.jwt"
         data-course-id="2"
         data-api-base="/local/courseapi/api">
    </div>
    ```
4.  **API Requests (React):** The React application reads the token from the mount point's data attributes. Every `fetch` or `axios` request to the API **must** include the token in the `Authorization` header:
    ```javascript
    const mountPoint = document.getElementById('course-management-app');
    const token = mountPoint.dataset.token;
    
    headers: {
      'Authorization': `Bearer ${token}`
    }
    ```
5.  **Token Validation (API Backend):** For every incoming request, the API backend must validate the JWT's signature and expiration before executing any logic.

#### **3. Data Models**

*   **ActivityResource:** Represents a single Moodle activity or resource (e.g., Quiz, Assignment, File).
    ```json
    {
      "id": 101,
      "name": "Week 1 Assignment",
      "modname": "assign",
      "modicon": "https://.../assign.svg",
      "visible": true
    }
    ```
*   **CourseSection:** Represents a section within a Moodle course.
    ```json
    {
      "id": 25,
      "name": "Week 1: Introduction",
      "visible": true,
      "summary": "<p>This week we cover the basics.</p>",
      "activities": [
        { ...ActivityResource... },
        { ...ActivityResource... }
      ]
    }
    ```

#### **4. Endpoint Definitions**

##### **GET /course/{courseId}/management_data**
*   **Description:** Fetches the entire initial state required to render the course management page. This is the first call the React application should make.
*   **Method:** `GET`
*   **URL Parameters:**
    *   `courseId` (integer): The ID of the Moodle course.
*   **Success Response (200 OK):**
    ```json
    {
      "course_name": "Introduction to Programming",
      "sections": [
        { ...CourseSection... },
        { ...CourseSection... }
      ]
    }
    ```

---

##### **PUT /activity/{activityId}**
*   **Description:** Updates the properties of a specific activity or resource.
*   **Method:** `PUT`
*   **URL Parameters:**
    *   `activityId` (integer): The ID of the course module (`cmid`).
*   **Request Body:**
    ```json
    {
      "name": "Updated Assignment Name", // Optional
      "visible": false // Optional
    }
    ```
*   **Success Response (200 OK):** Returns the fully updated `ActivityResource` object.

---

##### **PUT /section/{sectionId}**
*   **Description:** Updates the properties of a specific course section.
*   **Method:** `PUT`
*   **URL Parameters:**
    *   `sectionId` (integer): The ID of the course section.
*   **Request Body:**
    ```json
    {
      "name": "Updated Section Title", // Optional
      "visible": false, // Optional
      "summary": "<p>New summary content.</p>" // Optional
    }
    ```
*   **Success Response (200 OK):** Returns the fully updated `CourseSection` object (excluding the `activities` array for efficiency).

---

##### **POST /section/{sectionId}/reorder_activities**
*   **Description:** Updates the order of activities within a specific section. This is critical for drag-and-drop functionality.
*   **Method:** `POST`
*   **URL Parameters:**
    *   `sectionId` (integer): The ID of the section whose activities are being reordered.
*   **Request Body:** An array of activity IDs in their new desired order.
    ```json
    {
      "activity_ids": [103, 101, 102]
    }
    ```
*   **Success Response (200 OK):**
    ```json
    {
      "status": "success",
      "message": "Activities in section 25 reordered."
    }
    ```

---

##### **DELETE /activity/{activityId}**
*   **Description:** Deletes an activity or resource from a course.
*   **Method:** `DELETE`
*   **URL Parameters:**
    *   `activityId` (integer): The ID of the course module (`cmid`) to delete.
*   **Success Response (204 No Content):** An empty response indicating successful deletion.

---

##### **POST /auth/token**
*   **Description:** Generates a JWT token for API authentication. This endpoint is primarily for testing and external integrations.
*   **Method:** `POST`
*   **Request Body:**
    ```json
    {
      "username": "teacher1",
      "password": "Teacher123!",
      "course_id": 2  // Optional - if not provided, token will be valid for user operations only
    }
    ```
*   **Success Response (200 OK):**
    ```json
    {
      "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
      "expires_in": 3600,  // seconds until expiration
      "user": {
        "id": 3,
        "username": "teacher1",
        "firstname": "Test",
        "lastname": "Teacher"
      }
    }
    ```
*   **Error Responses:**
    *   **401 Unauthorized:** Invalid username or password
    *   **403 Forbidden:** User does not have access to the specified course (if course_id provided)

---

##### **GET /user/me**
*   **Description:** Returns information about the authenticated user.
*   **Method:** `GET`
*   **Success Response (200 OK):**
    ```json
    {
      "id": 3,
      "username": "teacher1",
      "firstname": "Test",
      "lastname": "Teacher"
    }
    ```

---

##### **POST /section/{sectionId}/move_activity**
*   **Description:** Moves an activity from another section into this section at a specified position.
*   **Method:** `POST`
*   **URL Parameters:**
    *   `sectionId` (integer): The ID of the target section.
*   **Request Body:**
    ```json
    {
      "activityid": 101,
      "position": 0  // 0-based index for position within the section
    }
    ```
*   **Success Response (200 OK):**
    ```json
    {
      "status": "success",
      "message": "Activity moved successfully"
    }
    ```

---

##### **POST /activity**
*   **Description:** Creates a new activity in a course section.
*   **Method:** `POST`
*   **Request Body:**
    ```json
    {
      "courseid": 2,
      "sectionid": 1,
      "modname": "assign",  // Module type: assign, quiz, forum, resource, etc.
      "name": "New Assignment",
      "intro": "Assignment description",
      "visible": true
    }
    ```
*   **Success Response (200 OK):** Returns the newly created `ActivityResource` object with its assigned ID.
