<?php

//client에서 보낸 멤버 가입 정보를 받아서  서버에 등록하는  코드


//PDO db 연결 파일
include('DbConnection/dbcon.php');

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

//sns 로그인  


//가입하려는 멤버 정보를  서버에 업로드 하는 쿼리문
$query='INSERT INTO member_info (email,password,phone_num,nickname,salt_value)
VALUES(:email,:password,:phone_num,:nickname,:salt_value)';


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


try{

  $insert_new_member_stmt->execute();

   if($insert_new_member_stmt){//서버에 회원가입 성공

      echo "1";

   }else{//회원가입 실패

      echo "2";

   }


}catch(PDOException $e){

    echo "$e";

}


?>