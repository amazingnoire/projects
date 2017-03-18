<?php require_once ("../Connections/Adventure.php"); include('Infoform.php');?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
<meta charset="utf-8">
<title></title>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC0e83RwQM34xIUikBGbPOzenGvKn8qplo&signed_in=true"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js" type="text/javascript"></script>
<style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #map {
        height: 100%;
      }
      .delete-menu {
        position: absolute;
        background: white;
        padding: 3px;
        color: #666;
        font-weight: bold;
        border: 1px solid #999;
        font-family: sans-serif;
        font-size: 12px;
        box-shadow: 1px 3px 3px rgba(0, 0, 0, .3);
        margin-top: -10px;
        margin-left: 10px;
        cursor: pointer;
      }
      .delete-menu:hover {
        background: #eee;
      }
</style>
<script type="text/javascript">
	var polyCoordinates = [];
	var polyPath  = [];
	//var new_p_position;
	  
	var marker;
	var markers = [];
	var id_marker;
	var title_marker=[];
	var time_marker=[];
	var photos_marker=[];
	var comment_marker=[];
	var mark_title;
	var mark_time;
	var mark_photos;
	var mark_comment;
	var added_markers=[];
	  
	var point_lat;
	var point_lng;
	var mark_lat;
	var mark_lng;
	var new_mark_lat;
	var new_mark_lng;
	  
	var pathArr;
	  
	var add_point=false;
	var add_mark=false;
	
	var del_point=false;
	var del_mark=false;

	var curPosition_mark;
		
  //УДАЛЕНИЕ маркера
var bindMarkerEvents = function(marker) 
{
    google.maps.event.addListener(marker, "rightclick", function () 
{
	var m_position=marker.getPosition();
	del_mark=true;
	for(var i=0;i<markers.length;i++)
{
	if(markers[i]==m_position)
{
	markers.splice(i, 1);
	title_marker.splice(i, 1);
	time_marker.splice(i, 1);
	photos_marker.splice(i, 1);
	comment_marker.splice(i, 1);
	marker.setMap(null);
}
}
//UPDATE
	for(var i=0;i<added_markers.length;i++)
{
	if(added_markers[i]==m_position)
{
	added_markers.splice(i, 1);
}
}
//END
});
};
	
function initialize() 
{
	var mapOptions = 
{
    zoom: 3,
    center: new google.maps.LatLng(0, -180),
    mapTypeId: google.maps.MapTypeId.TERRAIN,
	disableDoubleClickZoom: true
};

	var map = new google.maps.Map(document.getElementById('map'), mapOptions);
	var deleteMenu = new DeleteMenu();
  //ОТОБРАЖЕНИЕ
<?php
	$count_m = mysqli_query($Adventure, "SELECT COUNT(*) FROM `marks`");
	$count_mark = mysqli_fetch_row($count_m);
	
	for ($j2=0; $j2<$count_mark[0]; $j2++) 
{ 
	$select_mark = mysqli_query ($Adventure, "SELECT * FROM `marks` WHERE id=$j2+1"); 
	$sel_m = mysqli_fetch_array($select_mark); 
?>   
	var lat=<?php echo $sel_m["lat"];?>;
	var lng=<?php echo $sel_m["lng"];?>;

	var position = new google.maps.LatLng(lat, lng);
	markers.push( position );
	marker = new google.maps.Marker(
{
    position: position,
    map: map,
	draggable:true
});
    bindMarkerEvents(marker); 
	Displacement(marker);
	InfoWindow(marker);
	marker.setMap(map);
<?php 
}?>
  
<?php
	$count_p = mysqli_query($Adventure, "SELECT COUNT(*) FROM `points`");
	$count_point = mysqli_fetch_row($count_p);
	
	for ($j=0; $j<$count_point[0]; $j++) 
{ 
	$select_point = mysqli_query ($Adventure, "SELECT * FROM `points` WHERE id=$j+1"); 
	$sel_p = mysqli_fetch_array($select_point); 
?>   
	var lat=<?php echo $sel_p["lat"];?>;
	var lng=<?php echo $sel_p["lng"];?>;

	var position = new google.maps.LatLng(lat, lng);
	polyCoordinates.push( position );
<?php 
}?>

polyPath = new google.maps.Polyline(
  {
    path: polyCoordinates,
    geodesic: true,
    strokeColor: '#FF0000',
    strokeOpacity: 1.0,
    strokeWeight: 2,
	map: map,
	editable: true,
	draggable: true
});

polyPath.setMap(map);
  
//ДОБАВЛЕНИЕ маркера
var getLatLng = function(lat, lng) 
{
    return new google.maps.LatLng(lat, lng);
};

google.maps.event.addListener(polyPath, 'click', function(e) 
{
    var lat = e.latLng.lat(); 
    var lng = e.latLng.lng(); 
    marker = new google.maps.Marker(
	{
        position: getLatLng(lat, lng),
        map: map,
		draggable:true,
		animation: google.maps.Animation.BOUNCE
    });
	markers.push(marker.getPosition());
	added_markers.push(marker.getPosition());
    bindMarkerEvents(marker); 
	Displacement(marker);
	InfoWindow(marker);
	add_mark=true;
	toggleBounce();
});



//ДОБАВЛЕНИЕ ломаной			 
			 
google.maps.event.addListener(map, "dblclick", function (event) 
{
	polyPath.setMap(map);         
	polyPath.getPath().push(event.latLng);
	polyCoordinates.push(event.latLng);
	add_point=true;
});  
 		    
google.maps.event.addListener(polyPath, 'rightclick', function(e)
{
	if (e.vertex == undefined) 
{
	return;
}
	deleteMenu.open(map, polyPath.getPath(), e.vertex);
});

//google.maps.event.addListener(polyPath, 'mouseup', function(e) {
 //   new_p_position=e.latLng; 
//});

//google.maps.event.addListener(polyPath, "mouseup", function() {
//  new_p_position = polyPath.getPath().getArray().toString();
// });
}

