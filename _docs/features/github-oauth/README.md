# GitHub OAuth2 Authentication for Moodle 3.5

## The Problem

In the original Moodle 3.5 system, students and teachers had to create and remember yet another username and password combination. For a programming-focused educational platform, this creates unnecessary friction when most students already have GitHub accounts that serve as their professional developer identity. Managing separate credentials increases the likelihood of forgotten passwords and account management overhead.

## The New Feature

This feature modernizes the authentication process by allowing users to log into Moodle using their existing GitHub accounts through OAuth 2.0. When users click "Login with GitHub" on the login page, they're redirected to GitHub for authorization, and upon approval, they're automatically logged into Moodle. New users are created automatically using their GitHub profile information, making the onboarding process seamless.

### Key Benefits:

- **Single Sign-On**: Students use their existing GitHub credentials
- **Automatic Account Creation**: New users are created with GitHub profile data
- **Professional Identity**: Links academic work to developer profile
- **Reduced Password Fatigue**: No need to remember another password

## Implementation Notes

The implementation leverages Moodle's existing OAuth2 framework rather than creating a new authentication plugin. This approach ensures compatibility and maintainability. Key implementation details include:

### Custom GitHub Client

A custom OAuth2 client (`lib/classes/oauth2/client/github.php`) was created to handle GitHub's specific requirements:
- Fetches user emails from a separate API endpoint
- Properly maps GitHub fields to Moodle user fields (login → username)
- Handles the Accept header requirement for JSON responses
- Auto-confirms OAuth2 users since they're pre-verified by GitHub

### Database Configuration

The GitHub OAuth2 issuer is automatically created during the auth_oauth2 plugin upgrade process, including:
- OAuth2 issuer configuration with GitHub endpoints
- Field mappings between GitHub and Moodle user profiles
- Proper callback URL configuration

### Session Handling

Special session key validation was implemented to prevent timeout errors during the OAuth2 callback process, ensuring a smooth user experience.

## How to Test

(You can skip steps 1, 2 and 3 in the deployed version.)

### 1. Initial Setup

1. Log in as administrator
2. Navigate to **Site administration → Server → OAuth 2 services**
3. Verify that "GitHub" appears in the list of OAuth2 issuers

### 2. Configure GitHub OAuth Application
1. Go to https://github.com/settings/developers
2. Click "New OAuth App"
3. Fill in:
    - **Application name**: "Moodle LMS Test"
    - **Homepage URL**: Your Moodle URL (e.g., `http://localhost:8888`)
    - **Authorization callback URL**: `http://localhost:8888/admin/oauth2callback.php`
4. Click "Register application"
5. Note the **Client ID** and generate a **Client Secret**

### 3. Configure Moodle
1. In Moodle, go to **Site administration → Server → OAuth 2 services**
2. Click the settings icon next to "GitHub"
3. Enter your GitHub OAuth App's **Client ID** and **Client Secret**
4. Ensure "Show on login page" is checked
5. Save changes

### 4. Test Login with New User
1. Log out of Moodle
2. On the login page, click "Login with GitHub"
3. Authorize the application on GitHub
4. Verify you're logged into Moodle with a new account created from your GitHub profile

### 5. Test Login with Existing User
1. Log out and log back in with GitHub
2. Verify it uses your existing account without creating a duplicate

### 6. Verify Account Details
As an administrator:
1. Go to **Site administration → Users → Browse list of users**
2. Find the GitHub user
3. Verify:
    - Username matches GitHub login
    - Email matches GitHub primary email
    - Authentication method shows "OAuth 2"
    - First name is populated from GitHub

### Troubleshooting

- **No GitHub button**: Check that the issuer is enabled in OAuth 2 services
- **Invalid redirect URI error**: Ensure the callback URL matches exactly (including http/https)
- **Login fails**: Verify Client ID and Secret are entered correctly without extra spaces

The feature has been tested and works reliably for both new user creation and existing user authentication, providing a modern authentication experience for the programming education platform.
