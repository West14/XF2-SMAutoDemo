<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 06.06.2020
 * Time: 21:18
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\FsAdapter;


abstract class AbstractAdapter
{
    /** @var string */
    protected $adapterId;

    /** @var array */
    protected $options;

    abstract public function getAdapter(): \League\Flysystem\Adapter\AbstractAdapter;

    public function __construct(string $adapterId, array $options)
    {
        $this->adapterId = $adapterId;
        $this->options = $options;
    }

    /**
     * Performs validations on options
     * Returns an array with errors
     *
     * @return array
     */
    public function validateOptions(): array
    {
        return [];
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOptionsTemplate(): string
    {
        return 'admin:wsmad_fsadapater_options.' . $this->adapterId;
    }

    public function renderOptions(): string
    {
        $template = $this->getOptionsTemplate();
        if (!$template)
            return '';

        return \XF::app()->templater()->renderTemplate($template, [
            'options' => $this->options
        ]);
    }
}