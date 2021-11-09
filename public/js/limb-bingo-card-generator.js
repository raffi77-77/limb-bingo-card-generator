jQuery(document).ready(function ($) {
    /**
     * Draw new grid
     */
    function drawNewGrid() {
        const gridColsCount = $('#lbcg-grid-size').val().replace('grid-', '')[0],
            words = $('#lbcg-body-content').val().split("\n"),
            includeFreeSpace = $('#lbcg-free-space-check').is(':checked'),
            newItemsCount = gridColsCount ** 2;
        let gridItems = '';
        for (let i = 1; i <= newItemsCount; i++) {
            gridItems += '<div class="lbcg-card-col">' +
                '<span class="lbcg-card-text">' +
                (Math.round(newItemsCount / 2) === i && includeFreeSpace ? LBC['freeSquareWord'] : words[i - 1]) +
                '</span>' +
                '</div>'
        }
        const gridBodyEl = $('div.lbcg-card-body-grid'),
            currentGridSize = Math.sqrt(gridBodyEl.find('span.lbcg-card-text').length);
        gridBodyEl.removeClass('lbcg-grid-' + currentGridSize).addClass('lbcg-grid-' + gridColsCount);
        gridBodyEl.html(gridItems);
    }

    /**
     * Remove or add star in middle of grid
     */
    function changeFreeSpaceItem(add) {
        const gridItems = $('div.lbcg-card-body-grid').find('span.lbcg-card-text'),
            index = Math.round(gridItems.length / 2) - 1;
        if (add) {
            $(gridItems[index]).html(LBC['freeSquareWord']);
        } else {
            const words = $('#lbcg-body-content').val().split("\n");
            $(gridItems[index]).html(words[index]);
        }
    }

    /**
     * On sidebar header click
     */
    $('div.lbcg-sidebar-header').on('click', function () {
        const sidebarHeader = $(this).parent();
        if (sidebarHeader.hasClass('collapsed')) {
            sidebarHeader.removeClass('collapsed')
        } else {
            sidebarHeader.addClass('collapsed')
        }
    });

    /**
     * On title change
     */
    $('#lbcg-title').bind('input propertychange', function () {
        $('div.lbcg-card-header span.lbcg-card-header-text').html($(this).val());
    });

    /**
     * On subtitle letters change
     */
    $('label.lbcg-label--single input.lbcg-input').bind('input propertychange', function () {
        const letterElements = $('div.lbcg-card-subtitle span.lbcg-card-subtitle-text span');
        const $this = $(this);
        const id = $this.attr('id');
        const index = id.replace('lbcg-subtitle-', '') - 1;
        $(letterElements[index]).html($this.val());
    });

    /**
     * On bingo card content change
     */
    $('#lbcg-body-content').bind('input propertychange', function () {
        const $this = $(this),
            words = $this.val().split("\n"),
            gridItems = $('div.lbcg-card-body-grid span.lbcg-card-text'),
            includeFreeSpace = $('#lbcg-free-space-check').is(':checked');
        for (let i = 1; i <= gridItems.length; i++) {
            if (Math.round(gridItems.length / 2) === i && includeFreeSpace) {
                $(gridItems[i - 1]).html(LBC['freeSquareWord']);
            } else {
                $(gridItems[i - 1]).html(words[i - 1]);
            }
        }
    });

    /**
     * On grid size change
     */
    $('#lbcg-grid-size').on('change', function () {
        const gridSize = $("#lbcg-grid-size").val(),
            freeSpaceEl = $("#lbcg-free-space-check");
        if (gridSize === 'grid-4x4') {
            window.LBCIncludeFreeSpace = freeSpaceEl.is(':checked');
            freeSpaceEl.prop("checked", false);
            freeSpaceEl.parent().hide();
        } else {
            freeSpaceEl.prop("checked", window.LBCIncludeFreeSpace);
            freeSpaceEl.parent().show();
        }
        drawNewGrid();
    });

    /**
     * On font change
     */
    $('#lbcg-font').on('change', function () {
        document.documentElement.style.setProperty('--lbcg-card-header-font-family', LBC['fonts'][$(this).val()]['name'] + ', sans-serif');
    });

    /**
     * On free space checkbox change
     */
    $('#lbcg-free-space-check').change(function () {
        changeFreeSpaceItem(this.checked);
    });
});
