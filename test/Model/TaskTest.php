<?php
namespace MyApp\Test\Model;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\Capsule\Manager as CapsuleManager;
use Phinx\Migration\Manager as MigrationManager;
use Phinx\Config\Config as PhinxConfig;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;
use MyApp\Model\Task;

class TaskTest extends TestCase
{
    public function setUp()
    {
        $env = 'testing';
        $config = PhinxConfig::fromYaml(__DIR__.'/../../phinx.yml');
        $dbConfig = $config->getEnvironment($env);

        $capsule = new CapsuleManager();
        $capsule->addConnection([
            'driver' => $dbConfig['adapter'],
            'database' => (isset($dbConfig['memory']) ?? $dbConfig['memory']) ? ':memory:' : $dbConfig['name']
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $dbManager = $capsule->getDatabaseManager();
        $conn = $dbManager->connection('default')->getPdo();

        $migrationManager = new MigrationManager($config, new ArgvInput(), new NullOutput());
        $migrationManager->getEnvironment($env)->getAdapter()->setConnection($conn);
        $migrationManager->migrate($env);
    }

    public function testSave()
    {
        $task = new Task(['title' => 'abcde12345']);
        $task->save();
        $this->assertEquals($task->id, 1);
    }

    /**
     * @expectedException \Illuminate\Database\QueryException
     */
    public function testSaveWhenTitleIsNull()
    {
        $task = new Task();
        $task->save();
    }

}
