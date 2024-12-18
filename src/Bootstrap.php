<?php declare(strict_types=1);

/**
 * @package     Triangle Database Component
 * @link        https://github.com/Triangle-org/Database
 *
 * @author      Ivan Zorin <creator@localzet.com>
 * @copyright   Copyright (c) 2023-2024 Triangle Framework Team
 * @license     https://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License v3.0
 *
 *              This program is free software: you can redistribute it and/or modify
 *              it under the terms of the GNU Affero General Public License as published
 *              by the Free Software Foundation, either version 3 of the License, or
 *              (at your option) any later version.
 *
 *              This program is distributed in the hope that it will be useful,
 *              but WITHOUT ANY WARRANTY; without even the implied warranty of
 *              MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *              GNU Affero General Public License for more details.
 *
 *              You should have received a copy of the GNU Affero General Public License
 *              along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 *              For any questions, please contact <triangle@localzet.com>
 */

namespace Triangle\Database;

use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\MySqlConnection;
use Illuminate\Events\Dispatcher;
use Illuminate\Pagination\Cursor;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\Paginator;
use localzet\Server;
use localzet\Timer;
use MongoDB\Laravel\Connection as LaravelMongodbConnection;
use support\Container;
use Throwable;
use Triangle\Engine\BootstrapInterface;
use Triangle\MongoDB\Connection as TriangleMongodbConnection;
use function class_exists;
use function config;

/**
 * Класс Eloquent
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * Запускает приложение.
     *
     * @param Server|null $server
     *
     * @return void
     */
    public static function start(?Server $server = null): void
    {
        // Проверяем, существует ли класс Capsule.
        if (!class_exists(Capsule::class)) {
            return;
        }

        // Получаем конфигурацию базы данных.
        $config = config('database', []);
        $connections = $config['connections'] ?? [];

        // Если нет соединений, то выходим.
        if (!$connections) {
            return;
        }

        // Создаем экземпляр Capsule.
        $capsule = new Capsule(IlluminateContainer::getInstance());

        // Расширяем функциональность для MongoDB.
        $capsule->getDatabaseManager()->extend('mongodb', function ($config, $name) {
            $config['name'] = $name;
            return class_exists(LaravelMongodbConnection::class) ? new LaravelMongodbConnection($config) : new TriangleMongodbConnection($config);
        });


        // Добавляем соединения.
        $default = $config['default'] ?? false;
        $persistent = $config['persistent'] ?? true;
        if ($default) {
            $defaultConfig = $connections[$config['default']] ?? false;
            if ($defaultConfig) {
                $capsule->addConnection($defaultConfig);
            }
        }

        foreach ($connections as $name => $config) {
            $capsule->addConnection($config, $name);
        }

        // Устанавливаем диспетчер событий.
        if (class_exists(Dispatcher::class) && !$capsule->getEventDispatcher()) {
            $capsule->setEventDispatcher(Container::make(Dispatcher::class, [IlluminateContainer::getInstance()]));
        }

        // Устанавливаем Capsule как глобальный.
        $capsule->setAsGlobal();

        // Загружаем Eloquent.
        $capsule->bootEloquent();

        // Heartbeat
        if ($server && $persistent) {
            Timer::add(55, function () use ($default, $connections, $capsule) {
                foreach ($capsule->getDatabaseManager()->getConnections() as $connection) {
                    /* @var MySqlConnection $connection * */
                    if ($connection->getConfig('driver') == 'mysql' && $connection->getRawPdo()) {
                        try {
                            $connection->select('select 1');
                        } catch (Throwable) {
                        }
                    }
                }
            });
        }

        // Paginator
        if (class_exists(Paginator::class)) {
            if (method_exists(Paginator::class, 'queryStringResolver')) {
                Paginator::queryStringResolver(function () {
                    $request = request();
                    return $request?->queryString();
                });
            }
            Paginator::currentPathResolver(function () {
                $request = request();
                return $request ? $request->path() : '/';
            });
            Paginator::currentPageResolver(function ($pageName = 'page') {
                $request = request();
                if (!$request) {
                    return 1;
                }
                $page = (int)($request->input($pageName, 1));
                return $page > 0 ? $page : 1;
            });
            if (class_exists(CursorPaginator::class)) {
                CursorPaginator::currentCursorResolver(function ($cursorName = 'cursor') {
                    return Cursor::fromEncoded(request()->input($cursorName));
                });
            }
        }
    }
}
