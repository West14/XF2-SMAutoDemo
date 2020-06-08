<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 30.03.2020
 * Time: 17:24
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Admin\Controller;


use XF\Admin\Controller\AbstractController;
use XF\Mvc\ParameterBag;

class Server extends AbstractController
{
    public function actionIndex()
    {
        return $this->view('West\SMAutoDemo:Server\List', 'wsmad_server_list', [
            'servers' => $this->getServerRepo()->findServersForList()->fetch()
        ]);
    }

    public function actionAdd()
    {
        /** @var \West\SMAutoDemo\Entity\Server $server */
        $server = $this->em()->create('West\SMAutoDemo:Server');
        return $this->serverAddEdit($server);
    }

    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\View
     * @throws \XF\Mvc\Reply\Exception
     */
    public function actionEdit(ParameterBag $params)
    {
        $server = $this->assertServerExists($params->server_id);
        return $this->serverAddEdit($server);
    }

    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\Redirect
     * @throws \XF\PrintableException
     * @throws \XF\Mvc\Reply\Exception
     */
    public function actionSave(ParameterBag $params)
    {
        if ($params->server_id)
        {
            $server = $this->assertServerExists($params->server_id);
        }
        else
        {
            /** @var \West\SMAutoDemo\Entity\Server $server */
            $server = $this->em()->create('West\SMAutoDemo:Server');
        }

        $this->serverSaveProcess($server)->run();

        return $this->redirect($this->buildLink('wsmad-servers') . $this->buildLinkHash($server->server_id));
    }

    public function actionDelete(ParameterBag $params)
    {
        // TODO
    }

    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\View
     * @throws \XF\Mvc\Reply\Exception
     * @throws \Exception
     */
    public function actionGetOptions(ParameterBag $params)
    {
        $server = $this->assertServerExists($params->server_id);
        $id = $this->filter('id', 'str');

        $server->adapter_id = $id;
        $adapter = $server->getFsAdapter();

        $view = $this->view('West\SMAutoDemo:Server\Options');
        $view->setJsonParam('description', $adapter->renderOptions());
        return $view;
    }
    
    protected function serverSaveProcess(\West\SMAutoDemo\Entity\Server $server)
    {
        return $this->formAction()
            ->basicEntitySave(
                $server,
                $this->filter([
                    'name' => 'str',
                    'ip' => 'str',
                    'port' => 'uint',
                    'adapter_id' => 'str',
                    'adapter_options' => 'json-array'
                ])
            );
    }

    /**
     * @param \West\SMAutoDemo\Entity\Server $server
     * @return \XF\Mvc\Reply\View
     * @throws \Exception
     */
    protected function serverAddEdit(\West\SMAutoDemo\Entity\Server $server)
    {
        return $this->view('West\SMAutoDemo:Server\Edit', 'wsmad_server_edit', [
            'server' => $server,
            'fsAdapters' => $this->getFsAdapterRepo()->findAdaptersForList()->fetch(),
            'fsAdapterOptionsHtml' => $server->getFsAdapter()->renderOptions()
        ]);
    }

    /**
     * @param $id
     * @param null $with
     * @param null $phraseKey
     * @return \XF\Mvc\Entity\Entity|\West\SMAutoDemo\Entity\Server
     * @throws \XF\Mvc\Reply\Exception
     */
    protected function assertServerExists($id, $with = null, $phraseKey = null)
    {
        return $this->assertRecordExists('West\SMAutoDemo:Server', $id, $with, $phraseKey);
    }

    /**
     * @return \XF\Mvc\Entity\Repository|\West\SMAutoDemo\Repository\Server
     */
    protected function getServerRepo()
    {
        return $this->repository('West\SMAutoDemo:Server');
    }

    /**
     * @return \XF\Mvc\Entity\Repository|\West\SMAutoDemo\Repository\FsAdapter
     */
    protected function getFsAdapterRepo()
    {
        return $this->repository('West\SMAutoDemo:FsAdapter');
    }
}