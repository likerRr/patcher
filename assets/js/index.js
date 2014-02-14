$(function(){
    var $buildResult = $('.build-result'),
        $logTab = $('#show'),
        $logTabBadge = $('a[href="#show"]').find('.badge'),
        $checkBlock = $('#paths-to-check'),
        $source = $('input[type="radio"][name="source"]'),
        $patchPath = $('#patch-path'),
        $checkPatchPath = $('#check-patch-path'),
        $checkForm = $('#make-check-form'),
        $totalFiles = $checkBlock.prev().find('.count');

    /**
     * Submit form with checked paths
     */
    $checkForm.on('submit', function(e) {
        e.preventDefault();
        var $this = $(this),
            type = $source.val(),
            path = $checkPatchPath.val(),
            toCheck = $checkBlock.val(),
            $label = $this.find('.build-result');
        resetLabel();
        showLabel($label, 'label-info', 'Wait...');
        $.post(
            '/patch/check',
            {
                type: type,
                path: path,
                check: toCheck
            },
            function(data) {
                data = $.parseJSON(data);
                if (data.result == 'success') {
                    collapseLogs();
                    resetLabel();
                    showLabel($label, 'label-success', 'Success');
                    $logTab.find('.show-container > .show-log').prepend(data.data.view);
                    initLogEffects();
                    upLogCounter();
                }
                else {
                    showLabel($label, 'label-danger', 'Fail: ' + data.message);
                }
            }
        );
    });

    /**
     * Copy patch path from patch tab to check patch tab
     */
    $patchPath.on('change blur click keydown', function() {
        var text = $(this).val();
        $checkPatchPath.val(text);
    });

    /**
     * Handle when change, click, blur radio button in Check path tab
     */
    $source.on('change click blur', function() {
        var text = $checkBlock.val().trim(),
            source = $source.filter(':checked').val();

        if (source == 'git') {
            text = formatGitChanges(text);
        }

        $checkBlock.val(text);
    });
    /**
     * Handle when change, focus, blur, keyup at changes textarea
     */
    $checkBlock.on('focus blur paste', function(e) {
//        console.log(e.type);
        // 13 - enter, 8 - backspace, 46 - delete, 33,34,35,36,37,38,39,40 - arrows/home/end/PU/PD
        var $this = $(this),
            key         = e.which | e.code | e.keyCode,
            ignoredKeys = [13, 8, 46, 35, 36, 37, 38, 39, 40],
            ctrlKey = e.ctrlKey | false,
            cursorPosition;

//        console.log(ctrlKey, key);
        if (ctrlKey == false && in_array(key, ignoredKeys) === false) {
            setTimeout(function() {
                var text = $this.val().trim(),
                    source = $source.filter(':checked').val();

                // get current cursor position
                cursorPosition = $this.textrange('get', 'position') | 0;
                if (source == 'git') {
                    text = formatGitChanges(text);
                }
                $this.val(text);
                // return cursor after text replacement
                $this.textrange('setcursor', cursorPosition);
            }, 100);
        }
    });

    /**
     * Prepare git changes to confirmable view
     * @param text
     * @returns {string}
     */
    function formatGitChanges(text) {
        var arr = text.split("\n"),
            // detect paths to files
            editedPath  = /diff\s--git\sa(.*)\sb/,
            // detect deleted files
            deletedPath = /(\sdelete mode \d+\s)/,
            // detect created files
            createdPath = /(\screate mode \d+\s)/,
        // detect valid line, that no need to replace
            validLine   = /^(\*edited\*|\*deleted\*|\*created\*)(.+)/,
            // total edited files
            total       = 0,
            edited      = [],
            deleted     = [],
            created     = [],
            newText     = '';

        for (var i = 0; i < arr.length; i++) {
            var line = arr[i];

            if (validLine.test(line)) {
                if (strpos(line, 'edited') !== false) {
                    edited.push(line);
                    total += 1;
                }
                else if (strpos(line, 'created') !== false) {
                    created.push(line);
                }
                else if (strpos(line, 'deleted') !== false) {
                    deleted.push(line);
                }
            }
            else {
                if (editedPath.test(line)) {
                    line = line.replace(editedPath, "*edited* ");
                    edited.push(line);
                    total += 1;
                }
                else if (deletedPath.test(line)) {
                    line = line.replace(deletedPath, "*deleted* /");
                    deleted.push(line);
                }
                else if (createdPath.test(line)) {
                    line = line.replace(createdPath, "*created* /");
                    created.push(line);
                }
            }
        }
        // display total files counter
        $totalFiles.text(total);

        if (created.length > 0) {
            newText += created.join("\n") + "\n";
        }
        if (deleted.length > 0) {
            newText += deleted.join("\n") + "\n"
        }
        newText += edited.join("\n") + "\n";

        return newText;
    }

    /**
     * Handle tab click
     */
    $('#nav-tabs').find('a').on('click', function (e) {
        e.preventDefault();
        var $this = $(this);
        if (!$this.parent().hasClass('disabled')) {
            if ($this.attr('href') == '#show') {
                // reset log counter
                downLogCounter();
            }
            $this.tab('show');
        }
    });
    /**
     * Handle when blur project path to load data
     */
    $('#project').on('blur', function(){
        var $this = $(this),
            $ignoredFiles = $('#ignored'),
            $patchPath = $('#patch-path'),
            $lastUpdateTime = $('#date-time'),
            project = $this.val();
        resetLabel();
        $.post(
            '/project/find',
            {
                project: project
            },
            function (data) {
                data = $.parseJSON(data);
                if (data.result == 'success') {
                    if (data.ignored.length > 0 && $ignoredFiles.val().trim() == '') {
                        $ignoredFiles.val(data.ignored);
                    }
                    if ($patchPath.val().trim() == '') {
                        $patchPath.val(data.patchPath);
                    }
                    if ($lastUpdateTime.val().trim() == '') {
                        $lastUpdateTime.val(data.lastUpdateTime);
                    }
                }
                else {

                }
            }
        );

    });

    /**
     * Hide label, reset classes and text message
     */
    function resetLabel() {
        $buildResult.addClass('hide').removeClass('label-success label-danger label-info').text('');
    }

    /**
     * Close single log block
     */
    $('.show-log').on('click', '.panel-heading > button.close-btn', function(e){
        e.preventDefault();
        var $this = $(this);
        $this.parent().parent().remove();
    })
    /**
     * Collapse/expand single log block
     */
    .on('click', '.panel-heading > button.collapse-btn', function(e){
        e.preventDefault();
        var $this = $(this);
        $this.find('span').toggleClass('glyphicon-collapse-up glyphicon-collapse-down').end().parent().next().toggle();
    })
    /**
     * Quick search in single log block
     */
    .on('keyup', '#search', function(){
        var $this  = $(this),
            $panel = $this.closest('.panel-body'),
            text   = $this.val().toLowerCase(),
            logs   = $panel.find('.log-text');
        for (var i = 0; i < logs.length; i++) {
            if ($(logs[i]).text().toLowerCase().indexOf(text) === -1 && text != '') {
                $(logs[i]).fadeOut('fast');
            }
            else {
                $(logs[i]).fadeIn('fast');
            }
        }

    });

    function showLabel(obj, className, text) {
        obj.addClass(className).removeClass('hide').text(text);
    }

    function collapseLogs() {
        var $panels = $logTab.find('.panel');
        $panels.each(function() {
            var $this = $(this),
                $panelCollapse = $this.find('.size'),
                $panelBody = $this.find('.panel-body');
            if ($panelCollapse.hasClass('glyphicon-collapse-up')) {
                $panelCollapse.toggleClass('glyphicon-collapse-up glyphicon-collapse-down');
                $panelBody.hide();
            }
        });
    }

    /**
     * Handle when submit patch form
     */
    $('#make-patch-form').on('submit', function(e){
        e.preventDefault();
        var $this = $(this),
            $label = $this.find('.build-result');
        resetLabel();
        showLabel($label, 'label-info', 'Wait...');

        var project   = $('#project').val(),
            ignored   = $('#ignored').val(),
            datetime  = $('#date-time').val(),
            patchPath = $('#patch-path').val();
        $.post(
            '/patch/make',
            {
                project:  project,
                patch:    patchPath,
                ignored:  ignored,
                datetime: datetime
            },
            function(data) {
                data = $.parseJSON(data);
                resetLabel();
                if (data.result == 'success') {
                    collapseLogs();
                    showLabel($label, 'label-success', 'Success');
                    $logTab.find('.show-container > .show-log').prepend(data.data.view);
                    initLogEffects();
                    upLogCounter();
                }
                else {
                    showLabel($label, 'label-danger', 'Fail: ' + data.message);
                }
            }
        );
    });

    /**
     * Reset log counter to 0
     */
    function downLogCounter() {
        $logTabBadge.text(0).addClass('hide');
    }

    /**
     * Update log counter by incrementing to 1
     */
    function upLogCounter() {
        var badgeText = $logTabBadge.text(),
            currentCounter;
        if (badgeText == '') {
            $logTabBadge.text(1);
        }
        else {
            currentCounter = parseInt(badgeText);
            $logTabBadge.text(++currentCounter);
        }
        $logTabBadge.removeClass('hide');
    }
});

/**
 * Initialize path hover effects at single log block
 */
function initLogEffects() {
    $('.show-container').find('.panel-body:eq(0) > .log-text').each(function() {
        var $this = $(this);
        $this.html($this.text().replace(/(\\?)(\w+)(\\+)/g, "<span>$1$2$3</span>"));
    })
        .find('span:not(.bimbo)').hover(
        function() {
            var $this     = $(this),
                text      = $this.text(),
                $thisPrev = $this.prevAll(),
                $panel    = $this.closest('.panel-body'),
                prevPath  = $panel.find("span:contains("+text+")");
            prevPath.each(function(){
                var $thisPrevPath = $(this).prevAll();
                if ($thisPrevPath.text() == $thisPrev.text()) {
                    $thisPrevPath.andSelf().addClass('selected-path');
                }
            });
        },
        function() {
            var $this  = $(this),
                text   = $this.text(),
                $panel = $this.closest('.panel-body');
            $panel.find("span:contains(" + text + ")").prevAll().andSelf().removeClass('selected-path');
        }
    );
}