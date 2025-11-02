# Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.0.x   | :white_check_mark: |
| < 1.0   | :x:                |

## Reporting a Vulnerability

Please **DO NOT** report security vulnerabilities publicly.

Instead, please email **security@localzet.com** with:
- Description of the vulnerability
- Steps to reproduce
- Potential impact
- Suggested fix (if any)

We will acknowledge receipt within 24 hours and provide an initial response within 48 hours.

## Security Best Practices

1. **Always use parameterized queries** - Never concatenate user input into SQL queries
2. Use database connection pooling securely
3. Encrypt sensitive data at rest
4. Use strong database passwords
5. Limit database user permissions (principle of least privilege)
6. Regularly update database dependencies
7. Monitor database access logs
8. Use prepared statements for all queries

## SQL Injection Prevention

This component uses Illuminate Database which provides automatic SQL injection protection through:
- Parameter binding in query builder
- Prepared statements in Eloquent ORM
- Automatic escaping of bound parameters

Example of safe query:
```php
// ✅ Safe - uses parameter binding
DB::table('users')->where('email', $email)->first();

// ❌ Unsafe - never do this
DB::select("SELECT * FROM users WHERE email = '$email'");
```

