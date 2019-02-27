<?php 
include_once 'geoHash.php';


if(isset($_GET["keyword"])&&isset($_GET["category"])&&isset($_GET["distance"])) {

    $keyword = $_GET["keyword"];
    $distance = $_GET["distance"];
    if ($distance =="") { $distance = 10; }
    $SegmentId="";
    if ($_GET["category"] == "Music"){$SegmentId = "KZFzniwnSyZfZ7v7nJ";}
    if ($_GET["category"] == "Sports"){$SegmentId = "KZFzniwnSyZfZ7v7nE";}
    if ($_GET["category"] == "Arts & Theatre"){$SegmentId = "KZFzniwnSyZfZ7v7na";}
    if ($_GET["category"] == "Film"){$SegmentId = "KZFzniwnSyZfZ7v7nn";}
    if ($_GET["category"] == "Miscellaneous"){$SegmentId = "KZFzniwnSyZfZ7v7n1";}
    if ($_GET["category"] == "Default"){$SegmentId = "";}


    //start handle : here and location
    $lat=0; $lng=0;

    if ( isset($_GET["theHereLatitude"]) ){
        $lat = $_GET["theHereLatitude"];
        $lng = $_GET["theHereLongitude"];
    } else {
        $otherLocInput = $_GET["theOtherLoc"];
        $addressForApi=str_replace(" ", "+", $otherLocInput);
        $geocoding = "https://maps.googleapis.com/maps/api/geocode/json?address=".$addressForApi."&key=AIzaSyA8XjdOsEdYtgaETUMOuR8A20puTbTGVp0";
        $GeocodingObj = json_decode(file_get_contents($geocoding),true);

        $lat = $GeocodingObj["results"][0]["geometry"]["location"]["lat"];
        $lng = $GeocodingObj["results"][0]["geometry"]["location"]["lng"];

    }


    $geohash = encode($lat, $lng);
    //done with location and got geohash

    //start do the TM API part
    $TM_API_key = "yGT4UZmzzJAMkAAHwapvuMWivbUe4l79";
    $keywordForApi=str_replace(" ", "+", $keyword);
    $TM_API_request = "https://app.ticketmaster.com/discovery/v2/events.json?apikey=".$TM_API_key. "&keyword=".$keywordForApi."&segmentId=".$SegmentId."&radius=".$distance."&unit=miles&geoPoint=".$geohash;
    $TM_API_req = file_get_contents($TM_API_request);

    $TM_API_Obj = json_decode($TM_API_req,true); //decode to add inital location infomation
    $LsArray = array($lat, $lng);
    $initial_lat = "init_lat";
    $TM_API_Obj[$initial_lat] = $LsArray[0];
    $initial_lng = "init_lng";
    $TM_API_Obj[$initial_lng] = $LsArray[1];
    $TM_API_req = json_encode($TM_API_Obj);
    echo $TM_API_req;
    return;

    //done do the TM API part
}
else if(isset($_GET["venueKeyword"])) {

    $venueKeyword = $_GET["venueKeyword"];
    $venueKeywordForApi=str_replace(" ", "%20", $venueKeyword);
    $TM_API_key = "yGT4UZmzzJAMkAAHwapvuMWivbUe4l79";
    $TM_API_request2 = "https://app.ticketmaster.com/discovery/v2/venues?apikey=".$TM_API_key. "&keyword=".$venueKeywordForApi;
    $TM_API_req2 = file_get_contents($TM_API_request2);
    echo $TM_API_req2;
    return ;
}
else if (isset($_GET["id"])) {

    $theId = $_GET["id"];
    $TM_API_key = "yGT4UZmzzJAMkAAHwapvuMWivbUe4l79";
    $TM_API_request3 = "https://app.ticketmaster.com/discovery/v2/events/".$theId."?apikey=".$TM_API_key."&";
    $TM_API_req3 = file_get_contents($TM_API_request3);
    echo $TM_API_req3;
    return ;
}
?>






