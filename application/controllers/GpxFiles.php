<?php
header("Access-Control-Allow-Origin: *");
defined('BASEPATH') OR exit('No direct script access allowed');

class Gpxfiles extends CI_Controller {

	public function index()
	{
		
	}

	public function listWalks() {
		$iterator = new FilesystemIterator("./gpx_files");
		$filelist = array();
		foreach($iterator as $entry) {
			if($entry->getFilename() !== '.DS_Store') {
	        	$filelist[] = $entry->getFilename();
			}
		}
		print_r(json_encode($filelist)); 
	}

	public function getWalkDetails($filename) {
		
		//add back extension
		$gpxfilename = $filename.'.gpx'; 
		$filepath = "./gpx_files/".$gpxfilename; 
		$fileContents = file_get_contents($filepath); 
		//parse file contents
		$xml = simplexml_load_string($fileContents);
		$coordinates = $xml->rte->rtept; 

		//just return array of coordinates
		$coordinateArray=[]; 

		$i=0; 
		foreach($coordinates as $coordinate) {
			$coordinateArray[] = $coordinate['lon'].','.$coordinate['lat']; 	 
		}
		//for use for plotting line on map
		$walkDetails['plot-coordinates'] = json_encode($coordinateArray); 

		//get landmark coordinates / name / description
		$landmarkfilename = $filename.'.txt'; 
		$filepath = "./landmark_descriptions/".$landmarkfilename; 
		$fileContents = trim(file_get_contents($filepath));

		$landmarks = explode(';',$fileContents); 
		$landmarkNameDescription=[];
		$landmarkCoordinates=[];  

		foreach($landmarks as $landmark) {
			if($landmark) {
				$parts = explode(',',$landmark); 
				
				$landmarkNameDescription[$parts[0]] = $parts[1]; 
				$landmarkCoordinates[] = $parts[2]; 
			}
		}

		$data = array(
			'plot-coordinates' => $coordinateArray, 
			'name-desc-landmarks' => $landmarkNameDescription, 
			'landmark-coordinates' => $landmarkCoordinates
		); 
		
		print_r(json_encode($data)); 
	}

	// public function getDirections($filename) {
	// 	//add back extension
	// 	$filename .= '.gpx'; 
	// 	$filepath = "./gpx_files/".$filename; 
	// 	$fileContents = file_get_contents($filepath); 
	// 	//parse file contents
	// 	$xml = simplexml_load_string($fileContents);
	// 	$coordinates = $xml->rte->rtept; 

	// 	//build coordinates string for api 
	// 	$coordinatesString=''; 

	// 	$i=0; 
	// 	foreach($coordinates as $coordinate) {
	// 		$coordinatesString .= $coordinate['lon'].','.$coordinate['lat']; 
	// 		if($i !== sizeof($coordinates)-1) {
	// 			$coordinatesString .= ';'; 
	// 		}
	// 		$i++; 
	// 	}

	// 	$mapboxRequestUrl = "https://api.mapbox.com/directions/v5/mapbox/walking/".$coordinatesString."?steps=true&access_token=pk.eyJ1IjoiZW1pbGllZGFubmVuYmVyZyIsImEiOiJjaXhmOTB6ZnowMDAwMnVzaDVkcnpsY2M1In0.33yDwUq670jHD8flKjzqxg"; 

	// 	$curl = curl_init();
		
	// 	curl_setopt_array($curl, array(
	//     	CURLOPT_RETURNTRANSFER => 1,
	//     	CURLOPT_URL => $mapboxRequestUrl
	// 	));
		
	// 	$response = curl_exec($curl); 
	// 	curl_close($curl);
	// 	print_r($response); //send me off	
	// }

	public function getLandmarks($walkName) {
		$walkName .= '.txt'; 
		$filepath = "./landmark_descriptions/".$walkName; 
		$fileContents = file_get_contents($filepath); 

		print_r($fileContents); 	
	}
}