var Displacement = function(marker)
{
	google.maps.event.addListener(marker, "mousedown", function () 
{
	curPosition_mark=marker.getPosition();
});

google.maps.event.addListener(marker, "mouseup", function () 
{
	for(var i=0;i<markers.length;i++)
{
	if(markers[i]==curPosition_mark)
{
	markers[i]=marker.getPosition();
}
}
});	
}
//УДАЛЕНИЕ ломаной

function DeleteMenu() 
{
	this.div_ = document.createElement('div');
	this.div_.className = 'delete-menu';
	this.div_.innerHTML = 'Delete';
	
	var menu = this;
	google.maps.event.addDomListener(this.div_, 'click', function() 
{
	menu.removeVertex();
});
}

DeleteMenu.prototype = new google.maps.OverlayView();

DeleteMenu.prototype.onAdd = function() 
{
	var deleteMenu = this;
	var map = this.getMap();
	this.getPanes().floatPane.appendChild(this.div_);
	
	this.divListener_ = google.maps.event.addDomListener(map.getDiv(), 'mousedown', function(e) 
{
	if (e.target != deleteMenu.div_) 
{
	deleteMenu.close();
}
}, true);
};

DeleteMenu.prototype.onRemove = function() 
{
	google.maps.event.removeListener(this.divListener_);
	this.div_.parentNode.removeChild(this.div_);
	
	this.set('position');
	this.set('path');
	this.set('vertex');
};

DeleteMenu.prototype.close = function() 
{
	this.setMap(null);
};

DeleteMenu.prototype.draw = function() 
{
	var position = this.get('position');
	var projection = this.getProjection();

	if (!position || !projection) 
{
	return;
}

	var point = projection.fromLatLngToDivPixel(position);
	this.div_.style.top = point.y + 'px';
	this.div_.style.left = point.x + 'px';
};


DeleteMenu.prototype.open = function(map, path, vertex) 
{
	this.set('position', path.getAt(vertex));
	this.set('path', path);
	this.set('vertex', vertex);
	this.setMap(map);
	this.draw();
};


DeleteMenu.prototype.removeVertex = function() 
{
	var path = this.get('path');
	var vertex = this.get('vertex');

	if (!path || vertex == undefined) 
{
	this.close();
	return;
}
	del_point=true;
	path.removeAt(vertex);
	polyCoordinates.splice(vertex, 1);
	this.close();
};

