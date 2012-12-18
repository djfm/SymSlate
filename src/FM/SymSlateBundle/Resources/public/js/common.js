$(function(){
	$('select.autosubmit').change(function(){
		$(this).parents('form').submit();
	});
	$('textarea.HTML').each(function(i,t){
		CKEDITOR.replace(t);
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