<!DOCTYPE html>
<html>
    <head>

        <script async defer
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA8XjdOsEdYtgaETUMOuR8A20puTbTGVp0">
        </script>

        <style type="text/css">
            #fieldCSS{
                border-width: 3px;
                border: solid;
                border-color: #CCCCCC;
                background-color: #FAFAFA;
                position: relative;

            }
            a{
                text-decoration: none;
                color: black;
            }
            body {
                max-width: 1000px;
                margin: 0 auto;
            }

            .goThereButton{
                position: absolute;
                left: 680px; width: 90px; height: 21px; display: none; z-index: 2;
            }
            #VenueInfoAndPhotos {
                display: none; 
            }
            #toHideVenueInfo {
                display: none;
            }
            #toHidePhotos {
                display: none;
            }
            #toShowVenueInfo {
                display: block;
            }
            #toShowPhotos {
                display: block;
            }

        </style>
        <title>
            csci 571 HW6
        </title>

    </head>

    <body onload="getTheHereLocation()">
        <fieldset id="fieldCSS">
            <h2 align="center"><i>Events Search</i></h2>
            <hr>
            <form onsubmit="return false;">
                <div>
                    <b>Keyword</b> 
                    <input type="text" id="keyword" required>
                    <br>

                    <b>Category</b> 
                    <select name="category" type="text" id="category" >
                        <option value="Default">Default</option>
                        <option value="Music">Music</option>
                        <option value="Sports">Sports</option>
                        <option value="Arts&Theatre">Arts & Theatre</option>
                        <option value="Film">Film</option>
                        <option value="Miscellaneous">Miscellaneous</option>
                    </select>
                    <br>
                    <b> Distance&nbsp(miles)</b>
                    <input type="number"  id="distance" placeholder="10">
                    <b>from</b>
                    <input type="radio" name="fromwhere" value="Here" id="fromHere" checked onclick="disableTheRadio()">
                    Here
                    <br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                    <input type="radio" name="fromwhere" value="location" id="OtherLocRadio" onclick="disableTheRadio()"> 
                    <input type="text" id="theOtherLoc" placeholder="location" disabled="true" required>
                </div>
                <div>
                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                    <input id="Search" type="submit" value="Search" onclick="goSearch()" disabled >
                    <input type="reset" value="Clear" id="clear" onclick="clear_thisthe_Page()">
                </div>
            </form>
        </fieldset>



        <div id="theOuterPPage">	
            <div id="tables">
            </div>
            <div id="GoogleMap" class="theMapClass" style="position: absolute;
                                                           left: 680px; top:70px; width: 300px; height: 300px; display: none; z-index: 1;"> </div>
            <div id="buttonsDiv">
                <button id="walkThereButton" class="goThereButton" >Walk there</button>
                <button id="bikeThereButton" class="goThereButton">Bike there</button>
                <button id="driveThereButton" class="goThereButton">Drive there</button>
            </div>
        </div>



        <div id="TheEventName" style="text-align: center" ></div>
        <div id="basicInfo" style="text-align: center">  </div>

        <div id="VenueInfoAndPhotos">
            <div id="infoButton" style="text-align: center">
                <div id="toShowVenueInfo">
                    <p id="showVenueInfo"> click to show venue info</p>
                    <img id="infoArrowDown"  src="http://csci571.com/hw/hw6/images/arrow_down.png" style="width: 30px" onclick="shownArrowUp(true)">
                </div>
                <div id="toHideVenueInfo">
                    <p id="hideVenueInfo"> click to hide venue info</p>
                    <img id="infoArrowUp"  src="http://csci571.com/hw/hw6/images/arrow_up.png" style="width: 30px" onclick="shownArrowUp(true)">
                </div>
            </div>
            <div id="VenueInfo" style="display: none;"> </div>

            <div id="photoButton" style="text-align: center">
                <div id="toShowPhotos">
                    <p id="showPhotos"> click to show venue photos</p>
                    <img id="photoArrowDown"  src="http://csci571.com/hw/hw6/images/arrow_down.png" style="width: 30px" onclick="shownArrowUp(false)">
                </div>
                <div id="toHidePhotos">
                    <p id="hidePhotos"> click to hide venue photos</p>
                    <img id="photoArrowUp"  src="http://csci571.com/hw/hw6/images/arrow_up.png" style="width: 30px" onclick="shownArrowUp(false)">
                </div>
            </div>
            <div id="photos" style="display: none;"> </div>

        </div>
    </body>




    <script type="text/javascript">
        function getTheHereLocation(){
            var request;
            if (window.XMLHttpRequest){
                request = new XMLHttpRequest();
            }else{
                request = new ActiveXObject("Microsoft.XMLHTTP");
            }
            request.open("GET","http://ip-api.com/json",false);
            request.send();
            var info = JSON.parse(request.responseText); 
            //console.log(info);
            document.getElementById("Search").removeAttribute("disabled");

            return [JSON.parse(request.responseText).lat, JSON.parse(request.responseText).lon];
        }

        var the_here_loc = getTheHereLocation();
        var theHereLatitude = the_here_loc[0];
        var theHereLongitude = the_here_loc[1];

    </script>

    <script type="text/javascript">

        function goSearch(){

            if (document.getElementById("keyword").value==""){
                console.log("the keyword missing!");
                return;
            }
            var distanceCheck=document.getElementById("distance").value;
            if(distanceCheck==""){
                document.getElementById("distance").value=10;
            }

            var theInfoToServer="";
            //var strReplace = document.getElementById("keyword").value.replace(/ +/g, "+");
            if (document.getElementById("fromHere").checked==true) {
                console.log("started goSearch() from Here.");
                console.log(theHereLatitude+", "+theHereLongitude)

                //console.log(strReplace);
                theInfoToServer = "keyword="+ document.getElementById("keyword").value +'&'+"category=" + document.getElementById("category").value+'&'+"distance="+document.getElementById("distance").value+'&'+"theHereLatitude=" +theHereLatitude +'&'+"theHereLongitude=" +theHereLongitude;
                //console.log(theInfoToServer); checked
            }
            else if (document.getElementById("OtherLocRadio").checked == true){
                document.getElementById("theOtherLoc").setAttribute("required","");
                if (document.getElementById("theOtherLoc").value=="" & document.getElementById("OtherLocRadio").checked == true){

                    console.log("the location missing!");
                    return;
                }
                console.log("started goSearch() from Other location.");
                theOtherLoc = document.getElementById("theOtherLoc").value;


                theInfoToServer ="keyword="+document.getElementById("keyword").value+'&'+"category=" + document.getElementById("category").value+'&'+"distance="+document.getElementById("distance").value + '&' +"theOtherLoc=" + theOtherLoc;
                //console.log(theInfoToServer);
            }
            else{  return;
                }

            var requestTwo;
            if (window.XMLHttpRequest){
                requestTwo = new XMLHttpRequest();
            }else{
                requestTwo = new ActiveXObject("Microsoft.XMLHTTP");
            }
            requestTwo.open("GET", "index.php?"+theInfoToServer, false);
            requestTwo.send();

            responseTwoObj= JSON.parse(requestTwo.responseText);

            console.log(responseTwoObj );
            var theOuterTableText = "";
            if((responseTwoObj.page.totalElements==0)||(responseTwoObj.page.totalPages==0)){
                theOuterTableText += "<p class='table' style='text-align:center; border: 2px solid #DDDDDD;'><b>No Records has been found </b></p>";
            } else {
                //responseTwoObj= JSON.parse(requestTwo.responseText);
                console.log("The amount of events shown is: " + responseTwoObj._embedded.events.length);
                theOuterTableText += "<table border='1'  align='center' class='table'><tr><th style='width:100px;'>Date</th><th style='width:200px;'>Icon</th><th style='width:600px;'>Event</th> <th style='width:100px;'>Genre</th><th style='width:500px;'>Venue</th></tr>";



                for(let i = 0; i<responseTwoObj._embedded.events.length; i++){
                    theOuterTableText+="<tr>";
                    if(responseTwoObj._embedded.events[i].dates.start.localDate==undefined){
                        responseTwoObj._embedded.events[i].dates.start.localDate="";
                    }
                    if(responseTwoObj._embedded.events[i].dates.start.localTime==undefined){
                        responseTwoObj._embedded.events[i].dates.start.localTime="";
                    }
                    theOuterTableText+="<td>" + responseTwoObj._embedded.events[i].dates.start.localDate + "<br>";
                    theOuterTableText+=responseTwoObj._embedded.events[i].dates.start.localTime +"</td>"; //col1
                    theOuterTableText+="<td><img style='width: 100px' src='"+responseTwoObj._embedded.events[i].images[0].url+"'>"+"</td>";//col2
                    theOuterTableText+="<td>"+ "<a href='javascript: getInnerPage("+i+");'>" +responseTwoObj._embedded.events[i].name +"</a></td>"; //col3
                    if(responseTwoObj._embedded.events[i].classifications==undefined){
                        theOuterTableText+="<td>"  + "N/A" + "</td>";
                    }else{
                        theOuterTableText+="<td>" +responseTwoObj._embedded.events[i].classifications[0].segment.name + "</td>";//col4
                    }
                    theOuterTableText+="<td>" +"<a href='javascript:initMap({lat:" + 
                        responseTwoObj.init_lat + ",lng:" + responseTwoObj.init_lng+ "}, {lat:" + 
                        responseTwoObj._embedded.events[i]._embedded.venues[0].location.latitude +",lng:" + responseTwoObj._embedded.events[i]._embedded.venues[0].location.longitude
                        +"}, "+i+");'>" + responseTwoObj._embedded.events[i]._embedded.venues[0].name + "</a></td>";//col5
                    theOuterTableText+="</tr>";
                }
                theOuterTableText+="</table>";
            }
            //console.log(theOuterTableText); 

            document.getElementById("tables").innerHTML=theOuterTableText;

        }




        function getInnerPage(i){

            document.getElementById("theOuterPPage").style.display = "none";
            document.getElementById("VenueInfoAndPhotos").style.display = "block";


            //the following is about:  getEventName
            theInnerEventNameText=""; 

            theInnerEventNameText+="<b>"+ responseTwoObj._embedded.events[i].name+"</b>"; document.getElementById("TheEventName").innerHTML=theInnerEventNameText;

            //done
            //the following is about:  getEventInfo
            var requestFour;
            if (window.XMLHttpRequest){
                requestFour = new XMLHttpRequest();
            }else{
                requestFour = new ActiveXObject("Microsoft.XMLHTTP");
            }
            var theInfoToSvr_3 = "id="+responseTwoObj._embedded.events[i].id;
            requestFour.open("GET", "index.php?"+theInfoToSvr_3, false);
            requestFour.send();
            var responseFourObj= JSON.parse(requestFour.responseText);
            console.log(responseFourObj );

            theInnerTableText="";
            if(responseFourObj.dates.start.localTime==undefined){
                responseFourObj.dates.start.localTime=" ";
            }
            if(responseFourObj.dates.start.localDate==undefined){
                responseFourObj.dates.start.localDate=" ";
            }

            theInnerTableText+= "<table style='width:100%'><tr><td><b>Date</b></td>";
            if(responseFourObj.seatmap==undefined){
                theInnerTableText+="";
            } else{
                theInnerTableText+= "<td rowspan=14><img src='" + responseFourObj.seatmap.staticUrl + "'></td> </tr>";
            }

            theInnerTableText+= "<tr><td>"+ responseFourObj.dates.start.localDate + " " + responseFourObj.dates.start.localTime +"</td>";

            theInnerTableText+="</tr>";


            if (responseFourObj._embedded.attractions==undefined){
                theInnerTableText+="";
            }else{
                theInnerTableText+="<tr><td><b>Artist/Team</b></td></tr><tr>   <td>";
                for (let j = 0; j<responseFourObj._embedded.attractions.length; j++){
                    theInnerTableText+= "<a href ='"+ responseFourObj._embedded.attractions[j].url+"' target='_blank'>" + responseFourObj._embedded.attractions[j].name + "</a>";
                    if (j==responseFourObj._embedded.attractions.length-1){
                        theInnerTableText+="";}
                    else{
                    theInnerTableText+= " | ";
                        }
                }
            }

            theInnerTableText+="</td></tr><tr><td><b> Venue </b></td></tr><tr><td>"+ responseFourObj._embedded.venues[0].name+ "</td></tr>";
            //start of classifications
            if(responseFourObj.classifications==undefined){
                theInnerTableText+="";
            }else{
                
                
                
                
                
                theInnerTableText+="<tr><td><b> Genres </b></td></tr><tr><td>";
                if(responseFourObj.classifications[0].subGenre==undefined || responseFourObj.classifications[0].subGenre.name=="Undefined"){
                    theInnerTableText+="";
                }else{
                    theInnerTableText+=responseFourObj.classifications[0].subGenre.name;
                }
                if(responseFourObj.classifications[0].genre==undefined || responseFourObj.classifications[0].genre.name=="Undefined"){
                    theInnerTableText+="";}
                else{
                    theInnerTableText+=" | "+responseFourObj.classifications[0].genre.name;
                }
                if(responseFourObj.classifications[0].segment==undefined || responseFourObj.classifications[0].segment.name=="Undefined"){
                    theInnerTableText+="";
                }else{
                    theInnerTableText+=" | "+responseFourObj.classifications[0].segment.name;
                }
                if(responseFourObj.classifications[0].subType==undefined || responseFourObj.classifications[0].subType.name=="Undefined"){
                    theInnerTableText+="";
                }else{
                    theInnerTableText+=" | "+responseFourObj.classifications[0].subType.name;
                }
                if(responseFourObj.classifications[0].type==undefined || responseFourObj.classifications[0].type.name=="Undefined"){
                    theInnerTableText+="";
                }else{
                    theInnerTableText+=" | "+responseFourObj.classifications[0].type.name;
                }
                
                
                theInnerTableText+="</td></tr>";
            }
            //end of classifications
            //start of pricerange
            if (responseFourObj.priceRanges==undefined){
                theInnerTableText+="";
            }else {
                if(responseFourObj.priceRanges[0].min==undefined & responseFourObj.priceRanges[0].max!=undefined){
                    theInnerTableText+="<tr><td><b> Price Ranges </b></td></tr><tr>  <td>" + "max price " + responseFourObj.priceRanges[0].max + " " + responseFourObj.priceRanges[0].currency + "</td></tr>";
                }
                else if(responseFourObj.priceRanges[0].max==undefined & responseFourObj.priceRanges[0].min!=undefined){
                    theInnerTableText+="<tr><td><b> Price Ranges </b></td></tr><tr>  <td>" + "min price " + responseFourObj.priceRanges[0].min + " " + responseFourObj.priceRanges[0].currency + "</td></tr>";
                }
                else if(responseFourObj.priceRanges[0].max==undefined & responseFourObj.priceRanges[0].min==undefined){
                    theInnerTableText+="";
                }else{
                    theInnerTableText+="<tr><td><b> Price Ranges </b></td></tr><tr>  <td>" + responseFourObj.priceRanges[0].min + " - " + responseFourObj.priceRanges[0].max + " " + responseFourObj.priceRanges[0].currency + "</td></tr>";
                }
            }
            //end of pricerange
            if(responseFourObj.dates.status.code==undefined | responseFourObj.dates.status.code=="Undefined"){
                responseFourObj.dates.status.code="";
            }
            theInnerTableText+="<tr><td><b> Ticket Status </b></td></tr><tr><td>" + responseFourObj.dates.status.code +  "</td></tr>";

            theInnerTableText+="<tr><td><b> Buy Ticket At:</b></td></tr>    <tr><td>" + "<a href='" + responseFourObj.url +"' target='_blank' >"+"Ticketmaster"+"</a></td></tr>";

            theInnerTableText+="</table>";
            document.getElementById("basicInfo").innerHTML=theInnerTableText;   
            //done the upper upper table

            setTimeout(theFollowing, 1100);
            function theFollowing(){
                var requestThree;
                if (window.XMLHttpRequest){
                    requestThree = new XMLHttpRequest();
                }else{
                    requestThree = new ActiveXObject("Microsoft.XMLHTTP");
                }


                var theInfoToSvr_two = "venueKeyword="+responseTwoObj._embedded.events[i]._embedded.venues[0].name;
                requestThree.open("GET", "index.php?"+theInfoToSvr_two, false);
                requestThree.send();
                var responseThreeObj= JSON.parse(requestThree.responseText);
                console.log(responseThreeObj );
                //VenueInfo
                var theVenueInfoText = "";
                if(responseThreeObj.page.totalElements==0){
                    theVenueInfoText += "<p class='table' style='text-align:center; border: 2px solid #DDDDDD;'><b>No Venue Info or Detail Found </b></p>";
                } else {
                    theVenueInfoText += "<table border ='1' style='width:1000px;' align='center' class='table'>";
                    theVenueInfoText += "<tr><td><b>Name</b></td><td>"+ responseThreeObj._embedded.venues[0].name+"</td></tr>";
                    theVenueInfoText += "<tr><td><b>Map </b></td><td>";

                    theVenueInfoText += "<script>initMap( {lat:" + 
                        responseTwoObj.init_lat + ",lng:" + responseTwoObj.init_lng+ "}, {lat:" + 
                        responseTwoObj._embedded.events[i]._embedded.venues[0].location.latitude +",lng:" + responseTwoObj._embedded.events[i]._embedded.venues[0].location.longitude
                        +"}, "+i+");"+"</"+"script>";

                    theVenueInfoText +="</td></tr>";

                    theVenueInfoText += "<tr><td><b>Address</b></td><td>" + responseThreeObj._embedded.venues[0].address.line1 + "</td></tr>";
                    theVenueInfoText += "<tr><td><b>city </b></td><td>" +responseThreeObj._embedded.venues[0].city.name+"</td></tr>";
                    theVenueInfoText += "<tr><td><b>Postal Code </b></td><td>"+responseThreeObj._embedded.venues[0].postalCode+"</td></tr>";
                    theVenueInfoText += "<tr><td><b>Upcoming Events </b></td><td><a href='"+responseThreeObj._embedded.venues[0].url+"' target='_blank'>" + responseThreeObj._embedded.venues[0].name +" Tickets</a>"+"</td></tr>";


                    theVenueInfoText += "/<table>";

                }
                document.getElementById("VenueInfo").innerHTML=theVenueInfoText;



                //photos
                var thePhotosText = "";
                if(responseThreeObj.page.totalElements==0 || responseThreeObj._embedded.venues[0].images==undefined){
                    thePhotosText += "<p class='table' style='text-align:center; border: 2px solid #DDDDDD;'><b>No Venue Photos Found </b></p>";
                } else {
                    console.log("The amount of photos shown is: " + responseThreeObj._embedded.venues[0].images.length);
                    thePhotosText += "<table border='1' style='max-width:1000px;' align='center' class='table'>";
                    for (var k = 0; k<responseThreeObj._embedded.venues[0].images.length; k++){
                        thePhotosText += "<tr><td align='center'><img src = '"+ responseThreeObj._embedded.venues[0].images[k].url + "' style='max-width:998px;' ></td></tr>";        
                    }
                    thePhotosText += "</table>";

                }
                document.getElementById("photos").innerHTML=thePhotosText; 
            }
        }


    </script>

    <script type="text/javascript">

        function initMap(departLocation, arriveLocation, rowNumber ){


            if(document.getElementById("GoogleMap").style.display!="none"){
                var x = document.getElementsByClassName("goThereButton");
                var i = 0;
                while (i<x.length){
                    x[i].style.display = 'none';
                    i++;
                }
                document.getElementById("GoogleMap").style.display="none";
            }
            var y = document.getElementsByClassName("goThereButton");
            for (var q = 0; q<y.length; q++){
                y[q].style.display = "block";
            }

            document.getElementById("GoogleMap").style.display="block";

            var formula = rowNumber*68+290;

             
            document.getElementById("GoogleMap").style.top = formula+"px";  
            document.getElementById("walkThereButton").style.top = formula+"px";
            document.getElementById("bikeThereButton").style.top =30+ formula+"px";    
            document.getElementById("driveThereButton").style.top = 60+ formula+"px"; 
            document.getElementById("walkThereButton").style.left = 680+ "px";
            document.getElementById("bikeThereButton").style.left = 680+ "px";    
            document.getElementById("driveThereButton").style.left = 680+ "px"; 


            var directionsService = new google.maps.DirectionsService;
            var directionsDisplay = new google.maps.DirectionsRenderer;

            var map = new google.maps.Map(document.getElementById("GoogleMap"), {
                zoom: 11,
                center: departLocation
            });
            var marker = new google.maps.Marker({
                position: departLocation,
                map: map
            });
            directionsDisplay.setMap(map);


            document.getElementById("walkThereButton").addEventListener("click", function() {
                calculateAndDisplayRoute(directionsService, directionsDisplay,  departLocation, arriveLocation,"WALKING");
                marker.setMap(null);
            } );
            document.getElementById("bikeThereButton").addEventListener("click", function() {
                calculateAndDisplayRoute(directionsService, directionsDisplay,  departLocation, arriveLocation,"BICYCLING");
                marker.setMap(null);
            } );
            document.getElementById("driveThereButton").addEventListener("click", function() {
                calculateAndDisplayRoute(directionsService, directionsDisplay,  departLocation, arriveLocation,"DRIVING");
                marker.setMap(null);
            } );        	
        }

        function calculateAndDisplayRoute(directionsService, directionsDisplay, departLocation, arriveLocation, trafficTool) {
            console.log(trafficTool);
            directionsService.route({
                origin: departLocation,
                destination: arriveLocation,
                travelMode: trafficTool
            }, function(response, status) {
                if (status === 'OK') {
                    directionsDisplay.setDirections(response);
                } else {
                    window.alert("Failure due to: " + status);
                }
            }
                                   );
        }
    </script>




    <script type="text/javascript">
        function shownArrowUp(sth){

            var div_arrow_down1 = document.getElementById("toShowVenueInfo");//0
            var div_arrow_up1   = document.getElementById("toHideVenueInfo");//1
            var div_arrow_down2 = document.getElementById("toShowPhotos");//2
            var div_arrow_up2   = document.getElementById("toHidePhotos");//3
            var theVenueInfo= document.getElementById("VenueInfo");//4
            var thePhotos = document.getElementById("photos");//5

            var fruits = [div_arrow_down1, div_arrow_up1,div_arrow_down2, div_arrow_up2, theVenueInfo, thePhotos];

            if(sth==false){//
                if(fruits[2].style.display=="block"){ //expand thePhotos
                    fruits[2].style.display="none";
                    fruits[3].style.display="block";
                    fruits[5].style.display ="block";

                    fruits[0].style.display = "block";
                    fruits[1].style.display = "none";
                    fruits[4].style.display = "none";
                }
                else{ //hide thePhotos
                    fruits[2].style.display="block";
                    fruits[3].style.display="none";

                    fruits[5].style.display ="none";   					   					
                }
            }
            else{
                if(fruits[0].style.display=="block"){//expand theVenueInfo
                    fruits[0].style.display="none";
                    fruits[1].style.display="block";
                    fruits[4].style.display="block";

                    fruits[2].style.display = "block";
                    fruits[3].style.display = "none";	  	
                    fruits[5].style.display = "none";
                }
                else{//hide theVenueInfo
                    fruits[0].style.display="block";
                    fruits[1].style.display="none";

                    fruits[2].style.display = "block";
                    fruits[3].style.display = "none";
                    fruits[4].style.display ="none";   	

                }
            }

        }

        function disableTheRadio(){

            var theradio1 = document.getElementById("fromHere");
            var theradio2= document.getElementById("OtherLocRadio");

            if (theradio2.checked == true){ 
                document.getElementById("theOtherLoc").disabled=false;
            } else if (theradio1.checked == true){ 
                document.getElementById("theOtherLoc").disabled=true;

            }
            else{return;}
        }


        function clear_thisthe_Page(){	
            document.getElementById("tables").innerHTML="";
            document.getElementById("GoogleMap").innerHTML="";
            document.getElementById("TheEventName").innerHTML="";
            document.getElementById("basicInfo").innerHTML="";
            document.getElementById("theOuterPPage").style.display="block";
            if(document.getElementById("fromHere").checked = true){
                document.getElementById("theOtherLoc").disabled = true;
            }
            document.getElementById("keyword").value="";
            document.getElementById("distance").value="";
            document.getElementById("VenueInfo").innerHTML="";
            document.getElementById("photos").innerHTML="";
            document.getElementById("walkThereButton").style.display="none";
            document.getElementById("bikeThereButton").style.display="none";
            document.getElementById("driveThereButton").style.display="none";

            document.getElementById("VenueInfoAndPhotos").style.display="none";
            console.log("cleared the page.");


        }
    </script>


</html>