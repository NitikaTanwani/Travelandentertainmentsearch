<?php




/*The api key*/
$apiKey = 'AIzaSyDxtdoH1LD08xQbz5yNBEn4x3vT8rIMJIQ';


// User location
$userlocAPI = "http://ip-api.com/json";

// Center location
$centerLocation = 'http://freegeoip.net/json/github.com';

// Near by search place api
$nearBySearch = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?';

$addresSearch = "https://maps.googleapis.com/maps/api/geocode/json?";
$photoReviewsApi = "https://maps.googleapis.com/maps/api/place/details/json?placeid=";
$photoApi = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=1050&photoreference=";
$searchParams = "";

// All type of client request handling on server (ajax calls)
if(isset($_GET['getPlacesResult']) && $_GET['getPlacesResult'] ==1){
	$cLat = $_GET['cLat'];
	$cLang = $_GET['cLang'];
	$keyword = isset($_GET['keyword'])? $_GET['keyword'] : "";
	$category = isset($_GET['category'])? $_GET['category'] : "";
	$distance = isset($_GET['distance'])? $_GET['distance'] : "";
	$meterDistance = $distance*1609.34;
	
	$from_location = isset($_GET['from_location'])? $_GET['from_location']: "";

$location = isset($_GET['location'])? $_GET['location'] : "";
	
	//search near by
	$latLang = $cLat.",".$cLang;
	//$langLat = $lang.",".$lat;
	if($category !="default"){
		$category = str_replace(' ', '_', $category);
		//echo $category;
		$types = "&types=$category";
	}else{
		$types = "";
	}

	if($from_location == "here"){
		$url = $nearBySearch."location=".$latLang."&radius=".$meterDistance."".$types."&keyword=".urlencode($keyword)."&key=".$apiKey;
		$data = file_get_contents($url); 
		$apiData=json_decode($data);
		$fdata['data']=$apiData;
		echo json_encode($fdata);
		return;

	}else{
		// get latlang by address
		$addUrl = $addresSearch."address=".urlencode($location)."&key=".$apiKey;	
		$data = file_get_contents($addUrl);
		$data = json_decode($data,true);
		$data = $data['results'][0]['geometry']['location'];
		$addData=$data;
		$latLang = $data['lat'].",".$data['lng'];
		$url = $nearBySearch."location=".$latLang."&radius=".$meterDistance."".$types."&keyword=".urlencode($keyword)."&key=".$apiKey;
		$data = file_get_contents($url); 
		$apiData = json_decode($data);
		$fdata['data'] = $apiData;
		$fdata['userLat'] = $addData['lat'];
		$fdata['userLng'] = $addData['lng'];
		echo json_encode($fdata);return;


        //echo $data;return;
		//echo $rdata;return;
		//echo json_encode($fdata);return;
	}
}// Place search end


// Photo & Review start
if(isset($_GET['getPhotosReviews']) && $_GET['getPhotosReviews'] ==1){
$url = $photoReviewsApi.$_GET['place_id']."&key=".$apiKey;
		$data = file_get_contents($url);
        $result = $data;
        $result = json_decode($result,true);
		
		// Create image folder with full permission if not exists
		$dir = "images";
		if (!file_exists($dir)) {
			mkdir($dir, 0777);
		}
		
		// Delete previous files 
		for($k=0;$k<5;$k++){
			if (file_exists($dir."/photo_".$k.".jpg")) {
							unlink($dir."/photo_".$k.".jpg");
			}
		}

        // Parse data and save high resolution image
        if(!empty($result) && isset($result['result']['photos']) && !empty($result['result']['photos'])){
            $photos = $result['result']['photos'];
            $i =0;
            foreach ($photos as $key => $value) {
                if($i == 5) break;
                $photoPref = $value['photo_reference'];
                $photoUrl = $photoApi.$photoPref."&key=".$apiKey;
                $file = file_get_contents($photoUrl);
                file_put_contents($dir."/photo_".$i.".jpg",$file);
                ++$i;
                #echo $pData;die;
            }
        }
  echo $data;return;
	
}// Photo & Review start




?>


