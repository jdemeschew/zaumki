<div class="modal" id="modal-fileManager" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">

                            <div class="d-flex align-items-center mb-4">
                                <h3 class="me-auto m-0 p-0"><i class="bi bi-image"></i><?php $L->p('File Manager'); ?></h3>
                                <label id="btnUploadFile" class="btn btn-primary"><i class="bi bi-upload"></i><?php $L->p('Upload file'); ?><input type="file" id="filesToUpload" name="filesToUpload[]" multiple hidden></label>
                                <div id="progressUploadFile" class="progress w-25 d-none">
                                    <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col"><?php echo $L->g('Preview') ?></th>
                                        <th scope="col"><?php echo $L->g('Filename') ?></th>
                                        <th scope="col"><?php echo $L->g('Type') ?></th>
                                        <th scope="col"><?php echo $L->g('Size') ?></th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody id="fmFiles">
                                    <!-- <tr>
                      <td class="align-middle">
                          <img style="width: 32px" src="<?php echo HTML_PATH_CORE_IMG ?>default.svg" />
                      </td>
                      <td class="align-middle">photo.jpg</td>
                      <td class="align-middle">image/jpeg</td>
                      <td class="align-middle">300Kb</td>
                      <td class="align-middle">
                          <div class="dropdown">
                              <button class="btn btn-secondary dropdown-toggle" type="button" id="fileOptions" data-bs-toggle="dropdown" aria-expanded="false">
                                  Options
                              </button>
                              <ul class="dropdown-menu" aria-labelledby="fileOptions">
                                  <li><a class="dropdown-item" href="#">Insert</a></li>
                                  <li><a class="dropdown-item" href="#">Set as cover image</a></li>
                                  <li>
                                      <hr class="dropdown-divider">
                                  </li>
                                  <li><a class="dropdown-item" href="#"><?php $L->p('Delete') ?></a></li>
                              </ul>
                          </div>
                      </td>
                  </tr> -->
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Open File Manager modal
    function fmOpen() {
        $('#modal-fileManager').modal('show');
    }

    // Close File Manager modal
    function fmClose() {
        $('#modal-fileManager').modal('hide');
    }

    // Insert HTML code in the Editor content
    function fmInsertFile(filename, absoluteURL, mime) {
        if (mime == 'image/jpeg' || mime == 'image/png') {
            editorInsertContent(absoluteURL, 'image');
        } else {
            editorInsertContent('<a href="' + absoluteURL + '">' + filename + '</a>');
        }
    }

    // Get the files for the current page and show them
    function fmGetFiles() {
        logs('File Manager. Getting files for the current page: ' + _pageKey);
        api.getPageFiles({
            'pageKey': _pageKey
        }).then(function(response) {
            if (response.status == 0) {
                fmDisplayFiles(response.data);
            } else {
                logs("File Manager. An error occurred while trying to get the files for the current page.");
                showAlertError(response.message);
            }
        });
    }

    function setCoverImage(filename) {
        var image = DOMAIN_UPLOADS_PAGES+_pageKey+'/'+filename;
        $("#coverImage").val(filename);
        $("#coverImagePreview").attr("src", image);
    }

    // Displays the files in the table
    function fmDisplayFiles(files) {
        $('#fmFiles').empty();

        if (files.length == 0) {
            logs('File Manager. There are not files for the current page.');
            return false;
        }

        $.each(files, function(key, file) {
            console.log(file);
            var row = '<tr>' +
                '<td class="align-middle">' +
                '	<img style="width: 32px" src="' + file.thumbnailSmall + '" />' +
                '</td>' +
                '<td class="align-middle">' + file.filename + '</td>' +
                '<td class="align-middle">' + file.mime + '</td>' +
                '<td class="align-middle">' + formatBytes(file.size) + '</td>' +
                '<td class="align-middle text-center">' +
                '<div class="dropdown">' +
                '<button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="fileOptions" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-gear"></i><?php $L->p('Options') ?></button>' +
                '<ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="fileOptions">';

                if (ALLOWED_IMG_MIMETYPES.includes(file.mime)) {
                    row +=  '<li><a class="dropdown-item" href="#" onClick="fmInsertFile(\'' + file.filename + '\', \'' + file.absoluteURL + '\', \'' + file.mime + '\'); fmClose();"><i class="bi bi-plus-circle"></i><?php $L->p('Insert original size') ?></a></li>' +
                            '<li><a class="dropdown-item" href="#" onClick="fmInsertFile(\'' + file.filename + '\', \'' + file.thumbnailSmall + '\', \'' + file.mime + '\'); fmClose();"><i class="bi bi-plus-circle"></i><?php $L->p('Insert small size') ?></a></li>' +
                            '<li><a class="dropdown-item" href="#" onClick="fmInsertFile(\'' + file.filename + '\', \'' + file.thumbnailMedium + '\', \'' + file.mime + '\'); fmClose();"><i class="bi bi-plus-circle"></i><?php $L->p('Insert medium size') ?></a></li>' +
                            '<li><hr class="dropdown-divider"></li>' +
                            '<li><a class="dropdown-item" href="#" onClick="setCoverImage(\'' + file.filename + '\')"><i class="bi bi-image"></i><?php $L->p('Set as cover image') ?></a></li>';
                } else {
                    row += '<li><a class="dropdown-item" href="#" onClick="fmInsertFile(\'' + file.filename + '\', \'' + file.absoluteURL + '\', \'' + file.mime + '\'); fmClose();"><i class="bi bi-plus-circle"></i><?php $L->p('Insert file') ?></a></li>';
                }

                row += '<li><hr class="dropdown-divider"></li>' +
                '<li><a class="dropdown-item" href="#" onClick="fmDeleteFile(\'' + file.filename + '\');"><i class="bi bi-trash"></i><?php $L->p('Delete') ?></a></li>' +
                '</ul>' +
                '</div>' +
                '</td>' +
                '</tr>';
            $('#fmFiles').append(row);
        });

        return true;
    }

    // Upload a file for the current page
    function fmUploadFile(file) {
        logs('File Manager. Uploading file.');

        // Check file type/extension
        if (!ALLOWED_FILE_MIMETYPES.includes(file.type)) {
            logs("File Manager. File type is not supported.");
            showAlertError("<?php echo $L->g('File type is not supported. Allowed types:') . ' ' . implode(', ', $GLOBALS['ALLOWED_FILE_EXTENSIONS']) ?>");
            return false;
        }

        // Check file size and compare with PHP upload_max_filesize
        if (file.size > UPLOAD_MAX_FILESIZE) {
            logs("File Manager. File size is to big for PHP configuration.");
            showAlertError("<?php echo $L->g('Maximum load file size allowed:') . ' ' . ini_get('upload_max_filesize') ?>");
            return false;
        }

        // Start progress bar
        $('#btnUploadFile').addClass('d-none');
        $('#progressUploadFile').removeClass('d-none');
        $('#progressUploadFile').children('.progress-bar').width('0');

        // Data to send via AJAX
        var formData = new FormData();
        formData.append("file", file);
        formData.append("token", api.body.token);
        formData.append("authentication", api.body.authentication);

        $.ajax({
            url: api.apiURL + 'pages/files/' + _pageKey,
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            xhr: function() {
                var xhr = $.ajaxSettings.xhr();
                if (xhr.upload) {
                    xhr.upload.addEventListener("progress", function(e) {
                        if (e.lengthComputable) {
                            var percentComplete = (e.loaded / e.total) * 100;
                            $('#progressUploadFile').children('.progress-bar').width(percentComplete + '%');
                        }
                    }, false);
                }
                return xhr;
            }
        }).done(function(response) {
            if (response.status == 0) {
                logs("File Manager. File uploaded.");
                // Progress bar
                $('#progressUploadFile').addClass('d-none');
                $('#btnUploadFile').removeClass('d-none');
                // Get current files
                fmGetFiles();
            } else {
                logs("File Manager. An error occurred while trying to upload the file.");
                // Progress bar
                $('#progressUploadFile').children('.progress-bar').addClass('bg-danger');
                // Alert the user about the error
                showAlertError('File Manager. ' + response.message);
            }
        });
    }

    // Delete a file for the current page
    function fmDeleteFile(file) {
        logs('File Manager. Deleting file.');
        api.deletePageFile({
            'key': _pageKey,
            'file': file
        }).then(function(response) {
            if (response.status == 0) {
                logs("File Manager. File deleted.");
                // Get current files
                fmGetFiles();
            } else {
                logs("File Manager. An error occurred while trying to delete the file for the current page.");
                showAlertError('File Manager. ' + response.message);
            }
        });
    }

    // Initlization and events for the File Manager
    $(document).ready(function() {
        // Input file change event
        $('#filesToUpload').on("change", function(e) {
            var filesToUpload = $('#filesToUpload')[0].files;
            for (var i = 0; i < filesToUpload.length; i++) {
                fmUploadFile(filesToUpload[i]);
            }
        });

        // Drag and drop files to upload them
        $(window).on("dragover dragenter", function(e) {
            e.preventDefault();
            e.stopPropagation();
            fmOpen();
        });
        $(window).on("drop", function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#filesToUpload').prop('files', e.originalEvent.dataTransfer.files);
            $('#filesToUpload').trigger('change');
        });

    });
</script>
