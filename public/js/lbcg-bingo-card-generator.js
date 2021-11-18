document.addEventListener('DOMContentLoaded', function () {
    /**
     * Draw new grid
     */
    function drawNewGrid() {
        const gridColsCount = document.getElementById('lbcg-grid-size').value.replace('grid-', '')[0],
            words = document.getElementById('lbcg-body-content').value.split("\n"),
            includeFreeSpace = document.getElementById('lbcg-free-space-check').checked,
            newItemsCount = gridColsCount ** 2;
        let gridItems = '';
        for (let i = 1; i <= newItemsCount; i++) {
            gridItems += '<div class="lbcg-card-col">' +
                '<span class="lbcg-card-text">' +
                (Math.round(newItemsCount / 2) === i && includeFreeSpace ? LBCG['freeSquareWord'] : words[i - 1]) +
                '</span>' +
                '</div>'
        }
        const gridBodyEl = document.querySelector('div.lbcg-card-body-grid'),
            currentGridSize = Math.sqrt(document.querySelectorAll('div.lbcg-card-body-grid span.lbcg-card-text').length);
        gridBodyEl.classList.remove('lbcg-grid-' + currentGridSize);
        gridBodyEl.classList.add('lbcg-grid-' + gridColsCount);
        gridBodyEl.innerHTML = gridItems;
    }

    /**
     * Remove or add star in middle of grid
     */
    function changeFreeSpaceItem(add) {
        const gridItems = document.querySelectorAll('div.lbcg-card-body-grid span.lbcg-card-text'),
            index = Math.round(gridItems.length / 2) - 1;
        if (add) {
            gridItems[index].innerHTML = LBCG['freeSquareWord'];
        } else {
            const words = document.getElementById('lbcg-body-content').value.split("\n");
            gridItems[index].innerHTML = words[index];
        }
    }

    /**
     * Check and fix wrapped word in grid
     *
     * @param spans
     */
    function checkWrapWordInGrid(spans) {
        let fontSize = 0;
        for (let i = 0; i < spans.length; i++) {
            while (spans[i].offsetHeight > spans[i].parentNode.offsetHeight && fontSize < spans[i].offsetHeight) {
                fontSize = getComputedStyle(document.documentElement).getPropertyValue('--lbcg-grid-font-size');
                fontSize = parseFloat(fontSize.split('px')[0]);
                document.documentElement.style.setProperty('--lbcg-grid-font-size', (fontSize - 0.5) + 'px');
            }
        }
        return fontSize !== 0;
    }

    /**
     * Check small words and make bigger
     *
     * @param spans
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
        let fontSize = 0;
        while (spans[maxLengthIndex].offsetHeight < spans[maxLengthIndex].parentNode.offsetHeight && fontSize < spans[maxLengthIndex].offsetHeight) {
            fontSize = getComputedStyle(document.documentElement).getPropertyValue('--lbcg-grid-font-size');
            fontSize = parseFloat(fontSize.split('px')[0]);
            document.documentElement.style.setProperty('--lbcg-grid-font-size', (fontSize + 0.5) + 'px');
        }
        if (fontSize !== 0) {
            document.documentElement.style.setProperty('--lbcg-grid-font-size', (fontSize - 0.5) + 'px');
            return true;
        }
        return bigWordExists;
    }

    function checkGridFontSize() {
        const spans = document.getElementsByClassName('lbcg-card-text');
        const result = checkSmallWordInGrid(spans);
        if (result === true) {
            checkWrapWordInGrid(spans);
        }
    }
    window.onload = checkGridFontSize;

    document.addEventListener('click', function (event) {
        if (event.target.matches('div.lbcg-sidebar-header')) {
            // On sidebar header click
            const sidebarHeader = event.target.parentNode;
            if (sidebarHeader.classList.contains('collapsed')) {
                sidebarHeader.classList.remove('collapsed')
            } else {
                sidebarHeader.classList.add('collapsed')
            }
        }
    });

    document.addEventListener('input', function (event) {
        if (event.target.matches('#lbcg-title')) {
            // On title change
            document.querySelector('div.lbcg-card-header span.lbcg-card-header-text').innerHTML = event.target.value;
        } else if (event.target.matches('#lbcg-body-content')) {
            // On bingo card content change
            const words = event.target.value.split("\n"),
                gridItems = document.querySelectorAll('div.lbcg-card-body-grid span.lbcg-card-text'),
                includeFreeSpace = document.getElementById('lbcg-free-space-check').checked;
            for (let i = 1; i <= gridItems.length; i++) {
                if (Math.round(gridItems.length / 2) === i && includeFreeSpace) {
                    gridItems[i - 1].innerHTML = LBCG['freeSquareWord'];
                } else {
                    gridItems[i - 1].innerHTML = words[i - 1];
                }
            }
            checkGridFontSize();
        }
    });

    document.addEventListener('change', function (event) {
        if (event.target.matches('#lbcg-grid-size')) {
            // On grid size change
            const gridSize = event.target.value,
                freeSpaceEl = document.getElementById('lbcg-free-space-check');
            if (gridSize === '4x4') {
                window.LBCIncludeFreeSpace = freeSpaceEl.checked;
                freeSpaceEl.checked = false;
                freeSpaceEl.parentNode.style.display = 'none';
            } else {
                freeSpaceEl.checked = window.LBCIncludeFreeSpace;
                freeSpaceEl.parentNode.style.display = '';
            }
            drawNewGrid();
        } else if (event.target.matches('#lbcg-font')) {
            // On font change
            document.documentElement.style.setProperty('--lbcg-header-font-family', LBCG['fonts'][event.target.value]['name'] + ', sans-serif');
            document.documentElement.style.setProperty('--lbcg-grid-font-family', LBCG['fonts'][event.target.value]['name'] + ', sans-serif');
        } else if (event.target.matches('#lbcg-free-space-check')) {
            // On free space checkbox change
            changeFreeSpaceItem(event.target.checked);
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
                }
            }
            request.open(event.target.method, LBCG['ajaxUrl'], true);
            request.send(data);
        } else if (event.target.matches('#lbcg-bc-invitation')) {
            // On card invite button click
            event.preventDefault();
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
            }
            request.open(event.target.method, LBCG['ajaxUrl'], true);
            request.send(data);
        }
    });

    /**
     * Header/Grid/Card background
     */
    document.addEventListener('change', function (event) {
        if (event.target.matches('.bc-color')) {
            // On color change
            const type = event.target.getAttribute('data-bct');
            document.documentElement.style.setProperty('--lbcg-' + type + '-bg-color', event.target.value);
        } else if (event.target.matches('.bc-image')) {
            // On image change
            const type = event.target.getAttribute('data-bct');
            var aaa = event.target.files;
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
    // Remove image
    document.addEventListener('click', function (event) {
        if (event.target.matches('.remove-bc-image')) {
            event.preventDefault();
            const type = event.target.getAttribute('data-bct');
            document.getElementById('bc-' + type + '-image').value = '';
            document.getElementsByName('bc_' + type + '[remove_image]')[0].value = 1;
            document.documentElement.style.setProperty('--lbcg-' + type + '-bg-image', 'none');
        }
    });
});
