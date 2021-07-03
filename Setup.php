<?php

namespace West\SMAutoDemo;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

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

    public function installStep2()
    {
        $this->insertDefaultAdapters();
    }

    public function upgrade1000012Step1()
    {
        $tableNames = ['xf_wsmad_player', 'xf_wsmad_demo_player'];
        $tables = $this->getTables();

        foreach ($tableNames as $tableName)
        {
            $this->createTable($tableName, $tables[$tableName]);
        }
    }

    public function upgrade1000013Step1()
    {
        $this->alterTable('xf_wsmad_demo', function (Alter $table)
        {
            $table->addColumn('map', 'text');
            $table->addColumn('demo_started', 'int')->setDefault(0);
            $table->addColumn('demo_ended', 'int')->setDefault(0);
            $table->addColumn('tick_count', 'int')->setDefault(0);
        });
    }

    public function upgrade1000013Step2()
    {
        $db = $this->db();
        $demos = $db->fetchAll("
                    SELECT `demo_id`, `demo_data` 
                    FROM `xf_wsmad_demo` 
                    WHERE `download_state` = 'downloaded'
         ");

        foreach ($demos as $demo)
        {
            if ($demoData = \XF\Util\Json::decodeJsonOrSerialized($demo['demo_data']))
            {
                $db->update('xf_wsmad_demo', [
                    'map' => $demoData['play_map'],
                    'demo_started' => $demoData['start_time'],
                    'demo_ended' => $demoData['end_time'],
                    'tick_count' => $demoData['recorded_ticks']
                ], 'demo_id = ?', $demo['demo_id']);
            }
        }
    }

    public function upgrade1000013Step3()
    {
        $this->alterTable('xf_wsmad_demo', function (Alter $table)
        {
            $table->dropColumns(['demo_data']);
        });
    }

    public function upgrade1000014Step1()
    {
        $tableName = 'xf_wsmad_fsadapter';

        $this->createTable($tableName, $this->getTables()[$tableName]);
        $this->insertDefaultAdapters();
    }

    public function upgrade1000014Step2()
    {
        $this->alterTable('xf_wsmad_server', function (Alter $table)
        {
            $table->addColumn('adapter_id', 'varchar', 50);
            $table->renameColumn('sftp', 'adapter_options');
        });
    }

    public function upgrade1000014Step3()
    {
        $this->db()->update('xf_wsmad_server', [
            'adapter_id' => 'wsmadSftp'
        ], null);
    }

    public function upgrade1000015Step1()
    {
        $tableNames = ['xf_wsmad_demo_event', 'xf_wsmad_demo_event_data'];
        $tables = $this->getTables();

        foreach ($tableNames as $tableName)
        {
            $this->createTable($tableName, $tables[$tableName]);
        }
    }

    public function uninstallStep1()
    {
        foreach (array_keys($this->getTables()) as $tableName)
        {
            $this->dropTable($tableName);
        }
    }

    protected function insertDefaultAdapters()
    {
        // Setup default data
        $db = $this->db();
        $data = [
            'wsmadFtp' => 'West\SMAutoDemo:Ftp',
            'wsmadSftp' => 'West\SMAutoDemo:Sftp',
        ];

        $insert = [];
        foreach ($data as $id => $class)
        {
            $insert[] = [
                'adapter_id' => $id,
                'adapter_class' => $class,
                'addon_id' => 'West/SMAutoDemo'
            ];
        }
        $db->insertBulk('xf_wsmad_fsadapter', $insert);
    }

    protected function getTables()
    {
        $tables = [];

        $tables['xf_wsmad_server'] = function (Create $table)
        {
            $table->addColumn('server_id', 'int')->autoIncrement();
            $table->addColumn('name', 'text');
            $table->addColumn('ip', 'text');
            $table->addColumn('port', 'int')->setDefault(27015);
            $table->addColumn('adapter_id', 'varchar', 50);
            $table->addColumn('adapter_options', 'blob');
        };

        $tables['xf_wsmad_demo'] = function (Create $table)
        {
            $table->addColumn('demo_id', 'varchar', 36);
            $table->addColumn('server_id', 'int');
            $table->addColumn('map', 'text');
            $table->addColumn('demo_started', 'int')->setDefault(0);
            $table->addColumn('demo_ended', 'int')->setDefault(0);
            $table->addColumn('tick_count', 'int')->setDefault(0);
            $table->addColumn('download_state', 'enum')->values(['downloaded', 'enqueued', 'not_downloaded']);
            $table->addColumn('downloaded_at', 'int')->setDefault(0);
            $table->addPrimaryKey('demo_id');
        };

        $tables['xf_wsmad_player'] = function (Create $table)
        {
            $table->addColumn('account_id', 'int');
            $table->addColumn('username', 'text');
            $table->addPrimaryKey('account_id');
        };

        $tables['xf_wsmad_demo_player'] = function (Create $table)
        {
            $table->addColumn('demo_id', 'varchar', 36);
            $table->addColumn('account_id', 'int');
            $table->addColumn('data', 'blob');
            $table->addPrimaryKey(['demo_id', 'account_id']);
        };

        $tables['xf_wsmad_fsadapter'] = function (Create $table)
        {
            $table->addColumn('adapter_id', 'varchar', 50);
            $table->addColumn('adapter_class', 'text');
            $table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
            $table->addPrimaryKey('adapter_id');
        };

        $tables['xf_wsmad_demo_event'] = function (Create $table)
        {
            $table->addColumn('event_id', 'varchar', 36);
            $table->addColumn('demo_id', 'varchar', 36);
            $table->addColumn('name', 'varchar', 64);
            $table->addColumn('time', 'int');
            $table->addColumn('tick', 'int');
            $table->addPrimaryKey('event_id');
        };

        $tables['xf_wsmad_demo_event_data'] = function(Create $table)
        {
            $table->addColumn('event_id', 'varchar', 36);
            $table->addColumn('field', 'varchar', 64);
            $table->addColumn('value', 'varbinary', 128);
            $table->addPrimaryKey(['event_id', 'field']);
        };

        return $tables;
    }
}