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

function addNewPlayer(element) {
	// Clone self
	var container = $(element).parents('.playercharacter').clone(true);
	$(element).parents('#form_players').append(container);
	
	// Add Elements
	var template = $('#player_template');
	var templateHtml = template.html();
	var count = $('#form_players > div').length;
	templateHtml = templateHtml.replace(/index/gi, 'player' + count);
	
	var newGameDiv = $(templateHtml);
	$(element).parents('.playercharacter').html(newGameDiv);
	
	newGameDiv.find('.text.field input').each(function() {
		var availableTags = charNames;
		if ($(this).parent().hasClass('username'))
			availableTags = playerNames;
			
		$(this).autocomplete({
			source: availableTags,
			autoFocus: true,
			delay: 0
		});
		
		// For character, we should show more description
		/*._renderItem = function( ul, item ) {
			return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( "<a>" + item.label + "<br>" + item.desc + "</a>" )
				.appendTo( ul );
		};*/
	});
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
