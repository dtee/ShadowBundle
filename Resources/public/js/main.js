$(document).ready(function() {
	$('[data-jqtable]').jqtable();
})

function indexGameWinners()
{
    $('.games .game').each(function()
    {
        var winners = [];
        $(this).find('.player.won').each(function() {
            var player = $(this).attr('class').replace('player won ', '');
            winners.push(player);
        });

        $(this).addClass(winners.join(' '));
    });
}

function percentageFormatter(e) {
    var sorter = function(a, b) {
        // sort desc
        return b.y - a.y;
    };
    this.points.sort(sorter);

    var html = '<strong>Game #'+ this.points[0].x +'</strong><br/>';
    for (var index in this.points)
    {
        var point = this.points[index];
        var color = point.series.color;
        if (point.point.factionColor)
        {
            color = point.point.factionColor;
        }

        var place = parseInt(index) + 1;
        var div = '<strong style="color:' + color +';font-weight: bold;display:inline-block;width: 140px;">'
                + place + '. ' + point.series.name +':</strong>' + "\t" + point.y +'%';

        div += '<br/>';

        html += div;
    }

    return html;
}

function addNewPlayer() {
    var $container = $('#form-players');
    var prototype = $container.attr('data-prototype');
    var newForm = prototype.replace(/___name___/g, $container.children().length);

    // Display the form in the page in an li, before the "Add a tag" link li
    $container.append($(newForm));
}

function handleFilterWinners(e) {
    if ($(this).val() == 'all')
    {
        // click on all clear everything
        $("#winner-filter input:checked").each(function() {
            $(this).attr('checked', false);
        });
        $( "#winner-filter" ).buttonset('refresh');
    }
    else
    {
        // Uncheck all
        $("#winner-filter input:checked").each(function() {
            if ($(this).val() == 'all')
                $(this).attr('checked', false);
        });
        $( "#winner-filter" ).buttonset('refresh');
    }

    var values = [];
    $("#winner-filter input:checked").each(function()
    {
        values.push(this.value);
    });

    // Nothing is checked, then show all
    // If all is checked, then show all
    if (values.length == 0 ||
        (values.length > 0 && values[0] == 'all'))
    {
        $('.games .game').toggle(true);
        $('#winner-filter-total').html($('.games .game').length);
        return;
    }

    $('.games .game').toggle(false);
    for (var index in values)
    {
        var value = values[index];
        var css = '.games .game .player.won.' + value;

        // This is for non additive
        //$(css).parents('.game').toggle(true);
    }

    var css = '.games .game.' + values.join('.');
    $(css).toggle(true);
    $('#winner-filter-total').html($(css).length);
}
