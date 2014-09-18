<!doctype html>
<html lang="en">
<head>
  @include('includes.head')

  @yield('header')
  

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

        /*
         * Autocompletion
         */

        /* highlight results */
        .ui-autocomplete span.hl_results {
            background-color: #ffff66;
        }
         
        /* scroll results */
        .ui-autocomplete {
            max-height: 250px;
            overflow-y: auto;
            /* prevent horizontal scrollbar */
            overflow-x: visible;
            /* add padding for vertical scrollbar */
            padding-right: 5px;
        }

        .ui-autocomplete li {
            font-size: 16px;
        }

        /* IE 6 doesn't support max-height
        * we use height instead, but this forces the menu to always be this tall
        */
        * html .ui-autocomplete {
            height: 250px;
        }

        .ui-menu .ui-menu-item {
            height:18px;
            font-size:14px;
            background:white;
        }
  </style>

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
    <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.min.js"></script>
    @yield('scripts')
</body>
</html>
