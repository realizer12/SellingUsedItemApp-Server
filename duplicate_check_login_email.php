<?php
//로그인 이메일  중복 체크 해주는 서버 코드


//PDO db 연결 파일
include('DbConnection/dbcon.php');

//요청된 로그인 이메일 
$request_login_email=$_POST['login_email'];

//member_info 테이블에서 client에서 받아온  이메일  COUNT 조회
$query="SELECT COUNT(*) FROM member_info WHERE email=:login_email";

//중복체크하기 위한 select 쿼리문
$select_login_email_check_stmt=$pdo->prepare($query);

//해당 중복 체크 할  이메일 데이터 바인딩
$select_login_email_check_stmt->bindValue('login_email',$request_login_email);


try{

    $select_login_email_check_stmt->execute();

    //중복 이메일 조회 쿼리 성공시
    if($select_login_email_check_stmt){

      //카운트 조회니까 이렇게  하나의 컬럼값을 가져오는 fetchColumn을 사용함
      $result=$select_login_email_check_stmt->fetchColumn(); 

      if($result==0){

          echo "1";//중복이 없음
      
      }else if($result>0){

         echo "-2";//중복값이 있음

      }


    }else{//쿼리 실패시

        echo "-1";
    }


}catch(PDOException $e){

    echo "$e";
}



?>
