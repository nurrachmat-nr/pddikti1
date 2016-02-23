function openLOV(button_id, act, posx, posy, ret_function, width, height) {
	var button = document.getElementById(button_id);
	var block_id = "sevimalov_block";
	var block = document.getElementById(block_id);

	if (!posx)
		posx = 0;
	if (!posy)
		posy = 0;
	if (!width)
		width = '500px';
	if (!height)
		height = '100px';

	pushPopWindow(block_id);
	
	$('#sevimalov_div').html('<span style="color:white;background-color:#cc0000">Loading...</span>');
    
	ajaxurl = g_abs_url + "akad.php/ajax/"+act;

	$("#loadinglov").show();
	$.ajax({
		type: "POST",
		url: ajaxurl,
		data: {"ret_function":ret_function},
		success: function(ret){
			$("#loadinglov").hide();
			$('#sevimalov_div').html(ret);
		}
	});

	block.style.left = findPosX(button) + posx + "px";
	block.style.top = findPosY(button) + posy + "px";
	$("#"+block_id).css("width",width);
	$("#"+block_id).css("min-height",height);

	$("#"+block_id).show();
	
}

function searchLOV(act){
	ajaxurl = g_abs_url + "index.php/ajax/"+act;
	$("#loadinglov").show();
	$.ajax({
		type: "POST",
		url: ajaxurl,
		data: {"entry":1, "keyword":$("#sevimalov_keyword").val()},
		success: function(ret){
			$("#loadinglov").hide();
			$('#sevimalov_div').html(ret);
		}
	});
}

function navigateLOV(act, nav) {
	ajaxurl = g_abs_url + "index.php/ajax/"+nav+"/act/"+act;
	$("#loadinglov").show();
	$.ajax({
		type: "POST",
		url: ajaxurl,
		success: function(ret){
			$("#loadinglov").hide();
			$('#sevimalov_div').html(ret);
		}
	});
}

function resetLOV(act){
	ajaxurl = g_abs_url + "index.php/ajax/reset/act/"+act;
	$("#loadinglov").show();
	$.ajax({
		type: "POST",
		url: ajaxurl,
		success: function(ret){
			$("#loadinglov").hide();
		}
	});
}

function generateSevimaBlock() {
	jQuery("<div />")
		.attr("id", "sevimalov_block")
		.attr("class", "pop_window")
		.appendTo(jQuery("body"));
		
	$('#sevimalov_block').append('<div id="loadinglov" style="float:left;position:absolute;right:25px;top:3px"><img src="'+ g_abs_url +'application/assets/images/loading.gif" /></div>');
	$('#sevimalov_block').append('<div id="sevimalov_div"></div>');
}

$(document).ready(function() {
	generateSevimaBlock();
	$(function() {
		$( "#sevimalov_block" ).draggable();
	});
});
