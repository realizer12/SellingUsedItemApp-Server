<?php
//유저가 비밀번호 찾기를 진행할때 
//새로운  비밀번호를  입력해야 한다
//해당 처리를 위한 파일이다



//PDO db 연결 파일
include('../../DbConnection/dbcon.php');


$login_email=$_POST['login_email'];

$new_password_json=json_decode($_POST['new_password']);

//256 hashing 된 비밀번호
$password=$new_password_json->sha_value;

//비밀번호 해슁할때 첨가되는 salt 값
$salt_value=$new_password_json->salt_value;


//패스워드 와 salt value 업데이트 쿼리문
$update_passowrd_query='UPDATE member_info SET 
password=:new_password,salt_value=:salt_value WHERE email=:email';


$update_new_password_stmt=$pdo->prepare($update_passowrd_query);

//패스워드 binding
$update_new_password_stmt->bindValue('new_password',$password);

//salt 값 binding
$update_new_password_stmt->bindValue('salt_value',$salt_value);

//이메일
$update_new_password_stmt->bindValue('email',$login_email);

try{

  $update_new_password_stmt->execute();
    
    //업데이트 성공시
    if($update_new_password_stmt){

        echo "1";
         
        
    }else{//업데이트 실패

        echo "2";

    }

}catch(PDOException $e){

    echo "$e";

}



?>