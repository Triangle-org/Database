# Triangle Database

Triangle Database connects [Illuminate Database](https://laravel.com/docs/database) to applications running on Triangle Engine 9 and Localzet Server 7. It boots Eloquent and the query builder, registers database connections from the Engine configuration, and wires pagination to the current request.

This branch prepares the next major release, `2.x`. The published `1.x` line targets older Engine versions. Until `2.x` is released, install this branch only when you intend to test the Engine 9 migration.

## Compatibility

| Component | Supported version |
| --- | --- |
| PHP | 8.2 or newer |
| Triangle Engine | 9.x |
| Localzet Server | 7.x, provided by Triangle Engine |
| Illuminate Database, Events, Pagination | 12.x or 13.x |
| Symfony VarDumper | 7.4.x |

Engine 8 and earlier are not supported by this branch. A project migrating from the published 1.x line should upgrade its Triangle components together and review its dependency lock before deployment.

Illuminate 12 supports this package on PHP 8.2 and newer. Illuminate 13 requires PHP 8.3 or newer; Composer selects the compatible major version for the PHP version used to resolve dependencies.

## Install

After the 2.x release is published:

```sh
composer require triangle/database:^2.0
```

During development, use the repository branch or a local Composer path repository. Do not assume the Packagist 1.x release contains Engine 9 support.

## Configure a connection

Triangle Engine loads `config/database.php` during application startup. The package includes a configuration template with SQLite, MySQL, PostgreSQL, SQL Server, and MongoDB examples. Keep credentials in environment variables and enable only the drivers and PHP extensions used by the application.

For example, select a MySQL connection in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=app
DB_USERNAME=app
DB_PASSWORD=change-me
```

`DB_PASSWORD` is a placeholder; provide the real value through the deployment environment or a secret store. Do not commit credentials.

The package registers the selected default connection and each configured named connection. `persistent` defaults to `true`. When Engine starts the package with a Server instance, it schedules a MySQL keepalive query every 55 seconds. Set `persistent` to `false` when that behavior is not wanted. The manager and its connections are process-local; multiple Server workers each hold their own database manager.

## Use Eloquent and the query builder

Extend the package model to define an application model:

```php
<?php

use Triangle\Database\Model;

final class User extends Model
{
    protected $table = 'users';

    protected $fillable = ['name'];

    public $timestamps = false;
}
```

Then use the standard Illuminate APIs:

```php
$user = User::query()->create(['name' => 'Ada']);
$found = User::query()->where('id', $user->id)->first();

$rows = \Triangle\Database\Manager::table('users')
    ->where('name', 'Ada')
    ->get();
```

The package's `Model` extends Eloquent's model. `Manager` extends Illuminate's Capsule manager, so its connection, schema, and query-builder entry points follow Illuminate's APIs. For raw SQL, pass values through bindings; do not interpolate user input into a query string.

## Pagination and application startup

Engine loads `Triangle\Database\Bootstrap` through the component lifecycle. The bootstrap is a no-op when no database connections are configured. Otherwise it configures Capsule, boots Eloquent, and sets page, path, query-string, and cursor resolvers from the current Triangle request.

Use Illuminate's `paginate`, `simplePaginate`, or `cursorPaginate` methods from the request handler. Pagination resolvers are process-wide callbacks, so call them while handling a request rather than retaining request data in long-lived application state.

The package exposes Illuminate's schema builder but does not provide a separate migration CLI or run schema changes automatically. Manage migrations through the application's chosen deployment process.

## Drivers and extensions

Database driver availability depends on the installed PHP extensions and PDO drivers. SQLite is used by the package integration test. MySQL, PostgreSQL, and SQL Server require their corresponding PDO extensions. MongoDB is optional and uses an installed compatible MongoDB integration when available; MongoDB support is not covered by the local SQLite test.

## Verify locally

```sh
composer install --no-interaction
composer validate --strict --no-check-publish
composer test
```

The integration test boots Engine 9 against an in-memory SQLite database, creates a table, and verifies an Eloquent insert and read. GitHub Actions runs the checks on PHP 8.2 and 8.5 with both the lowest supported and latest allowed dependency versions. It also tests Illuminate 12 on PHP 8.2 and Illuminate 13 on PHP 8.3 and 8.5, covering the minimum and latest supported PHP versions for Illuminate 13. The workflow resolves dependencies from `composer.json` because this library does not commit a lock file. It does not test external database services or production credentials.

## Security and operations

- Use least-privilege database accounts and restrict database network access to application hosts.
- Keep connection secrets out of source control, logs, and exception responses.
- Prefer parameterized query-builder and Eloquent APIs. Bind every value passed to raw SQL.
- Set connection timeouts and TLS options in the driver configuration required by the deployment.
- Treat the 55-second MySQL keepalive as a connection-liveness mechanism, not as a pool, retry policy, or availability guarantee.
- Back up and migrate schemas through the deployment process; this component does not perform automatic migrations.

## License

AGPL-3.0-or-later. See [`LICENSE`](LICENSE).

Copyright © 2016-2026 [Localzet Group](https://www.localzet.com/). All rights reserved.
