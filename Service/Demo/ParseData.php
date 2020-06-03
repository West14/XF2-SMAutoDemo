<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 12.04.2020
 * Time: 14:09
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Service\Demo;


use XF\Service\AbstractService;

class ParseData extends AbstractService
{
    /** @var \West\SMAutoDemo\Entity\Demo */
    protected $demo;

    public function __construct(\XF\App $app, \West\SMAutoDemo\Entity\Demo $demo)
    {
        parent::__construct($app);
        
        $this->demo = $demo;
    }

    /**
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \XF\PrintableException
     */
    public function parse()
    {
        if (!$this->demo->isDownloaded())
        {
            \XF::logError('[SM AutoDemo] Cannot parse demo json because demo isn\'t downloaded');
            return;
        }

        $demoId = $this->demo->demo_id;
        $demoData = \XF\Util\Json::decodeJsonOrSerialized(
            \XF::app()->fs()->read($this->demo->getAbstractedJsonPath())
        );

        foreach ($demoData['players'] as $player)
        {
            $demoPlayer = $this->em()->create('West\SMAutoDemo:DemoPlayer');
            $demoPlayer->bulkSet([
                'demo_id' => $demoId,
                'account_id' => $player['account_id']
            ]);
            $demoPlayer->save();

            /** @var \West\SMAutoDemo\Entity\Player $ePlayer */
            $ePlayer = $demoPlayer->getRelationOrDefault('Player');
            $ePlayer->username = $player['name'];

            if ($ePlayer->hasChanges())
                $ePlayer->save();
        }

        // TODO: for BC purposes, will be moved to the columns in future
        $this->demo->demo_data = $demoData;
        $this->demo->save();
    }
}