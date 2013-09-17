<?php

class Helper_Youtube
{
    public static function getYoutube($config = null)
    {
        if (null === $config) {
            $config = Kohana::$config->load('youtube');
        }
        
        $httpClient = Zend_Gdata_ClientLogin::getHttpClient(
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
        
        $yt = new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);
        $yt->setMajorProtocolVersion(2);
        
        return $yt;
    }
    
    /**
     * Uploaduje podane video jako prywatne na kanał YT autoryzowanego usera.
     * 
     * Dane autoryzacyjne zawarte są w konfiguracji youtube.php
     * 
     * @param string $filepath pełna ścieżka do pliku filmu
     * @return bool|array false w przypadku bledu, video_edit_url i video_id do filmu w przypadku powodzenia
     */
    public static function upload($filepath)
    {
        $config = Kohana::$config->load('youtube');
        
        $yt = self::getYoutube($config);
        
        // create a new VideoEntry object
        $myVideoEntry = new Zend_Gdata_YouTube_VideoEntry();
        
        // create a new Zend_Gdata_App_MediaFileSource object
        $filesource = $yt->newMediaFileSource($filepath);
        $filesource->setContentType('video/quicktime');
        // set slug header
        $tmp = explode('/', $filepath);
        $filesource->setSlug($tmp[count($tmp)-1]);
        
        // add the filesource to the video entry
        $myVideoEntry->setMediaSource($filesource);
        
        $myVideoEntry->setVideoTitle('Video');
        $myVideoEntry->setVideoDescription('My video');
        // The category must be a valid YouTube category!
        $myVideoEntry->setVideoCategory('Entertainment');
        $myVideoEntry->setVideoPrivate();
        
        // Set keywords. Please note that this must be a comma-separated string
        // and that individual keywords cannot contain whitespace
        $myVideoEntry->SetVideoTags('entertainment');
        
        // upload URI for the currently authenticated user
        $uploadUrl = $config['upload_url'];
        
        // try to upload the video, catching a Zend_Gdata_App_HttpException, 
        // if available, or just a regular Zend_Gdata_App_Exception otherwise
        $newEntry = $yt->insertEntry($myVideoEntry, $uploadUrl, 'Zend_Gdata_YouTube_VideoEntry');
        $newEntry->setMajorProtocolVersion(2);
        
        return array(
            'video_id' => $newEntry->getVideoId(),
            'video_edit_url' => $newEntry->getEditLink()->getHref()
        );
    }
    
    public static function markPublic($videoId, $videoEditUrl)
    {
        $config = Kohana::$config->load('youtube');
        
        $yt = self::getYoutube($config);

		$videoEntry = $yt->getVideoEntry($videoId);
		$videoEntry->setVideoPublic();
		$yt->updateEntry($videoEntry, $videoEditUrl);
    }
    
    public static function markPrivate($videoId, $videoEditUrl)
    {
    	$config = Kohana::$config->load('youtube');
    
    	$yt = self::getYoutube($config);

		$videoEntry = $yt->getVideoEntry($videoId);
		$videoEntry->setVideoPrivate();
		$yt->updateEntry($videoEntry, $videoEditUrl);
    }
}
