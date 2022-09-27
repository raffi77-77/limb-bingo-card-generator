/**
 * Conversion of string to HTML entities
 */
String.prototype.lcToHtmlEntities = function() {
    return this.replace(/./gm, function(s) {
        // return "&#" + s.charCodeAt(0) + ";";
        return (s.match(/[a-z0-9\s]+/i)) ? s : "&#" + s.charCodeAt(0) + ";";
    });
};

document.addEventListener('DOMContentLoaded', function () {
    /**
     * Check words count in container
     *
     * @returns {string}
     */
    function checkWordsCount() {
        const words = document.getElementById('lbcg-body-content').value.split("\n"),
            bingoGridSize = document.getElementById('lbcg-grid-size').value,
            bingoCardType = document.getElementsByName('bingo_card_type')[0].value;
        if (bingoCardType === '1-75' || bingoCardType === '1-90') {
            return "";
        }
        let needWordsCount = 25;
        if (bingoGridSize === '3x3') {
            needWordsCount = 9;
        } else if (bingoGridSize === '4x4') {
            needWordsCount = 16;
        }
        if (words.filter(word => word !== '').length < needWordsCount) {
            return "Please fill minimum " + needWordsCount + " words/emojis or numbers, each in new line.";
        }
        return "";
    }

    checkGridFontSize();
    setCardCheckedState();

    /**
     * Draw new grid
     */
    function drawNewGrid() {
        const words_count_message = checkWordsCount(),
            gridColsCount = document.getElementById('lbcg-grid-size').value[0],
            words = document.getElementById('lbcg-body-content').value.split("\n"),
            includeFreeSpace = document.getElementById('lbcg-free-space-check').checked,
            newItemsCount = gridColsCount ** 2;
        let gridItems = '';
        for (let i = 1, j = 1; i <= newItemsCount; j++) {
            if (words_count_message === '' && (typeof words[j - 1] === 'undefined' || words[j - 1] === '')) {
                continue;
            }
            gridItems += '<div class="lbcg-card-col">' +
                '<span class="lbcg-card-text">' +
                (Math.round(newItemsCount / 2) === i && includeFreeSpace ? LBCG['freeSquareWord'] : (typeof words[j - 1] === 'undefined' ? '' : words[j - 1])) +
                '</span>' +
                '</div>';
            i++;
        }
        const gridBodyEl = document.querySelector('div.lbcg-card-body-grid'),
            currentGridSize = Math.sqrt(document.querySelectorAll('div.lbcg-card-body-grid span.lbcg-card-text').length);
        gridBodyEl.classList.remove('lbcg-grid-' + currentGridSize);
        gridBodyEl.classList.add('lbcg-grid-' + gridColsCount);
        gridBodyEl.innerHTML = gridItems;
    }

    /**
     * Remove or add star in middle of grid
     *
     * @param add
     */
    function changeFreeSpaceItem(add) {
        const gridItems = document.querySelectorAll('div.lbcg-card-body-grid span.lbcg-card-text'),
            index = Math.round(gridItems.length / 2) - 1;
        if (add) {
            gridItems[index].innerHTML = LBCG['freeSquareWord'];
            gridItems[index].parentNode.classList.add('lbcg-free-space');
        } else {
            const words = document.getElementById('lbcg-body-content').value.split("\n");
            gridItems[index].innerHTML = document.getElementsByName('bingo_card_type')[0].value === '1-75' ? 33 : words[index];
            gridItems[index].parentNode.classList.remove('lbcg-free-space');
        }
    }

    document.addEventListener('click', function (event) {
        if (event.target.matches('.bc-font-color')) {
            // On font color change
            const hsl = checkLFromHEX(event.target.value);
            if (hsl.l < 24) {
                event.target.value = HSLToHex(hsl.h, hsl.s, 120);
            }
        } else if (event.target.matches('.bc-border-color')) {
            // On grid border color change
            const hsl = checkLFromHEX(event.target.value);
            if (hsl.l < 24) {
                event.target.value = HSLToHex(hsl.h, hsl.s, 120);
            }
        } else if (event.target.matches('.bc-color')) {
            // On color change
            const hsl = checkLFromHEX(event.target.value);
            if (hsl.l < 24) {
                event.target.value = HSLToHex(hsl.h, hsl.s, 120);
            }
        } else if (event.target.matches('.remove-bc-image')) {
            // On remove image button click
            event.preventDefault();
            const type = event.target.getAttribute('data-bct');
            document.getElementById('bc-' + type + '-image').value = '';
            document.getElementsByName('bc_' + type + '[remove_image]')[0].value = 1;
            document.documentElement.style.setProperty('--lbcg-' + type + '-bg-image', 'none');
        } else if (event.target.matches('#lbcg-view-all-cards')) {
            // On view cards button click
            event.preventDefault();
            const ccEl = document.getElementById('lbcg-cards-custom-count');
            const bcc = parseInt(ccEl.value);
            if (isNaN(bcc) || bcc <= 0) {
                ccEl.disabled = true;
            }
            document.forms['lbcg-view-all-cards-form'].submit();
            ccEl.disabled = false;
        } else if (event.target.matches('div.lbcg-card-col') || event.target.matches('span.lbcg-card-text') || event.target.matches('span.lbcg-card-text img')) {
            // On grid square click
            let el = event.target;
            if (event.target.matches('span.lbcg-card-text')) {
                el = el.parentNode;
            } else if (event.target.matches('span.lbcg-card-text img')) {
                el = el.parentNode.parentNode;
            }
            // Toggle element
            let stateValue = false;
            if (el.classList.contains('lbcg-card-col-checked')) {
                el.classList.remove('lbcg-card-col-checked');
            } else if (el.children[0].hasChildNodes()) {
                el.classList.add('lbcg-card-col-checked');
                stateValue = true;
            }
            // Card post name input
            const postNameEl = document.getElementById('bc-pn');
            // Save state
            if (postNameEl) {
                const data = JSON.parse(localStorage.getItem('pn-' + postNameEl.value)) || [],
                    key = parseInt(el.getAttribute('data-key'));
                if (!isNaN(key)) {
                    data[key] = stateValue;
                }
                localStorage.setItem('pn-' + postNameEl.value, JSON.stringify(data));
            }
        } else if (event.target.matches('span.lbcg-sidebar-arrow')) {
            // On sidebar arrow clock
            const sidebarHeader = event.target.parentNode.parentNode;
            if (sidebarHeader.classList.contains('collapsed')) {
                sidebarHeader.classList.remove('collapsed');
            } else {
                sidebarHeader.classList.add('collapsed');
            }
        } else if (event.target.matches('.lbcg-card-preview-hover') || event.target.matches('.lbcg-card-preview-hover-text')) {
            // Remove preview image
            event.target.parentNode.remove();
        }
    });

    document.addEventListener('input', function (event) {
        if (event.target.matches('#lbcg-title')) {
            // On title change
            document.querySelector('div.lbcg-card-header span.lbcg-card-header-text').innerHTML = event.target.value;
        } else if (event.target.matches('#lbcg-body-content')) {
            // On bingo card content change
            toggleLoading(true);
            const words_count_message = checkWordsCount();
            const words = event.target.value.split("\n"),
                gridItems = document.querySelectorAll('div.lbcg-card-body-grid span.lbcg-card-text'),
                includeFreeSpace = document.getElementById('lbcg-free-space-check').checked;
            for (let i = 1, j = 1; i <= gridItems.length; j++) {
                if (Math.round(gridItems.length / 2) === i && includeFreeSpace) {
                    gridItems[i - 1].innerHTML = LBCG['freeSquareWord'];
                    i++;
                } else {
                    if (words_count_message === '' && (typeof words[j - 1] === 'undefined' || words[j - 1] === '')) {
                        continue;
                    }
                    gridItems[i - 1].innerHTML = typeof words[j - 1] === 'undefined' ? '' : words[j - 1];
                    i++;
                }
            }
            checkGridFontSize();
        } else if (event.target.matches('#lbcg-subtitle')) {
            // On subtitle change
            const specTitle = event.target.value !== '' ? event.target.value.split('') : [];
            let additionalPart = '';
            switch (specTitle.length) {
                case 4:
                case 3:
                    additionalPart = '<span></span>';
                    break;
                case 2:
                case 1:
                    additionalPart = '<span></span><span></span>';
                    break;
            }
            document.getElementsByClassName('lbcg-card-subtitle-text')[0].innerHTML = additionalPart + (specTitle.length ? '<span>' + specTitle.join('</span><span>') + '</span>' : '') + additionalPart;
        }
    });

    document.addEventListener('change', function (event) {
        if (event.target.matches('#lbcg-grid-size')) {
            // On grid size change
            const freeSpaceEl = document.getElementById('lbcg-free-space-check');
            if (event.target.value === '4x4') {
                window.LBCIncludeFreeSpace = freeSpaceEl.checked;
                freeSpaceEl.checked = false;
                freeSpaceEl.parentNode.style.display = 'none';
            } else {
                freeSpaceEl.checked = window.LBCIncludeFreeSpace;
                freeSpaceEl.parentNode.style.display = '';
            }
            let lineHeight;
            if (document.getElementById('lbcg-wrap-words-check').checked) {
                lineHeight = 1;
            } else {
                if (document.getElementsByName('bingo_card_type')[0].value === '1-90') {
                    lineHeight = '33.3px';
                } else if (event.target.value === '3x3') {
                    lineHeight = '102px';
                } else if (event.target.value === '4x4') {
                    lineHeight = '76.25px';
                } else {
                    lineHeight = '60.8px';
                }
            }
            const minWordsEl = document.getElementById('content-items-count');
            if (minWordsEl) {
                minWordsEl.innerText = event.target.value === '3x3' ? 9 : event.target.value === '4x4' ? 16 : 25;
            }
            const gridSizeEl = document.getElementsByName('bingo_grid_size')[0];
            if (gridSizeEl) {
                gridSizeEl.value = event.target.value;
            }
            document.documentElement.style.setProperty('--lbcg-grid-line-height', lineHeight);
            drawNewGrid();
            checkGridFontSize();
        } else if (event.target.matches('#lbcg-font')) {
            // On font change
            document.documentElement.style.setProperty('--lbcg-header-font-family', LBCG['fonts'][event.target.value]['name'] + ', sans-serif');
            document.documentElement.style.setProperty('--lbcg-grid-font-family', LBCG['fonts'][event.target.value]['name'] + ', sans-serif');
            checkGridFontSize();
        } else if (event.target.matches('#lbcg-free-space-check')) {
            // On free space checkbox change
            changeFreeSpaceItem(event.target.checked);
        } else if (event.target.matches('#lbcg-cards-custom-count')) {
            // On view cards custom count change
            const bcc = parseInt(event.target.value);
            document.getElementById('lbcg-cards-count').disabled = bcc > 0;
        } else if (event.target.matches('#lbcg-wrap-words-check')) {
            // On wrap word checkbox change
            let lineHeight;
            if (event.target.checked) {
                lineHeight = 1;
            } else {
                const bingoGridSize = document.getElementById('lbcg-grid-size').value;
                if (document.getElementsByName('bingo_card_type')[0].value === '1-90') {
                    lineHeight = '33.3px';
                } else if (bingoGridSize === '3x3') {
                    lineHeight = '102px';
                } else if (bingoGridSize === '4x4') {
                    lineHeight = '76.25px';
                } else {
                    lineHeight = '60.8px';
                }
            }
            document.documentElement.style.setProperty('--lbcg-grid-line-height', lineHeight);
            // document.documentElement.style.setProperty('--lbcg-grid-wrap-words', event.target.checked ? 'break-word' : 'anywhere');
            checkGridFontSize();
        } else if (event.target.matches('#lbcg-even-distribution-check')) {
            // On even distribution checkbox change
            if (event.target.checked) {
                const result = setEvenDistributionContent();
                if (result) {
                    document.getElementById('lbcg-cards-words-content').style.display = 'none';
                    document.getElementById('lbcg-even-distribution-content').style.display = 'flex';
                }
            } else {
                const result = setWordsFromEvenDistributionContent();
                if (result) {
                    document.getElementById('lbcg-even-distribution-content').style.display = 'none';
                    document.getElementById('lbcg-cards-words-content').style.display = 'flex';
                }
            }
        }
    });

    document.addEventListener('submit', function (event) {
        if (event.target.matches('#lbcg-bc-generation')) {
            // On card generation button click
            event.preventDefault();
            const submitButtons = [...document.querySelectorAll('#lbcg-bc-generation button[type="submit"]')];
            submitButtons.forEach(el => el.disabled = true);
            // Remove checked square classes
            [...document.getElementsByClassName('lbcg-card-col-checked')].forEach(el => el.classList.remove('lbcg-card-col-checked'));
            html2canvas(document.getElementsByClassName('lbcg-card')[0]).then(function (canvas) {
                // Get card image
                document.getElementsByName('bc_thumbnail')[0].value = canvas.toDataURL('image/webp');
                toggleLoading(true);
                submitButtons.forEach(el => el.disabled = false);
                const words_count_message = checkWordsCount();
                if (words_count_message !== '') {
                    alert(words_count_message);
                    return false;
                }
                const data = new FormData(event.target);
                const request = new XMLHttpRequest();
                request.onreadystatechange = function () {
                    if (request.readyState !== 4 || request.status !== 200) return;
                    // On success
                    const resData = JSON.parse(request.responseText);
                    if (resData.success === true) {
                        location.replace(resData.redirectTo);
                    } else {
                        alert(resData.errors.join("\n"));
                        toggleLoading(false);
                    }
                }
                request.open(event.target.method, LBCG['ajaxUrl'], true);
                request.send(data);
            });
        } else if (event.target.matches('#lbcg-bc-invitation')) {
            // On card invite button click
            event.preventDefault();
            const submitButton = document.querySelector('#lbcg-bc-invitation button[type="submit"]');
            submitButton.disabled = true;
            // Remove checked square classes
            [...document.getElementsByClassName('lbcg-card-col-checked')].forEach(el => el.classList.remove('lbcg-card-col-checked'));
            html2canvas(document.getElementsByClassName('lbcg-card')[0]).then(function (canvas) {
                // Get card image
                document.getElementsByName('bingo_card_thumb')[0].value = canvas.toDataURL('image/webp');
                toggleLoading(true);
                submitButton.disabled = false;
                const data = new FormData(event.target);
                const request = new XMLHttpRequest();
                request.onreadystatechange = function () {
                    if (request.readyState !== 4 || request.status !== 200) return;
                    // On success
                    const resData = JSON.parse(request.responseText);
                    if (resData.success === true) {
                        if (resData.failedInvites.length > 0) {
                            alert('Invitation finished. Failed to invite them: ' + resData.failedInvites.join(', ') + '.');
                        } else {
                            alert('Invitation finished successfully.');
                        }
                    } else {
                        alert(resData.errors.join("\n"));
                    }
                    toggleLoading(false);
                }
                request.open(event.target.method, LBCG['ajaxUrl'], true);
                request.send(data);
            });
        }
    });

    /**
     * Header/Grid/Card background
     */
    document.addEventListener('change', function (event) {
        if (event.target.matches('.bc-font-color')) {
            // On font color change
            const type = event.target.getAttribute('data-bct');
            document.documentElement.style.setProperty('--lbcg-' + type + '-text-color', event.target.value);
        } else if (event.target.matches('.bc-border-color')) {
            // On grid border color change
            const type = event.target.getAttribute('data-bct');
            document.documentElement.style.setProperty('--lbcg-' + type + '-border-color', event.target.value);
        } else if (event.target.matches('.bc-color')) {
            // On color change
            const type = event.target.getAttribute('data-bct');
            document.documentElement.style.setProperty('--lbcg-' + type + '-bg-color', event.target.value);
        } else if (event.target.matches('.bc-image')) {
            // On image change
            const type = event.target.getAttribute('data-bct');
            document.getElementsByName('bc_' + type + '[remove_image]')[0].value = '0';
            document.documentElement.style.setProperty('--lbcg-' + type + '-bg-image', 'url(' + URL.createObjectURL(event.target.files[0]) + ')');
        } else if (event.target.matches('.bc-pos')) {
            // On position change
            const type = event.target.getAttribute('data-bct');
            document.documentElement.style.setProperty('--lbcg-' + type + '-bg-pos', event.target.value);
        } else if (event.target.matches('.bc-repeat')) {
            // On repeat change
            const type = event.target.getAttribute('data-bct');
            document.documentElement.style.setProperty('--lbcg-' + type + '-bg-repeat', event.target.value);
        } else if (event.target.matches('.bc-size')) {
            // On size change
            const type = event.target.getAttribute('data-bct');
            document.documentElement.style.setProperty('--lbcg-' + type + '-bg-size', event.target.value);
            // Remove 'Background repeat' box unless 'Contain' option is selected in 'Background size' box
            const bcRepeatEl = event.target.parentNode.parentNode.parentNode.getElementsByClassName('bc-repeat')[0];
            if (event.target.value === 'contain') {
                bcRepeatEl.parentNode.parentNode.style.display = 'flex';
            } else {
                bcRepeatEl.parentNode.parentNode.style.display = 'none';
                bcRepeatEl.value = 'no-repeat';
            }
        } else if (event.target.matches('.bc-opacity')) {
            // On opacity change
            const type = event.target.getAttribute('data-bct');
            document.documentElement.style.setProperty('--lbcg-' + type + '-bg-opacity', event.target.value / 100);
        }
    });
    // Add words percents input events
    addEventsForWordsPercentsInputs();
});

