submit_translation = function(message_id, classification_id, translation_id, language_id)
{
	var text = $('#translation_text_'+classification_id).val();
	
	$.post('../submissions/create',{
			message_id: message_id,
			classification_id: classification_id,
			translation_id: translation_id,
			language_id: language_id,
			text: text
		},
		function(data){
			console.log(data);
		},
		'json');
}
