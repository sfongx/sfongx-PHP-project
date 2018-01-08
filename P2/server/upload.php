<?php
/*
	* File: upload.php
	* Date: 12/12/2017
	* Author: Sherwin Fong
	*
	* PHP-Project, P2
	* Description: PHP code to handle file uploads. Upon uploading a file,
		the user will be sent to this page showing what files could and
		could not be uploaded, with a link to each of them and
		a link back to home.
*/

# figure out the location of the json file written out, and the location
# of the uploaded files. Save the home address and suppress errors so
# they do not show up in the browser.
# only done when testing on school server
//error_reporting(E_ERROR);
//$path = preg_replace('/\/swe2017\/(\w+)\/.*/', '/\1/read-write/', getcwd());
//$dataFile = $path . 'fileData.json';
//$directory = $path;
//$sweURL = "https://swe.umbc.edu/~sfong2/cs433/web/p2/";
//$homeLink = "<a href='" . $sweURL . "'>Home</a></h2>"

# for testing on localhost, use this. I tested on localhost:8000
$directory = __DIR__ . "/userfiles/";
$dataFile = "./fileData.json";
$homeLink = "<a href='http://localhost:8000'>Home</a>";

$fileArr = $_FILES['files'];
$okay2Upload = false;
//$filename = $directory . basename($_FILES['files']['name']);
?>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.6.1/css/bulma.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
</head>
<body>
<?php

echo '<h2 class="title is-4">';

for ($j = 0; $j < sizeof($fileArr); $j++){
	
	if (file_exists($fileArr['tmp_name'][$j])){
		//only work through if the temp file exists
		
		//tell the user which file in the list it is
		$currNum = $j + 1;
		echo "File #$currNum: ";
		
		//securly get the name
		$currName = basename($fileArr['name'][$j]); //basename
		$hashName = hash('tiger128,3', $currName); //tiger128, 3 hash
		$targetFile = $directory . $currName;
		
		//file type and size check
		$currType = $fileArr['type'][$j];
		$currSize = $fileArr['size'][$j];
		
		$typeRes = fileTypeCheck($currType);
		$sizeRes = fileSizeCheck($currSize);
		
		if (file_exists($targetFile)){
			unlink($targetFile);
		}
		
		if ($typeRes && $sizeRes){
			$okay2Upload = true;
		}
		
		///server/get-file.php?name=527e450f758d13d16ef81484b0d13acd
		if ($okay2Upload){
			/*tmp_name is created when file first attempts to go in*/
			$hashSaveRes = addHashData($hashName, $currName, $currType, $currSize);
			$uploadRes = move_uploaded_file($fileArr['tmp_name'][$j], $targetFile);
			if ($hashSaveRes && $uploadRes){
				$newURL = "/server/get-file.php?name=" . $hashName;
				echo 'The file <strong><a href="' . $newURL . '">';
				echo $currName . "</a></strong> has been uploaded.";
			}
			else{
				echo "Error: file unable to be uploaded and/or hashed.";
			}
		}
		else{
			echo "Error. File too big, unsupported type, or exists already.";
		}
		echo "<br/><br/>";
	}
}

echo $homeLink;

echo "</h2>";

function fileSizeCheck($size){
	//note that the project limit was 50 kb, but I increased it to 500kb so
	//that I could test the larger files I had on my computer.
	if ($size > 500000){
		return false;
	}
	else{
		return true;
	}
}

function addHashData($hash, $filename, $filetype, $filesize){
	//function to add the hash data for security
	
	//parse the json file
	global $dataFile;
	$dataArr = json_decode(file_get_contents($dataFile), true);
	
	//generate a timestamp
	$currYear = date('Y');
	$currMonth = date('n');
	$currDay = date('d');
	$currHour = date('H:i:s');
	$currStamp = $currYear . "-" . $currMonth . "-" . $currDay . " " . $currHour;
	
	$unixTime = time();
	
	if ($dataArr){
		$infoArr = ['name'=>$filename, 'type'=>$filetype, 'size'=>$filesize, 'uTime'=> $unixTime,'tStamp'=> $currStamp, 'hash'=>$hash];
		$dataArr[$hash] = $infoArr;
		$writeResult = file_put_contents($dataFile, json_encode($dataArr, true));
		return $writeResult;
	}
	else{
		$infoArr = ['name'=>$filename, 'type'=>$filetype, 'size'=>$filesize, 'uTime'=>$unixTime, 'tStamp'=> $currStamp, 'hash'=>$hash];
		$newArr = array();
		$newArr[$hash] = $infoArr;
		$writeResult = file_put_contents($dataFile, json_encode($newArr, true));
		return $writeResult;
	}	
}

function fileTypeCheck($type){
	$toRet = false;
	switch($type){
		case "image/gif":
			$toRet = true;
		break;
		case "image/png":
			$toRet = true;
		break;
		case "image/jpeg":
			$toRet = true;
		break;
		case "text/plain":
			$toRet = true;
		break;
		case "text/html":
			$toRet = true;
		break;
		default:
			//do nothing, toRet stays false
		break;
	}
	return $toRet;
}
?></body>