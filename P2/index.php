	<!DOCTYPE html>
	<!--
	* File: index.php
	* Date: 12/12/2017
	* Author: Class Instructor (initial HTML code as viewable from a sample
		page in the browser),
		Sherwin Fong (PHP code, modified HTML tags so JS can target them)
	*
	* PHP-Project, P2
	* Description: The main index.php file provided with PHP code I added.
		It shows the files upload and options to upload new files, as well as 
		handle file deletion. The file upload and file viewing functionalities
		are implemented in separate PHP pages
	-->
<html>
	<head>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.6.1/css/bulma.min.css"/>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
	</head>
	<body>
		<div class="container">
		<h1 class="title is-1">File Manager</h1>
		<table class="table">
			<thead>
				<tr>
				<th>File Name 
					<a href="index.php?sort=name&order=ascending"><i class="fa fa-arrow-up" aria-hidden="true"></i></a>
					<a href="index.php?sort=name&order=descending"><i class="fa fa-arrow-down" aria-hidden="true"></i></a>
				</th>
				<th>File Size
					<a href="index.php?sort=size&order=ascending"><i class="fa fa-arrow-up" aria-hidden="true"></i></a>
						<a href="index.php?sort=size&order=descending"><i class="fa fa-arrow-down" aria-hidden="true"></i></a>
				</th>
				<th>File Type
				   <a href="index.php?sort=type&order=ascending"><i class="fa fa-arrow-up" aria-hidden="true"></i></a>
						<a href="index.php?sort=type&order=descending"><i class="fa fa-arrow-down" aria-hidden="true"></i></a>
				</th>
				<th>Last Modified
					<a href="index.php?sort=time&order=ascending"><i class="fa fa-arrow-up" aria-hidden="true"></i></a>
						<a href="index.php?sort=time&order=descending"><i class="fa fa-arrow-down" aria-hidden="true"></i></a>
				</th>
				</tr>
			</thead>
			<!--php code to handle sorting-->
			<?php			
				# figure out the location of the json file written out, and the
				# location of the uploaded files.
				# Suppress errors so they do not show up in the browser.
				# only done when testing on school servers
				//error_reporting(E_ERROR);
				//$path = preg_replace('/\/swe2017\/(\w+)\/.*/', '/\1/read-write/', getcwd());
				//$dataFile = $path . 'fileData.json';
				//$directory = $path;
				
				//for testing on localhost, use this
				$dataFile = "./server/fileData.json";
				$directory = "./server/userfiles";
				
				$orgArr = json_decode(file_get_contents($dataFile), true);
				$fileArr = $orgArr; //save the orignal array with hash vals
				//these may be null
				$order = $_GET['order'];
				$sortBy = $_GET['sort'];
				//sort by name ascending default
				if ($sortBy == false){
					$sortBy = 'name';
				}
				if ($order == false){
					$order = 'ascending';
				}
				mainSort($sortBy, $order); //sort the array
				
				?>
				<tbody id="fileList">
				<?php
				foreach ($fileArr as $currFile){
					$hashLink = "/server/get-file.php?name=" . $currFile['hash'];
					$filename = $currFile['name'];
					$huSize = genHuSize($currFile['size']);
					$huType = getHuType($currFile['type']);
					$huTime = $currFile['tStamp'];
					
					$linkStr = '<a href="'. $hashLink . '">' . $filename .'</a>';
					
					echo "<tr><td>" . $linkStr . "</td>";
					echo "<td>" . $huSize . "</td>";
					echo "<td>" . $huType . "</td>";
					echo "<td>" . $huTime . "</td></tr>";
					
				}
				//example for reference
				/*<tr>
					<td>
						<a href="/server/get-file.php?name=527e450f758d13d16ef81484b0d13acd">Sorry.png</a>
					</td>
					<td>7.37K</td>
					<td>PNG Image </td>
					<td>2017-12-07 15:15:11</td>
				</tr>				
				</tbody>*/
				
				function getHuType($type){
					$vals = explode("/", $type);
					if ($vals[1] == "html"){
						return "html";
					}
					else {
						$toRet = "" . $vals[1] . " " . $vals[0] . "";
						return $toRet;
					}					
				}
				
				function genHuSize($sizeVal){
					//function to generate human readable size
					if ($sizeVal >= 1000){
						return ($sizeVal / 1000) . "k";
					}
					else{
						return $sizeVal;
					}
				}
				
				//name, size, type, or time?
				function mainSort($sortBy, $order){
					global $fileArr;
					if ($order == 'descending'){
						switch($sortBy){
							case "name":
								usort($fileArr, "revName");
							break;
							case "size":
								usort($fileArr, "revSize");
							break;
							case "type":
								usort($fileArr, "revType");
							break;
							case "time":
								usort($fileArr, "revTime");
							break;
							default:
								return null;
							break;
						}
					}
					else{
						//assume ascending						
						switch($sortBy){
							case "name":
								usort($fileArr, "nameCMP");
							break;
							case "size":
								usort($fileArr, "sizeCMP");
							break;
							case "type":
								usort($fileArr, "typeCMP");
							break;
							case "time":
								usort($fileArr, "timeCMP");
							break;
							default:
								return null;
							break;
						}
					}
				}
				
				function nameCMP($a, $b){
					return strcmp($a['name'], $b['name']);
				}
				
				function revName($a, $b){
					//place a and b in backwards
					return strcmp($b['name'], $a['name']);
				}
				
				function sizeCMP($a, $b){
					if ($a['size'] == $b['size']){
						return 0;
					}
					else if ($a['size'] < $b['size']){
						return -1;
					}
					else{
						return 1;
					}
				}
				
				function revSize($a, $b){
					//reverse logic
					if ($a['size'] == $b['size']){
						return 0;
					}
					else if ($a['size'] < $b['size']){
						return 1; //!!!
					}
					else{
						return -1; //!!!
					}
				}
				
				function typeCMP($a, $b){
					return strcmp($a['type'], $b['type']);
				}
				
				function revType($a, $b){
					//place a and b in backwards
					return strcmp($b['type'], $a['type']);
				}
				
				function timeCMP($a, $b){
					if ($a['uTime'] == $b['uTime']){
						return 0;
					}
					else if ($a['uTime'] < $b['uTime']){
						return -1;
					}
					else{
						return 1;
					}
				}
				
				function revTime($a, $b){
					//reverse logic
					if ($a['uTime'] == $b['uTime']){
						return 0;
					}
					else if ($a['uTime'] < $b['uTime']){
						return 1; //!!!
					}
					else{
						return -1; //!!!
					}
				}
			?>
		</table>
			<div class="card">
  			<header class="card-header">
    			<p class="card-header-title">Delete Files</p>
    			<a class="card-header-icon" aria-label="show files to delete" id="delete-button">
      				<span class="icon">
        				<i class="fa fa-angle-down" aria-hidden="true"></i>
      				</span>
    				</a>
  			</header>
				<div class="card-content is-hidden" id="del-form">
					<div class="content">
						<form method="get" action="index.php" >
							<?php
							foreach ($fileArr as $currFile){
								$currHash = $currFile['hash'];
								$currName = $currFile['name'];
								echo '<div class="field"><label class="checkbox">';
								echo '<input type="checkbox" name="del[]" value="'. $currHash .'">' . $currName;
								echo '</label></div>';
							}
							/* for reference
							<div class="field">
								<label class="checkbox">
									<input type="checkbox" name="del[]" value="dd42e59e77cad1d542c62a02b3a5dcd3">code_bye.html
								</label>
							</div>
							*/
							?>
							<input type="hidden" value="delete" name="action"/>
							<input type="submit" class="button" value="Delete Selected Files"/>					
						</form>
						<?php
							$delArr = $_GET['del'];
							$deleteCount = 0;
							foreach ($delArr as $item){
								$currItem = $orgArr[$item];
								if ($currItem){
									//sometimes dummy items get sent in
									$currName = $currItem['name'];
									unset($orgArr[$item]);
									$writeResult = file_put_contents($dataFile, json_encode($orgArr, true));
									if ($writeResult == false){
										//cannot modify the metadata results in error
										echo "Sorry, unable to delete file: " . $currName;
									}
									else{
										$fullPath = "" . $directory . "/" . $currName . "";
										unlink($fullPath);
										$deleteCount++;
									}								
								}
							}
							if ($deleteCount > 0){
								//only refresh page if at least one delete happened
								$deleteCount = 0;
								header("Refresh:0");
							}
							
							
						?>
					</div>
				</div>
			</div>
			<div class="card">
				<header class="card-header">
					<p class="card-header-title"> Upload Files </p>
          <a class="card-header-icon" aria-label="show upload form" id="upload-button">
            <span class="icon">
							<i class="fa fa-angle-down" aria-hidden="true"></i>
						</span>
					</a>
				</header>
				<div class="card-content is-hidden" id="upload-form">
					<div class="content">
						<form method="post" action="/server/upload.php" onsubmit="return uploadValidate()" enctype="multipart/form-data">
							<div class="file has-name field">
								<label class="file-label">
									<input class="file-input" type="file" name="files[]" id="file1">
										<span class="file-cta">
											<span class="file-icon">
												<i class="fa fa-upload"></i>
											</span>
											<span class="file-label">
												Choose a file...
											</span>
										</span>
										<span class="file-name" id="filename1">
										</span>
								</label>
							</div>
							<div class="file has-name field">
                <label class="file-label">
                  <input class="file-input" type="file" name="files[]" id="file2">
                    <span class="file-cta">
                      <span class="file-icon">
                        <i class="fa fa-upload"></i>
											</span>
                      <span class="file-label">
												Choose a file...                                                       
											</span>
										</span>
										<span class="file-name" id="filename2">
										</span>
								</label>
							</div>
							<div class="file has-name field">
								<label class="file-label">
									<input class="file-input" type="file" name="files[]" id="file3">
										<span class="file-cta">
											<span class="file-icon">
												<i class="fa fa-upload"></i>
											</span>
											<span class="file-label">
												Choose a file...
											</span>
										</span>
										<span class="file-name" id="filename3">
										</span>
								</label>
							</div>
							<div class="file has-name field">
                <label class="file-label">
                  <input class="file-input" type="file" name="files[]" id="file4">
                    <span class="file-cta">
                      <span class="file-icon">
                        <i class="fa fa-upload"></i>
											</span>
                      <span class="file-label">
												Choose a file...                                                       
											</span>
										</span>
										<span class="file-name" id="filename4">
										</span>
								</label>
							</div>
							<div class="file has-name field">
                <label class="file-label">
                  <input class="file-input" type="file" name="files[]" id="file5">
                    <span class="file-cta">
                      <span class="file-icon">
                        <i class="fa fa-upload"></i>
											</span>
                      <span class="file-label">
												Choose a file...                                                       
											</span>
										</span>
										<span class="file-name" id="filename5">
										</span>
								</label>
							</div>
							<input type="hidden" value="upload" name="action"/>
							<input type="submit" class="button" value="Upload Selected Files"/>		
						</form>
					</div>
				</div>
			</div>
		<script src = 'js/main.js'></script>
	</body>

</html>
