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
            let lineHeight;
            if (document.getElementById('lbcg-wrap-words-check').checked) {
                lineHeight = 1;
            } else {
                if (type === '1-90') {
                    lineHeight = '33.3px';
                } else if (size === '3x3') {
                    lineHeight = '102px';
                } else if (size === '4x4') {
                    lineHeight = '76.25px';
                } else {
                    lineHeight = '60.8px';
                }
            }
            document.documentElement.style.setProperty('--lbcg-grid-line-height', lineHeight);
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
     * @returns {boolean}
     */
    function checkWordsCount() {
        let $this = document.getElementById('lbcg-body-content');
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

    if (document.getElementById('lbcg-body-content')) {
        document.getElementById('lbcg-body-content').addEventListener('input', checkWordsCount);
    }

    document.addEventListener('change', function (event) {
        if (event.target.matches('#bc-custom-css')) {
            event.preventDefault();
            // On custom CSS change
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
        } else if (event.target.matches('#lbcg-bc-type')) {
            event.preventDefault();
            // On card type change
            toggleLoading(true);
            const title = document.getElementById('lbcg-title').value,
                gridSize = document.getElementById('lbcg-grid-size'),
                gridSizeInput = document.getElementsByName('bingo_grid_size')[0],
                specTitleElement = document.getElementsByClassName('lbcg-input-wrap--subtitle')[0],
                contentElement = document.getElementById('lbcg-body-content'),
                wrapWordElement = document.getElementById('lbcg-wrap-words-check'),
                freeSquareElement = document.getElementById('lbcg-free-space-check');
            switch (event.target.value) {
                case '1-75':
                    // Only 5x5
                    gridSizeInput.value = '5x5';
                    gridSize.parentNode.style.display = 'none';
                    specTitleElement.parentNode.style.display = 'flex';
                    contentElement.parentNode.style.display = 'none';
                    wrapWordElement.parentNode.style.display = 'none';
                    freeSquareElement.parentNode.style.display = 'flex';
                    getCardContent('1-75', '5x5', freeSquareElement.checked, title, [...document.querySelectorAll('.lbcg-input-wrap--subtitle .lbcg-input')].map(item => item.value));
                    break;
                case '1-90':
                    // Only 9x3
                    gridSizeInput.value = '9x3';
                    gridSize.parentNode.style.display = 'none';
                    specTitleElement.parentNode.style.display = 'none';
                    contentElement.parentNode.style.display = 'none';
                    wrapWordElement.parentNode.style.display = 'none';
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
                    wrapWordElement.parentNode.style.display = 'flex';
                    freeSquareElement.parentNode.style.display = 'flex';
                    document.getElementById('content-items-count').innerText = 9;
                    getCardContent('generic', '3x3', freeSquareElement.checked, title, [], contentElement.value);
                    break;
            }
        }
    });

    /**
     * WP Media Uploader
     */
    document.addEventListener('click', function (event) {
        if (event.target.matches('#publish')) {
            // Check custom fields before save the post
            const cardType = document.getElementById('lbcg-bc-type').value;
            if (!cardType.length) {
                event.preventDefault();
                alert("Please select bingo card type before save the post.");
            } else if (!checkWordsCount()) {
                event.preventDefault();
                alert("Please provide a minimum words/emojis or numbers quantity.");
            } else {
                event.preventDefault();
                // Remove checked square classes
                [...document.getElementsByClassName('lbcg-card-square-checked')].forEach(el => el.classList.remove('lbcg-card-square-checked'));
                // Get image of card
                html2canvas(document.getElementsByClassName('lbcg-card')[0]).then(function (canvas) {
                    document.getElementsByName('bingo_card_thumbnail')[0].value = canvas.toDataURL();
                    document.getElementById('post').submit();
                });
            }
        } else if (event.target.matches('.bc-image-admin')) {
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
        } else if (event.target.matches('#lbcg-uc-image-set')) {
            // Set taxonomy image
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
                document.getElementById('lbcg-uc-image').value = attachment.id;
                const imgEl = document.getElementById('lbcg-uc-img');
                imgEl.style.display = '';
                imgEl.setAttribute('src', attachment.sizes.thumbnail.url);
            }).open();
        } else if (event.target.matches('#lbcg-uc-image-remove')) {
            // Remove taxonomy image
            document.getElementById('lbcg-uc-image').value = '';
            document.getElementById('lbcg-uc-img').style.display = 'none';
        }
    }, false);
}, false);