<html>
<head><title>Travel and Entertainment</title>
</head>
<style>
#overlay {
    position: fixed;
    display: none;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0,0,0,0.8);
    z-index: 2;
    cursor: pointer;
	text-align:center;
}
.mapWrapper {
	height: 320px;
	width: 425px;
	background: #eee;
	position: absolute;	margin-left:10px;margin-top:5px;	
}
#map{
	position: absolute; top: 0px; left: 0px; width: 425px; height: 320px; 
}

   /* Optional: Makes the sample page fill the window. */
#floating-panel {
	position: absolute;
    top: 0px;
    left: 0%;
    z-index: 5;
    background-color: #f1f1f1;
    padding: 0px;
    /*border: 1px solid #999;*/
    text-align: center;
    font-family: arial;
    /*line-height: 28px;*/
    padding-left: 0px;
    padding-right: 0px;
    font-size: 15px;

}
.search_table {
	border-collapse: collapse;
	margin-top:10px;
   width:50%;
}
#resultSet{margin-left:auto;margin-right:auto;width:90%; text-align:center;}
.photo_cls{ cursor:pointer;}

.addr{
	
	text-decoration:none;
	color:black;
	

}
a.addr:hover {
	color:#C0C0C0;
	text-decoration:none;
	
	
}
.drop_down{
	position: relative;
    display: inline-block;
    background-color: #f1f1f1;
    box-shadow: 0px 0px 0px 0px rgba(0,0,0,0.2);
    z-index: 1;
    color: black;
    padding:0px 0px;
    text-decoration: none;
    display: block;
    float:left;
    width:80px;
    padding-top: 5px;

}
.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f1f1f1;
    
   
    overflow: auto;
    box-shadow: 0px 0px 0px 0px rgba(0,0,0,0.2);
    z-index: 1;
    float:left;
}

.drop_down a {
    color: black;
    padding: 0px 0px;
    text-decoration: none;
    display: block;
    height:30px;
    min-width: 30px;
}

.drop_down a:hover {background-color: #ddd}
</style>
<script type="text/javascript">

// Define global variables
var ipApi = 'http://ip-api.com/json';
var freeGeo = 'http://freegeoip.net/json/github.com';
var currentLat = 0;
var currentLng = 0;

// Get current location start
function getCurrentLocation(){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
			var ipData = this.responseText;
			var ipData = JSON.parse(ipData);
			currentLat = ipData.lat;
			currentLng = ipData.lon;
			/*if(currentLat!=34.022351)
			{
				currentLat = 34.022351;
			}
			if(currentLng!=-118.285117)
			{
				currentLng= -118.285117;
			}*/
			if(xhttp.status!=200)
			{
				currentLat=34.022351;
				currentLng=-118.285117;
			}
			
			
			document.getElementById("searchButton").removeAttribute("disabled");
			//currentLat = ipData.lat;
			//currentLng = ipData.lon;
			//console.log(currentLng);
        }
    }
    xhttp.open("GET", ipApi+"?q="+Math.random(), true);
        xhttp.send();
}

getCurrentLocation();


// Get current location end

// Form validation start 
function enterpressalert(){
    //Handling...
}

function checkRadio(locType){
    if(locType == "location"){
        document.getElementById("location").removeAttribute("disabled");
        document.getElementById("location").setAttribute("required","");

    }else{
        document.getElementById("location").setAttribute("disabled","disabled");
        document.getElementById("location").removeAttribute("required");
		document.getElementById("location").value="";
    }
}

// Form validation end 

</script>
<body>
<?php 
$keyword = isset($_POST['keyword'])? $_POST['keyword'] : "";
if(!empty($keyword)){
	$category = isset($_POST['category'])? $_POST['category'] : "";
	$distance = isset($_POST['distance'])? $_POST['distance'] : "";
	$meterDistance = $distance*1609.34;
	$from_location = isset($_POST['from_location'])? $_POST['from_location'] : "";
	//echo $category;
	//$loc=urlencode($from_location);
	$location = isset($_POST['location'])? $_POST['location'] : "";
	$searchParams = "&keyword=".urlencode($keyword)."&distance=".$distance."&from_location=".$from_location."&location=".$location."&category=".$category;	
}else{
	$category = "";
	$distance = 10;
	$meterDistance = $distance*1609.34;
	$from_location = "";
	$location = "";
}
?>

