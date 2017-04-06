$('.image-upload').each(function () {
    const $element = $(this);
    const imagePath = $element.data('current-path') || '/img/upload-placeholder.jpg';
    const uploadPath = $element.data('upload-path');
    const beforeCallback = $element.data('before');
    const successCallback = $element.data('success');
    const errorCallback = $element.data('error');
    const errorTextClass = $element.data('error-text-class') || 'text-danger text-center';
    const responsePath = $element.data('response-path') || 'path';
    const errorResponseKey = $element.data('error-response-key') || 'message';
    const $preview = $('<div class="image-upload-preview"></div>');
    const $helpText = $('<span class="image-upload-preview-helptext">Click to upload photo.</span>');
    const $inputFile = $('<input type="file" class="image-upload-input" accept="image/*">');
    const $errorText = $(`<div class="${errorTextClass}"></div>`);

    $preview.css({
        'background-image': `url(${imagePath})`,
        'background-repeat': 'no-repeat',
        'background-size': '100% 100%'
    });
    $preview.append($helpText);

    $element.append($preview, $inputFile, $errorText);

    $inputFile.on('change', function () {
        if (this.files.length) {
            if (beforeCallback) {
                window[beforeCallback].apply(null, []);
            }
            $errorText.text('');
            const file = this.files[0];
            const formData = new FormData();
            formData.append('file', file, file.name);
            $.ajax({
                url: uploadPath,
                type: 'POST',
                data: formData,
                cache: false,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: response => {
                    const path = response[responsePath];
                    $preview.css('background-image', `url(${path})`);

                    if (successCallback) {
                        window[successCallback].apply(null, [response]);
                    }
                },
                error: (error) => {
                    if (errorCallback) {
                        window[errorCallback].apply(null, [error.responseJSON]);
                    }
                    $errorText.text(error.responseJSON[errorResponseKey]);
                }
            });
        }
    });

    $preview.on('click', () => {
        $inputFile.click();
    });
});
