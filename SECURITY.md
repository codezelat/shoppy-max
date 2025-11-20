# Security Guide

## Overview

This application implements comprehensive security measures including role-based access control (RBAC), permission-based authorization, and secure authentication practices.

## Authentication Security

### Password Security
- Passwords are hashed using bcrypt algorithm
- Minimum password requirements enforced by Laravel's default password rules
- Password confirmation required for sensitive operations
- Password reset functionality with secure tokens

### Session Security
- CSRF protection enabled on all forms
- Secure session configuration
- Session timeout after inactivity

## Authorization Security

### Permission-Based Access Control

The application uses **permission-based middleware** instead of role-based middleware for granular access control:

```php
// Controllers use permission checks, not role checks
$this->middleware(['auth', 'permission:view users'])->only(['index']);
$this->middleware(['auth', 'permission:create users'])->only(['create', 'store']);
```

This provides better security because:
- Permissions can be reassigned without changing code
- Users can have custom permissions beyond their role
- More flexible and maintainable access control

### Available Permissions

1. **User Management:**
   - `view users` - View user list
   - `create users` - Create new users
   - `edit users` - Edit existing users
   - `delete users` - Delete users

2. **Role Management:**
   - `view roles` - View role list
   - `create roles` - Create new roles
   - `edit roles` - Edit existing roles
   - `delete roles` - Delete roles

3. **Permission Management:**
   - `view permissions` - View permission list
   - `assign permissions` - Create, edit, delete permissions

### Role Hierarchy

1. **Super Admin** - Has all permissions by default
2. **Admin** - Limited permissions (view users, create users, edit users, view roles, view permissions)
3. **User** - No admin permissions by default

## Initial Setup Security

### ⚠️ IMPORTANT: Change Default Password

The default super admin account is created with password `password`. **This MUST be changed immediately in production!**

**To change the password:**

1. Using the profile page (recommended):
   - Log in as admin@shoppy-max.com
   - Go to Profile
   - Change password through the UI

2. Using Tinker:
   ```bash
   php artisan tinker
   User::where('email', 'admin@shoppy-max.com')->first()->update([
       'password' => Hash::make('your-secure-password')
   ]);
   ```

3. Using database seeder (before first deployment):
   - Edit `database/seeders/RolesAndPermissionsSeeder.php`
   - Change line 61: `'password' => Hash::make('your-secure-password')`

## Production Deployment Security

### Environment Configuration

1. **Set APP_ENV to production:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Generate secure APP_KEY:**
   ```bash
   php artisan key:generate
   ```

3. **Use HTTPS:**
   - Configure SSL certificate
   - Force HTTPS in production

4. **Secure database credentials:**
   - Use strong database passwords
   - Restrict database user permissions
   - Use environment variables for credentials

### Security Headers

Consider adding these security headers in your web server configuration:

```
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-XSS-Protection: 1; mode=block
Referrer-Policy: no-referrer-when-downgrade
Content-Security-Policy: default-src 'self'
```

### File Permissions

Set appropriate file permissions:
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

## Input Validation

All user inputs are validated:
- Email validation
- Password confirmation
- Unique email addresses
- Required fields validation
- XSS protection through Laravel's automatic escaping

## SQL Injection Protection

- All database queries use Eloquent ORM or Query Builder
- Prepared statements prevent SQL injection
- No raw SQL queries with user input

## CSRF Protection

- All forms include CSRF tokens
- AJAX requests include CSRF tokens
- Protection enabled by default in middleware

## Best Practices

1. **Regular Updates:**
   - Keep Laravel and packages updated
   - Monitor security advisories
   - Run `composer audit` regularly

2. **User Management:**
   - Assign minimum required permissions
   - Regularly review user access
   - Remove inactive users
   - Use strong password policies

3. **Monitoring:**
   - Log authentication attempts
   - Monitor for suspicious activity
   - Regular security audits

4. **Backup:**
   - Regular database backups
   - Secure backup storage
   - Test backup restoration

## Security Checklist for Production

- [ ] Changed default super admin password
- [ ] Set APP_ENV=production
- [ ] Set APP_DEBUG=false
- [ ] Generated new APP_KEY
- [ ] Configured HTTPS
- [ ] Set secure session configuration
- [ ] Configured proper file permissions
- [ ] Set up database backups
- [ ] Reviewed and assigned appropriate permissions
- [ ] Removed or disabled unnecessary features
- [ ] Configured security headers
- [ ] Set up monitoring and logging

## Reporting Security Issues

If you discover a security vulnerability, please email it to the maintainers immediately. Do not create a public issue.

## Additional Resources

- [Laravel Security Documentation](https://laravel.com/docs/security)
- [Spatie Permission Documentation](https://spatie.be/docs/laravel-permission)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