<!-- Search form start -->
<center>
<form id="searchForm" name="searchForm" action="" method="POST" style="margin:0;padding:0">
<!--<table class="search_table" border="1" align="center" cellpadding="5">-->
<fieldset style="background-color:#F8F8F8;width:670px;height:210px;border-color:silver;border-width:2px">
    
        <p style="text-align: center;font-size:35px;position:relative;top:-35px"><i>Travel and Entertainment Search</i></p>

        <hr style="border-color:#DCDCDC;position:relative;top:-60px">


    
       <div style="float:left;position:relative;top:-60px">
        <b>Keyword</b>
        <input required type="text" name="keyword" id="keyword" value="<?php echo $keyword; ?>" onKeyPress="return enterpressalert(event, this);">
    </div>

    <br>
    <div style="position:relative;left:-37%;top:-70px;">
      <br>
        <b>Category</b> 
    <?php 
      $catList = array("default","cafe","bakery","restaurant","beauty salon","casino","movie theater","lodging",
              "airport","train station","subway station","bus station"              
              );
    ?>
        <select name="category" id="category">
    <?php 
      for($t =0 ;$t<count($catList);$t++){
        $sel = ($category == $catList[$t])? 'selected="selected"' : '';
    ?>
<option value="<?php echo $catList[$t];?>" <?php echo $sel;?> ><?php echo $catList[$t];?></option>
    <?php 
      }
    ?>
      </select>
  </div>

  <br>
    
       <div style="float:left">
        
        <b style="position:relative;left:-1%;top:-80px">Distance(miles)</b> 

        <input style="position:relative;left:-2%;top:-80px" type="text" name="distance" id="distance" required value="<?php echo $distance; ?>" onKeyPress="return enterpressalert(event, this);">&nbsp;<b style="position:relative;left:-2%;top:-80px;">from</b>
       

       <input style="position:relative; top:5%;left:-3%;top:-80px;" type="radio" name="from_location" id="from_location_here" required value="here" checked onClick="checkRadio('here');" />

        <b style="position:relative;top:5%;left:-3%;top:-80px">Here</b><br/>

        <input style="position:relative;left:54%;top:-75px" type="radio" name="from_location" id="from_location" onClick="checkRadio('location');" required value="location" <?php if($from_location == "location"){echo "checked";} ?> />

        <input style="position:relative;left:53%;top:7%;top:-75px;" type="text" name="location" id="location" <?php if($from_location!= "location"){echo 'disabled="disabled"';} ?> value="<?php echo $location; ?>" placeholder="location" />

        <input type="hidden" id="opened_map_id" value="" />

        <br>
        <br>

        <button  style="position:relative; left:-20%;top:-80px" disabled="disabled" id="searchButton" onClick="return validateMiles();">Search</button>
            <!-- <input type="button"  name="search" id ="search" onClick="check();" value="Search" />-->
            <input style="position:relative;left:-20%;top:-80px" type="button" onClick="clearForm()" name="r" id ="r" value="Clear" />
        </div>
      </div>
        
    
</fieldset>

	
</form>
</center>

<!-- Search form end -->


<!-- Result area start -->
<!-- <div id="mapWrapper" style="display:none;">
	<div id="map"></div>
	<div id="floating-panel">
	<b>Mode of Travel: </b>
	<select id="mode">
	<option value="DRIVING">Driving</option>
	<option value="WALKING">Walking</option>
	<option value="BICYCLING">Bike</option>
	</select>
	</div>
</div>
-->
<div id="overlay" onclick="off()"></div>
<div id="resultSet"></div>
<!-- Result area end -->

</body>
<script type="text/javascript">

function validateMiles(){
	var dis = document.getElementById("distance").value;
	if(dis !=""){
		var meter = dis*1609.34;
		if(meter > 50000){
			alert("Meter should not be more then 50,000. Please reduce miles.");
			document.getElementById("distance").focus();
			return false;
		}
	}

}

