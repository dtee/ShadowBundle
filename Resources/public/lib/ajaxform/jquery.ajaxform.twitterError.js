(function($) {
    var options = {
        class_bad : 'error',
        class_good : '',
        selector_error : '.help-inline'
    };

    options.error_renderer = function(params) {
        var $container = params.container;
        var errors = params.errors;

        var $controlGroup = $container.parents('.control-group');
        if ($controlGroup.length > 0) {
            $container = $controlGroup;
        }

        $error = $container.find(this.options.selector_error);

        if ($error.length == 0) {
            $error = $('<span class="help-inline" />');

            var $input = $container.find('#' + params.index);
            if ($input.length > 0) {
                var $controls = $input.parents('.controls');

                if ($controls.length == 0) {
                    $input.after($error);
                }
                else {
                    $controls.append($error);
                }
            }
            else {
                $container.append($error);
            }
        }

        if (errors != null) {
            $container.addClass(this.options.class_bad).removeClass(
                    this.options.class_good);
            $error.toggle(true).html(errors.join('<br/>'));
        }
        else {
            $container.addClass(this.options.class_good).removeClass(
                    this.options.class_bad);
            $error.toggle(false).html('');
        }
    };

    if ($.ajaxForm) {
        $.ajaxForm(options);

        // Add listener to sessionStart
    }
})(jQuery);
