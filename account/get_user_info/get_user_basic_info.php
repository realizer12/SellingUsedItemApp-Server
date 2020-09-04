<?php

//기본적인 회원의  정보를 클라로 보내준다
//기본 정보 구성 - 프로필 이미지 서버 경로 및  코인 량  닉네임 가져온다
// error_reporting(E_ALL);

// ini_set("display_errors", 1);

//PDO db 연결 파일
include('../../DbConnection/dbcon.php');


//유저의 uid
$user_uid = $_POST['uid'];

$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

//유저의 정보를 가지고 오기위한 쿼리문 여러개 날림
$query = 'SELECT nickname FROM member_info WHERE uid= :user_uid; 
SELECT img_url FROM profile_img WHERE uid = :user_uid;
SELECT coin FROM user_coin WHERE uid = :user_uid;';



$select_user_info_stmt = $pdo->prepare($query);

$select_user_info_stmt->bindValue('user_uid',$user_uid);

//클라로 보낼  json encode 할 array 값
$callback_array=array();


try{

  //위 지정한 stmt 실행
  $select_user_info_stmt->execute();  

  

  //쿼리 성공시 
  if($select_user_info_stmt){

    $result=$select_user_info_stmt->fetch();

   // echo $result['nickname'];
    $callback_array['nickname']=$result['nickname'];
    $callback_array['img_url']=$result['img_url'];
    $callback_array['coin']=$result['coin'];


  }else{

    $callback_array['nickname']=null;
    $callback_array['img_url']=null;
    $callback_array['coin']=null;
  }

   
  echo json_encode($callback_array,JSON_UNESCAPED_UNICODE);

}catch(PDOException $e){

   echo "$e";
}



?>