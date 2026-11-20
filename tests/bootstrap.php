<?php declare(strict_types=1);

/**
 * @package     Triangle Database Component Tests
 * @link        https://github.com/Triangle-org/Database
 * @author      Ivan Zorin <creator@localzet.com>
 * @copyright   Copyright © 2016-2026 Localzet Group. All rights reserved.
 * @license     AGPL-3.0-or-later
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Database\Schema\Blueprint;
use Triangle\Database\Bootstrap;
use Triangle\Database\Manager;
use Triangle\Database\Model;
use Triangle\Engine\Config;

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$container = new IlluminateContainer();
IlluminateContainer::setInstance($container);

Config::clear();
Config::set([
    'container' => $container,
    'database' => [
        'default' => 'sqlite',
        'persistent' => false,
        'connections' => [
            'sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ],
        ],
    ],
]);

Bootstrap::start();

Manager::schema()->create('entries', static function (Blueprint $table): void {
    $table->increments('id');
    $table->string('name');
});

$model = new class extends Model {
    protected $table = 'entries';

    protected $fillable = ['name'];

    public $timestamps = false;
};

$model->newQuery()->create(['name' => 'Engine 9']);
$entry = $model->newQuery()->first();

expect($entry !== null, 'The SQLite row was not loaded.');
expect($entry->name === 'Engine 9', 'The model returned an unexpected value.');

echo "ENGINE9_SQLITE_BOOTSTRAP=ok\n";