// Load places result start
function loadPlacesResult() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
	if (this.readyState == 1){document.getElementById("resultSet").innerHTML = "Loading....";}
    if (this.readyState == 4 && this.status == 200) {
    var searchData = this.responseText;
    //console.log(searchData);
    var apiData = JSON.parse(searchData);
    var locLatitude = apiData.userLat;
    var locLongitude = apiData.userLng;
    var apiData = apiData.data.results;

    //override current lat lng if user search is by some location address

    if(typeof locLatitude!="undefined" && locLatitude!="")
    	currentLat=locLatitude;

    if(typeof locLongitude!="undefined" && locLongitude!="")
    	currentLng=locLongitude;


    var tStart    = '<table id="searchResult" class="search_table" border="1" align="center" cellpadding="3" style="width:90%;border-color:#DCDCDC"><tr><td style="text-align:center"><b>Category<b></td><td style="text-align:center"><b>Name</b></td><td style="text-align:center"><b>Address</b></td></tr>';
    var tA = "";
    //console.log(apiData);
    //return;
	if(apiData.length>0){
		for(var i=0;i<apiData.length;i++)
			{
			var rowData = apiData[i];
			var geometry = rowData.geometry.location;
			var lat = geometry.lat;
			var lng = geometry.lng;
        //console.log(rowData);return;
            var trStart  ="<tr>";
           // var td1="<td>"+obj[i]["id"]+"</td>";
            var td1='<td style="width:80px;"><img src="'+rowData.icon+'" width="40px;height:7px" /></td>';
            var plsId = ""+rowData.place_id.trim()+"";
			var placeId = "'"+rowData.place_id.trim()+"'";
			var placeName = "'"+rowData.name.trim()+"'";
            var td2='<td width="50%"><a style="text-decoration:none;color:black" onClick="showPlaceDetails('+placeId+','+placeName+')" href="javascript:void(0)">'+rowData.name+'</a></td>';

var td3='<td><a  class="addr"  onClick="showMap('+lat+','+lng+','+placeId+')" href="javascript:void(0)">'+rowData.vicinity+'</a>'+
            		'<div class="mapWrapper" style="display:none;" id="wrapper_'+plsId+'"></div></td>';
            var trEnd  ="<tr>";
            tA += trStart+td1+td2+td3+trEnd;
            var tEnd = "</table>";
		    var finalData = tStart+tA+tEnd;
			}
		}else{
			finalData= "<fieldset style='border-color:#DCDCDC;background-color:#F8F8F8;position:relative;top:40px;width:900px;left:15%' align='center'>No records has been found.</fieldset>";
		}
	    //var tEnd = "</table>";
		//var finalData = tStart+tA+tEnd;
        document.getElementById("resultSet").innerHTML = finalData;
    }
  };
  xhttp.open("POST", "updatedcomplete2.php?getPlacesResult=1&cLat="+currentLat+"&cLang="+currentLng+"<?php echo $searchParams;?>"+"&q="+Math.random(), true);
  xhttp.send();
}


// Load places result end


// Load photo and reviews start
//click=0;
function setmode(a){
    	if(a=="Drive there"){
	d=document.createElement("p");
	d.setAttribute("id","mode");
	d.setAttribute("value","DRIVING");
	//console.log("here");
	//click=1;
	//console.log(click);
	initMap1();
	//Delete d;
      }

if(a=="Walk there")
{

	e=document.createElement("p");
	e.setAttribute("id","mode");
	e.setAttribute("value","BICYCLING");
	click=2;
	initMap1();
	//Delete e;
}
if(a=="Bike there"){
	f=document.createElement("p");
	f.setAttribute("id","mode");
	f.setAttribute("value","DRIVING");
	click=3;
	initMap1();
	//Delete f;
   }
 }
 function setvalue(a)
 {mode="";
 	if(a=="Drive there")
 	{
 		//document.getElementById("mode").setAttribute("value","DRIVING");
 		mode="DRIVING";
 		console.log(mode);
 		initMap1();
 	}
 	if(a=="Walk there")
 	{
 		//document.getElementById("mode").setAttribute("value",'WALKING');
 		mode="WALKING";
 		console.log(mode);
 		initMap1();
 	}
 	if(a=="Bike there")
 	{
 		//document.getElementById("mode").setAttribute("value",'BICYCLING');
 		mode="BICYCLING";
 		console.log(mode);
 		initMap1();
 	}
 }

