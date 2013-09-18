kohana-youtube
==============

Kohana library which wraps around most usefull YouTube API functions.

Works with Kohana 3.3 for sure.

Uploading video to specified in configuration file youtube account
------------------------------------------------------------------

Example:

```
$videoEntry = new LemonSky_YouTube_VideoEntry('/path/to/your/film.mp4', 'title', 'description', 'Entertainment', 'entertainment');
$out = new LemonSky_YouTube_Upload()->uploadFileFromLocalStorage($videoEntry);
```
  