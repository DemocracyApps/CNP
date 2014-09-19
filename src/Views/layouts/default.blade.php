<!doctype html>
<html>
  <head>
      <meta charset="utf-8">
      <title>Community Narratives Platform</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
      <link rel="stylesheet" type="text/css" href="/css/local.css">
  </head>
<body>
  <div class="bs-header">
    <div class="container">
      <div class="row">
        <div class="col-md-2 hdr-logo">
          <img src="/img/DemocracyApps_logo-01_RGB.jpg" height="123" width="96" alt="DemocracyApps Logo"/>
        </div>
        <div class="col-md-7 hdr-main">
          <h1>Community Narratives Platform</h1>
          <p>The power of story</p>
        </div>
        <div class="col-md-3 hdr-right">
          <div class="btn-group" data-toggle="buttons">
            <?php
              $mode = \Session::get('cnpMode');
              $appClass = "btn btn-default mode-toggle";
              $demoClass = "btn btn-default mode-toggle";
              $appChecked = "";
              $demoChecked = "";
              if ($mode == 'app') {
                $appClass .= " active";
                $appChecked = 'checked';
              }
              else {
                $demoClass .= " active";
                $demoChecked = 'checked';
              }
              \Log::info("Operating mode is " . $mode);
            ?>
            <label class="{{$appClass}}">
              <input type="radio" name='mode' id='app' {{$appChecked}}> Application
            </label>
            <label class="{{$demoClass}}">
              <input type="radio" name='mode' id='demo' {{$demoChecked}}> Demo
            </label>

          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container app-container">
    <div class="row">
      <div class="col-md-3 app-navigation">
        <?php
          $menu = \DemocracyApps\CNP\Views\menus\MenuGenerator::generateMenu(0);
        ?>
        <h3>{{$menu['title']}}</h3>

        <ul class="nav bs-sidenav">
          @foreach ($menu['items'] as $majorItem)
            <a href="{{$majorItem['url']}}">{{$majorItem['name']}}</a>
            @foreach ($majorItem['items'] as $minorItem)
              <li><a href="{{$minorItem['url']}}">{{$minorItem['name']}}</a></li>
            @endforeach
          @endforeach
        </ul>
      </div>

      <div class="col-md-9 app-content">
          @yield('content')
      </div>
      <footer class="row">
        <div id="copyright">Copyright © 2014 DemocracyApps</div>
      </footer>
    </div>
  </div>
</div>
  <script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
  <script src="/js/bootstrap.min.js"></script>
  <script type="text/javascript">
    $('.mode-toggle').on('click', function() {
      var mode = $(this).find('input').attr('id');
      window.location.href = "/?mode="+mode;
    } );

  </script>
  @yield('scripts')
</body>

</html>

