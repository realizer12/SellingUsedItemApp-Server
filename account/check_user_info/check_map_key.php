<?php

//client 가  받은  sms 인증 키를  다시 서버에 보내어, 
//본인 확인을 하는 코드다
//인증키가 맞으면 1을  틀리면 0 의 콜백 값을 보내준다

//PDO db 연결 파일
include('../../DbConnection/dbcon.php');

//아래  sms 인증 시간이  다르게 나와서
//한국(서울) 시간을 default timezone으로 설정
date_default_timezone_set('Asia/Seoul');

//현재 시간  넣어줌 -위  default timezone-서울
$present_time = date('Y-m-d H:i:s');


// 사용자가 보낸 인증키
$sended_map_key=$_POST['map_auth_key'];

//사용자가 보낸 번호
$sended_phone_number=$_POST['phone_number'];


//휴대폰 인증 하는 이유  0=회원가입, 1=아이디 찾기, 2= 비밀 번호 찾기
$map_reason=$_POST['map_reason'];

//map 테이블에서  해당 번호와  인증 reason이  일치하는  가장  최근의 데이터를 조회한다
$select_map_table=$pdo->prepare('SELECT * FROM member_auth_phone_num WHERE map_phone_number=:phone_number AND map_reason=:reason AND map_status=0 ORDER BY map_id DESC');


$select_map_table->bindValue(':phone_number',$sended_phone_number); //핸드폰 번호 바인딩
$select_map_table->bindValue(':reason',$map_reason); //인증 이유 바인딩



$update_auth_check_status=$pdo->prepare('UPDATE member_auth_phone_num SET map_status=1 WHERE map_id=:map_id');


//해당 번호로 가입된  이메일 가져오기 위한 쿼리
$select_email_with_phonnumber=$pdo->prepare('SELECT email FROM member_info WHERE phone_num=:phone_number LIMIT 1');
$select_email_with_phonnumber->bindValue(':phone_number',$sended_phone_number);

try{

    //위  조회문 쿼리  실행
    $select_map_table->execute();

    if($select_map_table){//조회 쿼리문  성공시
       


        //조회문 내역 가지고옴
         $result=$select_map_table->fetch();
         
         $result_auth_key=$result['map_key'];
        
          //차이가 3분 이내라면 - 성공
         if($result_auth_key!=null){//해당 값 존재할때

      
           if($result_auth_key==$sended_map_key){//인증키와  해당 키가 맞는 경우         

              $update_auth_check_status->bindValue(":map_id",$result['map_id']);
              $update_auth_check_status->execute();

              if($update_auth_check_status){


               if($map_reason==1){//회원가입의 경우는 이메일을 같인 retun 시켜줘야되서 빼줌

                  //이메일 조회 쿼리 실행
                  $select_email_with_phonnumber->execute();
                 
                  if($select_email_with_phonnumber){//이메일 조회 성공

                     $row_for_email=$select_email_with_phonnumber->fetch();
                     echo json_encode(array('return_email'=>$row_for_email['email'],'return_status'=> 1)); 

                  }else{//이메일 조회 실패

                     echo "7";
                  }
                  
                
               
               }else{

                  echo "1";//회원가입이랑 비밀번호 찾기 때  인증코드 확인 성공
               }

            

              }else{
                 
               echo "5";//map 상태 업데이트 실패

              }
                  

           }else{//인증키가 틀린경우

              echo "2";
           }


         }else{//해당 값이 존재 하지 않을때

            echo "3";//
         } 
   
   }else{//조회 커리문 실패시
     
      echo "4";
  
   }

}catch(PDOException $e){

    echo "$e";
}






?>