$(function() {
  $('#file_upload').fileupload({
    forceIframeTransport: true,    // VERY IMPORTANT.  you will get 405 Method Not Allowed if you don't add this.
    autoUpload: true,
    add: function (event, data) {
      dropzone_reset();
      $.ajax({
        url: "resources/php/post/generate_credentials.php",
        type: 'POST',
        dataType: 'json',
        data: {filename: data.files[0].name},
        async: false,
        success: function(retdata) {
          save_data_to_form(retdata);
          $('#progress-filename').text(""+data.files[0].name);
        }
      });
      data.submit();
    },
    send: function(e, data) {
      $('#progress-title').text("Uploading...");
      $('#download-bar').hide();
      $('#progress').show();
      $('#progress-bar').show();
    },
    fail: function(e, data) {
      console.log('fail');
      console.log(data);
    },
    progressall: function (e, data) {
      var progress = parseInt(data.loaded / data.total * 100, 10);
      $('#progress-bar .progress-bar').css(
        'width',
        progress + '%'
      );
    },
    done: function (event, data) {
      $('#progress').hide();

      var aws_resized_image_key = 'resized-'+$('#file_upload').find('input[name=key]').val();
      $.ajax({
        url: "resources/php/get/generate_credentials.php",
        type: 'GET',
        dataType: 'json',
        data: {filekey: aws_resized_image_key},
        async: true,
        success: function(retdata) {
          process_resized_image(retdata.signed_get_url);
        }
      });
    },
  });
});

function save_data_to_form(data) {
  $('#file_upload').attr('action','http://'+data.bucket+'.s3.amazonaws.com');
  $('#file_upload').find('input[name=key]').val(data.key);
  $('#file_upload').find('input[name=AWSAccessKeyId]').val(data.AWSAccessKeyId);
  $('#file_upload').find('input[name=acl]').val(data.acl);
  $('#file_upload').find('input[name=success_action_status]').val(data.success_action_status);
  $('#file_upload').find('input[name=success_action_redirect]').val(data.success_action_redirect);
  $('#file_upload').find('input[name=Policy]').val(data.Policy);
  $('#file_upload').find('input[name=X-Amz-Signature]').val(data.XAmzSignature);
  $('#file_upload').find('input[name=x-amz-meta-tag]').val(data.XAmzMetaTag);
  $('#file_upload').find('input[name=Content-Type]').val(data.ContentType);
  $('#file_upload').find('input[name=x-amz-meta-uuid]').val(data.XAmzMetaUuid);
  $('#file_upload').find('input[name=x-amz-server-side-encryption]').val(data.XAmzServerSideEncryption);
  $('#file_upload').find('input[name=X-Amz-Credential]').val(data.XAmzCredential);
  $('#file_upload').find('input[name=X-Amz-Algorithm]').val(data.XAmzAlgorithm);
  $('#file_upload').find('input[name=X-Amz-Date]').val(data.XAmzDate);
}

function process_resized_image(url) {
  $('#progress-title').text("Processing...");
  $('#progress').show();

  $('#progress .progress-bar').css('width','100%');
  var progress_update = setInterval(function() {
    if (is_valid_url(url)) {
      clearTimeout(progress_timeout);
      clearInterval(progress_update);
      download_ready(url);
    }
  },1000);

  var progress_timeout = setTimeout(function(){
    clearInterval(progress_update);
    $('#progress-title').text("Timed out.");
    $('#progress-bar').hide();
  }, 20000);
}

function download_ready(url) {
  $('#download-bar').attr('href', url);
  $('#progress-title').text("Complete!");
  $('#download-bar').show();
  $('#progress-bar').hide();
}

function is_valid_url(url) {
    var is_valid = false;

    $.ajax({
      url: url,
      type: "get",
      dataType: "json",
      async: false,
      complete: function(xhr, textStatus) {
        is_valid = parseInt(xhr.status) < 400;
        console.log("status:"+xhr.status);
        console.log(xhr.status);
      }
    });

    return is_valid;
}
