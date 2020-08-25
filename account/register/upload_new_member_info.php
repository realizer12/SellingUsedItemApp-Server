<?php

//client에서 보낸 멤버 가입 정보를 받아서  서버에 등록하는  코드


//PDO db 연결 파일
include('../../DbConnection/dbcon.php');

//새로운  멤버  정보  json -decode 시킴
$new_member_info=json_decode($_POST['new_ember_info']);

//로그인 이메일
$login_email=$new_member_info->login_email;

//멤버 nickname
$member_nickname=$new_member_info->nick_name;

//패스워드 sort값이랑 hash값 같이  있는 jSONobject
$password_json=$new_member_info->password;

//salt 값
$salt_value=$password_json->salt_value;

//비밀번호 256 hash 값
$sha_value=$password_json->sha_value;

//핸드폰 번호
$phone_num=$new_member_info->phone_num;

//sns 로그인 여부 0이면 일반 로그인  구글=1 네이버=2 페이스북=3 
$sns_status=$new_member_info->sns_status;

//sns 회원가입일 경우 가입후 바로 로그인하기 위해  필요한 uuid
$uuid=$new_member_info->uuid;


//가입하려는 멤버 정보를  서버에 업로드 하는 쿼리문
$query='INSERT INTO member_info (email,password,phone_num,nickname,salt_value,sns_check)
VALUES(:email,:password,:phone_num,:nickname,:salt_value,:sns_check)';




//새 멤버 등록  쿼리문 날림
$insert_new_member_stmt=$pdo->prepare($query);

//로그인 이메일 바인딩
$insert_new_member_stmt->bindValue('email',$login_email);

//sha256 암호화된  비밀번호 바인딩
$insert_new_member_stmt->bindValue('password',$sha_value);

//핸드폰 번호  데이터 바인딩
$insert_new_member_stmt->bindValue('phone_num',$phone_num);

//멤버 닉네임 데이터 바인딩 
$insert_new_member_stmt->bindValue('nickname',$member_nickname);

//salt값  데이터 바인딩
$insert_new_member_stmt->bindValue('salt_value',$salt_value);

//sns 체크값 데이터 바인딩
$insert_new_member_stmt->bindValue('sns_check',$sns_status);


//로그인시 회원 테이블에 자동로그인을 위한  auth_token  insert하는 쿼리문
$query_for_insert_auth_token="UPDATE member_info SET auth_token=:hashed_token WHERE uid=:uid";
$stmt_update_auth_token=$pdo->prepare($query_for_insert_auth_token);

//client로  callback 할 
$callback_data=array();

try{

  $insert_new_member_stmt->execute();

   if($insert_new_member_stmt){//서버에 회원가입 성공


         //만약에 sns 회원가입일때 uuid도 같이 받아서 진행한다
         if($sns_status>0){//sns회원 가입 일때

      
            //가장 최근  insert된 row 의 uid
            $last_uid=$pdo->lastInsertId();


            //받은  client의 uuid와 uid를  합친 문자열을 
            //hash 256 암호화를 적용하여  hash 값을 만들어낸다 
            $hash_256=hash("sha256",$uuid.$last_uid);

            //위에서 만든  해쉬값 대문자 형태로 바꿔줌
            $hashed_result=strtoupper($hash_256);
   
   
            //auth_token 이랑 uid 쿼리에 binding 
            $stmt_update_auth_token->bindValue('hashed_token',$hashed_result);
            $stmt_update_auth_token->bindValue('uid',$last_uid);

            $stmt_update_auth_token->execute();


            //auth_token 업데이트 성공
            if($stmt_update_auth_token){


               $callback_data['success']=true;
               $callback_data['status']=1;
               $callback_data['uid']=$last_uid;

            }else{//토큰 업데이트 실패

               $callback_data['success']=true;
               $callback_data['status']=2;
           
           
            }

         }else if($sns_status==0){//일반 회원 가입일때
            
            
            $callback_data['success']=true;
            $callback_data['status']=3;
            $callback_data['email']=$login_email;

         }

   }else{//회원가입 실패

      $callback_data['success']=false;
      $callback_data['status']=4;
     

   }

   //위  array를  json 형식으로 encode 해서 보냄
   echo  json_encode($callback_data);


}catch(PDOException $e){

    echo "$e";

}


?>