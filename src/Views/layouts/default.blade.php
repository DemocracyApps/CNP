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
          <div class="btn-group">
            <button type="button" class="btn btn-default">Application</button>
            <button type="button" class="btn btn-default active">Demo</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container app-container">
    <div class="row">
      <div class="col-md-3 app-navigation">
        <h3>Demo Navigation</h3>
        <ul class="nav bs-sidenav">
          <a href="#overview">Overview</a>
          <li><a href="#">A sample</a></li>
          <li><a href="#">A sample</a></li>

          <a href="#overview">Easy In/Easy Out</a>
          <li><a href="#">A sample</a></li>
          <li><a href="#">A sample</a></li>
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
  @yield('scripts')
</body>

</html>

