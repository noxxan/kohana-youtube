<?php

class LemonSky_YouTube_Upload
{
    protected $_yt;
    
    public function __construct($config = null)
    {
        $this->_yt = LemonSky_YouTube_Client::getAuthorizedYTInstance();
        $this->_config = Kohana::$config->load('youtube');
    }
    
    /**
     * Uploads video as private on current logged YT user channel.
     *
     * Configuration data are availabe in youtube.php config file.
     *
     * @param string $filepath full path to file
     * @return bool|array false in case of error, video_edit_url i video_id of video on success
     */
    public function uploadFileFromLocalStorage($videoEntry)
    {
        $slug = explode('/', $videoEntry->getFilePath());
        $slug = end($slug);
        
        $filesource = $this->_yt->newMediaFileSource($videoEntry->getFilePath());
        $filesource->setContentType('video/quicktime');
        $filesource->setSlug($slug);
        
        $videoEntry->setSource($filesource);
        
        $newEntry = $this->_yt->insertEntry($videoEntry->getVideo(), $this->_config['upload_url'], 'Zend_Gdata_YouTube_VideoEntry');
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