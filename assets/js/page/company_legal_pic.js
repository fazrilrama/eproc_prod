$(document).ready(function () {
    $('#position_type').change(function () {
        let value = $(this).val();

        if (value == 1) {
            $('#attachment').removeAttr('data-validation');
            $('#attachment').removeAttr('data-validation-error-msg');
            $('#attachment').parent('div').parent('div').find('label').each(function () {
                $(this).html('Surat Kuasa');
            });
        }
        else {
            $('#attachment').attr('data-validation', 'required mime size');
            $('#attachment').attr('data-validation-max-size', '50MB');
            $('#attachment').attr('data-validation-allowing', 'pdf, png, jpeg, jpg, rar, zip');
            $('#attachment').removeAttr('data-validation-error-msg');
            $('#attachment').parent('div').parent('div').find('label').each(function () {
                $(this).html('Surat Kuasa<span style="color:red;">*</span>');
            });

        }
    });
});