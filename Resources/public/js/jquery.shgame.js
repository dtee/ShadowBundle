(function($) {
	function GameForm($editForm) {
		var me = this;
		this.playerNames = $editForm.data('players');
		this.charNames = $editForm.data('characters');
		this.$editForm = $editForm;

		//$editForm.ajaxForm();		-- For now, lets just use form submit
		this.attachAutocomplete();

		// Attach new player button
		$editForm.find('.btn.new-player').click(function () {
			var $container = $editForm.find('#form-players');
			var prototype = $container.data('prototype');
			var $newForm = $(prototype.replace(/__name__/g, $container.children().length));

			// Display the form in the page in an li, before the "Add a tag" link li
			$container.append($newForm);
			me.attachAutocomplete();			// Reattach events
			me.attachClose($newForm);
		});

		// Apply faction class name (edit flow)
		$editForm.find('.character input:text').each(function() {
			var char = $(this).val();
			var faction = me.charNames[char]
			if (faction) {
				$(this).parents('.gamer').addClass(faction);
			}
		});

		$editForm.find('.gamer').each(function() {
			me.attachClose($(this));
		})
	};

	GameForm.prototype.attachClose = function($container) {
		$container.css('position', 'relative');

		var $link = $('<button class="close">x</button>').click(function() {
			$container.remove();
		}).css({
			top: '2px',
			right: '5px',
			position: 'absolute'
		});

		$container.append($link);
	}

	GameForm.prototype.attachAutocomplete = function() {
		var me = this;
		var charNames = [];
		for (var name in me.charNames) {
			charNames.push({
				value: name,
				label: name,
				faction: me.charNames[name]
			})
		}
		this.$editForm.find('input:text').each(function() {
			var availableTags = charNames;
			var type = 'character';
			if ($(this).parent().hasClass('username')) {
				availableTags = me.playerNames;
				type = 'username';
			}

			$(this).autocomplete({
				// Exclude names that are already included
				source: function(request, response) {
					response( $.grep( availableTags, function( value ) {
						var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
						value = value.label || value.value || value;

						// Exclude if already excluded
						var isAlreadySelected = false;
						me.$editForm.find('.' + type + ' input:text').each(function() {
							if (!isAlreadySelected && $(this).val() == value) {
								isAlreadySelected = true;
							}
						});

						return !isAlreadySelected && matcher.test( value );
					}) );
				},
				autoFocus: true,
				delay: 0,
				select: function(event, ui){
					if (ui.item.faction) {
						$(this).parents('.gamer').removeClass('shadow');
						$(this).parents('.gamer').removeClass('neutral');
						$(this).parents('.gamer').removeClass('hunter');

						$(this).parents('.gamer').addClass(ui.item.faction);
					}
				}
			});
		});

		// Chain faction win/loss checkbox
		var winSelector = ".isWin input:checkbox";
		this.$editForm.find(winSelector).click(function() {
			// If shadow and win, then check all shadow wins
			var $element = $(this);
			var isChecked = $(this).is(':checked');

			$.each(['hunter', 'shadow'], function(index, faction) {
				var factionSelector = '.gamer.' + faction + ' ' + winSelector;
				if ($element.parents('.' + faction).length > 0) {
					log('has parent: ' + faction);
					me.$editForm.find(factionSelector).attr('checked', isChecked);
				}
			});
		});
	};

	// Valid to see if the teams are even (number of shadow = hunters)
	GameForm.prototype.validate = function() {
		var errors = [];
		var $hunters = this.$editForm.find('.gamer.hunter');
		var $shadows = this.$editForm.find('.gamer.hunter');

		if ($hunters.length == 0) {
			errors.push('Must have at least one hunter');
		}

		if ($hunters.length == 0) {
			errors.push('Must have at least one shadow');
		}

		if ($hunters.length != $shadows.length) {
			errors.push('Total hunters ' + $hunters.length + ', must equals shadows ' + $shadows.length);
		}

		// Make sure character or player doesn't appear more than once
	}

	/** JQuery Wrapper starts here **/
	var methods = {};

	methods.init = function(options) {
		var $editForm = $(document).find('#add_game');

		if ($editForm.length > 0) {
			new GameForm($editForm);
		}
	}

	$.fn.shgame = function(method) {
		// Method calling logic
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		}
		else {
			if (typeof method === 'object' || !method) {
				return methods.init.apply(this, arguments);
			}
			else {
				$.error('Method ' + method + ' does not exist on jQuery.ajaxForm');
			}
		}
	};
})(jQuery);

$(document).ready(function() {
	$(document).shgame();
});
