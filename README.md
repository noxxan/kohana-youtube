kohana-youtube
==============

Kohana library which wraps around most usefull YouTube API functions.

Works with Kohana 3.3 for sure.

Uploading video to specified in configuration file youtube account
------------------------------------------------------------------

Example:

```
$yt = new LemonSky_YouTube_Upload();
$out = $yt->uploadFileFromLocalStorage('/path/to/your/film.mp4');
```
  