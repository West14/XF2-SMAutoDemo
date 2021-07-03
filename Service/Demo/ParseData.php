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
     * @throws \Exception
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

        $this->demo->bulkSet([
            'map' => $demoData['play_map'],
            'demo_started' => $demoData['start_time'],
            'demo_ended' => $demoData['end_time'],
            'tick_count' => $demoData['recorded_ticks']
        ]);
        $this->demo->save();

        foreach ($demoData['events'] as $event)
        {
            /** @var \West\SMAutoDemo\Entity\DemoEvent $demoEvent */
            $demoEvent = $this->em()->create('West\SMAutoDemo:DemoEvent');
            $demoEvent->bulkSet([
                'demo_id' => $demoId,
                'name' => $event['event_name'],
                'time' => $event['time'],
                'tick' => $event['tick']
            ]);
            $demoEvent->save();

            foreach ($event['data'] as $field => $value)
            {
                $eventData = $this->em()->create('West\SMAutoDemo:DemoEventData');
                $eventData->bulkSet([
                    'event_id' => $demoEvent->event_id,
                    'field' => $field,
                    'value' => $value
                ]);
                $eventData->save();
            }
        }
    }
}