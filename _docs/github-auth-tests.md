# GitHub OAuth2 Authentication - Acceptance Tests

## Prerequisites
- Administrator access to Moodle
- GitHub account for testing
- Ability to create GitHub OAuth applications
- HTTPS enabled on Moodle site (required for OAuth2)
- Access to Moodle server logs

## Test 1: Initial Setup Verification

### Steps:
1. Log in as administrator
2. Navigate to Site administration → Server → OAuth 2 services
3. Check if GitHub appears in the list of issuers

### Expected Results:
- [ ] OAuth 2 services page loads without errors
- [ ] Page shows existing OAuth2 issuers (if any)
- [ ] No PHP errors in error log

## Test 2: GitHub OAuth2 Setup Script

### Steps:
1. Navigate to `/admin/tool/oauth2/github_setup.php`
2. Review the setup instructions displayed
3. Note the callback URL shown for GitHub configuration

### Expected Results:
- [ ] Setup page loads successfully
- [ ] Instructions are clear and complete
- [ ] Callback URL is displayed as: `https://yourmoodle.com/auth/oauth2/callback.php`
- [ ] Message confirms "GitHub OAuth2 issuer created successfully!"
- [ ] Lists next steps for configuration

## Test 3: Create GitHub OAuth Application

### Steps:
1. Go to https://github.com/settings/developers
2. Click "New OAuth App"
3. Fill in the form:
   - Application name: "Moodle Test"
   - Homepage URL: Your Moodle URL
   - Authorization callback URL: Copy from setup page
4. Click "Register application"
5. Copy the Client ID
6. Generate and copy a Client Secret

### Expected Results:
- [ ] GitHub accepts the application registration
- [ ] Client ID is provided
- [ ] Client Secret can be generated
- [ ] Application appears in your GitHub OAuth apps list

## Test 4: Configure GitHub Issuer in Moodle

### Steps:
1. In Moodle, go to Site administration → Server → OAuth 2 services
2. Find "GitHub" in the issuer list
3. Click the edit (gear) icon
4. Enter the Client ID from GitHub
5. Enter the Client Secret from GitHub
6. Ensure "Show on login page" is checked
7. Enable the issuer
8. Save changes

### Expected Results:
- [ ] GitHub issuer appears in the list
- [ ] Can enter Client ID and Secret
- [ ] Settings save without errors
- [ ] Issuer shows as "Enabled"
- [ ] No error messages appear

## Test 5: Verify Endpoints Configuration

### Steps:
1. In the OAuth 2 services list, click "Configure endpoints" for GitHub
2. Verify the following endpoints exist:
   - authorization_endpoint: `https://github.com/login/oauth/authorize`
   - token_endpoint: `https://github.com/login/oauth/access_token`
   - userinfo_endpoint: `https://api.github.com/user`

### Expected Results:
- [ ] All three endpoints are listed
- [ ] URLs match exactly as shown above
- [ ] No duplicate endpoints
- [ ] Can edit endpoints if needed

## Test 6: Login Page Verification

### Steps:
1. Log out of Moodle
2. Go to the Moodle login page
3. Look for GitHub login option

### Expected Results:
- [ ] "Login with GitHub" button appears
- [ ] GitHub logo/icon is visible
- [ ] Button is positioned appropriately
- [ ] Traditional login form still available

## Test 7: First-Time GitHub Login (New User)

### Steps:
1. From Moodle login page, click "Login with GitHub"
2. If not logged into GitHub, enter GitHub credentials
3. Review permissions requested by Moodle
4. Click "Authorize" to grant access
5. Wait for redirect back to Moodle

### Expected Results:
- [ ] Redirects to GitHub authorization page
- [ ] Shows Moodle app requesting access to:
   - Email addresses (read-only)
   - Profile information
- [ ] After authorization, redirects back to Moodle
- [ ] Automatically logged into Moodle
- [ ] New user account created
- [ ] User lands on dashboard or preferred page

## Test 8: Verify User Account Creation

### Steps:
1. As admin, go to Site administration → Users → Browse list of users
2. Search for the GitHub username
3. Click on the user to view profile
4. Check the authentication method

### Expected Results:
- [ ] User appears in user list
- [ ] Username matches GitHub login
- [ ] Email matches GitHub primary email
- [ ] Auth method shows "OAuth 2"
- [ ] First name populated from GitHub
- [ ] Profile picture from GitHub (if available)

## Test 9: Subsequent GitHub Login (Existing User)

### Steps:
1. Log out of Moodle
2. Click "Login with GitHub" again
3. If still authorized on GitHub, should auto-redirect

### Expected Results:
- [ ] Faster login process
- [ ] No re-authorization needed (unless revoked)
- [ ] Logs into existing account
- [ ] Previous course enrollments maintained

## Test 10: Link Existing Account to GitHub

### Steps:
1. Log in with a traditional Moodle account
2. Go to Preferences → Linked logins
3. Find GitHub in the list
4. Click "Link" next to GitHub
5. Authorize on GitHub
6. Return to Moodle

