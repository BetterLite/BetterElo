<?php

namespace Mellooh\BetterElo\libs\Himari;

use Mellooh\BetterElo\libs\Himari\async\AsyncExecutor;
use Mellooh\BetterElo\libs\Himari\config\DatabaseConfig;
use Mellooh\BetterElo\libs\Himari\core\Connector;
use Mellooh\BetterElo\libs\Himari\exception\ConnectionException;
use Mellooh\BetterElo\libs\Himari\log\Logger;
use Mellooh\BetterElo\libs\Himari\query\QueryFileLoader;
use pocketmine\plugin\PluginBase;

class Himari {

    private static function createConnectorInternal(DatabaseConfig $config, array $sqlPaths, AsyncExecutor $executor, ?Logger $logger = null): Connector{
        $driver = strtolower($config->driver);

        if($driver !== 'mysql' && $driver !== 'sqlite'){
            throw new ConnectionException('Unsupported driver: ' . $config->driver);
        }

        $queries = QueryFileLoader::loadFromFiles($driver, $sqlPaths);

        return new Connector($queries, $executor, $logger);
    }

    public static function createConnector(PluginBase $plugin, DatabaseConfig $config, array $sqlPaths, ?Logger $logger = null): Connector{
        $executor = new AsyncExecutor($plugin, $config);
        return self::createConnectorInternal($config, $sqlPaths, $executor, $logger);
    }
}