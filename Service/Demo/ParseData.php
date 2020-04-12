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
        if (!$this->demo->is_downloaded)
        {
            \XF::logError('[SM AutoDemo] Cannot parse demo json because demo isn\'t downloaded');
            return;
        }

        $this->demo->demo_data = \XF\Util\Json::decodeJsonOrSerialized(
            \XF::app()->fs()->read($this->demo->getAbstractedJsonPath())
        );

        $this->demo->save();
    }
}