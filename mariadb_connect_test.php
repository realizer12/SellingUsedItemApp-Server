<?php

//PDO db 연결 파일
include('DbConnection/dbcon.php');

$name="shawn";
$select_data_stmt = $pdo->prepare('SELECT * FROM test_table');
$select_data_stmt->bindValue(':name',$name,PDO::PARAM_STR);
$select_data_stmt->execute();
$result = $select_data_stmt->fetch(PDO::FETCH_ASSOC);



echo "<pre>";
print_r($result);
echo "</pre>";

// while (($result = $select_data_stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
    
//     echo 'db에서 가지고 온 값 ->  '.$result['name'].'</br>';
// }


$conn


?>


