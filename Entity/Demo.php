<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 30.03.2020
 * Time: 0:51
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\Entity;


use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string demo_id
 * @property int server_id
 * @property string map
 * @property int demo_started
 * @property int demo_ended
 * @property int tick_count
 * @property string download_state
 * @property int downloaded_at
 *
 * RELATIONS
 * @property \West\SMAutoDemo\Entity\Server Server
 * @property \XF\Mvc\Entity\AbstractCollection|\West\SMAutoDemo\Entity\DemoPlayer[] Players
 */
class Demo extends Entity
{
    public function getAbstractedPath()
    {
        return "data://wsmad-demos/{$this->server_id}/{$this->demo_id}";
    }

    public function getAbstractedZipPath()
    {
        return $this->getAbstractedPath() . '.zip';
    }
    
    public function getAbstractedJsonPath()
    {
        return $this->getAbstractedPath() . '.json';
    }

    public function isDownloaded()
    {
        return $this->download_state == 'downloaded';
    }

    protected function getDeletableFiles()
    {
        return [$this->getAbstractedZipPath(), $this->getAbstractedJsonPath()];
    }

    protected function _preDelete()
    {
        $demoHoldSettings = \XF::options()->wsmadDemoHold;

        if ($demoHoldSettings['enabled'])
        {
            $customFieldValueList = $this->finder('XF:ThreadFieldValue')
                ->where([
                    ['field_id', '=', $demoHoldSettings['customFieldId']],
                    ['field_value', '=', $this->demo_id]
                ])
                ->with('Thread')
                ->fetch();

            foreach ($customFieldValueList as $item)
            {
                /** @var \XF\Entity\Thread $thread */
                $thread = $item->Thread;
                if ($thread->discussion_open)
                {
                    $this->error(\XF::phrase('wsmad_there_are_open_discussion_related_to_this_demo', [
                        'threadTitle' => $thread->title,
                        'threadUrl' => $this->app()->router('public')->buildLink('threads', $thread)
                    ]));
                }
            }
        }
    }

    public function _postDelete()
    {
        if ($this->isDownloaded())
        {
            $fs = $this->app()->fs();
            foreach ($this->getDeletableFiles() as $path)
            {
                try {
                    $fs->delete($path);
                } catch (\League\Flysystem\FileNotFoundException $e)
                {
                    \XF::logException($e);
                    continue;
                }
            }
        }
    }

    public static function getStructure(Structure $structure)
    {
        $structure->table = 'xf_wsmad_demo';
        $structure->shortName = 'West\SMAutoDemo:Demo';
        $structure->primaryKey = 'demo_id';
        $structure->columns = [
            'demo_id' => ['type' => self::STR, 'maxLength' => 36, 'required' => true],
            'server_id' => ['type' => self::UINT, 'required' => true],
            'map' => ['type' => self::STR, 'default' => ''],
            'demo_started' => ['type' => self::UINT, 'default' => 0],
            'demo_ended' => ['type' => self::UINT, 'default' => 0],
            'tick_count' => ['type' => self::UINT, 'default' => 0],
            'download_state' => [
                'type' => self::STR, 'default' => 'not_downloaded',
                'allowedValues' => ['downloaded', 'enqueued', 'not_downloaded']
            ],
            'downloaded_at' => ['type' => self::UINT, 'default' => 0]
        ];

        $structure->relations = [
            'Server' => [
                'entity' => 'West\SMAutoDemo:Server',
                'type' => self::TO_ONE,
                'conditions' => 'server_id'
            ],
            'Players' => [
                'entity' => 'West\SMAutoDemo:DemoPlayer',
                'type' => self::TO_MANY,
                'conditions' => 'demo_id',
                'cascadeDelete' => true
            ],
            'Events' => [
                'entity' => 'West\SMAutoDemo:DemoEvent',
                'type' => self::TO_MANY,
                'conditions' => 'demo_id',
                'cascadeDelete' => true
            ]
        ];

        return $structure;
    }
}