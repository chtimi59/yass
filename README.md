# yass
Yet-Another-SlideShow

# Features
- no installation needed (except the server), client should only display a webpage
- supports multiples display client
- full html5 slides (hence support image, videos, HTML5 animation, etc...)
- admin interface:

# Motivation
- First I want to make it very simple and free to use and share !
- I want to have a versatile slideslow, so here slide are actually html page

# Usage

Page which needs to load by Display-Client: http://yourserver/display

Admin page: http://yourserver/admin

# Principe

a database contains 2 tables

one with a list of assets (i.e slide) defined by:
- an id, name
- an optional start and stop date for squeduling
- an optional duration
- a path to where the actual slide's HTML5 page is

a second one contains one row by display (automatically added when a display made a connection)
the field are:
- ip [Primary key]
- date, i.e. a kind of heartbit the see active displays]
- current asset id showed by the display.

From there it's quite easy,

when clients (display) load http://yourserver/display they are actually redirected on the next asset HTML5 page:
```
header('Location: '.$assetPath);     
```

Then before HTML5 page little wrapper, include the following header:
```
header("Refresh: timeInSec; url='http://yourserver/display', true, 303)
```

This implied, whatever occurs in the HTML page, client will be redirected to http://yourserver/display after **timeInSec** second.

Note: for video for instance, no duration is need so a *manual* js redirection is done when the video is finished
```
window.location.href = 'http://yourserver/display';
```

And that's it, that's all!

**Playing with HTTP redirection do the trick!**

# Find new asset

```
define('STATUS_BACKSTAGE', 0);
define('STATUS_PENDING',   1);
define('STATUS_LIVE',      2);
define('STATUS_FINISHED',  3);
```


# Sources

Arborescence tree:
```
\admin - admin page
\display - display page (should be load by display's client)
```


# server setup

load : http://yourserver/setup and follow this instruction

# client setup

None! Simply load http://yourserver/display and the show should run.