/**
 * Show or hide loading element
 *
 * @param show
 */
function toggleLoading(show) {
    const els = document.getElementsByClassName('lbcg-parent');
    for (let i = 0; i < els.length; i++) {
        if (show === true) {
            els[i].classList.add('lbcg-loading');
        } else {
            els[i].classList.remove('lbcg-loading');
        }
    }
}

/**
 * Set checked squares
 */
function setCardCheckedState() {
    // Card post name input
    const postNameEl = document.getElementById('bc-pn');
    // Set state
    if (postNameEl) {
        const data = JSON.parse(localStorage.getItem('pn-' + postNameEl.value)) || [];
        for (const i in data) {
            const el = document.querySelector('div.lbcg-card-col[data-key="' + i + '"]');
            if (el && data[i]) {
                el.classList.add('lbcg-card-col-checked');
            }
        }
    }
}

/**
 * Check small words and make bigger
 *
 * @param spans
 * @returns {boolean}
 */
function checkSmallWordInGrid(spans) {
    let maxLengthIndex = 0, maxLength = 0, bigWordExists = false;
    for (let i = 0; i < spans.length; i++) {
        if (spans[i].innerHTML.length > maxLength) {
            maxLength = spans[i].innerHTML.length;
            maxLengthIndex = i;
        }
        if (spans[i].offsetHeight > spans[i].parentNode.offsetHeight || spans[i].scrollWidth > spans[i].offsetWidth) {
            bigWordExists = true;
        }
    }
    let madeChange = false,
        fontSize = getComputedStyle(document.documentElement).getPropertyValue('--lbcg-grid-font-size'),
        lineHeight = spans[0].parentNode.offsetHeight,
        maxFontSize = lineHeight * 0.71,
        prevEOH = -1,
        prevESW = -1;
    fontSize = parseFloat(fontSize.split('px')[0]);
    while (spans[maxLengthIndex].offsetHeight <= spans[maxLengthIndex].parentNode.offsetHeight && spans[maxLengthIndex].scrollWidth <= spans[maxLengthIndex].offsetWidth && fontSize < maxFontSize && fontSize < lbcgFontSize) {
        // Save previous result
        prevEOH = spans[maxLengthIndex].offsetHeight;
        prevESW = spans[maxLengthIndex].scrollWidth;
        //
        document.documentElement.style.setProperty('--lbcg-grid-font-size', (fontSize += 0.5) + 'px');
        madeChange = true;
        if (prevEOH === spans[maxLengthIndex].offsetHeight && prevESW === spans[maxLengthIndex].scrollWidth || fontSize < 0) {
            document.documentElement.style.setProperty('--lbcg-grid-font-size', (fontSize -= 0.5) + 'px');
            break; // Infinite loop detection
        }
    }
    if (madeChange) {
        document.documentElement.style.setProperty('--lbcg-grid-font-size', (fontSize - 0.5) + 'px');
        return true;
    }
    return bigWordExists;
}

