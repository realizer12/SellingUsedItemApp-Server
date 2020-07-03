<?php

//회원가입시  멤버 본인 핸드폰 번호 인증요청시
//인증코드를  만들어 내어  청기와 랩으로  sms 요청을 하는 코드
//모든 처리에 대하여  성공 여부를  client에 콜백한다


//PDO db 연결 파일
include('DbConnection/dbcon.php');

//아래  sms 인증 시간이  다르게 나와서
//한국(서울) 시간을 default timezone으로 설정
date_default_timezone_set('Asia/Seoul');


// 사용자가 보낸 번호
$sended_phone_number=$_POST['phone_number'];

//휴대폰 인증 하는 이유  0=회원가입, 1=아이디 찾기, 2= 비밀 번호 찾기
$map_reason=$_POST['map_reason'];

//현재 시간  넣어줌 -위  default timezone-서울
$present_time = date('Y-m-d H:i:s');


$select_past_auth_info=$pdo->prepare('SELECT map_key_generate_date FROM member_auth_phone_num WHERE map_phone_number=:phone_num ORDER BY map_id DESC');
    
$select_past_auth_info->bindValue(':phone_num','01073807810');    


try{

    //쿼리  실행
    $select_past_auth_info->execute();

    if($select_past_auth_info){
         
       $result=$select_past_auth_info->fetch();
       
       $right_past_time= $result['map_key_generate_date'];


       $now=new DateTime($present_time);
       $before=new DateTime($right_past_time);

       $diff=$now->getTimestamp() - $before->getTimestamp();

    }

}catch(PDOException $e){

    echo "$e";
}


    

//prepare 문을 사용해서 member_auth_phone_num 테이블 insert 쿼리 준비
$insert_phone_auth_data_stmt=$pdo->prepare(
    'INSERT INTO member_auth_phone_num 
     (map_phone_number,map_key,map_key_generate_date,map_reason) 
     values(:phone_num,:map_key,:map_key_generate_date,:map_reason)');

//5자리 랜덤 key 생성
$map_key_number = sprintf('%05d',rand(00000,99999));

$insert_phone_auth_data_stmt->bindValue(':phone_num',$sended_phone_number);//폰번호 바인딩
$insert_phone_auth_data_stmt->bindValue(':map_key',$map_key_number);//인증 키 바인딩
$insert_phone_auth_data_stmt->bindValue(':map_key_generate_date',$present_time);//인증 신청 시간 바인딩
$insert_phone_auth_data_stmt->bindValue(':map_reason',$map_reason);//인증 출처 바인딩




try{

    //쿼리  실행
    $insert_phone_auth_data_stmt->execute();

    //insert 성공시 앱으로 성공메세지 1 보냄
    if($insert_phone_auth_data_stmt){
       
        include('sms_auth/sendsms.php');
        echo "1";

    }

}catch(PDOException $e){

    echo "$e";
}

    
 

?>