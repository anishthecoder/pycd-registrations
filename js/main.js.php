<?php
?>
<script type="text/javascript">
/**
 * Sends a rendering request to the specified controller and puts the rendered
 * content in the specified target.
 *
 * @argument {string} target The jQuery selector string that will provide the
 *            unique target into which the content will be rendered. This
 *            element will be cleared / emptied before the rendered content is
 *            entered.
 * @argument {string} controller The name of the controller class that will
 *            handle the rendering.
 * @argument {string} renderAction The rendering action that is requested. Refer
 *            to the documentation of the individual controllers.
 * @argument {context array} the parameters that need to be passed for the
 *						rendering.
 */
function render(){
	if (arguments.length === 3){
		target = arguments[0];
		controller = arguments[1];
		renderAction = arguments[2];
		context = {
			<?=Controller::$ID?>: controller,
			<?=Controller::$ACTION?>: renderAction
		};
	}
	else if (arguments.length === 2){
		target = arguments[0];
		context = arguments[1];
	}

  $.blockUI();
	$.get(
		<?=CONTROLLER?>,
		context,
		function(data){
      $(target).empty();
			$(target).append(data);
		})
  .fail(function() {
    smoke.alert("Oops! Something wrong on the server. Try again later.");
  })
  .always(function() {
    $.unblockUI();
  });
}// close render

/**
 *	Sends a rendering request to the specified controller through the fields of
 *	a form, so additional information required for the rendering can be provided.
 *
 * @argument {string} formId attribute of the form that contains the information
 *						required for the rendering.
 * @argument {string} target The jQuery selector string that will provide the
 *            unique target into which the content will be rendered. This
 *            element will be cleared / emptied before the rendered content is
 *            entered.
 * @returns {undefined}
 */
function renderByForm(target, formId){
  $.blockUI();
  $('.<?=Render::$CUSTOM_SCRIPTS_CLASS?>').remove();
	$.get(
		<?=CONTROLLER?>,
		$("#"+formId).serialize(),
		function(data){
      $(target).empty();
			$(target).append(data);
		})
  .fail(function() {
    smoke.alert("Oops! Something wrong on the server. Try again later.");
  })
  .always(function() {
    $.unblockUI();
  });
}// close renderByForm(

/**
 * Sends a POST request to the main controller after serializing the information
 * in the indicated form.
 *
 * @argument {string} Id attribute of the form that needs to be POSTed.
 */
function sendFormRequest(formId){
  $.blockUI();
  $('.<?=Render::$CUSTOM_SCRIPTS_CLASS?>').remove();
	$.post(
		<?=CONTROLLER?>,
		$("#"+formId).serialize(),
		function(data){
			$('body').append(data);
		})
  .fail(function() {
    smoke.alert("Oops! Something wrong on the server. Try again later.");
  })
  .always(function() {
    $.unblockUI();
  });
}

function sendRequest(controller, action){
  $.blockUI();
  $('.<?=Render::$CUSTOM_SCRIPTS_CLASS?>').remove();
	$.post(
		<?=CONTROLLER?>,
		{<?=Controller::$ID?>: controller,
     <?=Controller::$ACTION?>: action},
		function(data){
			$('body').append(data);
		})
  .fail(function() {
    smoke.alert("Oops! Something wrong on the server. Try again later.");
  })
  .always(function() {
    $.unblockUI();
  });
}

function sendFormRequestJson(formId){
  $.blockUI();
  $('.<?=Render::$CUSTOM_SCRIPTS_CLASS?>').remove();
	$.post(
		<?=CONTROLLER?>,
		$("#"+formId).serialize(),
		function(data){
			return data;
		}, "json")
  .fail(function() {
    smoke.alert("Oops! Something wrong on the server. Try again later.");
  })
  .always(function() {
    $.unblockUI();
  });
}

function sendRequestJson(dataToSend){
  $.blockUI();
  $('.<?=Render::$CUSTOM_SCRIPTS_CLASS?>').remove();
	$.post(
		<?=CONTROLLER?>,
		dataToSend,
		function(data){
			$('body').append(data);
		})
  .fail(function() {
    smoke.alert("Oops! Something wrong on the server. Try again later.");
  })
  .always(function() {
    $.unblockUI();
  });
}

function getObject(dataToSend){
  $.blockUI();
  $('.<?=Render::$CUSTOM_SCRIPTS_CLASS?>').remove();
	$.post(
		<?=CONTROLLER?>,
		dataToSend,
		function(data){
			return data;
		}, 'json')
  .fail(function() {
    smoke.alert("Oops! Something wrong on the server. Try again later.");
  })
  .always(function() {
    $.unblockUI();
  });
}


/**
 * Given an input form element, this function determines the form id, and calls
 * the sendFormRequest method with that id.
 *
 * @argument {DOM Object}
 */
function sendForm(obj){
	form = $(obj).parents('form');
	formId = form.attr('id');
	sendFormRequest(formId);
}

function sendFormJson(obj){
	form = $(obj).parents('form');
	formId = form.attr('id');
	return sendFormRequestJson(formId);
}

</script>