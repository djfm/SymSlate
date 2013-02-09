$(function(){
	/*
	$('iframe').load(function() {
	    this.style.height = this.contentWindow.document.body.offsetHeight * 1.3  + 'px';

	    

		console.log('loaded iframe');
		if(ckeditor = $(this).attr('ckeditor'))
		{
			console.log(ckeditor);
			var conf   = {
				
				toolbar: 'Basic',
				extraPlugins: 'autogrow',
				removePlugins: 'elementspath, resize',
				resize_enabled: false,
				on:{
					instanceReady: function(e){
						var a;
						var t = $(e.target);
						if(a = $(t).attr('ckadjust'))
						{
							var h = $('#cke_'+$(t).attr('id')).css('height');
							$('#'+a).css('height',h);
						}
					}
				}
			};
			var editor = CKEDITOR.replace($('#'+ckeditor)[0],conf);
		}

	});*/

	$('textarea.HTML').each(function(i,t){
		var conf   = {
			toolbar: 'Basic',
			height: '500px',
			removePlugins: 'elementspath',
			resize_enabled: false,
			fullPage: true,
			on:{
				instanceReady: function(e){
					var a;
					if(a = $(t).attr('ckadjust'))
					{
						var editor = $('#cke_'+$(t).attr('id'));
						var bar = editor.find('span.cke_top').remove();
						bar.find('span.cke_toolbar_break').remove();
						bar.hide();
						$('#ckeditor_toolbars').append(bar);
						var h = editor.css('height');
						editor.css('border','0');
						$('#'+a).css('height',h);
					}
				}
			}
		}
		var editor = CKEDITOR.replace(t,conf);
	});

});

/*
	    $('textarea.HTML').each(function(i,t){
		var conf   = {
				height: $(t).css('height'), 
				toolbar: 'Basic',
				removePlugins: 'elementspath',
				resize_enabled: false,
				on:{
					instanceReady: function(e){
						var a;
						return;
						if(a = $(t).attr('ckadjust'))
						{
							var h = $('#cke_'+$(t).attr('id')).css('height');
							$('#'+a).css('height',h);
						}
					}
				}
		};
		var editor = CKEDITOR.replace(t,conf);*/