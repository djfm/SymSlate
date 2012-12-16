$(function(){
	$('select.autosubmit').change(function(){
		$(this).parents('form').submit();
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