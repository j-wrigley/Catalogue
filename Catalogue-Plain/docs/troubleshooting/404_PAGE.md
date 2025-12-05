# 404 Page Issues

Troubleshooting custom 404 error page problems.

## Common Issues

### Custom 404 Not Showing

**Symptoms:**
- Default Apache 404 shown
- Custom page not displayed
- ErrorDocument not working

**Solutions:**

1. **Check .htaccess Configuration**
   ```apache
   ErrorDocument 404 /catalogue/404-handler.php
   ```
   - Must be in root `.htaccess`
   - Path must be correct
   - Check for subfolder if needed

2. **Verify Handler Exists**
   ```bash
   ls catalogue/404-handler.php
   ```
   - File must exist
   - Must be in catalogue directory
   - Check permissions

3. **Check Handler Path**
   ```php
   // 404-handler.php should reference
   $htmlFile = __DIR__ . '/../404.html';
   ```
   - Path must be correct
   - Relative to handler location
   - Verify file exists

### 404.html Not Generated

**Symptoms:**
- Handler exists but no HTML
- 404 page blank
- Generation fails

**Solutions:**

1. **Check Blueprint Exists**
   ```bash
   ls catalogue/blueprints/404.blueprint.yml
   ```
   - Blueprint must exist
   - Filename must be exact
   - Must be valid YAML

2. **Check Template Exists**
   ```bash
   ls catalogue/templates/404.php
   ```
   - Template must exist
   - Must be valid PHP
   - Check syntax

3. **Check Content File**
   ```bash
   ls catalogue/content/pages/404/404.json
   ```
   - Content must exist
   - Must be valid JSON
   - Check structure

4. **Manually Generate**
   - Use "Regenerate All" in admin
   - Or generate manually
   - Check for errors

### Handler Returns Wrong Status

**Symptoms:**
- Page shows but wrong status code
- Search engines see 200 OK
- SEO issues

**Solutions:**

1. **Check Status Code**
   ```php
   // 404-handler.php should have
   http_response_code(404);
   ```
   - Must set 404 status
   - Before output
   - Verify correct

2. **Verify Headers**
   ```php
   header('HTTP/1.1 404 Not Found');
   http_response_code(404);
   ```
   - Set both headers
   - Ensure before output
   - Check order

### 404 Page Shows for Valid Pages

**Symptoms:**
- Valid pages return 404
- All pages show 404
- Handler too aggressive

**Solutions:**

1. **Check .htaccess Rules**
   ```apache
   # Should skip existing files
   RewriteCond %{REQUEST_FILENAME} -f
   RewriteRule ^ - [L]
   ```
   - Existing files should not trigger 404
   - Check rule order
   - Verify conditions

2. **Check Handler Logic**
   ```php
   // Handler should only serve 404.html
   // Not interfere with valid pages
   ```
   - Verify handler only for 404
   - Check path matching
   - Ensure correct logic

## Configuration Issues

### Subfolder Installation

**Symptoms:**
- 404 handler path incorrect
- Handler not found
- Path issues

**Solutions:**

1. **Update .htaccess**
   ```apache
   # For subfolder: /mysite/
   ErrorDocument 404 /mysite/catalogue/404-handler.php
   ```

2. **Check Handler Path**
   ```php
   // Handler should use relative paths
   $htmlFile = __DIR__ . '/../404.html';
   ```

3. **Verify BASE_PATH**
   ```php
   // Handler should account for BASE_PATH
   // If needed for asset paths
   ```

### Apache Configuration

**Symptoms:**
- ErrorDocument not working
- Apache ignores directive
- Configuration issues

**Solutions:**

1. **Check AllowOverride**
   ```apache
   <Directory /var/www/html>
       AllowOverride All
   </Directory>
   ```
   - Must allow .htaccess
   - Check Apache config
   - Restart if needed

2. **Verify mod_rewrite**
   ```bash
   apache2ctl -M | grep rewrite
   ```
   - Must be enabled
   - Enable if missing
   - Restart Apache

## Debugging Steps

### Step 1: Check Files Exist

```bash
ls -la catalogue/404-handler.php
ls -la catalogue/blueprints/404.blueprint.yml
ls -la catalogue/templates/404.php
ls -la catalogue/content/pages/404/404.json
ls -la 404.html
```

### Step 2: Test Handler Directly

```bash
curl -I http://example.com/catalogue/404-handler.php
```

### Step 3: Test 404 Page

```bash
curl -I http://example.com/nonexistent-page
```

### Step 4: Check Error Logs

```bash
tail -f catalogue/logs/php_errors.log
tail -f /var/log/apache2/error.log
```

## Best Practices

### 404 Page Design

- **Helpful content** - Guide users
- **Navigation links** - Help find content
- **Search option** - If available
- **Clear messaging** - Explain error

### Configuration

- **Test regularly** - Verify 404 works
- **Check status code** - Ensure 404 not 200
- **Monitor logs** - Watch for issues
- **Update content** - Keep 404 page current

## See Also

- [404 Page Documentation](../site-generation/404_PAGE.md) - 404 system details
- [HTML Generation Issues](./HTML_GENERATION.md) - Generation problems
- [Path & URL Issues](./PATHS_URLS.md) - URL problems

