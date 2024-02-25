<?php
$connect = mysqli_connect('localhost','root','root','database');
// Проверка соединения
if (!$connect) {
    die("Подключение не удалось: " . mysqli_connect_error());
 }
echo "Подключение успешно установлено";
// $connect->close();
