<?php

//sns로그인을 담당하는  파일이다
//sns 로그인시  해당 

//PDO db 연결 파일
include('../../DbConnection/dbcon.php');


//어떤 sns 플랫폼인지 구글 =1,  네이버 =2, 페이스북=3
$sns_status=$_POST['sns_login_status'];

//sns 로그인 이메일
$sns_email=$_POST['sns_email'];


//유저 식별을 위한  파이어베이스 uuid
$uuid=$_POST['uuid'];


//client에서 보낸 sns 이메일 중복 여부를 판단한다 -> 1개만 있을거니까  LIMIT 1로 해줌
$query="SELECT*FROM member_info WHERE email=:sns_email LIMIT 1";

//로그인시 회원 테이블에 자동로그인을 위한  auth_token  insert하는 쿼리문
$query_for_insert_auth_token="UPDATE member_info SET auth_token=:hashed_token WHERE uid=:uid";



//위 쿼리 문 prepare
$stmt=$pdo->prepare($query);
$stmt_update_auth_token=$pdo->prepare($query_for_insert_auth_token);


//sns 이메일은  client 에서 받아온  sns 이메일  binding
$stmt->bindValue('sns_email',$sns_email);


try{

   //위  문장들 실행
   $stmt->execute();

   //FETCH_ASSOC-> 각 column name 형식으로  조회 가능 
   $stmt->setFetchMode(PDO::FETCH_ASSOC);
   
   //조회한  row 가지고옴
   $row=$stmt->fetch();       

   //client로  callback 할 
   $callback_data=array();
   
     //조회한 값이  있을때
     if($row!=null){
        

        //가입한 유저가 맞을때
        if($sns_status==$row['sns_check']){


          


           //받은  client의 uuid와 uid를  합친 문자열을 
           //hash 256 암호화를 적용하여  hash 값을 만들어낸다 
           $hash_256=hash("sha256",$uuid.$row['uid']);

           //위에서 만든  해쉬값 대문자 형태로 바꿔줌
           $hashed_result=strtoupper($hash_256);


           //auth_token 이랑 uid 쿼리에 binding 
           $stmt_update_auth_token->bindValue('hashed_token',$hashed_result);
           $stmt_update_auth_token->bindValue('uid',$row['uid']);

           $stmt_update_auth_token->execute();


           //update 성공
           if($stmt_update_auth_token){

            //여기서  uid랑  client에서 받은  uuid를  가지고 auth_token만들고 
            //db저장 하고  uid만  넘겨주기 ㄱㄱ
            $callback_data['response']=1;
            $callback_data['uid']=$row['uid'];
 

           }else{//업데이트 실패


            $callback_data['response']=-1;
            $callback_data['uid']=null;

           }


                     
        }else{//가입한 유저는 맞지만, 가입 경로가 달라서  로그인 또는 회원가입 불가 
           
            $callback_data['response']=2;
            $callback_data['uid']=null;

        }

       

    }else{// 회원가입 진행이 가능함
  
        $callback_data['response']=3;
        $callback_data['uid']=null;
  
    }
  

    //위  array를  json 형식으로 encode 해서 보냄
    echo  json_encode($callback_data);
  
  }catch(PDOException $e){
  
      echo $e;
  
  }


?>