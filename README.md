# yass
Yet-Another-SlideShow, an open source digital signage solution

![alt tag](https://raw.githubusercontent.com/chtimi59/yass/master/docs/admin1.png)
![alt tag](https://raw.githubusercontent.com/chtimi59/yass/master/docs/admin2.png)


# Features
- no installation needed (except the server), clients should only display a webpage
- supports multiples display's clients
- full html5 slides (hence support images, videos, HTML5 animations, etc...)
- admin interface

# Motivation
- First I want to make it very simple and free to use and share!
- I want to have a versatile slideslow, so here slides are actually html pages

# Limitation
- There is no transition between slides
- IE11/Edge doesn't work see ['How does it works'](#how-does-it-works) for more details.

# Assets-type
- jpeg - for static image
- mp4  - for video
- zip  - for html5 page

...and potentially much more, as far as full HTML5 is supported !

see ['docs\html5-clock.zip'](https://raw.githubusercontent.com/chtimi59/yass/master/docs/html5-clock.zip) to get an idea of what is achievable easily.

Note: for html5 page, the main one should be nammed 'page.html'


# Usage
- Webpage which needs to be loaded by Display-Client is http://yourserver/display
- Admin page is here http://yourserver/admin

# How does it works

![alt tag](https://raw.githubusercontent.com/chtimi59/yass/master/docs/schema.png)

A database with 2 tables is defined:
- assets table
- displays table

1-**Assets table** defines a list of assets (or 'slides') as follow:
- an id
- an optionnal asset name, for convenience.
- an optional start and stop date for ['scheduling'](#scheduling)
- an optional duration
- a path to where the actual slide's HTML5 page is (page.html)
- a status (i.e. 'backstage', 'pending', 'live' or 'finished')

1- **Displays table** contains one row per Display (or client)
Entries in this table are automatically added when new connections occurs.

We actually use a cookie (with an expiration date of 10 Years) to dinstiguish, if this connection is new or not.

The fields  are:
- id, set by a cookie
- ip address, mainly for debug/status (note: this don't work if displays are behing a proxy)
- date, i.e. a kind of heartbit the see active displays
- current asset id showed by the display.

From there it's quite easy,

when clients/displays loads http://yourserver/display they are actually redirected on the next asset HTML5 page, by a pure HTTP/1.1 redirection as follow:
```
header('Location: '.$assetPath);     
```

from there HTML5 pages are actually wrapped by the following HTTP header:
```
header("Refresh: timeInSec; url='http://yourserver/display', true, 303)
```

That implied, whatever occurs in the HTML page, clients will be redirected-back to http://yourserver/display after **timeInSec** seconds.

And this close the loop to be able to get a sequential process of HTML's slide.

Actually is important to notice that 'Refresh' is a Proprietary/non-standard HTTP header. However in 2017 more of less all browsers (Firefox, Chrome) supports it (it's a Netscape Legacy).

some refences about HTTP redirection here:
- ['Wiki URL redirect'](https://en.wikipedia.org/wiki/URL_redirection#Refresh_Meta_tag_and_HTTP_refresh_header)
- ['Wiki Http Header'](https://en.wikipedia.org/wiki/List_of_HTTP_header_fields)


Note: for video, no duration (set to 0) is needed, and a *manual* js redirection is done by the HTML page itselft when the video is finished
```
window.location.href = 'http://yourserver/display';
```

# Scheduling

Nothing really complicate here, asset are read from the lastest inserted (throught the admin interface) to the oldest.

Only asset with 'LIVE' status are used.

```
define('STATUS_BACKSTAGE', 0);
define('STATUS_PENDING',   1);
define('STATUS_LIVE',      2);
define('STATUS_FINISHED',  3);
```

- BACKSTAGE means, that admin should validate it to make it live
- PENDING/FINISHED are related to asset startDate and endDate if defined

# Sources

This repo use submodule, so don't forget:

```
git submodule update --init
```

Arborescence tree:
```
\admin   - admin page
\display - display page (which should be load by display's client)
\setup   - used only once for the page setup

\docs    - various docs an sample
```

# Setup

## Prequists
- git
- php5 with mcrypt extension
- mysql

Note: you may also have to update your upload filesize setting

example in *php.ini*:
```
post_max_size = 100M
upload_max_filesize = 100M
```
example in *nginx.conf*:
```
client_max_body_size 100M;
```


## Installation

In your `www` folder
```
sudo git clone --recursive https://github.com/chtimi59/yass.git
```
then go to [http://yourserver/yass/setup/](http://yourserver/yass/setup/) and follows the instructions,
the process should end with the following prompt:

**Warning** The setup should ends with **" Congratulation! "**

At this point you should now have:

- [http://yourserver/yass/admin/](http://yourserver/yass/admin/) Admin Interface
- [http://yourserver/yass/](http://yourserver/yass/) page to be load by displays

## Yass update

on Unix
```
sudo ./update.sh
```
on Windows
```
update.bat
```


