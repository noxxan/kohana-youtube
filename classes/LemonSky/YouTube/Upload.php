<?php

class LemonSky_YouTube_Upload
{
    protected $_yt;
    
    public function __construct($config = null)
    {
        if (null === $config) {
            $this->_config = Kohana::$config->load('youtube');
        } else {
            $this->_config = $config;
        }
        
        $adapter = new \Zend\Http\Client\Adapter\Curl();
        $adapter = $adapter->setCurlOption(CURLOPT_SSL_VERIFYHOST,false);
        $adapter = $adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER,false);
        $httpClient = new \ZendGData\HttpClient();
        $httpClient->setAdapter($adapter);
        
        $httpClient = \ZendGData\ClientLogin::getHttpClient(
			$this->_config['username'],
            $this->_config['password'],
            'youtube',
            $httpClient,
            $this->_config['source'],
            null,
            null,
            $this->_config['auth_url']
        );
    
        $developerKey = $this->_config['dev_key'];
        $applicationId = $this->_config['source'];
        $clientId = $this->_config['source'];
    
        $this->_yt = new \ZendGData\YouTube($httpClient, $applicationId, $clientId, $developerKey);
        $this->_yt->setMajorProtocolVersion(2);
        $this->_yt->getHttpClient()->setOptions(array('sslverifypeer' => false));
    }
    
    /**
     * Uploads video as private on current logged YT user channel.
     *
     * Configuration data are availabe in youtube.php config file.
     *
     * @param string $filepath full path to file
     * @return bool|array false in case of error, video_edit_url i video_id of video on success
     */
    public function uploadFileFromLocalStorage($filepath)
    {
        $slug = explode('/', $filepath);
        $slug = end($slug);
        
        $filesource = $this->_yt->newMediaFileSource($filepath);
        $filesource->setContentType('video/quicktime');
        $filesource->setSlug($slug);
        
        $myVideoEntry = new \ZendGData\YouTube\VideoEntry();
        $myVideoEntry->setMediaSource($filesource);
        $myVideoEntry->setVideoTitle('Video');
        $myVideoEntry->setVideoDescription('My video');
        $myVideoEntry->setVideoCategory('Entertainment');
        $myVideoEntry->setVideoPrivate();
        $myVideoEntry->SetVideoTags('entertainment');

        $newEntry = $this->_yt->insertEntry($myVideoEntry, $this->_config['upload_url'], 'ZendGData\YouTube\VideoEntry');
        $newEntry->setMajorProtocolVersion(2);
    
        return array(
            'video_id' => $newEntry->getVideoId(),
            'video_edit_url' => $newEntry->getEditLink()->getHref()
        );
    }
    
    public function markPublic($videoId, $videoEditUrl)
    {
        $videoEntry = $this->_yt->getVideoEntry($videoId);
        $videoEntry->setVideoPublic();
        $this->_yt->updateEntry($videoEntry, $videoEditUrl);
    }
    
    public function markPrivate($videoId, $videoEditUrl)
    {
        $videoEntry = $this->_yt->getVideoEntry($videoId);
        $videoEntry->setVideoPrivate();
        $this->_yt->updateEntry($videoEntry, $videoEditUrl);
    }
}