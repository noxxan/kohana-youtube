<?php

class LemonSky_YouTube_Playlist
{
    protected $_yt;
    
    public function __construct()
    {
        $this->_yt = LemonSky_YouTube_Client::getAuthorizedYTInstance();
    }
    
    public function add($title, $description)
    {
        $newPlaylist = $this->_yt->newPlaylistListEntry();
        $newPlaylist->title = $this->_yt->newTitle()->setText($title);
        $newPlaylist->summary = $this->_yt->newDescription()->setText($description);
    
        $postLocation = 'http://gdata.youtube.com/feeds/api/users/default/playlists';
        try {
            $playlist = $this->_yt->insertEntry($newPlaylist, $postLocation);
            $playlistId = explode(":", $playlist->id->text);
            $playlistId = end($playlistId);
            return $playlistId;
        } catch (Zend_Gdata_App_Exception $e) {
            Kohana::$log->add(Log::ERROR, $e->getMessage());
        }
    }
    
    public function addVideo($playlistId, $videoId)
    {
        $postUrl = $this->_getPlaylistVideoFeedUrl($playlistId);
        $videoEntryToAdd = $this->_yt->getVideoEntry($videoId);
        $newPlaylistListEntry = $this->_yt->newPlaylistListEntry($videoEntryToAdd->getDOM());

        try {
            $this->_yt->insertEntry($newPlaylistListEntry, $postUrl);
            return true;
        } catch (Zend_App_Exception $e) {
            Kohana::$log->add(Log::ERROR, $e->getMessage());
            return false;
        }
    }
    
    protected function _getPlaylistVideoFeedUrl($playlistId)
    {
        return 'http://gdata.youtube.com/feeds/api/playlists/' . $playlistId;
    }
}