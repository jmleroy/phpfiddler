### About PHP Fiddler
PHP Fiddler is a small tool and library to create fiddles and have it show side by side the tested code and its result.
This work started as a QAD project, so please be comprehensive.

#### How to use it?
**phpfiddler** requires PHP 5.6 and Composer to work properly.

`web/` is the public directory for your web server.
A symbolic link `web/vendor` links to the vendor directory, until a better solution is implemented. 
A configuration snippet `vhost.conf` for Apache is also provided.

Two major routes for the fiddler:

- examples/*fiddle_name* : calls `examples/fiddle_name.php`.
- fiddles/*fiddle_name* : calls `fiddles/fiddle_name.php`. This directory does not version php scripts, so you can put whatever you want there and not risk to lose them by a `git pull`.

