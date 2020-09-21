<?php

//클라로부터 받아온  기본정보  수정 사항을  
//가지고 update를 진행한다


//PDO db 연결 파일
include('../../DbConnection/dbcon.php');

//유저 uid
$user_uid= $_POST['uid'];


//유저 닉네임
$user_nickname=$_POST['nickname'];


//새 패스워드 -json
$new_password_json=$_POST['new_password'];

$target_dir = "profileimage1/chatting_video/";//비디오 -> 들어감

$tartget_file=$target_dir.basename($_FILES["uploaded_img"]["name"]);//비디오 경로

$a=$_FILES["uploaded_img"]["name"];

echo "$a";






?>