function showPlaceDetails(place_id,place_name){
	document.getElementById("resultSet").innerHTML = "";
    var xhttp1 = new XMLHttpRequest();
        xhttp1.onreadystatechange = function() {
			if (this.readyState == 1){document.getElementById("resultSet").innerHTML = "Loading....";}
            if (this.readyState == 4 && this.status == 200) {
                  var placeData = this.responseText;
            //console.log(placeData);
            //console.log(searchData);return;

var apiData = JSON.parse(placeData);
            var apiData = apiData.result;
            // Review table

			var placeName = '<table id="placeName" class="search_table" border="0" align="center" cellpadding="3" style="width:70%;border-color:#DCDCDC"><tr align="center"><td><b>'+place_name+'</b></td></tr></table><br>';

			var reviewHeading = '<table id="placeResult" class="search_table" border="0" align="center" cellpadding="3" style="width:70%;border-color:#DCDCDC"><tr align="center"><td><p id="reviewLabel">Click to show reviews</td></tr><tr align="center"><td><img id="reviewArrow" onClick="showHideReviews();" src="http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png" style="cursor:pointer; width:50px;"></td></tr></table>';

            var tStart    = '<table id="reviewList" style="display:none;border-color:#DCDCDC" class="search_table" border="1" align="center" cellpadding="3" style="width:70%;border-color:#DCDCDC">';
            var tA = "";
            //console.log(apiData);return;
            var reviews = apiData.reviews;
			//console.log(typeof reviews);
			if((typeof reviews == "undefined") || reviews.length==0){
			 tA = "<tr><td style='border-color:#DCDCDC' align='center'><b>No Reviews Found.<b></td></tr>";
			 //return false;
			}

			if((typeof reviews != "undefined") && reviews.length>0){

				for(var i=0;i<reviews.length;i++)
					{
					var rowData = reviews[i];
					//console.log(rowData);return;
						//var trStart  ="<tr>";
					   // var td1="<td>"+obj[i]["id"]+"</td>";
					   var profPhoto = "<img style='height:60px' src='"+rowData.profile_photo_url+"' />";
					   var reviewText = rowData.text;
					   //var td3='<td align="center">'+profPhoto+'<br/><b style="position:relative;left:90px;top:-20px">'+rowData.author_name+'<b></td>';
					   //var td4='<td>'+reviewText+'</td>';
						var td1='<tr><td align="center">'+profPhoto+'<b style="position:relative;left:6px;top:5px">'+rowData.author_name+'</b></td></tr>';
						//var trEnd  ="</tr>";
						var td2="";
						if(reviewText!="")
						 td2='<tr><td>'+reviewText+'</td></tr>';
						//var td4="</tr>";
					
						tA += td1+td2;
				}
			}
			//else{
//tA = "<tr><td align='center'>No Reviews Found.</td></tr>";
			//}
			var tEnd = "</table>";
			var reviewData = tStart+tA+tEnd;
			
            //PHOTO TABLE
			
			var photoHeading = '<table id="placeResult" class="search_table" border="0" align="center" cellpadding="3" style="width:85%;border-color:#DCDCDC"><tr><td align="center"><p id="photoLabel">Click to show photos</td></tr><tr align="center"><td><img id="photoArrow" onClick="showHidePhotos();" src="http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png" style="cursor:pointer; width:50px;"></td></tr></table><tr>';


            var tStart    = '<table id="photoList" style="display:none;border-color:#DCDCDC" class="search_table" border="1" align="center" cellpadding="3" style="width:500p"><tr>';
            var tA = "";
            //console.log(apiData);return;
            var photo;
            var photos = apiData.photos;

           if((typeof photos == "undefined") || photos.length==0 )
           {
           	tA = "<tr style='border-color:#778899'><td style='border-color:#DCDCDC' align='center'><b>No Photos Found.</b></td></tr>";
           }



			if((typeof photos != "undefined") && photos.length > 0)
			{
				for(var i=0;i<photos.length;i++)
					{
					if(i==5)break;
					if((typeof reviews != "undefined") && reviews.length>0)
					var rowData = reviews[i];
					//console.log(rowData);return;
						var trStart  ="<tr>";
					   // var td1="<td>"+obj[i]["id"]+"</td>";
					    p = "images/photo_"+i+".jpg";
					   

					   imgName = "'images/photo_"+i+".jpg'";
					   
					   var td1='<td align="center" style="width:150px;vertical-align:middle;horizontal-align:middle"><img style="width:95%" class="photo_cls" onClick="on('+imgName+');" src="'+p+'" width="200px" /></td>';
					var trEnd  ="</tr>";
						tA += trStart+td1+trEnd;
				}
			}
			//else{
//tA = "<tr style='border-color:#D3D3D3'><td style='border-color:#D3D3D3' align='center'><b>No Photos Found.</b></td></tr>";
//			}
            var tEnd = "</table>";
            var photoData = tStart+tA+tEnd;
			//console.log(placeName+reviewHeading+reviewData+photoHeading+photoData);
            document.getElementById("resultSet").innerHTML = placeName+reviewHeading+reviewData+photoHeading+photoData;
        }
    }
    xhttp1.open("POST", "updatedcomplete2.php?getPhotosReviews=1&place_id="+place_id+"&q="+Math.random(), true);
        xhttp1.send();
}



