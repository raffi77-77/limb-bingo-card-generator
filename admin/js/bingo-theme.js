document.addEventListener('DOMContentLoaded', function () {
    /**
     * Check words count in container
     *
     * @param event
     * @returns {boolean}
     */
    function checkWordsCount(event) {
        let $this;
        if (typeof event !== 'undefined') {
            event.preventDefault();
            $this = event.target;
        } else {
            $this = document.getElementById('bc-content');
        }
        const words = $this.value.split("\n"),
            bingoGridSize = document.getElementById('bc-size').value,
            bingoCardType = document.getElementById('bc-type').value;
        if (bingoCardType === '1-75' || bingoCardType === '1-90') {
            return true;
        }
        let needWordsCount = 25;
        if (bingoGridSize === '3x3') {
            needWordsCount = 9;
        } else if (bingoGridSize === '4x4') {
            needWordsCount = 16;
        }
        if (words.filter(word => word !== '').length < needWordsCount) {
            $this.style.border = '2px solid #b32d2e';
            return false;
        }
        $this.style.border = '1px solid #8c8f94';
        return true;
    }

    document.getElementById('bc-content').addEventListener('input', checkWordsCount);

    /**
     * Grid size value changed
     */
    document.getElementById('bc-size').addEventListener('change', function (event) {
        event.preventDefault();
        const value = event.target.value,
            freeSquareEl = document.getElementById('free-square'),
            bingoCardType = document.getElementById('bc-type').value;
        if (value !== null) {
            document.getElementsByName('bingo_grid_size')[0].value = value;
            const countEl = document.getElementById('content-items-count');
            if (value === '3x3') {
                countEl.innerHTML = 9;
            } else if (value === '4x4') {
                countEl.innerHTML = 16;
            } else {
                countEl.innerHTML = 25;
            }
            if (value === '4x4' || bingoCardType === '1-75' || bingoCardType === '1-90') {
                freeSquareEl.style.display = 'none';
            } else {
                freeSquareEl.style.display = '';
            }
            checkWordsCount();
        }
    });

    /**
     * Card type is changed
     */
    document.getElementById('bc-type').addEventListener('change', function (event) {
        event.preventDefault();
        const $this = event.target,
            thisValue = $this.value,
            gridSize = document.getElementById('bc-size'),
            gridSizeInput = document.getElementsByName('bingo_grid_size')[0],
            specTitleElements = document.querySelectorAll('td.bc-title-1-75'),
            contentElements = document.querySelectorAll('td.bc-content');
        switch (thisValue) {
            case '1-75':
                // Only 5x5
                gridSize.value = '5x5';
                gridSizeInput.value = '5x5';
                gridSize.setAttribute('disabled', 'disabled');
                specTitleElements.forEach(function (el) {
                    el.style.display = '';
                });
                contentElements.forEach(function (el) {
                    el.style.display = 'none';
                });
                document.getElementById('bc-free-square').checked = true;
                break;
            case '1-90':
                // Only 9x3
                gridSize.value = '9x3';
                gridSizeInput.value = '9x3';
                gridSize.setAttribute('disabled', 'disabled');
                specTitleElements.forEach(function (el) {
                    el.style.display = 'none';
                });
                contentElements.forEach(function (el) {
                    el.style.display = 'none';
                });
                break;
            case 'generic':
                // All grids
                gridSize.value = '3x3';
                gridSize.removeAttribute('disabled');
                specTitleElements.forEach(function (el) {
                    el.style.display = 'none';
                });
                contentElements.forEach(function (el) {
                    el.style.display = '';
                });
                break;
        }
        gridSize.dispatchEvent(new Event('change'));
    });

    /**
     * Check custom fields before save the post
     */
    document.getElementById('publish').addEventListener('click', function (event) {
        const cardType = document.querySelectorAll('[name^=bingo_card_type]')[0].value;
        if (!cardType.length) {
            event.preventDefault();
            alert("Please select bingo card type before save the post.");
        } else if (!checkWordsCount()) {
            event.preventDefault();
            alert("Please provide a minimum words/emojis or numbers quantity.");
        }
    });

    /**
     * WP Media Uploader
     */
    document.addEventListener('click', function (event) {
        if (event.target.matches('.bc-image-upload') || event.target.matches('.lbcg-image-uploaded')) {
            // Set image
            event.preventDefault();
            let button = event.target;
            if (event.target.matches('.lbcg-image-uploaded')) {
                button = event.target.parentNode;
            }
            const custom_uploader = wp.media({
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
                    button.innerHTML = '<img src="' + attachment.sizes.thumbnail.url + '" class="lbcg-image-uploaded">';
                    let next = button.nextElementSibling;
                    next.style.display = '';
                    next = next.nextElementSibling;
                    next.value = attachment.id;
                }).open();
        } else if (event.target.matches('.bc-remove-uploaded-image')) {
            // Remove image
            event.preventDefault();
            const button = event.target;
            button.nextElementSibling.value = 0;
            button.style.display = 'none';
            button.previousElementSibling.innerHTML = 'Upload image';
        }
    }, false);
}, false);
