// https://stackoverflow.com/questions/6848043/how-do-i-detect-a-file-is-being-dragged-rather-than-a-draggable-element-on-my-pa

var dragTimer;
$(document).on('dragover', function(e) {
  var dt = e.originalEvent.dataTransfer;
  if (dt.types && (dt.types.indexOf ? dt.types.indexOf('Files') != -1 : dt.types.contains('Files'))) {
    $("#file_upload").removeClass('col-xs-4 col-xs-offset-4');
    $("#file_upload").addClass('col-xs-8 col-xs-offset-2');
    $("#dropzone").addClass('file-available');
    $("#fileupload").addClass('file-available');
    window.clearTimeout(dragTimer);
  }
});

$(document).on('dragleave', function(e) {
  dragTimer = window.setTimeout(function() {
    dropzone_reset();
  }, 25);
});

// $(#dropzone).on('dragover', function(e) {
//   var dt = e.originalEvent.dataTransfer;
//   if (dt.types && (dt.types.indexOf ? dt.types.indexOf('Files') != -1 : dt.types.contains('Files'))) {
//     $("#dropzone").css('background-color','gray');
//     window.clearTimeout(dragTimer);
//   }
// });
//
// $(#dropzone).on('dragleave', function(e) {
//   dragTimer = window.setTimeout(function() {
//     $("#dropzone").css('background-color','white');
//   }, 25);
// });

function dropzone_reset() {
  $("#file_upload").removeClass('col-xs-8 col-xs-offset-2');
  $("#file_upload").addClass('col-xs-4 col-xs-offset-4');
  $("#dropzone").removeClass('file-available');
  $("#fileupload").removeClass('file-available');
}
