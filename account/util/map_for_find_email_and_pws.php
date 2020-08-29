<?php

//이메일 찾기랑  비밀번호 찾기 할때 
//진행하는  sms 코드 보내기 


//PDO db 연결 파일
include('../../DbConnection/dbcon.php');

//아래  sms 인증 시간이  다르게 나와서
//한국(서울) 시간을 default timezone으로 설정
date_default_timezone_set('Asia/Seoul');


// 사용자가 보낸 번호
$sended_phone_number=$_POST['phone_number'];


//비밀 번호 찾기때 필요한  로그인 이메일
$login_email=$_POST['login_email'];


//휴대폰 인증 하는 이유  0=회원가입, 1=아이디 찾기, 2= 비밀 번호 찾기
$map_reason=$_POST['map_reason'];

//핸드폰 번호 중복 되는지 확인하기
$check_phone_number_used=$pdo->prepare('SELECT phone_num FROM member_info WHERE phone_num=:phone_number');

//핸드폰 번호  데이터 바인딩
$check_phone_number_used->bindValue('phone_number',$sended_phone_number);

try{

//핸드폰 번호 중복 여부 조회
$check_phone_number_used->execute();


if($check_phone_number_used){

    $result=$check_phone_number_used->fetchColumn();

   if($result!=null){//중복값 있음
        

        //prepare 문을 사용해서 member_auth_phone_num 테이블 insert 쿼리 준비
        $insert_phone_auth_data_stmt=$pdo->prepare(
            'INSERT INTO member_auth_phone_num 
            (map_phone_number,map_key,map_reason) 
            values(:phone_num,:map_key,:map_reason)');

        //5자리 랜덤 key 생성
        $map_key_number = sprintf('%05d',rand(00000,99999));

        $insert_phone_auth_data_stmt->bindValue('phone_num',$sended_phone_number);//폰번호 바인딩
        $insert_phone_auth_data_stmt->bindValue('map_key',$map_key_number);//인증 키 바인딩
        $insert_phone_auth_data_stmt->bindValue('map_reason',$map_reason);//인증 출처 바인딩

        try{

            //쿼리  실행
            $insert_phone_auth_data_stmt->execute();

            //insert 성공시 앱으로 성공메세지 1 보냄
            if($insert_phone_auth_data_stmt){
        
                include('../../sms_auth/sendsms.php');//청기와랩 api sms  보내는 파일
                echo "1";//중복값 없어서  sms 인증코드 보냄

            }

        }catch(PDOException $e){

            echo "$e";
        }


    

   }else{//중복값없음

    echo "5";//중복값 없음
     
   }// db    중복 번호가 없을 경우 끝

}else{//핸드폰 중복 여부 조회  실패 

   echo "-4";//중복조회 실패

}

}catch(PDOException $e){

    echo "$e";
}












?>