</script>
</head>
<body>
<script>
<?php
	$count_m2 = mysqli_query($Adventure, "SELECT COUNT(*) FROM `marks`");
	$count_mark2 = mysqli_fetch_row($count_m2);
	
	for ($x=0; $x<$count_mark2[0]; $x++) 
{ 
	$select_mark2 = mysqli_query ($Adventure, "SELECT * FROM `marks` WHERE id=$x+1"); 
	$sel_m2 = mysqli_fetch_array($select_mark2); 
?>   
	var title='<?php echo $sel_m2["title"];?>';
	var time='<?php echo $sel_m2["time"];?>';
	var photos='<?php echo $sel_m2["photos"];?>';
	var comment='<?php echo $sel_m2["comment"];?>';
	title_marker.push( title );
	time_marker.push( time );
	photos_marker.push( photos );
	comment_marker.push( comment );
<?php 
}?>
</script>
<div style="position:fixed; margin-top:1%; margin-left:96%; z-index:99998;">
<form id="myForm" name="myForm" method="POST">
<input id="TITLE" name="TITLE" type="hidden" value="">
<input id="ID" name="ID" type="hidden" value="">
<input id="POSITION" name="POSITION" type="hidden" value="">
<input id="len_p" name="len_p" type="hidden" value="">
<input id="len_m" name="len_m" type="hidden" value="">
<input id="count_p" name="count_p" type="hidden" value="">
<input id="count_m" name="count_m" type="hidden" value="">
<input id="del_mark" name="del_mark" type="hidden" value="">
<input id="del_point" name="del_point" type="hidden" value="">
<input name="go" type="submit" onClick="updPoints()" value="Save">
<script>
//ИНФООКНО
	var InfoWindow = function(marker) 
{
google.maps.event.addListener(marker, 'click', function() 
{
	var m_position=marker.getPosition();
	for(var i=0;i<markers.length;i++)
{
	if(markers[i]==m_position)
{
	id_marker = i+1;
	$('#ID').val(id_marker);
		if(added_markers.length==0) 
		document.getElementById("myForm").submit();
	else alert("Please save new marks before trying to edit it");
}
}
});
}
function updPoints() {
<?php 
	$count_p = mysqli_query($Adventure, "SELECT COUNT(*) FROM `points`");
	$cnt_p = mysqli_fetch_row($count_p);
	
	$count_m = mysqli_query($Adventure, "SELECT COUNT(*) FROM `marks`");
	$cnt_m = mysqli_fetch_row($count_m);
?>
	var count_p=<?php echo $cnt_p[0];?>;
	var count_m=<?php echo $cnt_m[0];?>;		
	
	var upd_point_1=[];
	var upd_point_2=[];
	var upd_point_3=[];
	
	var ins_point_1=[];
	var ins_point_2=[];
	var ins_point_3=[];
	
	var upd_mark_1=[];
	var upd_mark_2=[];
	var upd_mark_3=[];
	var upd_mark_4=[];
	var upd_mark_5=[];
	var upd_mark_6=[];
	var upd_mark_7=[];
	
	var ins_mark_1=[];
	var ins_mark_2=[];
	var ins_mark_3=[];
		
	pathArr = polyPath.getPath();
	var len_p=pathArr.length;
	$('#len_p').val(len_p);
	$('#count_p').val(count_p);
	
	var len_m=markers.length;
	$('#len_m').val(len_m);
	$('#count_m').val(count_m);
	
	$('#del_mark').val(del_mark);
	$('#del_point').val(del_point);
	
	$('#POSITION').val(curPosition_mark);
	$('#TITLE').val(title_marker[0]);
	
	for (var i = 0; i < len_m; i++)
{
	mark_lat=markers[i].lat();
	mark_lng=markers[i].lng();
	mark_title=title_marker[i];
	mark_time=time_marker[i];
	mark_photos=photos_marker[i];
	mark_comment=comment_marker[i];
	
    upd_mark_1[i+1] = document.createElement('input');
    upd_mark_1[i+1].type = 'hidden';
    upd_mark_1[i+1].name = 'mark_lat['+(i+1)+']';
    upd_mark_1[i+1].value = mark_lat;    
    document.getElementById("myForm").appendChild( upd_mark_1[i+1] );
	
	upd_mark_2[i+1] = document.createElement('input');
    upd_mark_2[i+1].type = 'hidden';
    upd_mark_2[i+1].name = 'mark_lng['+(i+1)+']';
    upd_mark_2[i+1].value = mark_lng;    
    document.getElementById("myForm").appendChild( upd_mark_2[i+1] );
	
	upd_mark_3[i+1] = document.createElement('input');
    upd_mark_3[i+1].type = 'hidden';
    upd_mark_3[i+1].name = 'id_mark['+(i+1)+']';
    upd_mark_3[i+1].value = i+1;    
    document.getElementById("myForm").appendChild( upd_mark_3[i+1] );
	
	upd_mark_4[i+1] = document.createElement('input');
    upd_mark_4[i+1].type = 'hidden';
    upd_mark_4[i+1].name = 'mark_title['+(i+1)+']';
    upd_mark_4[i+1].value = mark_title;    
    document.getElementById("myForm").appendChild( upd_mark_4[i+1] );
	
	upd_mark_5[i+1] = document.createElement('input');
    upd_mark_5[i+1].type = 'hidden';
    upd_mark_5[i+1].name = 'mark_time['+(i+1)+']';
    upd_mark_5[i+1].value = mark_time;    
    document.getElementById("myForm").appendChild( upd_mark_5[i+1] );
	
	upd_mark_6[i+1] = document.createElement('input');
    upd_mark_6[i+1].type = 'hidden';
    upd_mark_6[i+1].name = 'mark_photos['+(i+1)+']';
    upd_mark_6[i+1].value = mark_photos;    
    document.getElementById("myForm").appendChild( upd_mark_6[i+1] );
	
	upd_mark_7[i+1] = document.createElement('input');
    upd_mark_7[i+1].type = 'hidden';
    upd_mark_7[i+1].name = 'mark_comment['+(i+1)+']';
    upd_mark_7[i+1].value = mark_comment;    
    document.getElementById("myForm").appendChild( upd_mark_7[i+1] );
	
};
<?php 
	$len_m = $_POST['len_m'];
	$del_mark = $_POST['del_mark'];
	
	for($j=1;$j<$len_m+1;$j++)
{
	$mark_lat = $_POST['mark_lat'][$j];
	$mark_lng = $_POST['mark_lng'][$j];
	$mark_title = $_POST['mark_title'][$j];
	$mark_time = $_POST['mark_time'][$j];
	$mark_photos = $_POST['mark_photos'][$j];
	$mark_comment = $_POST['mark_comment'][$j];
	$id = $_POST['id'][$j];
	
	$update = mysqli_query ($Adventure, "UPDATE `marks` SET title = '$mark_title', lat = '$mark_lat', lng = '$mark_lng', time = '$mark_time', photos = '$mark_photos', comment = '$mark_comment' WHERE id = $id");
}
	if($len_m==0 && $del_mark==true) $delete = mysqli_query ($Adventure, "DELETE FROM `marks`");
	else if ($del_mark==true) $delete = mysqli_query ($Adventure, "DELETE FROM `marks` WHERE id>$id");
?>

	for (var i = 0; i < len_p; i++)
{
	point_lat=pathArr.getAt(i).lat();
	point_lng=pathArr.getAt(i).lng();
		
    upd_point_1[i+1] = document.createElement('input');
    upd_point_1[i+1].type = 'hidden';
    upd_point_1[i+1].name = 'point_lat['+(i+1)+']';
    upd_point_1[i+1].value = point_lat;    
    document.getElementById("myForm").appendChild( upd_point_1[i+1] );
	
	upd_point_2[i+1] = document.createElement('input');
    upd_point_2[i+1].type = 'hidden';
    upd_point_2[i+1].name = 'point_lng['+(i+1)+']';
    upd_point_2[i+1].value = point_lng;    
    document.getElementById("myForm").appendChild( upd_point_2[i+1] );
	
	upd_point_3[i+1] = document.createElement('input');
    upd_point_3[i+1].type = 'hidden';
    upd_point_3[i+1].name = 'id['+(i+1)+']';
    upd_point_3[i+1].value = i+1;    
    document.getElementById("myForm").appendChild( upd_point_3[i+1] );
};
<?php 
	$len_p = $_POST['len_p'];
	$del_point = $_POST['del_point'];
	
	for($j=1;$j<$len_p+1;$j++)
{
	$point_lat = $_POST['point_lat'][$j];
	$point_lng = $_POST['point_lng'][$j];
	$id = $_POST['id'][$j];
	
	$update = mysqli_query ($Adventure, "UPDATE `points` SET lat = '$point_lat', lng = '$point_lng' WHERE id = $id");
}
	if($len_p==0 && $del_point==true) $delete = mysqli_query ($Adventure, "DELETE FROM `points`");
	else if ($del_point==true) $delete = mysqli_query ($Adventure, "DELETE FROM `points` WHERE id>$id");
?>

	if(add_mark==true)
{
	for (var i2 = count_m; i2 < len_m; i2++)
{
	mark_lat=markers[i2].lat();
	mark_lng=markers[i2].lng();

    ins_mark_1[i2+1] = document.createElement('input');
    ins_mark_1[i2+1].type = 'hidden';
    ins_mark_1[i2+1].name = 'mark_lat['+(i2+1)+']';
    ins_mark_1[i2+1].value = mark_lat;    
    document.getElementById("myForm").appendChild( ins_mark_1[i2+1] );
	
	ins_mark_2[i2+1] = document.createElement('input');
    ins_mark_2[i2+1].type = 'hidden';
    ins_mark_2[i2+1].name = 'mark_lng['+(i2+1)+']';
    ins_mark_2[i2+1].value = mark_lng;    

    document.getElementById("myForm").appendChild( ins_mark_2[i2+1] );
	
	ins_mark_3[i2+1] = document.createElement('input');
    ins_mark_3[i2+1].type = 'hidden';
    ins_mark_3[i2+1].name = 'id['+(i2+1)+']';
    ins_mark_3[i2+1].value = i2+1;    
    document.getElementById("myForm").appendChild( ins_mark_3[i2+1] );
};
<?php 
	$len_m = $_POST['len_m'];
	$count_m = $_POST['count_m'];
	
	if($count_m==0)
{
	for($j2=1;$j2<$len_m+1;$j2++)
{
	$mark_lat = $_POST['mark_lat'][$j2];
	$mark_lng = $_POST['mark_lng'][$j2];

	$insert = mysqli_query ($Adventure, "INSERT INTO `marks` (id, title, lat, lng, time, photos, comment) VALUES ('$j2', NULL, '$mark_lat', '$mark_lng', NULL, NULL, NULL)");
}
}
	else 
{
	for($j2=$count_m+1;$j2<$len_m+1;$j2++)
{
	$mark_lat = $_POST['mark_lat'][$j2];
	$mark_lng = $_POST['mark_lng'][$j2];

	$count_m=mysqli_query($Adventure, "SELECT COUNT(*) FROM `marks`");
	$cnt_m = mysqli_fetch_row($count_m);
	$n_id=$cnt_m[0]+1;

	$insert = mysqli_query ($Adventure, "INSERT INTO `marks` (id, lat, lng) VALUES ('$n_id', '$mark_lat', '$mark_lng')");
}
}
?>
}

	if(add_point==true)
{
	for (var i2 = count_p; i2 < len_p; i2++)
{
	point_lat=pathArr.getAt(i2).lat();
	point_lng=pathArr.getAt(i2).lng();	

    ins_point_1[i2+1] = document.createElement('input');
    ins_point_1[i2+1].type = 'hidden';
    ins_point_1[i2+1].name = 'point_lat['+(i2+1)+']';
    ins_point_1[i2+1].value = point_lat;    
    document.getElementById("myForm").appendChild( ins_point_1[i2+1] );
	
	ins_point_2[i2+1] = document.createElement('input');
    ins_point_2[i2+1].type = 'hidden';
    ins_point_2[i2+1].name = 'point_lng['+(i2+1)+']';
    ins_point_2[i2+1].value = point_lng;    

    document.getElementById("myForm").appendChild( ins_point_2[i2+1] );
	
	ins_point_3[i2+1] = document.createElement('input');
    ins_point_3[i2+1].type = 'hidden';
    ins_point_3[i2+1].name = 'id['+(i2+1)+']';
    ins_point_3[i2+1].value = i2+1;    
    document.getElementById("myForm").appendChild( ins_point_3[i2+1] );
};
<?php 
	$len_p = $_POST['len_p'];
	$count_p = $_POST['count_p'];
	
	if($count_p==0)
{
	for($j2=1;$j2<$len_p+1;$j2++)
{
	$point_lat = $_POST['point_lat'][$j2];
	$point_lng = $_POST['point_lng'][$j2];

	$insert = mysqli_query ($Adventure, "INSERT INTO `points` (id, lat, lng) VALUES ('$j2', '$point_lat', '$point_lng')");
}
}
	else 
{
	for($j2=$count_p+1;$j2<$len_p+1;$j2++)
{
	$point_lat = $_POST['point_lat'][$j2];
	$point_lng = $_POST['point_lng'][$j2];

	$count_p=mysqli_query($Adventure, "SELECT COUNT(*) FROM `points`");
	$cnt_p = mysqli_fetch_row($count_p);
	$n_id=$cnt_p[0]+1;

	$insert = mysqli_query ($Adventure, "INSERT INTO `points` (id, lat, lng) VALUES ('$n_id', '$point_lat', '$point_lng')");
}
}
?>
}

//GDownloadUrl("upload.php", function(){}, "poly_lat="+poly_lat+"poly_lng="+poly_lng);
}
</script>
<?php 
	if($_POST['go'])
	echo "<meta http-equiv='refresh' content='0;url=http://localhost/GPSTracker/OnlyOne.php'>";
?>
</form>
</div>
<div id="map"></div>
<script>google.maps.event.addDomListener(window, 'load', initialize);</script>
</body>
</html>