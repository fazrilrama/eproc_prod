$(document).ready(function () {
    $('#pkp').change(function () {
        let value = $(this).val();

        if (value == 0) {

            $('#no_pkp').removeAttr('data-validation');
            $('#no_pkp').removeAttr('data-validation-error-msg');
            $('#no_pkp').parent('div').parent('div').find('label').each(function () {
                $(this).html('No.PKP');
            });

            $('#sppkp_date').removeAttr('data-validation');
            $('#sppkp_date').removeAttr('data-validation-error-msg');
            $('#sppkp_date').parent('div').parent('div').find('label').each(function () {
                $(this).html('Tanggal SPPKP');
            });

            $('#attachment').removeAttr('data-validation');
            $('#attachment').removeAttr('data-validation-error-msg');
            $('#attachment').parent('div').parent('div').find('label').each(function () {
                $(this).html('File');
            });

        }
        else {


            $('#no_pkp').attr('data-validation', 'required');
            $('#no_pkp').attr('data-validation-error-msg', 'No PKP Tidak Valid!');
            $('#no_pkp').parent('div').parent('div').find('label').each(function () {
                $(this).html('No.PKP<span style="color:red;">*</span>');
            });

            $('#sppkp_date').attr('data-validation', 'required');
            $('#sppkp_date').attr('data-validation-error-msg', 'SPPKP Tidak Valid!');
            $('#sppkp_date').parent('div').parent('div').find('label').each(function () {
                $(this).html('Tanggal SPPKP<span style="color:red;">*</span>');
            });

            $('#attachment').attr('data-validation', 'required mime size');
            $('#attachment').attr('data-validation-max-size', '50MB');
            $('#attachment').attr('data-validation-allowing', 'pdf, png, jpeg, jpg, rar, zip');
            $('#attachment').removeAttr('data-validation-error-msg');
            $('#attachment').parent('div').parent('div').find('label').each(function () {
                $(this).html('File<span style="color:red;">*</span>');
            });

        }
    });
});