<?php

class LemonSky_YouTube_Upload
{
    protected $_yt;
    
    public function __construct()
    {
        if (null === $config) {
            $config = Kohana::$config->load('youtube');
        }
    
        $httpClient = \ZendGdata\ClientLogin::getHttpClient(
			$username = $config['username'],
            $password = $config['password'],
            $service = 'youtube',
            $client = null,
            $source = $config['source'],
            $loginToken = null,
            $loginCaptcha = null,
            $config['auth_url']
        );
    
        $developerKey = $config['dev_key'];
        $applicationId = $config['source'];
        $clientId = $config['source'];
    
        $this->_yt = new \ZendGdata\YouTube($httpClient, $applicationId, $clientId, $developerKey);
        $this->_yt->setMajorProtocolVersion(2);
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
        $filesource = $this->_yt->newMediaFileSource($filepath);
        $filesource->setContentType('video/quicktime');
        $filesource->setSlug(end(explode('/', $filepath)));
        
        $myVideoEntry = new \ZendGdata\YouTube\VideoEntry();
        $myVideoEntry->setMediaSource($filesource);
        $myVideoEntry->setVideoTitle('Video');
        $myVideoEntry->setVideoDescription('My video');
        $myVideoEntry->setVideoCategory('Entertainment');
        $myVideoEntry->setVideoPrivate();
        $myVideoEntry->SetVideoTags('entertainment');

        $newEntry = $this->_yt->insertEntry($myVideoEntry, $config['upload_url'], 'Zend_Gdata_YouTube_VideoEntry');
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