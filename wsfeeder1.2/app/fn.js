var xls=true;
function getFileExtension(filename){
  var ext = /^.+\.([^.]+)$/.exec(filename);
  return ext == null ? "" : ext[1];
}
function ValidateSingleInput(oInput) {
	var _validFileExtensions = [".xlsx",".xls"];  
    if (oInput.type == "file") {
        var sFileName = oInput.value;
		var ext=getFileExtension(sFileName);
         if (sFileName.length > 0) {
            var blnValid = false;
            for (var j = 0; j < _validFileExtensions.length; j++) {
                var sCurExtension = _validFileExtensions[j];
                if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                    blnValid = true;
					xls=true;
                    break;
                }
            }
             
            if (!blnValid) {
                alert('Harus File Excel (XLS/X / Bukan *.'+ext+')');
                oInput.value = "";
				xls=false;
                return false;
            }
        }
    }
    return true;
}