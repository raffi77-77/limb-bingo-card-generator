document.addEventListener('DOMContentLoaded', function () {
    /**
     * Get card content
     *
     * @param type
     * @param size
     * @param freeSquare
     * @param title
     * @param specTitle
     * @param content
     */
    function getCardContent(type, size = '', freeSquare = false, title = '', specTitle = [], content = '') {
        const request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (request.readyState !== 4 || request.status !== 200) return;
            // On success
            document.getElementsByClassName('lbcg-card')[0].innerHTML = request.responseText;
            document.documentElement.style.setProperty('--lbcg-grid-line-height', type === '1-90' ? '33.3px' : (size === '3x3' ? '102px' : (size === '4x4' ? '76.25px' : '60.8px')));
            if (type !== '1-75' && type !== '1-90') {
                checkGridFontSize();
            } else {
                document.documentElement.style.setProperty('--lbcg-grid-font-size', type === '1-90' ? '16px' : (type === '1-75' ? '31.5px' : '16px'));
            }
            toggleLoading(false);
        }
        request.open('post', LBCG['ajaxUrl'], true);
        let reqData = new FormData();
        reqData.append('action', 'lbcg_get_card_content');
        reqData.append('card_type', type);
        reqData.append('card_grid_size', size);
        reqData.append('free_square', freeSquare ? 'true' : 'false');
        reqData.append('card_title', title);
        reqData.append('spec_title', specTitle.join(';'));
        reqData.append('card_content', content);
        request.send(reqData);
    }

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
            $this = document.getElementById('lbcg-body-content');
        }
        const words = $this.value.split("\n"),
            bingoGridSize = document.getElementsByName('bingo_grid_size')[0].value,
            bingoCardType = document.getElementById('lbcg-bc-type').value;
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

    document.getElementById('lbcg-body-content').addEventListener('input', checkWordsCount);

    /**
     * On custom CSS change
     */
    document.getElementById('bc-custom-css').addEventListener('change', function (event) {
        event.preventDefault();
        let styleEl = document.getElementById('lbcg-custom-css');
        if (styleEl === null) {
            styleEl = document.createElement('style');
            styleEl.setAttribute('id', 'lbcg-custom-css');
            styleEl.setAttribute('type', 'text/css');
            const head = document.head || document.getElementsByTagName('head')[0];
            head.appendChild(styleEl);
        }
        if (styleEl.styleSheet) {
            styleEl.styleSheet.cssText = event.target.value;
        } else {
            styleEl.innerHTML = event.target.value;
        }
    });

    /**
     * Grid size value changed
     */
    document.getElementById('lbcg-grid-size').addEventListener('change', function (event) {
        event.preventDefault();
        const value = event.target.value,
            freeSquareEl = document.getElementById('lbcg-free-space-check'),
            bingoCardType = document.getElementById('lbcg-bc-type').value;
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
                freeSquareEl.parentNode.style.display = 'none';
            } else {
                freeSquareEl.parentNode.style.display = '';
            }
            checkWordsCount();
        }
    });

    /**
     * On card type change
     */
    document.getElementById('lbcg-bc-type').addEventListener('change', function (event) {
        event.preventDefault();
        toggleLoading(true);
        const title = document.getElementById('lbcg-title').value,
            gridSize = document.getElementById('lbcg-grid-size'),
            gridSizeInput = document.getElementsByName('bingo_grid_size')[0],
            specTitleElement = document.getElementsByClassName('lbcg-input-wrap--subtitle')[0],
            contentElement = document.getElementById('lbcg-body-content'),
            freeSquareElement = document.getElementById('lbcg-free-space-check');
        switch (event.target.value) {
            case '1-75':
                // Only 5x5
                gridSizeInput.value = '5x5';
                gridSize.parentNode.style.display = 'none';
                specTitleElement.parentNode.style.display = 'flex';
                contentElement.parentNode.style.display = 'none';
                freeSquareElement.parentNode.style.display = 'none';
                getCardContent('1-75', '5x5', true, title, [...document.querySelectorAll('.lbcg-input-wrap--subtitle .lbcg-input')].map(item => item.value));
                break;
            case '1-90':
                // Only 9x3
                gridSizeInput.value = '9x3';
                gridSize.parentNode.style.display = 'none';
                specTitleElement.parentNode.style.display = 'none';
                contentElement.parentNode.style.display = 'none';
                freeSquareElement.parentNode.style.display = 'none';
                getCardContent('1-90', '9x3', false, title);
                break;
            case 'generic':
                // All grids
                gridSizeInput.value = '3x3';
                gridSize.value = '3x3';
                gridSize.parentNode.style.display = 'flex';
                specTitleElement.parentNode.style.display = 'none';
                contentElement.parentNode.style.display = 'flex';
                freeSquareElement.parentNode.style.display = 'flex';
                getCardContent('generic', '3x3', freeSquareElement.checked, title, [], contentElement.value);
                break;
        }
    });

    /**
     * Check custom fields before save the post
     */
    document.getElementById('publish').addEventListener('click', function (event) {
        const cardType = document.getElementById('lbcg-bc-type').value;
        if (!cardType.length) {
            event.preventDefault();
            alert("Please select bingo card type before save the post.");
        } else if (!checkWordsCount()) {
            event.preventDefault();
            alert("Please provide a minimum words/emojis or numbers quantity.");
        } else {
            event.preventDefault();
            html2canvas(document.getElementsByClassName('lbcg-card')[0]).then(function (canvas) {
                document.getElementsByName('bingo_card_thumbnail')[0].value = canvas.toDataURL();
                document.getElementById('post').submit();
            });
        }
    });

    /**
     * WP Media Uploader
     */
    document.addEventListener('click', function (event) {
        if (event.target.matches('.bc-image-admin')) {
            // Set image
            event.preventDefault();
            const type = event.target.getAttribute('data-bct');
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
                document.getElementById('bc-' + type + '-image').value = attachment.id;
                document.documentElement.style.setProperty('--lbcg-' + type + '-bg-image', 'url(' + attachment.url + ')');
            }).open();
        } else if (event.target.matches('.remove-bc-image-admin')) {
            // Remove image
            event.preventDefault();
            const type = event.target.getAttribute('data-bct');
            document.getElementById('bc-' + type + '-image').value = '';
            document.documentElement.style.setProperty('--lbcg-' + type + '-bg-image', 'none');
        }
    }, false);
}, false);