/**
 * Check is word of span is single and wrapped
 *
 * @param span
 * @param fontSize
 * @returns {boolean}
 */
function isSingleWordWrapped(span, fontSize) {
    const isSingleWord = span.innerText.trim().indexOf(' ') === -1;
    return isSingleWord && span.offsetHeight > fontSize * 1.5;
    // const wordsCount = span.innerText.trim().split(' ').length;
    // return span.offsetHeight > (fontSize * (wordsCount + 0.5));
}

/**
 * Check and fix wrapped word in grid
 *
 * @param spans
 * @param checkSmallResult
 * @returns {boolean}
 */
function checkWrapWordInGrid(spans, checkSmallResult) {
    if (checkSmallResult) {
        const wrapWordsEl = document.getElementById('lbcg-wrap-words-check');
        let wrapWords;
        if (wrapWordsEl) {
            wrapWords = wrapWordsEl.checked;
        } else {
            wrapWords = getComputedStyle(document.documentElement).getPropertyValue('--lbcg-grid-wrap-words').trim() === 'break-word';
        }
        let madeChange = false,
            fontSize = getComputedStyle(document.documentElement).getPropertyValue('--lbcg-grid-font-size'),
            lineHeight = spans[0].parentNode.offsetHeight,
            maxFontSize = lineHeight * 0.71,
            prevEOH, prevESW;
        fontSize = parseFloat(fontSize.split('px')[0]);
        for (let i = 0; i < spans.length; i++) {
            while (spans[i].offsetHeight > spans[i].parentNode.offsetHeight || spans[i].scrollWidth > spans[i].offsetWidth || fontSize > maxFontSize) {
                // Save previous result
                prevEOH = spans[i].offsetHeight;
                prevESW = spans[i].scrollWidth;
                //
                document.documentElement.style.setProperty('--lbcg-grid-font-size', (fontSize -= 0.5) + 'px');
                madeChange = true;
                if (prevEOH === spans[i].offsetHeight && prevESW === spans[i].scrollWidth || fontSize < 0) {
                    document.documentElement.style.setProperty('--lbcg-grid-font-size', (fontSize += 0.5) + 'px');
                    break; // Infinite loop detection
                }
            }
            while (wrapWords && isSingleWordWrapped(spans[i], fontSize)) {
                document.documentElement.style.setProperty('--lbcg-grid-font-size', (fontSize -= 0.5) + 'px');
                madeChange = true;
            }
            // Wait for new changes
            setTimeout(() => {
                while (spans[i].offsetHeight > spans[i].parentNode.offsetHeight || spans[i].scrollWidth > spans[i].offsetWidth || fontSize > maxFontSize) {
                    // Save previous result
                    prevEOH = spans[i].offsetHeight;
                    prevESW = spans[i].scrollWidth;
                    //
                    document.documentElement.style.setProperty('--lbcg-grid-font-size', (fontSize -= 0.5) + 'px');
                    madeChange = true;
                    if (prevEOH === spans[i].offsetHeight && prevESW === spans[i].scrollWidth || fontSize < 0) {
                        document.documentElement.style.setProperty('--lbcg-grid-font-size', (fontSize += 0.5) + 'px');
                        break; // Infinite loop detection
                    }
                }
                while (wrapWords && isSingleWordWrapped(spans[i], fontSize)) {
                    document.documentElement.style.setProperty('--lbcg-grid-font-size', (fontSize -= 0.5) + 'px');
                    madeChange = true;
                }
            }, 10);
        }
        return madeChange;
    } else {
        let madeChange = false,
            fontSize = getComputedStyle(document.documentElement).getPropertyValue('--lbcg-grid-font-size'),
            lineHeight = spans[0].parentNode.offsetHeight,
            maxFontSize = lineHeight * 0.71;
        fontSize = parseFloat(fontSize.split('px')[0]);
        while (fontSize > maxFontSize) {
            document.documentElement.style.setProperty('--lbcg-grid-font-size', (fontSize -= 0.5) + 'px');
            madeChange = true;
        }
        return madeChange;
    }
}

