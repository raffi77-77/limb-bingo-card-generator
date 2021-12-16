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

    toggleLoading(true);

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
        } else {
            const words = document.getElementById('lbcg-body-content').value.split("\n");
            gridItems[index].innerHTML = document.getElementsByName('bingo_card_type')[0].value === '1-75' ? 33 : words[index];
        }
    }

    document.addEventListener('click', function (event) {
        if (event.target.matches('div.lbcg-sidebar-header') || event.target.matches('a.lbcg-sidebar-btn')) {
            // On sidebar header click
            event.preventDefault();
            let sidebarHeader = event.target.parentNode;
            if (event.target.matches('a.lbcg-sidebar-btn')) {
                sidebarHeader = sidebarHeader.parentNode;
            }
            if (sidebarHeader.classList.contains('collapsed')) {
                sidebarHeader.classList.remove('collapsed')
            } else {
                sidebarHeader.classList.add('collapsed')
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
            if (el.classList.contains('lbcg-card-col-checked')) {
                el.classList.remove('lbcg-card-col-checked');
            } else {
                el.classList.add('lbcg-card-col-checked');
            }
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
            document.documentElement.style.setProperty('--lbcg-grid-wrap-words', event.target.checked ? 'break-word' : 'anywhere');
            checkGridFontSize();
        }
    });

    /**
     * On subtitle letters change
     */
    document.querySelectorAll('label.lbcg-label--single input.lbcg-input').forEach(function (el) {
        el.addEventListener('input', function (event) {
            const $this = event.target,
                letterElements = document.querySelectorAll('div.lbcg-card-subtitle span.lbcg-card-subtitle-text span'),
                id = $this.getAttribute('id'),
                index = id.replace('lbcg-subtitle-', '') - 1;
            letterElements[index].innerHTML = $this.value;
        });
    });

    document.addEventListener('submit', function (event) {
        if (event.target.matches('#lbcg-bc-generation')) {
            // On card generation button click
            event.preventDefault();
            toggleLoading(true);
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
        } else if (event.target.matches('#lbcg-bc-invitation')) {
            // On card invite button click
            event.preventDefault();
            toggleLoading(true);
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
        } else if (event.target.matches('.bc-opacity')) {
            // On opacity change
            const type = event.target.getAttribute('data-bct');
            document.documentElement.style.setProperty('--lbcg-' + type + '-bg-opacity', event.target.value / 100);
        }
    });
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
        if (spans[i].offsetHeight > spans[i].parentNode.offsetHeight) {
            bigWordExists = true;
        }
    }
    let madeChange = false,
        fontSize = getComputedStyle(document.documentElement).getPropertyValue('--lbcg-grid-font-size'),
        lineHeight = spans[0].parentNode.offsetHeight,
        maxFontSize = lineHeight * 0.71;
    fontSize = parseFloat(fontSize.split('px')[0]);
    while (spans[maxLengthIndex].offsetHeight <= spans[maxLengthIndex].parentNode.offsetHeight && fontSize < maxFontSize) {
        madeChange = true;
        document.documentElement.style.setProperty('--lbcg-grid-font-size', (fontSize += 0.5) + 'px');
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
            maxFontSize = lineHeight * 0.71;
        fontSize = parseFloat(fontSize.split('px')[0]);
        for (let i = 0; i < spans.length; i++) {
            while (spans[i].offsetHeight > (spans[i].parentNode.offsetHeight + 0.1) || fontSize > maxFontSize) {
                document.documentElement.style.setProperty('--lbcg-grid-font-size', (fontSize -= 0.5) + 'px');
                madeChange = true;
            }
            while (wrapWords && isSingleWordWrapped(spans[i], fontSize)) {
                document.documentElement.style.setProperty('--lbcg-grid-font-size', (fontSize -= 0.5) + 'px');
                madeChange = true;
            }
            // Wait for new changes
            setTimeout(() => {
                while (spans[i].offsetHeight > (spans[i].parentNode.offsetHeight + 0.1) || fontSize > maxFontSize) {
                    document.documentElement.style.setProperty('--lbcg-grid-font-size', (fontSize -= 0.5) + 'px');
                    madeChange = true;
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
        const spans = document.getElementsByClassName('lbcg-card-text');
        const result = checkSmallWordInGrid(spans);
        checkWrapWordInGrid(spans, result);
    }
    toggleLoading(false);
}

window.onload = checkGridFontSize;