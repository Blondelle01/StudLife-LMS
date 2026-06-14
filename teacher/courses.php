<?php

include("../config/database.php");

if(isset($_POST['save'])){

$module=$_POST['module'];

$titre=$_POST['titre'];

$description=$_POST['description'];

$teacher=1;

$sql="INSERT INTO courses
(module_id,teacher_id,titre,description)
VALUES
('$module','$teacher','$titre','$description')";

$conn->query($sql);

}
?>

<form method="POST">

<select name="module">

<?php

$result=$conn->query("SELECT * FROM modules");

while($row=$result->fetch_assoc()){

echo "<option value='".$row['id']."'>"
.$row['titre'].
"</option>";

}
?>

</select>

<input type="text"
name="titre"
placeholder="Titre du cours">

<textarea
name="description">
</textarea>

<button name="save">
Ajouter
</button>

</form>