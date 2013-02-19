function fixHeights()
{
	console.log("FIXIN!");
	$('[same_height]').each(function(i,e){

		var reference = $('.'+$(e).attr('same_height'))
		var me = $(e);

		var desired_height = me.height() + reference.outerHeight() - me.outerHeight();

		var threshold = 40;

		if(desired_height > threshold)
		{
			me.height(desired_height);
		}
		
	});
}

var fixHeightsRequested = 0;
function delayedFixHeights()
{
	fixHeightsRequested += 1;
	window.setTimeout(function(){
		fixHeightsRequested -= 1;
		if(fixHeightsRequested == 0)
		{
			fixHeights();
		}
	},500);
}

function loadiframe()
{
	/*
	$(this).ready(function()
	{
		//console.log($(this).contents().find('html'));
		if(!('srcdoc' in $('<iframe/>')[0]))
		{
			console.log(this);
			if(src = $(this).attr('srcdoc'))
			{

			}
		}
	});*/
}

$(document).ready(function(){
	
	
	if(!('srcdocument' in $('<iframe/>')[0]))
	{
		$('iframe[srcdoc]').each(function(i, iframe){
			console.log("Iframe needs js loading");

			iframe.contentWindow.document.open();
			iframe.contentWindow.document.write($(this).attr('srcdoc'));
			iframe.contentWindow.document.close();

			//$(this).contents().find('html').replaceWith();
		});
	}

	$('select.autosubmit').change(function(){
		$(this).parents('form').submit();
	});

	$('legend.toggleable').click(function(e){
		$(e.target).parent().children('div.toggleable').toggle();
	});

	fixHeights();

	$(window).resize(function(){
		delayedFixHeights();
	});

	$('form:has("#page")').submit(function(){
		if(!just_switching_page)
		{
			$('#page').val(1);
		}
		else
		{
			just_switching_page = false;
		}
	});

});

just_switching_page = false;

goto_page = function(page)
{
	just_switching_page = true;
	$('#page').val(page).parents('form').submit();	
}

oops = function(message)
{
	$('#notification').html("<div class='alert'><button type='button' class='close' data-dismiss='alert'>&times;</button><strong>Oops!</strong>&nbsp;"+message+"</div>");
}

unOops = function()
{
	$('#notification').html('');
}

