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

use Closure;
use Illuminate\Database\Connection;

/**
 * Класс Manager
 * Этот класс предоставляет статические методы для работы с базой данных.
 *
 * @link https://laravel.com/docs/8.x/database
 *
 * Методы:
 * @method static array select(string $query, $bindings = [], $useReadPdo = true) Выполняет SELECT-запрос в базе данных и возвращает результат.
 * @method static int insert(string $query, $bindings = []) Выполняет INSERT-запрос в базе данных и возвращает количество затронутых строк.
 * @method static int update(string $query, $bindings = []) Выполняет UPDATE-запрос в базе данных и возвращает количество затронутых строк.
 * @method static int delete(string $query, $bindings = []) Выполняет DELETE-запрос в базе данных и возвращает количество затронутых строк.
 * @method static bool statement(string $query, $bindings = []) Выполняет SQL-запрос в базе данных и возвращает true в случае успеха и false в случае неудачи.
 * @method static mixed transaction(Closure $callback, $attempts = 1) Выполняет транзакцию в базе данных.
 * @method static void beginTransaction() Начинает транзакцию в базе данных.
 * @method static void rollBack($toLevel = null) Откатывает транзакцию в базе данных.
 * @method static void commit() Фиксирует транзакцию в базе данных.
 */
class Manager extends \Illuminate\Database\Capsule\Manager
{
    /**
     * @return object
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * @return Connection[]
     */
    public static function getConnections()
    {
        return static::$instance->getDatabaseManager()->getConnections();
    }
}
