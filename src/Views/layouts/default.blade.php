<!doctype html>
<html>
  <head>
      <meta charset="utf-8">
      <title>Community Narratives Platform</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
      <link rel="stylesheet" type="text/css" href="/css/local.css">
     <!-- STYLES TO MAKE IT NICER LOOKING -->
      <style>
        h1, h2, h3, h4, h5, h6{
          font-family: 'Titillium Web' !important;
        }
        h1, h2, h3{
          font-weight: 300;
        }
        a{
          color: #EB7722;
        }
      </style>
      <!-- END STYLES TO MAKE IT NICER LOOKING -->
  </head>

  <body>
    <div class="cnp-header">
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
        <!-- Navigation Panel -->
        <div class="col-md-3 app-navigation">
          <?php
            $menu = \DemocracyApps\CNP\Views\menus\MenuGenerator::generateMenu(0);
          ?>
          <h3>{{$menu['title']}}</h3>
          <!-- Top of the menu -->
          <div class="panel-group" id="accordion">
            <?php
              $first = true;
            ?>

            @foreach ($menu['items'] as $section)
              <div class="panel panel-default"> <!-- Begin Section -->
                <div class="panel-heading"> <!-- Begin Section Heading -->
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#{{$section['tag']}}">
                      {{$section['name']}}
                    </a>
                  </h4>
                </div> <!-- End Section Heading -->
                <?php
                  $sectionClass = "panel-collapse collapse";
                  if ($first) { $sectionClass .= " in"; $first = false; }
                ?>
                <div id="{{$section['tag']}}" class="{{$sectionClass}}">
                  <div class="panel-body">
                    <ul class="nav cnp-sidenav">
                      @foreach ($section['items'] as $item)
                        <li><a href="{{$item['url']}}">{{$item['name']}}</a></li>
                      @endforeach
                    </ul> 
                  </div>
                </div>
              </div> <!-- End Section -->
            @endforeach

          </div> <!-- accordion end -->
          <!-- Bottom of the menu -->
        </div> <!-- End of the app-navigation DIV -->

        <div class="col-md-9 app-content">
            @yield('content')
        </div>
        <footer class="row">
          <div id="copyright">Copyright © 2014 DemocracyApps</div>
        </footer>
      </div>
    </div>
  </div>
    <script src="/js/jquery-2.1.1.min.js"></script>
    <script src="/js/jquery-ui-1.11.1/jquery-ui.min.js"></script>
  <script src="../js/bootstrap.min.js"></script>

    <script type="text/javascript">
      $('.mode-toggle').on('click', function() {
        var mode = $(this).find('input').attr('id');
        window.location.href = "/?mode="+mode;
      } );

    </script>

    @yield('scripts')
  </body>

</html>

