var upload;

function initRefresh() {
	$(document).keydown(function(e) {
		var ev = (window.event) ? window.event : e;
		var key = (ev.keyCode) ? ev.keyCode : ev.which;
		
		if(key == 116) {
			$('#act').val('');
			$('#main_form')[0].submit();
			
			return false;
		}
		
		return true;
	});
}

function showChooseFoto() {
	$("#foto").click();
}

function chooseFoto() {
	if(upload)
		uploadFoto();
}

function setUploadFoto() {
	upload = true;
	showChooseFoto();
}

function uploadFoto() {
	var form = $('#main_form')[0];
	var target = form.target;
	
	showWait();
	$('#act').val('uploadfoto');
	
	form.target = "upload_iframe";
	form.submit();
	form.target = target;
}

function deleteFoto() {
	var hapus = confirm("Apakah anda yakin akan menghapus foto ini?");
	if(hapus) {
		var form = $('#main_form')[0];
		var target = form.target;
		
		showWait();
		$('#act').val('deletefoto');
		
		form.target = "upload_iframe";
		form.submit();
		form.target = target;
	}
}

function showWait() {
	$("#div_loading").show();
}

function hideWait() {
	$("#div_loading").hide();
}

jQuery.fn.center = function (obj) {
	var loc = obj.offset();
	
	this.css("top",(obj.outerHeight() - this.outerHeight()) / 2 + loc.top + 'px');
	this.css("left",(obj.outerWidth() - this.outerWidth())  / 2 + loc.left+ 'px');
	
	return this;
}