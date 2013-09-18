<?php

class LemonSky_YouTube_VideoEntry
{
    protected $_videoEntry;
    protected $_filePath;
    
    public function __construct($filepath, $title, $description, $category, $tags)
    {
        $this->_filePath = $filepath;
        
        $this->_videoEntry = new Zend_Gdata_YouTube_VideoEntry();
        $this->_videoEntry->setVideoPrivate();
        $this->_videoEntry->setVideoTitle($title);
        $this->_videoEntry->setVideoDescription($description);
        $this->_videoEntry->setVideoCategory($category);
        $this->_videoEntry->SetVideoTags($tags);
    }
    
    public function setSource($filesource)
    {
        $this->_videoEntry->setMediaSource($filesource);
    }
    
    public function getFilePath()
    {
        return $this->_filePath;
    }
    
    public function getVideo()
    {
        return $this->_videoEntry;
    }
}