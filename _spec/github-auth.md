### **Feature 3: Modern Authentication via GitHub**

*   **Objective:** To modernize the login process by allowing users to authenticate using their existing GitHub accounts, which is standard practice for developers.
*   **User Story:** "As a student, I want to log into Moodle with my GitHub account, so I don't have to remember another password and my identity is linked to my professional developer profile."
*   **Acceptance Criteria:**
    1.  A "Login with GitHub" button appears on the Moodle 3.5 login page.
    2.  Clicking the button initiates a standard OAuth 2.0 flow, redirecting the user to GitHub for authorization.
    3.  On successful authorization, the user is redirected back to Moodle and logged in.
    4.  If the user does not exist, a new Moodle account is created automatically using their GitHub username and email.

*   **Technical Specification:**
    *   **Moodle Component:** New Authentication Plugin (`auth_github`).
    *   **Configuration:** The plugin settings will require an administrator to enter the GitHub OAuth App's **Client ID** and **Client Secret**.
    *   **Logic:** The plugin will manage the entire OAuth 2.0 flow:
        1.  Redirecting the user to `github.com/login/oauth/authorize`.
        2.  Handling the callback from GitHub, which includes an authorization code.
        3.  Making a server-to-server request to exchange the code for an access token.
        4.  Using the access token to fetch user details from the GitHub API (`api.github.com/user`).
        5.  Searching for a user in `mdl_user` with the matching GitHub username or email; if found, log them in. If not found, create a new user and then log them in.

# Implementation Plan

## Overview
This feature adds GitHub OAuth 2.0 authentication to Moodle 3.5, allowing users to log in with their GitHub accounts. New users will be automatically created using their GitHub profile information.

## Existing Code Analysis

### Relevant Database Tables
1. **User Authentication**:
   - `mdl_user` - User accounts
   - `mdl_user_preferences` - User preferences
   - `mdl_auth_oauth2_linked_login` - Links OAuth accounts to users

2. **OAuth2 Configuration**:
   - `mdl_oauth2_issuer` - OAuth2 provider configurations
   - `mdl_oauth2_endpoint` - OAuth2 endpoints (auth, token, userinfo)
   - `mdl_oauth2_user_field_mapping` - Maps OAuth fields to user fields

3. **Configuration**:
   - `mdl_config_plugins` - Plugin-specific configuration

### Key Code Components
1. **Authentication Framework**:
   - `/lib/authlib.php` - Base auth plugin class
   - `/auth/oauth2/` - Existing OAuth2 implementation
   - `/login/index.php` - Login page

2. **OAuth2 Core**:
   - `/lib/classes/oauth2/issuer.php` - OAuth2 provider management
   - `/lib/classes/oauth2/client.php` - OAuth2 client implementation
   - `/lib/classes/oauth2/api.php` - OAuth2 API utilities

3. **User Management**:
   - `/user/lib.php` - User creation functions
   - `/lib/moodlelib.php` - `authenticate_user_login()` function

## Current Code Flow

### OAuth2 Authentication Flow
1. User clicks OAuth2 provider button on login page
2. Redirects to `/auth/oauth2/login.php?id={issuerid}`
3. OAuth2 client initiates authorization:
   - Redirects to provider's authorization URL
   - User approves access
   - Provider redirects back with code
4. Exchange code for access token
5. Fetch user info from provider API
6. Match or create Moodle user
7. Complete login process

### User Creation Flow
1. Check if OAuth2 account already linked
2. Check if email matches existing user
3. If no match, create new user:
   - Validate email domain
   - Generate username
   - Set user fields from OAuth data
   - Create confirmed/unconfirmed account
4. Link OAuth2 account to user

## Implementation Approach

Since Moodle 3.5 already has a robust OAuth2 system, we'll configure GitHub as a new OAuth2 issuer rather than creating a new auth plugin. This approach:
- Leverages existing, tested OAuth2 code
- Provides admin UI for configuration
- Supports field mapping
- Handles account linking

### Configuration Steps

1. **Create GitHub OAuth2 Issuer**:
   ```php
   // Database record for oauth2_issuer
   $issuer = new stdClass();
   $issuer->name = 'GitHub';
   $issuer->image = 'https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png';
   $issuer->baseurl = 'https://github.com';
   $issuer->clientid = ''; // From GitHub OAuth App
   $issuer->clientsecret = ''; // From GitHub OAuth App
   $issuer->loginscopes = 'user:email';
   $issuer->loginscopesoffline = 'user:email';
   $issuer->showonloginpage = 1;
   $issuer->enabled = 1;
   $issuer->sortorder = 0;
   ```

2. **Configure OAuth2 Endpoints**:
   ```php
   // Authorization endpoint
   $endpoints[] = [
       'name' => 'authorization_endpoint',
       'url' => 'https://github.com/login/oauth/authorize'
   ];
   
   // Token endpoint
   $endpoints[] = [
       'name' => 'token_endpoint',
       'url' => 'https://github.com/login/oauth/access_token'
   ];
   
   // User info endpoint
   $endpoints[] = [
       'name' => 'userinfo_endpoint',
       'url' => 'https://api.github.com/user'
   ];
   ```

