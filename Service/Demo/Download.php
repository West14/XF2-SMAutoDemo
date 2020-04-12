<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 30.03.2020
 * Time: 19:45
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Service\Demo;


use League\Flysystem\FileExistsException;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use West\SMAutoDemo\Util\Path;
use West\SMAutoDemo\Util\Path as PathUtil;
use XF\Service\AbstractService;

class Download extends AbstractService
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
    public function download()
    {
        $fs = $this->app->fs();
        $server = $this->demo->Server;

        $zip = new \League\Flysystem\Filesystem(
            new ZipArchiveAdapter(PathUtil::buildPathFromRoot([
                'data', 'wsmad-demos', $this->demo->demo_id . '.zip'
            ]))
        );

        $server->mountFs();
        $fs->mountFilesystem('wsmad-temp-demo-zip', $zip);

        try {
            $fs->copy(
                $server->getFsPrefix() . '://' . $this->getDemoFilename(),
                "wsmad-temp-demo-zip://" . $this->getDemoFilename()
            );
            $fs->copy(
                $server->getFsPrefix() . '://' . $this->getJsonFilename(),
                PathUtil::buildPath(['data://wsmad-demos', $server->server_id, $this->getJsonFilename()])
            );
        } catch (FileExistsException $e) {
            \XF::logException($e);
            return;
        }

        $this->demo->fastUpdate([
            'is_downloaded' => true,
            'downloaded_at' => \XF::$time
        ]);

        /** @var ParseData $parser */
        $parser = $this->service('West\SMAutoDemo:Demo\ParseData', $this->demo);
        $parser->parse();
    }

    protected function getDemoFilename()
    {
        return $this->demo->demo_id . '.dem';
    }

    protected function getJsonFilename()
    {
        return $this->demo->demo_id . '.json';
    }
}