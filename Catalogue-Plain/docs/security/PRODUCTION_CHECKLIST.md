# Production Deployment Checklist

Security checklist for deploying the CMS to production.

## Pre-Deployment

### Server Configuration

- [ ] **PHP Version** - PHP 7.4+ installed
- [ ] **HTTPS Enabled** - SSL certificate configured
- [ ] **Error Reporting** - `display_errors` set to `0`
- [ ] **Log Errors** - `log_errors` set to `1`
- [ ] **File Permissions** - Directories `755`, files `644`
- [ ] **Upload Limits** - `upload_max_filesize` and `post_max_size` configured
- [ ] **Memory Limit** - Sufficient for your content size

### File Permissions

```bash
# Directories
chmod 755 catalogue/content
chmod 755 catalogue/data
chmod 755 catalogue/uploads
chmod 755 catalogue/logs

# Files
chmod 644 catalogue/content/**/*.json
chmod 644 catalogue/data/*.json
```

### Configuration Files

- [ ] **config.php** - Review all settings
- [ ] **.htaccess** - Verify paths are correct
- [ ] **BASE_PATH** - Set correctly for your installation
- [ ] **CMS_URL** - Set correctly for your installation
- [ ] **ASSETS_URL** - Set correctly for your installation

## Security Configuration

### Authentication

- [ ] **Default Password Changed** - No default credentials
- [ ] **Strong Passwords** - All users have strong passwords
- [ ] **User Accounts** - Only necessary users created
- [ ] **Session Lifetime** - Appropriate timeout configured

### CSRF Protection

- [ ] **CSRF Tokens** - Enabled on all forms
- [ ] **Token Expiry** - Configured appropriately
- [ ] **Token Validation** - Working on all POST requests

### File Security

- [ ] **Upload Directory** - Isolated from web root (if possible)
- [ ] **File Validation** - MIME type and extension checks working
- [ ] **Size Limits** - Appropriate limits set
- [ ] **Path Traversal** - Protection verified

### Security Headers

- [ ] **Content Security Policy** - Configured
- [ ] **X-Frame-Options** - Set to DENY or SAMEORIGIN
- [ ] **X-XSS-Protection** - Enabled
- [ ] **Referrer-Policy** - Configured
- [ ] **Strict-Transport-Security** - Set if using HTTPS

## Content Security

### Blueprints

- [ ] **Blueprint Files** - All blueprints reviewed
- [ ] **Field Validation** - Required fields configured
- [ ] **File Fields** - Upload limits set appropriately

### Content

- [ ] **Content Files** - All content reviewed
- [ ] **Metadata** - No sensitive data in content
- [ ] **User Files** - User data secured

## Testing

### Functionality

- [ ] **Login** - Login works correctly
- [ ] **Content Creation** - Can create pages/collections
- [ ] **Content Editing** - Can edit content
- [ ] **File Uploads** - Uploads work correctly
- [ ] **HTML Generation** - Pages generate correctly
- [ ] **404 Page** - Custom 404 works

### Security Testing

- [ ] **CSRF Protection** - Forms require tokens
- [ ] **XSS Protection** - Scripts don't execute
- [ ] **Path Traversal** - Cannot access outside directories
- [ ] **File Upload** - Only allowed file types accepted
- [ ] **Authentication** - Protected pages require login
- [ ] **Rate Limiting** - Login rate limiting works

## Monitoring

### Error Logging

- [ ] **Error Log** - Configured and monitored
- [ ] **Access Log** - Server access logs reviewed
- [ ] **PHP Errors** - Logged to file

### Security Monitoring

- [ ] **Failed Logins** - Monitor login attempts
- [ ] **File Changes** - Monitor content changes
- [ ] **User Activity** - Review user actions

## Backup

### Regular Backups

- [ ] **Content Files** - Backed up regularly
- [ ] **User Files** - Backed up regularly
- [ ] **Uploads** - Backed up regularly
- [ ] **Configuration** - Backed up regularly

### Backup Locations

- [ ] **Off-Site Backup** - Backups stored off-site
- [ ] **Backup Testing** - Backups tested regularly
- [ ] **Recovery Plan** - Recovery procedure documented

## Post-Deployment

### Initial Checks

- [ ] **HTTPS Working** - Site accessible via HTTPS
- [ ] **Admin Panel** - Admin panel accessible
- [ ] **Content Display** - Content displays correctly
- [ ] **File Uploads** - Uploads work correctly

### Ongoing Maintenance

- [ ] **Updates** - PHP version updated regularly
- [ ] **Security Patches** - Applied promptly
- [ ] **User Review** - Users reviewed periodically
- [ ] **Content Review** - Content reviewed regularly

## Server-Specific Checklist

### Apache

- [ ] **mod_rewrite** - Enabled
- [ ] **.htaccess** - Allowed
- [ ] **Error Pages** - Custom error pages configured

### Nginx

- [ ] **URL Rewriting** - Configured correctly
- [ ] **PHP-FPM** - Configured correctly
- [ ] **File Permissions** - Set correctly

### Shared Hosting

- [ ] **PHP Version** - Compatible version available
- [ ] **File Permissions** - Can set required permissions
- [ ] **.htaccess** - Supported
- [ ] **Error Logs** - Accessible

## Security Hardening

### Additional Measures

- [ ] **Firewall** - Server firewall configured
- [ ] **Intrusion Detection** - IDS configured (if applicable)
- [ ] **Regular Audits** - Security audits scheduled
- [ ] **Vulnerability Scanning** - Regular scans performed

### Access Control

- [ ] **SSH Access** - Secured (if applicable)
- [ ] **FTP Access** - Secured (if applicable)
- [ ] **Database Access** - N/A (no database)
- [ ] **File Access** - Restricted appropriately

## Emergency Procedures

### Incident Response

- [ ] **Response Plan** - Documented
- [ ] **Contact Information** - Updated
- [ ] **Backup Access** - Backup access verified
- [ ] **Recovery Steps** - Documented

### Rollback Plan

- [ ] **Previous Version** - Previous version available
- [ ] **Rollback Procedure** - Documented
- [ ] **Testing** - Rollback tested

## Documentation

### User Documentation

- [ ] **User Guide** - Available for users
- [ ] **Security Guide** - Security practices documented
- [ ] **Troubleshooting** - Common issues documented

### Technical Documentation

- [ ] **Configuration** - Configuration documented
- [ ] **Architecture** - System architecture documented
- [ ] **Security** - Security measures documented

## Final Verification

### Pre-Launch

- [ ] **All Tests Pass** - All functionality tested
- [ ] **Security Verified** - Security measures verified
- [ ] **Backups Created** - Initial backups created
- [ ] **Monitoring Active** - Monitoring configured

### Launch

- [ ] **DNS Updated** - DNS points to server
- [ ] **SSL Certificate** - Valid and working
- [ ] **Site Accessible** - Site loads correctly
- [ ] **Admin Accessible** - Admin panel works

## Post-Launch Monitoring

### First 24 Hours

- [ ] **Error Logs** - Monitor error logs
- [ ] **User Access** - Verify users can access
- [ ] **Content Creation** - Verify content creation works
- [ ] **File Uploads** - Verify uploads work

### First Week

- [ ] **Performance** - Monitor performance
- [ ] **Security Events** - Review security events
- [ ] **User Feedback** - Collect user feedback
- [ ] **Bug Reports** - Address bug reports

## See Also

- [Best Practices](./BEST_PRACTICES.md) - Security guidelines
- [Authentication](./AUTHENTICATION.md) - Authentication security
- [File Security](./FILE_SECURITY.md) - File upload security

