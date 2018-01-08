<?php
/*
	* File: get-file.php
	* Date: 12/12/2017
	* Author: Sherwin Fong
	*
	* Project 5, P2
	* Description: PHP code to handle displaying files. When clicking
		on a file link, the user will be sent to a url ending in
		"get-file.php?name=...", with name being a	tiger128,3 hash
		of the actual filename. 
*/


# figure out the location of the json file written out, and the location
# of the uploaded files. Save the home address and suppress errors so
# they do not show up in the browser.
# only done when testing on UMBC gl server
//error_reporting(E_ERROR);
//$path = preg_replace('/\/swe2017\/(\w+)\/.*/', '/\1/read-write/', getcwd());
//$dataFile = $path . 'fileData.json';
//$directory = $path;
//$sweURL = "https://swe.umbc.edu/~sfong2/cs433/web/p2/";
//$homeLink = "<a href='" . $sweURL . "'>Home</a></h2>"

//for testing on localhost, use this. I tested on localhost:8000
$dataFile = "./fileData.json";
$directory = "./userfiles/";
$homeLink = "<a href='http://localhost:8000'>Home</a>";

$currHash = $_GET['name']; //this is a hash value
	
if ($currHash){
	//check to make sure name param exists
	//get the filename and check the hash
	$fileData = getFileData($currHash);
	if ($fileData){
		$hashRes = hashCheck($currHash, $fileData['name']);
		if ($hashRes){
			displayFile($fileData);
		}			
	}
	else{
		//display error page with this message
		displayError("Error. File not found.");
	}
}

function getFileData($hashVal){
	//function to get the file data based on the hash key
	global $dataFile;
	$dataArr = json_decode(file_get_contents($dataFile), true);
	if ($dataArr){
		//may be null
		$fileData = $dataArr[$hashVal];
		return $fileData;			
	}
	else{
		return NULL;
	}	
}

function hashCheck($hashVal, $filename){
	//function hashes the file name to see if it matches
	//what was in the json file
	$expected = hash('tiger128,3', $filename);
	if ($hashVal == $expected){
		return true;
	}
	else{
		return false;
	}
}

function displayError($message){
	//error message will have the actual headers with link back to home
	global $homeLink;
?>
	<head>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.6.1/css/bulma.min.css"/>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
	</head>
	<body>
		<h2 class='title is-4'><?php
		echo $message;
		echo "<br/><br/>";
		echo $homeLink;
		echo "</h2>";
		?>
	</body>
<?php	
}

function displayFile($fileData){
	//see what type of file it is and call the appropriate function
	$type = $fileData['type'];
	if ($type == "image/png" || $type == "image/gif" || $type == "image/jpeg"){
		displayImg($fileData['name']);
	}
	else if ($type == "text/plain"){
		displayTxt($fileData['name']);
	}
	else if ($type == "text/html"){
		displayHTML($fileData['name']);
	}
	else{
		
	}
}

function displayHTML($filename){
	$currPath = "./userfiles/" . $filename;
	$openFile = fopen($currPath, "r");
	if ($openFile){
		while (($line = fgets($openFile)) != false){
			echo $line;
		}
	}
	else{
		//display error showing file could not be opened
		$message = "Error opening file " . $filename . ".";
		displayError($message);
	}
}

function displayTxt($filename){
	global $homeLink;
	$currPath = "./userfiles/" . $filename;
	$openFile = fopen($currPath, "r");
	if ($openFile){
		//no css headers. Just print the text
		?>
		<pre style="word-wrap: break word; white-space:pre-wrap;">
		<?php
		while (($line = fgets($openFile)) != false){
			echo $line;
		}
		echo "</pre>";
		echo $homeLink;
	}
	else{
		//display error showing file could not be opened
		$message = "Error opening file " . $filename . ".";
		displayError($message);
	}
}

function displayImg($filename){
	global $directory;
	$currPath = $directory . $filename;
	
	$currMime = mime_content_type($currPath);
	$headerStr = "Content-Type:" . $currMime;
	header($headerStr);
	readfile($currPath);
}
?>