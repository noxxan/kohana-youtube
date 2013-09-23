<?php

class LemonSky_YouTube_Client
{
    public static function getAuthorizedYTInstance()
    {
        $config = Kohana::$config->load('youtube');
        
        $httpClient = Zend_Gdata_ClientLogin::getHttpClient(
            $config['username'],
            $config['password'],
            'youtube',
            null,
            $config['source'],
            null,
            null,
            $config['auth_url']
        );
        
        $developerKey = $config['dev_key'];
        $applicationId = $config['source'];
        $clientId = $config['source'];
        
        $yt = new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);
        $yt->setMajorProtocolVersion(2);
        
        return $yt;
    }
}