<?php

namespace Spatie;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\DriverManager;
use Dotenv\Dotenv;
use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;
use Throwable;

class Console extends Application
{
    public function __construct($name = 'Spatie\'s performance playground', $version = '0.1')
    {
        parent::__construct($name, $version);

        $this->setupEnv();

        $connection = $this->setupDatabase();

        $this->loadCommands($connection);
    }

    protected function setupEnv(): void
    {
        $env = new Dotenv(__DIR__ . '/../');
        $env->load();
    }

    protected function setupDatabase(): Connection
    {
        $config = new Configuration();

        $parameters = [
            'dbname' => getenv('DB_NAME'),
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'host' => getenv('DB_HOST'),
            'driver' => 'pdo_mysql',
        ];

        return DriverManager::getConnection($parameters, $config);
    }

    protected function loadCommands(Connection $connection)
    {
        $commandFiles = Finder::create()->in(__DIR__ . '/Command')->name('*.php');

        /** @var \Symfony\Component\Finder\SplFileInfo $commandFile */
        foreach ($commandFiles as $commandFile) {
            $commandName = pathinfo($commandFile->getFilename(), PATHINFO_FILENAME);
            $className = 'Spatie\\Command\\' . $commandName;

            require_once $commandFile->getPathname();

            $this->add(new $className(strtolower($commandName), $connection));
        }
    }
}
