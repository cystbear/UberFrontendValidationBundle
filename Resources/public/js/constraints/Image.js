/**
 * Check if element is image
 *
 * @constructor
 */
function UberImageValidationConstraint(field)
{
    this.message = 'This {{value}} is not image!';

    this.validate = function () {
        var error = '';
        var field_id = field.attr('id');
        if (((document.getElementById(field_id).files.length) == 0) || (!fileInput.files[0].fileName.match(/\.(jpg|jpeg|png|gif)$/))) {
            error = this.message.replace('{{value}}', String(parse_field_name(field.attr('name'))));
        }

        return error;
    }
}