/**
 * Check grid font size
 */
function checkGridFontSize() {
    toggleLoading(true);
    const bingoCardType = document.getElementsByName('bingo_card_type')[0].value;
    if (bingoCardType !== '1-75' && bingoCardType !== '1-90') {
        const wordsInput = document.getElementById('lbcg-body-content'),
            spans = document.getElementsByClassName('lbcg-card-text');
        if (!!wordsInput && spans.length) {
            const words = wordsInput.value.split('\n'),
                pagesCount = Math.ceil(words.length / spans.length);
            window.lbcgFontSize = 100;
            let i, j, index, result;
            for (j = 0; j < pagesCount; j++) {
                for (i = 0; i < spans.length; i++) {
                    index = j * spans.length + i;
                    if (index in words) {
                        spans[i].innerHTML = words[index];
                    }
                }
                result = checkSmallWordInGrid(spans);
                checkWrapWordInGrid(spans, result);
                window.lbcgFontSize = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--lbcg-grid-font-size').split('px')[0]);
            }
            for (i = 0; i < spans.length; i++) {
                spans[i].innerHTML = words[i];
            }
            document.getElementsByName('lbcg_font_size')[0].value = window.lbcgFontSize;
        }
    }
    toggleLoading(false);
}

function checkLFromHEX(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    r = parseInt(result[1], 16) / 255;
    g = parseInt(result[2], 16) / 255;
    b = parseInt(result[3], 16) / 255;
    const max = Math.max(r, g, b),
        min = Math.min(r, g, b);
    let h, s, l = (max + min) / 2;
    if (max === min) {
        h = s = 0; // achromatic
    } else {
        var d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch (max) {
            case r:
                h = (g - b) / d + (g < b ? 6 : 0);
                break;
            case g:
                h = (b - r) / d + 2;
                break;
            case b:
                h = (r - g) / d + 4;
                break;
        }
        h /= 6;
    }
    return {
        h: Math.round(h * 240),
        s: Math.round(s * 240),
        l: Math.round(l * 240)
    };
}

