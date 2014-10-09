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
        p, li, th, td, input, a {
          font-size: 110%;
        }
        div.presentation {
            font-size: 110%;
        }
      </style>
      <!-- END STYLES TO MAKE IT NICER LOOKING -->
  </head>

  <body>
    @include('layouts.partials.datarider')
    <div class="cnp-header">
      <div class="container">
        <div class="row">
          <div class="col-md-1 hdr-logo">
            <img src="/img/DemocracyApps_logo-01_RGB.jpg" height="123" width="96" alt="DemocracyApps Logo"/>
          </div>
          <div class="col-md-8 hdr-main">
            <h1>Community Narratives Platform</h1>
          </div>
          <div class="col-md-3 hdr-right">
            <!--
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
          -->
          </div>
        </div>
      </div>
    </div>

    <div class="container app-container">
        @yield('content')
    </div>
    <br>
      <footer class="row">
        <div class="col-md-1">
        </div>
        <div class="col-md-5">
          <div id="copyright">Copyright © 2014 DemocracyApps</div>
        </div>
        <div class="col-md-5" style="text-align:right;">
          @yield('footer_right')
        <div class="col-md-1">
        </div>
      </footer>
    <br>
  </div>
    <script src="/js/jquery-2.1.1.min.js"></script>
    <script src="/js/jquery-ui-1.11.1/jquery-ui.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="/js/jquery.cookie.js"></script>
    @yield('scripts')
  </body>

</html>
