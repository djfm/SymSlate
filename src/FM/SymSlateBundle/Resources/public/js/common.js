$(function(){
	$('select.autosubmit').change(function(){
		$(this).parents('form').submit();
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
});


next_page = function()
{
	$('#page').val(parseInt($('#page').val())+1).parents('form').submit();	
}

previous_page = function()
{
	$('#page').val(parseInt($('#page').val())-1).parents('form').submit();	
}

goto_page = function(page)
{
	$('#page').val(page).parents('form').submit();	
}