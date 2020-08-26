<?php

//일반 로그인을 담당하는 서버  파일
//비밀번호랑 이메일  받아서  조회 한다


//PDO db 연결 파일
include('../../DbConnection/dbcon.php');


//유저가 보낸 이메일
$user_email=$_POST['user_email'];

//유저의 패스워드 
$user_password=$_POST['user_password'];

//유저 식별을 위한 파이어베이스 uuid
$uuid=$_POST['uuid'];



//client에서 보낸 이메일로 회원인지 조회 
$query_for_select_email="SELECT*FROM member_info WHERE email=:user_email LIMIT 1";


//로그인시 회원 테이블에 자동로그인을 위한  auth_token  update하는 쿼리문
$query_for_insert_auth_token="UPDATE member_info SET auth_token=:hashed_token WHERE uid=:uid";



//위 쿼리문 prepare 
$email_select_stmt=$pdo->prepare($query_for_select_email);
$auth_token_update_stmt=$pdo->prepare($query_for_insert_auth_token);


//받아온 이메일 binding
$email_select_stmt->bindValue('user_email',$user_email);



try{
    
    //이메일 조회 실행 
    $email_select_stmt->execute();

    //FETCH_ASSOC-> 각 column name 형식으로  조회 가능 
    $email_select_stmt->setFetchMode(PDO::FETCH_ASSOC);

    //조회한 row
    $selected_row=$email_select_stmt->fetch();


    //해당 조회한  이메일로 값이 있을 경우
    if($selected_row != null){


        //일반 로그인 이메일 경우이다
       if($selected_row['sns_check']==0){

          
         //받아온 패스워드 saltvalue 넣어서 해쉬 진행
         $hased_password=make_256hash($selected_row['salt_value'].$user_password);

        
        if($selected_row['password']==$hased_password){//패스워드가 맞는 경우

            //위에서 만든  해쉬값 대문자 형태로 바꿔줌
            $hashed_token=make_256hash($uuid.$selected_row['uid']);

            //auth_token 이랑 uid 쿼리에 binding 
            $auth_token_update_stmt->bindValue('hashed_token',$hashed_token);
            $auth_token_update_stmt->bindValue('uid',$selected_row['uid']);

            $auth_token_update_stmt->execute();


            //auth_token 업데이트 성공
            if($auth_token_update_stmt){

                $callback_data['response']=3;
                $callback_data['uid']=$selected_row['uid'];

            }else{//auth_token 업데이트 실패

                $callback_data['response']=4;
                $callback_data['uid']=null;

            }


        }else{//password 가틀림

            $callback_data['response']=5;
            $callback_data['uid']=null;

        }
        

       }else{//sns 로그인용 이메일일 경우

            $callback_data['response']=2;
            $callback_data['uid']=null;

       }

    }else{//조회한 이메일로 값이 없을 경우
         
        $callback_data['response']=1;
        $callback_data['uid']=null;
    }

     
    //위  array를  json 형식으로 encode 해서 보냄
    echo  json_encode($callback_data);
  

}catch(PDOException $e){

    echo $e;
   

}

//값을 256해쉬값으로 변환 시켜주는 함수
function make_256hash($before_hased_value){

    $hash_256=hash("sha256",$before_hased_value);

    //위에서 만든  해쉬값 대문자 형태로 바꿔줌
    $result=strtoupper($hash_256);


    return $result;
}

?>