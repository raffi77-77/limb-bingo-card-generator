jQuery(document).ready(function ($) {
    /**
     * Check words count in container
     */
    function checkWordsCount() {
        const $this = $('#bc-content'),
            words = $this.val().split("\n"),
            bingoGridSize = $('#bc-size').val();
        let needWordsCount = 100;
        if (bingoGridSize === '3x3') {
            needWordsCount = 36;
        } else if (bingoGridSize === '4x4') {
            needWordsCount = 64;
        }
        if (words.length !== needWordsCount) {
            $this.css('border', '2px solid #b32d2e');
        } else {
            $this.css('border', '1px solid #8c8f94');
        }
    }

    /**
     * Check custom fields before save the post
     */
    $('#submitdiv').on('click', 'input[name="publish"]', function () {
        const cardType = $('select[name="bingo_card_type"]').val();
        if (!cardType.length) {
            alert("Please select bingo card type before save the post.");
            return false;
        }
        return true;
    })

    /**
     * Change grid size value
     */
    $('#bc-size').on('change', function () {
        const value = $(this).val(),
            freeSquareEl = $('#free-square');
        if (value !== null) {
            $('input[name="bingo_grid_size"]').val(value);
            // Change words/emojis or numbers count information
            const countEl = $('#content-items-count');
            if (value === '3x3') {
                countEl.html(36);
                freeSquareEl.show();
            } else if (value === '4x4') {
                countEl.html(64);
                freeSquareEl.hide();
            } else {
                countEl.html(100);
                freeSquareEl.show();
            }
            checkWordsCount();
        }
    });

    /**
     * Change grid size related by card type
     */
    $('#bingo-theme-custom-fields').on('change', 'select[name="bingo_card_type"]', function () {
        const $this = $(this),
            thisValue = $this.val(),
            gridSize = $('#bc-size'),
            gridSizeInput = $('input[name="bingo_grid_size"]'),
            specTitleElements = $('td.bc-title-1-75'),
            contentElements = $('td.bc-content'),
            contentItemsCount = $('#content-items-count'),
            freeSquareEl = $('#free-square');
        switch (thisValue) {
            case '1-9':
                // Only 3x3
                gridSize.val('3x3').change();
                gridSizeInput.val('3x3');
                gridSize.prop('disabled', 'disabled');
                specTitleElements.each(function () {
                    $(this).hide();
                });
                contentElements.each(function () {
                    $(this).show();
                });
                freeSquareEl.show();
                contentItemsCount.html(36);
                break;
            case '1-75':
                // Only 5x5
                gridSize.val('5x5').change();
                gridSizeInput.val('5x5');
                gridSize.prop('disabled', 'disabled');
                specTitleElements.each(function () {
                    $(this).show();
                });
                contentElements.each(function () {
                    $(this).hide();
                });
                freeSquareEl.hide();
                $('#bc-free-square').prop('checked', 'checked')
                break;
            case '1-90':
                // Only 9x3
                gridSize.val('9x3').change();
                gridSizeInput.val('9x3');
                gridSize.prop('disabled', 'disabled');
                specTitleElements.each(function () {
                    $(this).hide();
                });
                contentElements.each(function () {
                    $(this).hide();
                });
                freeSquareEl.hide();
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
                contentElements.each(function () {
                    $(this).show();
                });
                freeSquareEl.show();
                break;
        }
    });

    /**
     * Checking words count when content changed
     */
    $('#bc-content').bind('input propertychange', checkWordsCount);

    /**
     * WP Media Uploader
     */
    $('body').on('click', '.bc-image-upload', function (e) {
        e.preventDefault();
        const button = $(this),
            custom_uploader = wp.media({
                title: 'Insert image',
                library: {
                    type: 'image'
                },
                button: {
                    text: 'Use this image'
                },
                multiple: false
            }).on('select', function () {
                const attachment = custom_uploader.state().get('selection').first().toJSON();
                button.html('<img src="' + attachment.sizes.thumbnail.url + '" style="margin-top: 12px; width: 50px;">').next().show().next().val(attachment.id);
            }).open();
    });
    $('body').on('click', '.bc-remove-uploaded-image', function (e) {
        e.preventDefault();
        const button = $(this);
        button.next().val(0);
        button.hide().prev().html('Upload image');
    });
});
