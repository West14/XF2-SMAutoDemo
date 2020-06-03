<?php

namespace West\SMAutoDemo\XF\Entity;

class User extends XFCP_User
{
    /**
     * @return string
     * @throws \Kruzya\SteamIdConverter\Exception\InvalidSteamIdException
     */
    public function getAccountId()
    {
        $steamId64 = $this->ConnectedAccounts['steam']->provider_key;
        return $steamId64 ? (new \Kruzya\SteamIdConverter\SteamID($steamId64))->accountId() : null;
	}
}