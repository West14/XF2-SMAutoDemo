<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 12.04.2020
 * Time: 17:35
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Pub\View\Demo;


use XF\Mvc\View;

class Download extends View
{
    /**
     * @return \XF\Http\ResponseStream
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function renderRaw()
    {
        /** @var \West\SMAutoDemo\Entity\Demo $demo */
        $demo = $this->params['demo'];

        $fs = \XF::app()->fs();
        $path = $demo->getAbstractedZipPath();

        $this->response->setAttachmentFileParams("{$demo->demo_id}.zip", 'zip');

        return $this->response->responseStream(
            $fs->readStream($path),
            $fs->getSize($path)
        );
    }
}