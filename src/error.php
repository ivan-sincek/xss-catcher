<?php
$message = '';
switch (http_response_code()) {
    case 400:
        $message = '400 Bad Request';
        break;
    case 401:
        $message = '401 Unauthorized';
        break;
    case 403:
        $message = '403 Forbidden';
        break;
    case 404:
        $message = '404 Not Found';
        break;
    case 500:
        $message = '500 Internal Server Error';
        break;
    default:
        $message = 'Something went wrong!';
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title><?php echo $message; ?> | XSS Catcher</title>
		<meta name="description" content="Something went wrong!">
		<meta name="keywords" content="HTML, CSS, PHP, XSS, catcher">
		<meta name="author" content="Ivan Šincek">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<style>
			html {
				height: 100%;
			}
			body {
				background-color: #262626;
				display: flex;
				flex-direction: column;
				margin: 0;
				height: inherit;
				color: #F8F8F8;
				font-family: Arial, Helvetica, sans-serif;
				font-size: 1em;
				font-weight: 400;
				text-align: left;
			}
			.error-page {
				display: flex;
				flex-direction: column;
				align-items: center;
				flex: 1 0 auto;
				padding: 2.25em 1em;
			}
			.error-page header {
				text-align: center;
			}
			.error-page header .title {
				margin: 0;
				font-weight: 400;
			}
			@media screen and (max-width: 320px) {
				.error-page header .title {
					font-size: 1.5em;
				}
			}
		</style>
	</head>
	<body>
		<div class="error-page">
			<header>
				<h1 class="title"><?php echo $message; ?></h1>
			</header>
		</div>
	</body>
</html>
