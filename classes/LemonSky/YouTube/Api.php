<?php

class LemonSky_YouTube_Api
{
    protected $_yt;
    protected $_debug;
    
    public function __construct()
    {
        $yt = new Zend_Gdata_YouTube();
        $yt->setMajorProtocolVersion(2);

        $this->_yt = $yt;
        $this->_developerKey = Kohana::$config->load('youtube.dev_key');
    }
    
    public function setDebug($debug)
    {
        $this->_debug = $debug;
    }
    
    public function getDebug()
    {
        return $this->_debug;
    }
    
    public function collectVideos($channel, $maxPages = 41)
    {
        // user uploads
        $location = 'https://gdata.youtube.com/feeds/api/users/' . $channel . '/uploads';
        
        // fetch all pages
        $startIndex = 1;
        $youtubePerPage = 25;
        
        $page = 0;
        $videos = array();
        
        while ($page < $maxPages) {
            $url = $location . '?start-index=' . $startIndex . '&key=' . $this->_developerKey;
            $uploads = $this->_yt->getUserUploads(null, $url);
            
            if ($this->getDebug()) {
                echo sprintf("COLLECT VIDEOS, URL: %s, VIDEOS NB: %d\n", $url, count($uploads));
            }
            
            if (count($uploads) == 0) {
                break;
            }
        
            foreach ($uploads as $video) {
                if ($video) {
                    $videoId = end(explode(":", $video->id->text));
                    
                    if (mb_strpos($videoId, '/') !== false) {
                        $videoId = end(explode("/", $video->id->text));
                    }
                    
                    $comments = !empty($video->comments->feedLink->countHint) ? $video->comments->feedLink->countHint : 0;
                    
                    $videos[] = array('id' => $videoId, 'comments' => $comments);
                }
            }
        
            $startIndex += $youtubePerPage;
            $page++;
        }
    }
    
    public function collectCommentsForAllVideos($channel)
    {
        $videos = $this->collectVideos($channel);
        
        $comments = array();
        
        foreach ($videos as $video) {
            try {
                $comments[$video['id']] = $this->collectCommentsForVideo($channel, $video['id']);
            } catch (Exception $e) {
                echo 'error ' . $e->getMessage();
            }
        }
        
        return $comments;
    }
    
    public function collectCommentsForVideo($channel, $videoId, $maxPages = 41)
    {
        $startIndex = 1;
        $youtubePerPage = 25;
        $page = 0;
        
        $comments = array();
        
        while ($page < $maxPages) {
            $url = 'https://gdata.youtube.com/feeds/api/videos/' . $videoId . '/comments?start-index=' . $startIndex . '&key=' . $this->_developerKey;
            $commentFeed = $this->_yt->getVideoCommentFeed(null, $url);
            
            if ($this->getDebug()) {
                echo sprintf("COLLECT COMMENTS, VIDEO: %s, COMMENTS NB: %d, URL: %s\n", $videoId, count($commentFeed), $url);
            }
            
            if (count($commentFeed) == 0) {
                break;
            }
            
            foreach ($commentFeed as $commentEntry) {
                $entry = array(
                    'youtube_id' => $commentEntry->id->text,
                    'comment' => $commentEntry->title->text,
                    'comment_full' => $commentEntry->content->text,
                    'author' => end(explode("/", $commentEntry->author[0]->uri->text)),
                    'youtube_date' => date('Y-m-d H:i:s', strtotime($commentEntry->updated->text)),
                    'created_at' => date('Y-m-d H:i:s')
                );
                
                $comments[] = $entry;
            }
        
            $startIndex += $youtubePerPage;
            $page++;
        }
        
        return $comments;
    }
    
    public function collectVideosFromPlaylist($playlist, $maxPages = 41)
    {
        // user uploads
        $location = 'https://gdata.youtube.com/feeds/api/playlists/' . $playlist;
    
        // fetch all pages
        $startIndex = 1;
        $youtubePerPage = 25;
    
        $page = 0;
        $videos = array();
    
        while ($page < $maxPages) {
            $url = $location . '?start-index=' . $startIndex . '&key=' . $this->_developerKey;
            $uploads = $this->_yt->getPlaylistVideoFeed($url);
    
            if (count($uploads) == 0) {
                break;
            }

            foreach ($uploads as $video) {
                if ($video) {
                    $v = array(
                        'id' => $video->mediaGroup->videoid->text,
                        'published' => date('Y-m-d H:i:s', strtotime($video->published->text)),
                        'title' => $video->title->text,
                        'author_name' => !empty($video->author[0]->name->text) ? $video->author[0]->name->text : null,
                        'author_uri' => !empty($video->author[0]->uri->text) ? $video->author[0]->uri->text : null,
                        'content' => isset($video->content->text) ? isset($video->content->text) : null,
                        'position_in_playlist' => isset($video->position->text) ? $video->position->text : null,
                    );
                    
                    $videos[] = $v;
                }
            }
    
            $startIndex += $youtubePerPage;
            $page++;
        }
        
        return $videos;
    }
}