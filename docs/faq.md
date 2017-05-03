# Fail to update

## 'php' command not found
```
'php' is not recognized as an internal or external command,
operable program or batch file.
```
Solution: check your PATH variable

*update* script actually use php in cli (command line) mode.

## 'Could not connect'
You get the following message, which actually means that php cli couldn't connect to database.
```
Could not connect: No connection could be made because the target machine actively refused it.
```
Solution: check your mysql port in *php.ini*.
```
; Default port number for mysql_connect().  If unset, mysql_connect() will use
; the $MYSQL_TCP_PORT or the mysql-tcp entry in /etc/services or the
; compile-time value defined MYSQL_PORT (in that order).  Win32 will only look
; at MYSQL_PORT.
; http://php.net/mysql.default-port
mysql.default_port = 3306
```
For instance on *wampserver* (for Windows) changes the mysql default port and apply it to a "C:\wamp64\bin\php\php5.6.16\phpForApache.ini" file. So make sure that "C:\wamp64\bin\php\php5.6.16\php.ini" reflect this change.


