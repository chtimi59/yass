# yass
Yet-Another-SlideShow

# Features
- no installation needed (except the server), clients should only display a webpage
- supports multiples display's clients
- full html5 slides (hence support images, videos, HTML5 animations, etc...)
- admin interface:

# Motivation
- First I want to make it very simple and free to use and share!
- I want to have a versatile slideslow, so here slides are actually html pages

# Usage

Webpage which needs to be loaded by Display-Client is http://yourserver/display

Admin page is here http://yourserver/admin

# Principe

First a database contains 2 tables as described below:

One with a list of assets (i.e slides) defined by:
- an id, name
- an optional start and stop date for scheduling
- an optional duration
- a path to where the actual slide's HTML5 page is (page.html)
- a status (backstage, pending, live or finished)

The other one contains one row per Display-Client (automatically added when a display made a connection)
the fields  are:
- ip [Primary key]
- date, i.e. a kind of heartbit the see active displays]
- current asset id showed by the display.

From there it's quite easy,

when clients (display) load http://yourserver/display they are actually redirected on the next asset HTML5 page:
```
header('Location: '.$assetPath);     
```

HTML5 pages are actually wrapped by the following HTTP header:
```
header("Refresh: timeInSec; url='http://yourserver/display', true, 303)
```

That implied, whatever occurs in the HTML page, client will be redirected (back) to http://yourserver/display after **timeInSec** seconds.

And that's it, that's all!

**Playing with HTTP redirection do the trick!**

Note: for video, no duration (set to 0) is needed, and a *manual* js redirection is done by the HTML page itselft when the video is finished
```
window.location.href = 'http://yourserver/display';
```



# Squeduling


```
define('STATUS_BACKSTAGE', 0);
define('STATUS_PENDING',   1);
define('STATUS_LIVE',      2);
define('STATUS_FINISHED',  3);
```


# Sources
```
git submodule update --init
```

Arborescence tree:
```
\admin - admin page
\display - display page (should be load by display's client)
```


# server setup

load : http://yourserver/setup and follow this instruction

# client setup

None! Simply load http://yourserver/display and the show should run.
