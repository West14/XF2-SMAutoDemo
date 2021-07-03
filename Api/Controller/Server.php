<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 30.03.2020
 * Time: 0:50
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Api\Controller;


use XF\Api\Controller\AbstractController;
use XF\Mvc\ParameterBag;

class Server extends AbstractController
{
    /**
     * @param ParameterBag $params
     * @return \XF\Api\Mvc\Reply\ApiResult
     * @throws \XF\Mvc\Reply\Exception
     * @throws \XF\PrintableException
     */
    public function actionPostNewDemo(ParameterBag $params)
    {
        $this->assertRequiredApiInput(['demo_id']);
        $this->assertServerExists($params->server_id);

        $demo = $this->em()->create('West\SMAutoDemo:Demo');
        $demo->server_id = $params->server_id;
        $demo->bulkSet(
            $this->filter([
                'demo_id' => 'str'
            ])
        );
        $demo->save();

        $this->app()->jobManager()->enqueueUnique(
            'wsmad-demoDl-' . substr($demo->demo_id, 0, 8),
            'West\SMAutoDemo:DemoDownload',
            [
                'demoId' => $demo->demo_id
            ]
        );
        return $this->apiSuccess();
    }

    /**
     * @param $id
     * @param null $with
     * @param null $phraseKey
     * @return \XF\Mvc\Entity\Entity
     * @throws \XF\Mvc\Reply\Exception
     */
    protected function assertServerExists($id, $with = null, $phraseKey = null)
    {
        return $this->assertRecordExists('West\SMAutoDemo:Server', $id, $with, $phraseKey);
    }
}