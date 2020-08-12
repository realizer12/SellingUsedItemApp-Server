<?php
//회원가입 할때  닉네임 중복 체크를 위한 서버 코드이다


//PDO db 연결 파일
include('DbConnection/dbcon.php');

//요청된  닉네임 
$request_nickname=$_GET['nickname'];


//mmber_info에서 client에서 받아온 nick_name을  조회 한다
$query="SELECT COUNT(*) FROM member_info WHERE nickname=:nick_name";

//중복 체크 하기 위한 select 쿼리문 prepare
$select_nickname_check_stmt=$pdo->prepare($query);

//중복 체크할  닉네임  데이터 바인딩 해줌
$select_nickname_check_stmt->bindValue('nick_name',$request_nickname);


try{
  
  $select_nickname_check_stmt->execute();
  
  if($select_nickname_check_stmt){//쿼리 성공시

    //카운트 조회 니까 하나의 컬럼값 가져오는 fetchColumn 사용함
    $result=$select_nickname_check_stmt->fetchColumn();

    if($result==0){

       echo "1";//중복이 없음

    }else if($result>0){

        echo "-2";//중복값이 있음

    }


  }else{

    echo "-1";//쿼리 실패시
  }


}catch(PDOException $e){

    echo "$e";
}



?>