### Expected Results:
- [ ] Linked logins page shows available providers
- [ ] Can link GitHub to existing account
- [ ] After linking, shows as "Linked"
- [ ] Can now login with either method
- [ ] Same account accessed both ways

## Test 11: Unlink GitHub Account

### Steps:
1. In Preferences → Linked logins
2. Click "Unlink" next to GitHub
3. Confirm the action

### Expected Results:
- [ ] Unlink option available
- [ ] Confirmation requested
- [ ] Successfully unlinks
- [ ] Can no longer login via GitHub
- [ ] Traditional login still works

## Test 12: Email Conflict Handling

### Steps:
1. Create a Moodle account with email: test@example.com
2. Ensure GitHub account uses same email
3. Try to login with GitHub

### Expected Results:
- [ ] System detects email match
- [ ] Either:
   - Prompts to link accounts, or
   - Automatically links based on email, or
   - Shows appropriate error message
- [ ] No duplicate accounts created

## Test 13: Private Email Handling

### Steps:
1. In GitHub settings, set email to private
2. Try to create new account via GitHub login

### Expected Results:
- [ ] Login process handles private email
- [ ] Either uses GitHub-provided private email
- [ ] Or prompts for email address
- [ ] Account creation succeeds

## Test 14: Profile Data Sync

### Steps:
1. Login via GitHub
2. Check Moodle profile for:
   - Name
   - Profile picture
   - Location (city)
   - Description (bio)
   - Website URL

### Expected Results:
- [ ] GitHub data populates Moodle profile
- [ ] Profile picture downloads from GitHub
- [ ] Bio becomes description
- [ ] GitHub profile URL saved
- [ ] Data formatted correctly

## Test 15: Revoke Access from GitHub

### Steps:
1. Go to GitHub Settings → Applications → Authorized OAuth Apps
2. Find your Moodle application
3. Click "Revoke access"
4. Try to login to Moodle with GitHub

### Expected Results:
- [ ] GitHub shows access revoked
- [ ] Moodle login redirects to GitHub
- [ ] Must re-authorize the application
- [ ] After re-authorization, login works
- [ ] Previous account still accessible

## Test 16: Error Handling - Invalid Credentials

### Steps:
1. As admin, edit GitHub issuer
2. Change Client Secret to invalid value
3. Try to login with GitHub

### Expected Results:
- [ ] Login fails gracefully
- [ ] Error message displayed to user
- [ ] Not stuck on blank page
- [ ] Can return to regular login
- [ ] Error logged for admin

## Test 17: Session and Security

### Steps:
1. Login via GitHub in one browser
2. Login via GitHub in another browser
3. Check active sessions
4. Logout from one browser

### Expected Results:
- [ ] Multiple sessions supported
- [ ] Each session independent
- [ ] Logout affects only one session
- [ ] Session timeouts work normally

## Test 18: Performance Test

### Steps:
1. Time a regular login
2. Time a GitHub login (authorized)
3. Compare login times
4. Monitor server resources

### Expected Results:
- [ ] GitHub login within 2-5 seconds
- [ ] No significant server load
- [ ] Acceptable user experience
- [ ] No timeout errors

## Test 19: Mobile Browser Test

### Steps:
1. Access Moodle on mobile browser
2. Use GitHub login
3. Complete authorization flow
4. Verify mobile experience

### Expected Results:
- [ ] GitHub button visible on mobile
- [ ] OAuth flow works on mobile
- [ ] Proper redirects on small screens
- [ ] No display issues

## Test 20: Disable/Enable Issuer

### Steps:
1. As admin, disable GitHub issuer
2. Check login page
3. Re-enable GitHub issuer
4. Check login page again

### Expected Results:
- [ ] Disabled: GitHub button disappears
- [ ] Disabled: Existing sessions remain active
- [ ] Enabled: GitHub button reappears
- [ ] No errors during toggle

## Common Issues and Solutions

### Issue: "Invalid redirect URI"
- **Check**: Callback URL matches exactly
- **Check**: No trailing slashes
- **Check**: HTTPS is used

### Issue: No GitHub button on login
- **Check**: Issuer is enabled
- **Check**: "Show on login page" is checked
- **Check**: Theme supports OAuth2 display

### Issue: "Error: invalid_client"
- **Check**: Client ID and Secret are correct
- **Check**: No extra spaces in credentials
- **Check**: OAuth app not deleted on GitHub

## Security Checklist

- [ ] HTTPS enforced for OAuth2
- [ ] Client Secret not visible in logs
- [ ] Tokens stored securely
- [ ] No sensitive data in URLs
- [ ] Rate limiting prevents abuse

## Sign-off

- [ ] All tests completed successfully
- [ ] GitHub login works reliably
- [ ] User experience is smooth
- [ ] Security measures verified
- [ ] Performance is acceptable

**Tester Name**: _________________

**Date**: _________________

**Notes**: _________________