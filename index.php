<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
  <!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
  <script
  src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"
  integrity="sha256-xNjb53/rY+WmG+4L6tTl9m6PpqknWZvRt0rO1SRnJzw="
  crossorigin="anonymous"></script>
  <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
  <script src="https://cdn.jsdelivr.net/jquery.fileupload/9.9.0/js/jquery.iframe-transport.js"></script>
  <!-- The basic File Upload plugin -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.18.0/js/jquery.fileupload.js"></script>
  <script src="assets/javascript.js"></script>


  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.18.0/css/jquery.fileupload.css">
  <link rel="stylesheet" href="assets/styles.css">
</head>

<body class="col-xs-10 col-xs-offset-1">

  <h1>Magic Photo Resizer</h1>
  <h3>Resize any photo to 620px wide!</h3>

  <form action="" method="post" enctype="multipart/form-data" id="file_upload" class="col-xs-4 col-xs-offset-4">
    <br>
    <span id="dropzone" class="btn fileinput-button drop-zone">
      <p style="position:relative;top:10%;font-size:100%;">Drag a file here!</p>
      <p style="position:relative;top:15%;font-size:75%;">or click this box to choose one</p>
      <input id="fileupload" type="file" name="file" multiple class="drop-zone">
    </span>
    <br><br>

    <input type="hidden" name="key" value="" />
    <input type="hidden" name="acl" value="" />
    <input type="hidden" name="success_action_redirect" value="" />

    <input type="hidden" name="Content-Type" value="" />
    <input type="hidden" name="x-amz-meta-uuid" value="" />
    <input type="hidden" name="x-amz-server-side-encryption" value="" />
    <input type="hidden" name="X-Amz-Credential" value="" />
    <input type="hidden" name="X-Amz-Algorithm" value="" />
    <input type="hidden" name="X-Amz-Date" value="" />

    <input type="hidden" name="x-amz-meta-tag" value="" />
    <input type="hidden" name="Policy" value='' />
    <input type="hidden" name="X-Amz-Signature" value="" />
  </form>

  <div class="container col-xs-12">
    <!-- The global progress bar -->
    <div id="progress" style="display:none;">
      <p id="progress-title" class="progress-title col-xs-2"></p>
      <div id="progress-bar" class="progress progress-bar-thing col-xs-8 col-offset-xs-2">
        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      <a id="download-bar" class="download-bar btn btn-success col-xs-8 col-offset-xs-2" style="display:none;" href="" download="" target="_blank">Download</a>
      <p id="progress-filename" class="progress-filename col-xs-2 col-offset-xs-10"></p>
    </div>
  </div>
</body>
</html>
