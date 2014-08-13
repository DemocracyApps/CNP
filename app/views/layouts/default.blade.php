<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>CNP</title>
	<style>
		@import url(//fonts.googleapis.com/css?family=Lato:700);

		body {
			margin:10px;
			font-family:'Lato', sans-serif;
			text-align:left;
			color: #555;
		}
        span.error {
          color: #f00;
        }

		.welcome {
			width: 300px;
			height: 200px;
			position: absolute;
			left: 50%;
			top: 50%;
			margin-left: -150px;
			margin-top: -100px;
		}

		a, a:visited {
			text-decoration:none;
		}

		h1 {
			font-size: 32px;
			margin: 16px 0 0 0;
		}
	</style>
	@yield('header')
</head>
<body>

  @yield('content')

</body>
</html>
