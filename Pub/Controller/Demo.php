<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 12.04.2020
 * Time: 17:03
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Pub\Controller;


use XF\Mvc\ParameterBag;
use XF\Pub\Controller\AbstractController;

class Demo extends AbstractController
{
    /**
     * @return \XF\Mvc\Reply\View
     * @throws \XF\Mvc\Reply\Exception
     */
    public function actionIndex()
    {
        $page = $this->filterPage();
        $perPage = $this->options()->wsmadDemosPerPage;

        $finder = $this->getDemoRepo()
            ->findDemosForView()
            ->limitByPage($page, $perPage);

        $total = $finder->total();

        $this->assertValidPage($page, $perPage, $total, 'demos');

        return $this->view('West\SMAutoDemo:Demo\List', 'wsmad_demo_list', [
            'demos' => $finder->fetch(),

            'page' => $page,
            'perPage' => $perPage,
            'total' => $total
        ]);
    }

    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\View
     * @throws \XF\Mvc\Reply\Exception
     */
    public function actionDownload(ParameterBag $params)
    {
        $this->setResponseType('raw');

        return $this->view('West\SMAutoDemo:Demo\Download', '', [
            'demo' => $this->assertDemoExists($params->demo_id)
        ]);
    }

    /**
     * @param $id
     * @param null $with
     * @param null $phraseKey
     * @return \XF\Mvc\Entity\Entity|\West\SMAutoDemo\Entity\Demo
     * @throws \XF\Mvc\Reply\Exception
     */
    protected function assertDemoExists($id, $with = null, $phraseKey = null)
    {
        return $this->assertRecordExists('West\SMAutoDemo:Demo', $id, $phraseKey);
    }

    /**
     * @return \XF\Mvc\Entity\Repository|\West\SMAutoDemo\Repository\Demo
     */
    protected function getDemoRepo()
    {
        return $this->repository('West\SMAutoDemo:Demo');
    }
}