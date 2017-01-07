<?php
$uploaddir = 'files/';
$uploadfile = $uploaddir.basename($_FILES['myfile']['name']);

$ext = pathinfo($_FILES['myfile']['name']);
if(strcmp($ext['extension'], 'csv')) 
   die('Данный тип файла фотографий не поддерживается.');

move_uploaded_file($_FILES['myfile']['tmp_name'], $uploadfile);

?>