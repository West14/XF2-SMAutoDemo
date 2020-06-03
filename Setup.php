<?php

namespace West\SMAutoDemo;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;

class Setup extends AbstractSetup
{
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public function installStep1()
    {
        foreach ($this->getTables() as $tableName => $closure)
        {
            $this->createTable($tableName, $closure);
        }
    }

    public function upgrade1000012Step1()
    {
        $tables = ['xf_wsmad_player', 'xf_wsmad_demo_player'];

        foreach ($tables as $tableName)
        {
            $this->createTable($tableName, $this->getTables()[$tableName]);
        }
    }

    public function uninstallStep1()
    {
        foreach (array_keys($this->getTables()) as $tableName)
        {
            $this->dropTable($tableName);
        }
    }

    protected function getTables()
    {
        $tables = [];

        $tables['xf_wsmad_server'] = function (\XF\Db\Schema\Create $table)
        {
            $table->addColumn('server_id', 'int')->autoIncrement();
            $table->addColumn('name', 'text');
            $table->addColumn('ip', 'text');
            $table->addColumn('port', 'int')->setDefault(27015);
            $table->addColumn('sftp', 'blob');
        };

        $tables['xf_wsmad_demo'] = function (\XF\Db\Schema\Create $table)
        {
            $table->addColumn('demo_id', 'varchar', 36);
            $table->addColumn('server_id', 'int');
            $table->addColumn('demo_data', 'blob');
            $table->addColumn('download_state', 'enum')->values(['downloaded', 'enqueued', 'not_downloaded']);
            $table->addColumn('downloaded_at', 'int')->setDefault(0);
            $table->addPrimaryKey('demo_id');
        };

        $tables['xf_wsmad_player'] = function (\XF\Db\Schema\Create $table)
        {
            $table->addColumn('account_id', 'int');
            $table->addColumn('username', 'text');
            $table->addPrimaryKey('account_id');
        };

        $tables['xf_wsmad_demo_player'] = function (\XF\Db\Schema\Create $table)
        {
            $table->addColumn('demo_id', 'varchar', 36);
            $table->addColumn('account_id', 'int');
            $table->addColumn('data', 'blob');
            $table->addPrimaryKey(['demo_id', 'account_id']);
        };

        return $tables;
    }
}