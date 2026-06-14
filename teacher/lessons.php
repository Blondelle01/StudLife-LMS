<?php

include("../config/database.php");

if(isset($_POST['upload'])){

$titre=$_POST['titre'];

$course=$_POST['course'];

$type=$_POST['type'];

$file=$_FILES['file'];

$filename=time()."_".$file['name'];

if($type=="pdf"){

move_uploaded_file(
$file['tmp_name'],
"../uploads/pdf/".$filename
);

}

if($type=="video"){

move_uploaded_file(
$file['tmp_name'],
"../uploads/videos/".$filename
);

}

$sql="INSERT INTO lessons
(course_id,titre,contenu,type)
VALUES
('$course','$titre','$filename','$type')";

$conn->query($sql);

}
?>

<form method="POST"
enctype="multipart/form-data">

<input type="text"
name="titre"
placeholder="Titre leçon">

<select name="type">

<option value="pdf">
PDF
</option>

<option value="video">
Vidéo
</option>

</select>

<input type="file"
name="file">

<button name="upload">
Téléverser
</button>

</form>