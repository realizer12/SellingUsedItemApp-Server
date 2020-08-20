<?php

//client에서 uid와 uuid를 보내서 
//여기서 해쉬를 만들어서 db에 있는 auth_token과 비교후
//자동 로그인이 가능한지 여부를 판단해서 돌려준다


//PDO db 연결 파일
include('../../DbConnection/dbcon.php');

//client에서 보내온  uid
$user_uid=$_POST['user_uid'];

//client에서 보낸 uuid
$uuid=$_POST['uuid'];

//위 uid에 해당하는  유저의 해당 auth_token이 존재하는지  여부 조회 
$query_for_check_auth_token="SELECT COUNT(*) FROM member_info WHERE uid=:uid AND auth_token=:auth_token";

//쿼리문 prepare
$stmt=$pdo->prepare($query_for_check_auth_token);

//받은  client의 uuid와 uid를  합친 문자열을 
//hash 256 암호화를 적용하여  hash 값을 만들어낸다 
$hash_256=hash("sha256",$uuid.$user_uid);

//저장된 문자열 이 대문자 형태이므로 대문자로 변환
$hashed_result=strtoupper($hash_256);

//위 쿼리문 uid  및  auth_token 넣어줌
$stmt->bindValue('uid',$user_uid);
$stmt->bindValue('auth_token',$hashed_result);



try{

   //위 prepare한 statment 실행 
   $stmt->execute();
   
   if($stmt){//쿼리 성공시
         
        //카운트 조회니까 이렇게  하나의 컬럼값을 
        //가져오는 fetchColumn을 사용함  
        $result=$stmt->fetchColumn();
  
        //개수가 1개 일때 -> 성공 
        if($result==1){
  
            
            echo json_encode(array('response'=>true,'status'=>null));
  
        }else{
  
            echo json_encode(array('response'=>false,'status'=>2));
        }
  
    }else{//쿼리 실패시
  
        echo json_encode(array('response'=>false,'status'=>3));
  
    }

}catch(PDOException $e){

    echo "$e";

}



?>