reviewHidden = 1;


   //eled;
  

function showHideReviews(){
	if(photoHidden==0)
	{
		photoHidden=1;
		document.getElementById('photoLabel').innerHTML = 'Click to show reviews';
		document.getElementById('photoList').style.display = 'none';
		document.getElementById("photoArrow").src="http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png";

	}
	if(reviewHidden ==1){
		reviewHidden = 0;
		photoHidden=1;
		document.getElementById('reviewLabel').innerHTML = 'Click to hide reviews';
		document.getElementById('reviewList').style.display = '';
		document.getElementById("reviewArrow").src="http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png";
	}else{
		reviewHidden = 1;
		document.getElementById('reviewLabel').innerHTML = 'Click to show reviews';
		document.getElementById('reviewList').style.display = 'none';
		document.getElementById("reviewArrow").src="http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png";
	}
}

photoHidden = 1;

function showHidePhotos(){

	if(reviewHidden==0)
	{
		reviewHidden=1;
		document.getElementById('reviewLabel').innerHTML = 'Click to show reviews';
		document.getElementById('reviewList').style.display = 'none';
		document.getElementById("reviewArrow").src="http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png";

	}
	if(photoHidden ==1){
		photoHidden = 0;
		reviewHidden=1;
		document.getElementById('photoLabel').innerHTML = 'Click to hide photos';
		document.getElementById('photoList').style.display = '';
		document.getElementById("photoArrow").src="http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png";
	}else{
		photoHidden = 1;
		document.getElementById('photoLabel').innerHTML = 'Click to show photos';
		document.getElementById('photoList').style.display = 'none';
		document.getElementById("photoArrow").src="http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png";	
	}
}

