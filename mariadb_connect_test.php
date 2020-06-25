<?php

//db  연결
include('DbConnection/dbcon.php');

//test_table db가져옴
$query="select*from test_table";

//쿼리 날림
$send=mysqli_query($db,$query);

//쿼리 결과 array로
$row=mysqli_fetch_array($send);

//가장  첫번째 값
$result=$row[0];

echo '결과 가지고 옴->'.$result;


mysqli_close($db);

?>


