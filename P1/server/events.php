<?php

	/* File: events.php
	* Date: 12/8/2017
	* Author: Sherwin Fong
	*
	* PHP-Project, P1
	* Description: The PHP code to handle AJAX requests every time
		the calendar is modified. Much of this code is lifted from
		433-JS-Project/P2/server/shout.php, but we needed to define how to
		handle the JSON data, it needed both add and delete	functionality,
		and it needed to handle POST requests.
	*/
	
  # figure out the location of the json file written out
	# only done when testing on UMBC gl server
	//$path = preg_replace('/\/swe2017\/(\w+)\/.*/', '/\1/read-write/', getcwd());
  //$jsonFile = $path . 'events.json';
	
	# when testing on localhost, use this:
	$jsonFile = "./events.json";
	
	if ($_SERVER['REQUEST_METHOD'] == 'GET'){
		if($_REQUEST['start'] && $_REQUEST['end'] && $_REQUEST['_']){
			//check the params
			//output only items within the bounds of the user request
			$dayData = get_json_data(); //parse the json file into array
			if ($dayData){
				//may be null if file not populated yet
				
				//what data actually gets sent based on params
				$outArr = array(); 
				
				$start = $_REQUEST['start'];
				$end = $_REQUEST['end'];
				foreach ($dayData as $item){
					if ($item['end'] <= $end || $item['start'] >= $start){
						$outArr[] = $item;
					}
				}
				//send out the response text by printing
				print json_encode($outArr);
			}
		}
		else{
			//send out an error response text
			print json_encode(array('result' => 'false', 'message' => 'Missing start, end, or underscore args.'));
		}		
	}
	else if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		if($_REQUEST['action'] == 'create'){
			if ($_REQUEST['title'] && $_REQUEST['start'] && $_REQUEST['end']){
				//check the params
				//add in a new item; begin by initializing key-value pairs
				$title = $_REQUEST['title'];
				$start = $_REQUEST['start'];
				$end = $_REQUEST['end'];
				$id = uniqid();			
				$newItem = array('id' => $id, 'title' => $title, 'start' => $start, 'end' => $end);
				//add it into the json file
				$addResult = addData($newItem);
				//may have been unable to write the file
				//response text will state whether or not it happend okay
				if ($addResult){
					//file wrote with new event
					$createResponse = array('result' => 'true', 'message' => 'Event added successfully.');
					print json_encode($createResponse);
				}
				else{
					//file unable to write
					$createResponse = array('result' => 'false', 'message' => 'Event unable to be added.');
					print json_encode($createResponse);
				}
			}
			else{
				//send out an error response text if args missing
				print json_encode(array('result' => 'false', 'message' => 'Missing start, end, or title args.'));
			}
		}
		else if ($_REQUEST['action'] == 'delete'){
			if ($_REQUEST['id']){
				//check the params
				//take out the id in question
				$currId = $_REQUEST['id'];
				$deleteResult = deleteData($currId);
				if ($deleteResult){
					//file able to write with removed event
					$createResponse = array('result' => 'true', 'message' => 'Event deleted successfully.');
					print json_encode($createResponse);
				}
				else{
					//file unable to write
					$createResponse = array('result' => 'false', 'message' => 'Event unable to be deleted.');
					print json_encode($createResponse);
				}				
			}
			else{
				//send out an error response text if id arg missing
				print json_encode(array('result' => 'false', 'message' => 'Missing id arg.'));				
			}
		}
		else{
			print json_encode(array('result' => 'error', 'message' => 'Missing action argument'));
		}		
	}
	else{
		//error with unrecognized/invalid request method
		array('result' => 'error', 'message' => 'Unrecognized request method');
	}
	
	function addData($item){
		//function to add a new item to list
		global $jsonFile;
		//get the current existing data
		$dataArr = json_decode(file_get_contents($jsonFile), true);
		if ($dataArr){
			//add new item to start of existing
			array_unshift($dataArr, $item);
			//write it back to the file
			$writeResult = file_put_contents($jsonFile, json_encode($dataArr, true));
			return $writeResult;
		}
		else{
			//create a new json file
			$dataArr[] = $item;
			//write a new json file
			$writeResult = file_put_contents($jsonFile, json_encode($dataArr, true));
			return $writeResult;
		}
	}
	
	function deleteData($id){
		//function to delete item to list
		global $jsonFile;
		//get the current existing data
		$dataArr = json_decode(file_get_contents($jsonFile), true);
		//loop through array by numeric index
		for ($j = 0; $j < sizeof($dataArr); $j++){
			$currItem = $dataArr[$j];
			if ($currItem['id'] == $id){
				//if the id found, remove the item and break
				unset($dataArr[$j]);
				break;
			}
		}	
		$writeResult = file_put_contents($jsonFile, json_encode($dataArr, true));
		return $writeResult;
	}
	
	function get_json_data(){
		//function to parse the json file
		global $jsonFile;
		$dataArr = json_decode(file_get_contents($jsonFile), true);
		if ($dataArr == NULL){
			//return an empty array
			return array();
		}
		else{
			//return the newly filled array
			return $dataArr;
		}
	}

?>