function HSLToHex(h, s, l) {
    s /= 240;
    l /= 240;

    let c = (1 - Math.abs(2 * l - 1)) * s,
        x = c * (1 - Math.abs((h / 40) % 2 - 1)),
        m = l - c / 2,
        r = 0,
        g = 0,
        b = 0;

    if (0 <= h && h < 40) {
        r = c;
        g = x;
        b = 0;
    } else if (40 <= h && h < 80) {
        r = x;
        g = c;
        b = 0;
    } else if (80 <= h && h < 120) {
        r = 0;
        g = c;
        b = x;
    } else if (120 <= h && h < 160) {
        r = 0;
        g = x;
        b = c;
    } else if (160 <= h && h < 200) {
        r = x;
        g = 0;
        b = c;
    } else if (200 <= h && h <= 240) {
        r = c;
        g = 0;
        b = x;
    }
    // Having obtained RGB, convert channels to hex
    r = Math.round((r + m) * 255).toString(16);
    g = Math.round((g + m) * 255).toString(16);
    b = Math.round((b + m) * 255).toString(16);

    // Prepend 0s, if necessary
    if (r.length === 1)
        r = "0" + r;
    if (g.length === 1)
        g = "0" + g;
    if (b.length === 1)
        b = "0" + b;

    return "#" + r + g + b;
}