3. **Field Mapping Configuration**:
   ```php
   // Map GitHub fields to Moodle user fields
   $mappings = [
       'login' => 'username',        // GitHub username
       'email' => 'email',           // Primary email
       'name' => 'firstname',        // Full name (will need parsing)
       'avatar_url' => 'picture',    // Profile picture
       'bio' => 'description',       // User bio
       'location' => 'city',         // Location
       'html_url' => 'url'          // GitHub profile URL
   ];
   ```

### Custom Implementation Requirements

1. **GitHub-Specific Client Extension**:
   ```php
   // In /lib/classes/oauth2/client/github.php
   namespace core\oauth2\client;
   
   class github extends \core\oauth2\client {
       /**
        * GitHub requires Accept header for JSON responses
        */
       protected function request($url, $options = array()) {
           if (!isset($options['CURLOPT_HTTPHEADER'])) {
               $options['CURLOPT_HTTPHEADER'] = array();
           }
           $options['CURLOPT_HTTPHEADER'][] = 'Accept: application/json';
           return parent::request($url, $options);
       }
       
       /**
        * Parse GitHub name field into first/last
        */
       public function map_userinfo_to_fields($userinfo) {
           $fields = parent::map_userinfo_to_fields($userinfo);
           
           // Split full name into first/last
           if (!empty($userinfo->name)) {
               $parts = explode(' ', $userinfo->name, 2);
               $fields['firstname'] = $parts[0];
               $fields['lastname'] = isset($parts[1]) ? $parts[1] : '';
           }
           
           return $fields;
       }
   }
   ```

2. **Installation Script**:
   ```php
   // In /auth/oauth2/db/install.php or upgrade script
   function install_github_oauth2_issuer() {
       global $DB;
       
       // Check if GitHub issuer already exists
       if ($DB->record_exists('oauth2_issuer', ['name' => 'GitHub'])) {
           return;
       }
       
       // Create issuer
       $issuer = \core\oauth2\api::create_issuer((object)[
           'name' => 'GitHub',
           'image' => 'https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png',
           'baseurl' => 'https://github.com',
           'clientid' => '',
           'clientsecret' => '',
           'loginscopes' => 'user:email',
           'loginscopesoffline' => 'user:email',
           'showonloginpage' => 1
       ]);
       
       // Create endpoints
       \core\oauth2\api::create_endpoint((object)[
           'issuerid' => $issuer->get('id'),
           'name' => 'authorization_endpoint',
           'url' => 'https://github.com/login/oauth/authorize'
       ]);
       
       \core\oauth2\api::create_endpoint((object)[
           'issuerid' => $issuer->get('id'),
           'name' => 'token_endpoint',
           'url' => 'https://github.com/login/oauth/access_token'
       ]);
       
       \core\oauth2\api::create_endpoint((object)[
           'issuerid' => $issuer->get('id'),
           'name' => 'userinfo_endpoint',
           'url' => 'https://api.github.com/user'
       ]);
       
       // Create field mappings
       $mappings = [
           'login' => 'username',
           'email' => 'email',
           'name' => 'firstname',
           'avatar_url' => 'picture'
       ];
       
       foreach ($mappings as $external => $internal) {
           \core\oauth2\api::create_user_field_mapping((object)[
               'issuerid' => $issuer->get('id'),
               'externalfield' => $external,
               'internalfield' => $internal
           ]);
       }
   }
   ```

### Admin Configuration

1. **GitHub OAuth App Setup**:
   - Application name: "Moodle LMS"
   - Homepage URL: `http://yourmoodle.com`
   - Authorization callback URL: `http://yourmoodle.com/auth/oauth2/callback.php`

2. **Moodle Configuration**:
   - Navigate to: Site administration → Server → OAuth 2 services
   - Edit GitHub issuer
   - Enter Client ID and Client Secret from GitHub
   - Save changes

### UI Changes

1. **Login Page**:
   - GitHub button automatically appears when issuer is enabled
   - Uses GitHub logo and "Login with GitHub" text
   - Styled consistently with other OAuth2 providers

2. **Account Linking** (existing functionality):
   - Users can link GitHub account in preferences
   - Multiple OAuth2 accounts can be linked

## Security Considerations

1. **HTTPS Required**: OAuth2 requires secure connections
2. **State Parameter**: Prevents CSRF attacks (handled by core)
3. **Scope Limitations**: Only request necessary scopes (user:email)
4. **Token Storage**: Access tokens stored encrypted
5. **Email Verification**: Can require email confirmation for new accounts

## Development Checklist

1. [ ] Register GitHub OAuth App
2. [ ] Create GitHub client class extending OAuth2 client
3. [ ] Implement name parsing for first/last name split
4. [ ] Create installation/upgrade script
5. [ ] Add GitHub issuer configuration
6. [ ] Configure endpoints
7. [ ] Set up field mappings
8. [ ] Test authentication flow
9. [ ] Test new user creation
10. [ ] Test existing user matching
11. [ ] Verify profile picture import
12. [ ] Add GitHub icon/branding
13. [ ] Document configuration steps

## Testing Approach

1. Test with new GitHub account (creates new user)
2. Test with existing email match
3. Test account linking for existing users
4. Test profile field mapping
5. Test error cases (denied authorization, network errors)
6. Verify session handling
7. Test concurrent logins
8. Test with GitHub accounts lacking email
