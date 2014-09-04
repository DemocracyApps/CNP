<!doctype html>
<html lang="en">
<head>
  @include('includes.head')
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
        }
        table th, table td {
          border:1px solid black; padding:10px;
        }
        table#short-table th, table#short-table td {
          border:1px solid black; padding:10px;
        }
        table#long-table th, table#long-table td {
          border:1px solid black; padding:3px;
        }
        table#simple-table, table#simple-table th, table#simple-table td {
          border:0px solid white; padding:3px;
        }
  </style>
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
</body>
</html>