function addEventsForWordsPercentsInputs() {
    document.querySelectorAll('.lbcg-word-percent').forEach(inputEl => {
        inputEl.addEventListener('focus', function (event) {
            event.target.oldValue = event.target.value;
        });
        inputEl.addEventListener('blur', function (event) {
            if (typeof event.target.oldValue !== 'undefined') {
                try {
                    if (event.target.value > 100) {
                        event.target.value = 100;
                    } else if (event.target.value < 0) {
                        event.target.value = 0;
                    }
                    const diff = event.target.oldValue - event.target.value;
                    event.target.value = parseFloat(event.target.value).toFixed(2);
                    if (diff !== 0) {
                        const index = event.target.getAttribute('data-wpi'),
                            percentsElements = document.querySelectorAll('input.lbcg-word-percent:not([data-wpi="' + index + '"])'),
                            percents = [...percentsElements].map(e => parseFloat(e.value)),
                            sum = percents.reduce((accumulator, value) => accumulator + value, 0);
                        if (sum !== 0) {
                            percentsElements.forEach(e => {
                                e.value = (e.value * (1 + diff / sum)).toFixed(2);
                            });
                        }
                    }
                } catch (e) {
                    event.target.value = event.target.oldValue;
                }
            }
        });
    });
}

function setEvenDistributionContent() {
    try {
        // Remove old input data
        const percentsElements = document.querySelectorAll('#lbcg-even-distribution-content .lbcg-word-percent');
        let percents = [];
        if (percentsElements.length) {
            percents = [...percentsElements].map(e => e.value);
        }
        const wordsRows = document.querySelectorAll('#lbcg-even-distribution-content .lbcg-input-wrap--words-distribution')
        if (wordsRows.length) {
            [...wordsRows].map(e => e.remove());
        }
        // Create new inputs content
        const words = document.getElementById('lbcg-body-content').value.split("\n").filter(w => w),
            percent = (100 / words.length).toFixed(2); // name="bingo_card_content_words[${i}][value]"
        const content = words.map((w, i) => `<div class="lbcg-input-wrap-in lbcg-input-wrap--words-distribution">
    <label class="lbcg-label lbcg-label--single">
        <input class="lbcg-input" type="text" value="${w.lcToHtmlEntities()}" readonly/>
    </label>
    <label class="lbcg-label lbcg-label--single">
        <input class="lbcg-input lbcg-word-percent" name="bcc_words_percents[]" type="number" min="0" max="100" step="0.01" value="${percents.length ? percents[i] : percent}" data-wpi="${i}"/>
    </label>
</div>`).join('');
        // Add percents inputs content
        const contentEl = document.getElementById('lbcg-even-distribution-content');
        contentEl.innerHTML = contentEl.innerHTML + content;
        // Add percents inputs events
        addEventsForWordsPercentsInputs();
        return true;
    } catch (e) {
        console.error(e.message);
        return false;
    }
}

function setWordsFromEvenDistributionContent() {
    try {
        // document.getElementById('lbcg-body-content').value = [...document.querySelectorAll('.lbcg-input-wrap--words-distribution input[type="text"]')].map(e => e.value).join("\n");
        return true;
    } catch (e) {
        console.error(e.message);
        return false;
    }
}