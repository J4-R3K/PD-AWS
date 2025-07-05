jQuery(document).ready(function($) {

    /***************************************
     * ADD NEW EQUIPMENT ROW
     ***************************************/
    $('#add-equipment').on('click', function() {
        let newRow = `
            <tr>
                <td><input type="text" name="details[]" placeholder="Equipment name"></td>
                <td>
                    <select name="load_type[]">
                        <option value="DOL">DOL</option>
                        <option value="SD">SD</option>
                        <option value="SS">SS</option>
                        <option value="VSD">VSD</option>
                        <option value="STD">STD</option>
                    </select>
                </td>
                <td><input type="text" name="lra[]" placeholder="0"></td>
                <td><input type="text" name="sc[]" placeholder="0"></td>
                <td><input type="text" name="flc[]" placeholder="0"></td>
                <td><input type="text" name="start_time[]" placeholder="1"></td>
                <td><button type="button" class="remove">X</button></td>
            </tr>
        `;
        $('#equipment-table tbody').append(newRow);
    });

    /***************************************
     * REMOVE ROW
     ***************************************/
    $(document).on('click', '.remove', function() {
        $(this).closest('tr').remove();
    });

    /***************************************
     * CALCULATE LOAD (AJAX)
     ***************************************/
    $('#load-form').on('submit', function(e) {
        e.preventDefault();

        // Basic check
        let invalid = false;
        $('#equipment-table tbody tr').each(function(){
            let flc  = $(this).find('input[name="flc[]"]').val().trim();
            let lra  = $(this).find('input[name="lra[]"]').val().trim();
            let sc   = $(this).find('input[name="sc[]"]').val().trim();
            let start= $(this).find('input[name="start_time[]"]').val().trim();

            if(flc === '' || lra === '' || sc === '' || start === '') {
                invalid = true;
            }
        });

        if(invalid) {
            alert('⚠ Please fill in all fields (Equipment, LRA, SC, FLC, Start Time).');
            return false;
        }

        // AJAX to calculate
        $.post(
            ajax_object.ajax_url,
            $(this).serialize() + '&action=calculate_load',
            function(response) {
                $('#results').html(response);
            }
        );
    });

    /***************************************
     * EXPORT PDF (AJAX) => Open in NEW TAB
     ***************************************/
    $('#pdf-form').on('submit', function(e) {
        e.preventDefault();

        let formData = $(this).serialize();
        // Include the HTML from #results
        formData += '&results=' + encodeURIComponent($('#results').html());

        // Post => open new tab
        $.post(
            ajax_object.ajax_url,
            formData + '&action=export_pdf',
            function(response) {
                window.open(response, '_blank');
            }
        );
    });

});
