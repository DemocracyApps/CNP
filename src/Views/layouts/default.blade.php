<!doctype html>
<html lang="en">
<head>
	<style>
        span.error {
          color: #f00;
        }
        .menu ul {
          list-style: none;
          margin-left: 0; 
          padding-left: 0;
        }
        li.menu {
          display: inline-block;
          font-weight: bold;
          width:100px;
        }
	</style>
  @include('includes.head')
	@yield('header')
</head>
<body>
	@include('layouts.partials.datarider')

	<div class="container">
		<header class="row">
			@include('includes.header')
		</header>

		<div id="main" class="container">

  			@yield('content')

  		</div>

  		@include('includes.lowermenu')

  		<footer class="row">
  			@include('includes.footer')
  		</footer>
  	</div>
    <script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="/main.js"></script>
</body>
</html>
