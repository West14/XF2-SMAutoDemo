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
                'data', 'wsmad-demos', $server->server_id, $this->demo->demo_id . '.zip'
            ]))
        );

        $server->mountFs();
        $fs->mountFilesystem('wsmad-temp-demo-zip', $zip);

        try {
            $serverFsPrefix = $server->getFsPrefix();
            $demoFilename = $this->getDemoFilename();
            $jsonFilename = $this->getJsonFilename();

            $serverDemPath = "{$serverFsPrefix}://{$demoFilename}";
            $serverJsonPath = "{$serverFsPrefix}://{$jsonFilename}";

            /*$demoStream = $fs->readStream($serverDemPath);
            $fs->writeStream("wsmad-temp-demo-zip://{$demoFilename}", $demoStream);*/

            /**
             *  Modern problems needs modern solutions
             *  West, 19.05.2020 22:54
             */
            $demSize = $fs->getSize($serverDemPath);
            $availableMemory = \XF::getAvailableMemory();
            if (!$demSize || ($availableMemory > 0 && $demSize >= $availableMemory))
            {
                \XF::logError("Size of the \"{$this->demo->demo_id}\" demo is greater than amount of available memory.");
                return;
            }

            $fs->copy($serverDemPath, "wsmad-temp-demo-zip://{$demoFilename}");
            $fs->copy(
                $serverJsonPath,
                PathUtil::buildPath(['data://wsmad-demos', $server->server_id, $jsonFilename], true)
            );

            $fs->delete($serverDemPath);
            $fs->delete($serverJsonPath);

        } catch (FileExistsException $e) {
            \XF::logException($e);
            return;
        }

        $this->demo->bulkSet([
            'download_state' => 'downloaded',
            'downloaded_at' => \XF::$time
        ]);
        $this->demo->save();

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