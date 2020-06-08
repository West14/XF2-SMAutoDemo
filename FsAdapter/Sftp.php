<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 08.06.2020
 * Time: 1:04
 * Made with <3 by West from TechGate Studio
 */

namespace West\SMAutoDemo\FsAdapter;


class Sftp extends AbstractAdapter
{
    public function getAdapter(): \League\Flysystem\Adapter\AbstractAdapter
    {
        return new \League\Flysystem\Sftp\SftpAdapter($this->options);
    }

    public function validateOptions(): array
    {
        $errors = [];
        $options = $this->options;

        if (empty($options['host']) || empty($options['user']))
            $errors[] = \XF::phrase('wsmad_host_and_user_is_required');

        if (isset($options['password']) && isset($options['privateKey']))
            $errors[] = \XF::phrase('wsmad_password_cant_be_used_together_with_privatekey');

        if (empty($options['password']) && empty($options['privateKey']))
            $errors[] = \XF::phrase('wsmad_authentication_data_is_required');

        return $errors;
    }
}