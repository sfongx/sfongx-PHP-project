/*
	* File: main.js
	* Date: 12/12/2017
	* Author: Sherwin Fong
	*
	* Project 5, P2
	* Description: Javascript code to handle file upload and delete events
*/
				
/*--event listeners for each file upload field---*/
				
var file = document.getElementById("file1");
file.onchange = function(){
  if(file.files.length > 0){					
    document.getElementById('filename1').innerHTML = file.files[0].name;
  }
};

var file2 = document.getElementById("file2");
file2.onchange = function(){
	if(file2.files.length > 0){                                       
		document.getElementById('filename2').innerHTML = file2.files[0].name;
	}
};

var file3 = document.getElementById("file3");
  file3.onchange = function(){
  if(file3.files.length > 0){                                       
    document.getElementById('filename3').innerHTML = file3.files[0].name;
  }
};

var file4 = document.getElementById("file4");
  file4.onchange = function(){
  if(file4.files.length > 0){                                       
    document.getElementById('filename4').innerHTML = file4.files[0].name;
  }
};

var file5 = document.getElementById("file5");
  file5.onchange = function(){
  if(file5.files.length > 0){                                       
    document.getElementById('filename5').innerHTML = file5.files[0].name;
  }
};

/*---------------------*/

//upload and delete buttons event listener
var up = document.getElementById("upload-button");
up.addEventListener('click',function() {
	document.getElementById('upload-form').classList.toggle('is-hidden');	
	document.getElementById('del-form').classList.add('is-hidden');		
});

var del = document.getElementById("delete-button");
del.addEventListener('click',function() {
	document.getElementById('del-form').classList.toggle('is-hidden');   
	document.getElementById('upload-form').classList.add('is-hidden');       
});
/*---------------*/

function uploadValidate(){
	//validation method: see if user put in any files.
	var currEntry;
	var currName;
	var count = 0;
	
	for (var j = 1; j <= 5; j++){
		currEntry = "filename" + j;
		console.log(currEntry);
		currName = document.getElementById(currEntry).innerHTML;
		if (currName == false){
			count++;
		}		
	}
	if (count == 5){
		alert("Please upload a file");
		return false;
	}
	else{
		return true;
	}
}