function on(src) {
	var img = '<img src="'+src+'"/>';
	var path=src.replace(/'/g, "");
	//var path=imgName.replace(/'/g, "");
	//path=imgName.replace("", "");
	document.getElementById("overlay").innerHTML =window.open(path,'_blank');
	path="";
    //document.getElementById("overlay").style.display = "block";

}

function off() {
	document.getElementById("overlay").innerHTML = '';
    document.getElementById("overlay").style.display = "none";
}

// Load photo and reviews end

// Google Map Dispaly
var late = 0;
var longe = 0;
var showMapDiv = 0;
function showMap(lat,lng,placeId){
showMapDiv = "wrapper_"+placeId;

	// Remove previous map if exists
	var openedMap = document.getElementById("opened_map_id").value;



	if(openedMap !=""){
		document.getElementById("opened_map_id").value = "";
		document.getElementById(openedMap).style.display = "none";
		document.getElementById(openedMap).innerHTML = "";
    	}
   


	if(openedMap==showMapDiv)
	{
		document.getElementById("opened_map_id").value = "";
		document.getElementById(openedMap).style.display = "none";
		document.getElementById(openedMap).innerHTML = "";
	}
	

	else{

		

   
    //console.log(late,longe)

	 mapStr = '<div id="map"></div><div id="floating-panel"><div id="mode" class="drop_down"><a href="#" id="1" onClick="setvalue(this.text)">Walk there</a><br><a href="#" id="2" onClick="setvalue(this.text);">Bike there</a><br><a id="3" href="#" onClick="setvalue(this.text);">Drive there</a></div></div>'

	 document.getElementById(showMapDiv).innerHTML = mapStr;
	document.getElementById(showMapDiv).style.display = "";
	document.getElementById("opened_map_id").value = showMapDiv;
	
    late = lat;
    longe = lng;

    //console.log(click);

	 initMap();
	}
	

	/*<select style="background-color:#f1f1f1;" size="3" id="mode"><option value="WALKING">Walk there</option>'+
				'<option value="BICYCLING">Bike there</option>'+
				'<option value="DRIVING">Drive there</option></select></div>';*/

	/*document.getElementById(showMapDiv).innerHTML = mapStr;
	document.getElementById(showMapDiv).style.display = "";
	document.getElementById("opened_map_id").value = showMapDiv;
	
    late = lat;
    longe = lng;
    //console.log(late,longe);
    

    
    initMap();*/
    

    //console.log(late,longe);
    
    //initMap();

 }

    /*document.getElementById(showMapDiv).innerHTML = mapStr;
	document.getElementById(showMapDiv).style.display = "";
	document.getElementById("opened_map_id").value = showMapDiv;
	
    late = lat;
    longe = lng;

    initMap();*/
  // }
//}
 //var markers = [];
       /* function setMapOnAll(map) {

        for (var i = 0; i < markers.length; i++) {
          markers[i].setMap(map);
        }
      }*/

function initMap() {
    //console.log('tt');console.log(late,longe,'ss');
if(late == 0 && longe == 0) return;
console.log("without mode");

  var directionsDisplay = new google.maps.DirectionsRenderer;
 var directionsService = new google.maps.DirectionsService;

   var uluru = {lat: late, lng: longe};

 var map = new google.maps.Map(document.getElementById('map'), {
    zoom: 17,
    center: uluru
  });
  marker = new google.maps.Marker({
    position: uluru,
    map: map
  });

directionsDisplay.setMap(map);

}


//if(document.getElementById("mode").value != ""){
	//setMapOnAll(null);

//document.getElementById("myAnchor").getAttribute("target");



function initMap1(){
	if(late == 0 && longe == 0) return;
console.log("without mode");

var directionsDisplay = new google.maps.DirectionsRenderer;
var directionsService = new google.maps.DirectionsService;

  var uluru = {lat: late, lng: longe};

var map = new google.maps.Map(document.getElementById('map'), {
    zoom: 17,
    center: uluru
  });
  marker = new google.maps.Marker({
    position: uluru,
    map: map
  });

directionsDisplay.setMap(map);

if(mode != ""){
	//setMapOnAll(null);
      
	calculateAndDisplayRoute(directionsService, directionsDisplay);

}

document.getElementById("mode").addEventListener('change', function() {

	if(mode!=""){
		//setMapOnAll(null);
      
	calculateAndDisplayRoute(directionsService, directionsDisplay);
	}
});
}
//else
//console.log("element with id, mode not found");



function calculateAndDisplayRoute(directionsService, directionsDisplay) {
    //console.log(currentLat)
   /* map1 = new google.maps.Map(document.getElementById('map'), {
    zoom: 17,
    center: uluru
  });*/
  

marker.setVisible(false);
      
    //directionsDisplay.setMap(map)
        var selectedMode = mode;
        console.log(selectedMode);
        //console.log("route");
        directionsService.route({
          origin: {lat: currentLat, lng: currentLng},  // Haight.
          destination: {lat: late, lng: longe},  // Ocean Beach.
          // Note that Javascript allows us to access the constant
          // using square brackets and a string value as its
          // "property."
          travelMode: google.maps.TravelMode[selectedMode]
        }, function(response, status) {
 if (status == 'OK') {
            directionsDisplay.setDirections(response);
          } else {
            window.alert('Directions request failed due to ' + status);
          }
        });
      }
// Google Map Dispaly end


// Clear the form
function clearForm(){
document.getElementById("keyword").value = "";
document.getElementById("distance").value = 10;
document.getElementById("category").value = "default";
document.getElementById("from_location_here").checked = true;
document.getElementById("location").value = "";
document.getElementById("resultSet").innerHTML = "";
}
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDxtdoH1LD08xQbz5yNBEn4x3vT8rIMJIQ">
</script>
<?php 
// If form is submitted then get the places results
if(!empty($keyword)){ ?>
<script type="text/javascript">
setTimeout(loadPlacesResult, 1500);
</script>
<?php }//end ?>
</html>
