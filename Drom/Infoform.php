<?php require_once ("../Connections/Adventure.php");?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
<meta charset="utf-8">
<title></title>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC0e83RwQM34xIUikBGbPOzenGvKn8qplo&signed_in=true"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js" type="text/javascript"></script>
<style type="text/css">
	  .hider {
	  display:none;
	  }
</style>
</head>
<body>
<?php 
	$id = $_POST['ID'];
?>
<div class="hider" style="cursor:pointer; position:fixed; margin-top:4%; margin-left:94%; z-index:99999;">
Infowindow - Marker #<?php echo $id;?>
</div>
<div id="hidden" class="hidden" style="display:none; width:270px; height:auto; position:fixed; margin-top:6%; margin-left:85%; z-index:99999; background-color:#FFFFFF;">
	<form method="post" id="InfoForm" name="InfoForm" onSubmit="return duplication();"><br>
<?php 
	$info_win = mysqli_query($Adventure, "SELECT * FROM `marks` WHERE id=$id");
	$i_w = mysqli_fetch_array($info_win);
	
	if($id!=0){
	?><style type="text/css"> 
.hider { 
display:block; 
} 
</style> 
<?php }?>	
	<input type="hidden" name="id" value="<?php echo $i_w['id'];?>"/>
	<input type="hidden" name="photos" id="photos" value="<?php echo $i_w['photos'];?>"/>
	<table>
		  <tr><td>Title:</td> <td><input type="text" name="title" value="<?php echo $i_w['title'];?>" style="width: 150px; outline: none; resize: none;"/> </td> </tr>
		  <tr><td>Date:</td> <td><input type="date" name="date" value="<?php echo date("Y-m-d", strtotime($i_w['time']));?>" style="width: 150px; outline: none; resize: none;"/> </td> </tr>
		  <tr><td>Time:</td> <td><input type="time" name="time" value="<?php echo date("H:i:s", strtotime($i_w['time']));?>" style="width: 150px; outline: none; resize: none;" step="1"/> </td> </tr>
		  <tr><td>Latitude:</td> <td><input type="text" value="<?php echo $i_w['lat'];?>" name="lat" style="width: 150px; outline: none; resize: none;" readonly/> </td> </tr>
		  <tr><td>Longitude:</td> <td><input type="text" value="<?php echo $i_w['lng'];?>" name="lng" style="width: 150px; outline: none; resize: none;" readonly/> </td> </tr>
		  <tr><td>Comment:</td> <td><textarea type="text" name="comment" style="width: 148px; outline: none; resize: none;"><?php echo $i_w['comment'];?></textarea> </td> </tr>
		  <tr><td>Photos:</td> <td>
<?php 
		$photo=$i_w['photos'];
		$photos=explode(",",$photo);
		if(empty($photo)) echo "There aren't any photos yet";
		else for($i=0;$i<count($photos);$i++)
{ 
?>
		<a href="<?php echo $photos[$i];?>" target="_blank"><img src="<?php echo $photos[$i];?>" width="150px"></a><br>
<?php 
} 
?>
		  </td> </tr>
		  <tr><td>Add photo:</td> <td><input type="text" value="" name="addphoto" id="addphoto" style="width: 150px; outline: none; resize: none;" placeholder="Image URL"/> </td> </tr>
		  <tr><td><input name="Save" type="submit" value="Save" ></td> <td><a href="#" class="deletelink">Delete photo</a></td></tr>
	</table>
</div>
<div id="delete" class="delete" style="display:none; width:270px; height:auto; position:fixed; margin-top:6%; margin-left:70%; z-index:99999; background-color:#FFFFFF;">
<table>
<?php 
	for($j=0;$j<count($photos);$j++)
{ 
if(!empty($photo))
{
	$img=explode("/", $photos[$j]);
	$l=count($img);
?>
	<tr><td><input type="checkbox" name="photo[]" value="<?php echo $photos[$j];?>" /><?php echo $img[$l-1];?></td></tr>
<?php 
}
}

if($_POST['Save'])
{
	$photo =  $_POST['photos'];
	$photos=explode(",",$photo);
	$id = $_POST['id'];
	$title = $_POST['title'];
	$date = $_POST['date'];
	$time = $_POST['time'];
	$timestamp=date("Y-m-d H:i:s", strtotime($date.$time));
	$comment = $_POST['comment'];
	$addphoto= $_POST['addphoto'];
	//UPDATE
	if($_POST['addphoto'])
	{
	if(empty($photo)) $upd_photo=$addphoto;
	else $upd_photo=$photo.",".$addphoto;
	$upd_info = mysqli_query($Adventure, "UPDATE `marks` SET title='$title', time='$timestamp', photos='$upd_photo', comment='$comment' WHERE id=$id");
	}
	else $upd_info = mysqli_query($Adventure, "UPDATE `marks` SET title='$title', time='$timestamp', comment='$comment' WHERE id=$id");
	//END
	
	
	$photodel=$photo;
	for($g=0;$g<count($photos);$g++)
{ 
	if($_POST['photo'][$g])
	{
	$selectedOption=$_POST['photo'][$g];
	if(empty($photos)) 	$upd_photos = mysqli_query ($Adventure, "UPDATE `marks` SET photos=NULL WHERE id=$id");
	else 
	{
	$newphoto = str_replace($selectedOption,"", $photodel);
	$newphoto2 = str_replace(",,",",", $newphoto);
	$newphoto3=trim($newphoto2,",");
	$upd_photos = mysqli_query ($Adventure, "UPDATE `marks` SET photos='$newphoto3' WHERE id=$id");
	$photodel=$newphoto3;
	}
	}
}
}
?>
</table>
</div>
	<script>
	//UPDATE
function duplication()
{
	var addphoto=document.getElementById('addphoto').value;
	var photos=document.getElementById('photos').value;
	var photo=photos.split(',');
	var not_error=true;
	for(var y=0;y<photo.length;y++)
{
	if(addphoto==photo[y]) not_error=false;
}
	if(not_error==false) alert("Error: The same link already exists. Please enter a new link");
	return not_error;
};
	//END
	</script>

	</form>
<script type="text/javascript">//
$(document).ready(function(){
    $(".hider").click(function(){
        $("#hidden").slideToggle("slow");
        return false;
    });
});

$(document).ready(function(){
    $(".deletelink").click(function(){
        $("#delete").slideToggle("slow");
        return false;
    });
});
</script>

</body>
</html>