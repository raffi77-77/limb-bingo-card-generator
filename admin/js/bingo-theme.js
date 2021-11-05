jQuery(document).ready(function() {
    const $ = jQuery;

    // Check custom fields before save the post
    $('#submitdiv').on('click', 'input[name="publish"]', function () {
        const cardType = $('select[name="bingo_card_type"]').val();
        if (!cardType.length) {
            alert("Please select bingo card type before save the post.");
            return false;
        }
        return true;
    })

    // Change grid size value
    $('#bg-size').on('change', function() {
        const value = $(this).val();
        if (value !== null) {
            $('input[name="bingo_grid_size"]').val(value);
        }
    });

    // Change grid size related by card type
    $('#bingo-theme-custom-fields').on('change', 'select[name="bingo_card_type"]', function () {
        const $this = $(this),
            thisValue = $this.val(),
            gridSize = $('#bg-size'),
            gridSizeInput = $('input[name="bingo_grid_size"]'),
            specTitleElements = $('td.bg-title-1-75');
        switch (thisValue) {
            case '1-9':
                // Only 3x3
                gridSize.val('3x3');
                gridSizeInput.val('3x3');
                gridSize.prop('disabled', 'disabled');
                specTitleElements.each(function () {
                    $(this).hide();
                });
                break;
            case '1-75':
                // Only 5x5
                gridSize.val('5x5');
                gridSizeInput.val('5x5');
                gridSize.prop('disabled', 'disabled');
                specTitleElements.each(function () {
                    $(this).show();
                });
                break;
            case '1-90':
                // Only 9x3
                gridSize.val('9x3');
                gridSizeInput.val('9x3');
                gridSize.prop('disabled', 'disabled');
                specTitleElements.each(function () {
                    $(this).hide();
                });
                break;
            case '1-25':
            case '1-80':
            case '1-100':
                // All grids
                gridSize.val('5x5').change();
                gridSize.prop('disabled', false);
                specTitleElements.each(function () {
                    $(this).hide();
                });
                break;
            default:
                break;
        }
    });
});
