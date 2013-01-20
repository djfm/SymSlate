$(function(){
	$('select.autosubmit').change(function(){
		$(this).parents('form').submit();
	});

	$('legend.toggleable').click(function(e){
		$(e.target).parent().children('div.toggleable').toggle();
	});
	
	$('[same_height]').each(function(i,e){
		var h = $('#'+$(e).attr('same_height')).css('height');
		if(parseInt($(e).css('height')) < parseInt(h))$(e).css('height',h);
		else $('#'+$(e).attr('same_height')).css('height',$(e).css('height'));
	});
	
	$('textarea.HTML').each(function(i,t){
		var conf   = {
				height: $(t).css('height'), 
				toolbar: 'Basic',
				removePlugins: 'elementspath',
				resize_enabled: false,
				on:{
					instanceReady: function(e){
						var a;
						if(a = $(t).attr('ckadjust'))
						{
							var h = $('#cke_'+$(t).attr('id')).css('height');
							$('#'+a).css('height',h);
						}
					}
				}
		};
		var editor = CKEDITOR.replace(t,conf); 
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

