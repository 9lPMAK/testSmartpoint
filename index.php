<?php
require_once 'connect.php'; //(подключить 1 раз к БД) 
include_once __DIR__ . '/phpQuery-onefile.php'; // (библиотека для парсинга) 

//подключаем файл коннект с соединением и проверкой
$goods = mysqli_query($connect, "SELECT * FROM `otzivs`");

function getcontents($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}
 
$doc = phpQuery::newDocument(getcontents('https://101hotels.com/opinions/hotel/volzhskiy/gostinitsa_ahtuba.html#reviews-container'));

// ====================================== массив с именами ================================

$arrNames = array();
$entry = $doc->find('.reviewer');
foreach($entry AS $item){
	$str = pq($item)->text();
	$str = explode(' ',$str)[40];
	$str = preg_replace('/[0-9]+/', '', $str);
	$arrNames[] = $str;
	
}

// ====================================== массив с датами ================================
$arrDate = array();
$entry = $doc->find('.review-date');
foreach($entry AS $item){
	$str = pq($item)->text($val);
	$arrDate[] =  $str;
}

// ====================================== массив с отзывами ================================
$arrOtziv = array();
$entry = $doc->find('.review');
foreach($entry AS $item){
	$str = pq($item)->text($val);
	$str = preg_replace('/[0-9]+/', '', $str);
	$str = trim(str_replace(' ', '', $str));
	$str = trim(str_replace('/\n/', '', $str));
	$str = preg_replace( "/\r|\n/", "", $str );
	$arrOtziv[] =  $str;
}

// ====================================== добавление в БД ================================

for ($i=0; $i<count($arrOtziv); $i++) {
	$sql =  "INSERT INTO `otzivs` (`id`, `name`, `text`, `rating`, `date`) VALUES (NULL, '$arrNames[$i]','$arrOtziv[$i]','0','$arrDate[$i]')";
	if (mysqli_query($connect, $sql)) {
			echo "Успешно создана новая запись";
		} else {
			echo "Ошибка: " . $sql . "<br>"."\n" . mysqli_error($connect);
		}
}

mysqli_close($connect);

?>

