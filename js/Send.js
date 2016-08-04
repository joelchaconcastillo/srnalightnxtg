
 function fileSelected(file,Name) {
        var file =file ;
        if (file) {
          var fileSize = 0;
          if (file.size > 1024 * 1024)
            fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
          else
            fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';
		 
		    $("#"+Name+" #fileName").html('Name: ' + file.name);
		    $("#"+Name+" #fileSize").html('Size: ' + fileSize);
		    $("#"+Name+" #fileType").html('Type: ' + file.type);
        }
      }
var Bar,val;
 function uploadFiles(file1,file2,file3,file4,script,progress) {
		 Bar=progress;
		 Loading(true);
        var fd = new FormData();
        fd.append("fileUpload1", file1[0]);
	    fd.append("fileUpload2", file2[0]);
		fd.append("fileUpload3", file3[0]);
		fd.append("fileUpload4", file4[0]);
		fd.append("Origen", "Indexar_Genoma");
		fd.append("Id_Organismo",document.getElementById('Select_Organismo').value);
		fd.append("Ngenome",document.getElementById('Ngenome').value);
		fd.append("Version",document.getElementById('Version').value);
		fd.append("indexar",document.getElementById('indexar').checked);
		fd.append("Organismo",document.getElementById('txtOrganismo').value);
		fd.append("Ecotipo",document.getElementById('txtEcotype').value);
        var xhr = new XMLHttpRequest();
        xhr.upload.addEventListener("progress", uploadProgress, false);
        xhr.addEventListener("load", uploadCompleteGenome, false);
        xhr.addEventListener("error", uploadFailed, false);
        xhr.addEventListener("abort", uploadCanceled, false);
        xhr.open("POST", script);
        xhr.send(fd);
      }

      function uploadProgress(evt) {
        if (evt.lengthComputable) {
          var percentComplete = Math.round(evt.loaded * 100 / evt.total);
          $("#Progreso_Numero").html(percentComplete.toString() + '%');
		  $("#Progreso").val(evt.loaded  / evt.total);
		 
        }
        else {
          document.getElementById('Progreso_Numero').innerHTML = 'unable to compute';
        }
      }

      function uploadCompleteGenome(evt) {
        /* This event is raised when the server send back a response */	   
	   //$("#Directory").html(evt.target.responseText);
		LoadGenomes();
		$("#Ngenome").val("");
		$("#Version").val("");
		$("#File1").val("");
		$("#File2").val("");
		$("#File3").val("");
		$("#File4").val("");
		$("#Progreso_Numero").html("0%");
		$("#Progreso").val("");
		 Loading(false);
		alert("The elements has been processed");
	/*if(evt.target.reponseText)
		{
		   $("#formUpload").slideDown(1100);
		  
		}*/
		  
      }
      function uploadFailed(evt) {
        alert("There was an error attempting to upload the file.");
      }

      function uploadCanceled(evt) {
        alert("The upload has been canceled by the user or the browser dropped the connection.");
      }
