<?php 

function validate($request)
{
	if(isset($request['lang_id']))
	{
		$lang=$request['lang_id'];
	}
	else
	{
		$lang=1;
	}
	$validation_array=[];
	$error=[];
	$i=0;
if(!isset($request['email']))
{
	$error[$i]=language_error("email" ,$lang );
$validation_array[$i]['field']=language_field("email" , $lang);
$validation_array[$i]['message']=language_error("email" , $lang);
$i++;
}
else
{
	$email = test_input($request["email"]);
    // check if e-mail address is well-formed
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error[$i]=language_error("email_err" ,$lang );
$validation_array[$i]['field']=language_field("email" , $lang);
$validation_array[$i]['message']=language_error("email_err" , $lang); 
    }
}
if(!isset($request['password']))
{
	$error[$i]=language_error("password" ,$lang );
$validation_array[$i]['field']=language_field("password" , $lang);
$validation_array[$i]['message']=language_error("password" , $lang);
$i++;
}
else
{
	$password = $request["password"];
    // check if e-mail address is well-formed
    if (strlen($request["password"]) < '3' && strlen($request["password"]) > '8') {
      $error[$i]=language_error("password_err" ,$lang );
$validation_array[$i]['field']=language_field("email" , $lang);
$validation_array[$i]['message']=language_error("password_err" , $lang); 
    }
}
if(count($validation_array) > 0 )
{
	$userdata =  array("status" => array("code"=>2,"message"=>"error!","error_details"=>$error,"validation_errors"=>$validation_array), "content" => array());
 return json_encode($userdata);
}
else
{
	return false;
}
 
}

function language_error($field , $lang)
{
	if($field == "email" && $lang == 1)
	{
		return "Email is required";
	}
	if($field == "email" && $lang == 2)
	{
		return "حقل البريد الالكترونى مطلوب";
	}
	if($field == "email_err" && $lang == 1)
	{
		return "Invalid email format";
	}
	if($field == "email_err" && $lang == 2)
	{
		return "برجاء كتابه الايميل بطريقه صحيحه";
	}
	if($field == "password" && $lang == 1)
	{
		return "Password is required";
	}
	if($field == "password" && $lang == 2)
	{
		return "حقل كلمه المرور مطلوب";
	}
	if($field == "password_err" && $lang == 1)
	{
		return "Invalid password format";
	}
	if($field == "password_err" && $lang == 2)
	{
		return "فورمات حقل كلمه المرور خطأ";
	}

}
function language_field($field , $lang)
{
if($field == "email" && $lang == 1)
	{
		return "email";
	}
	if($field == "email" && $lang == 2)
	{
		return "البريد الالكترونى";
	}
	if($field == "password" && $lang == 1)
	{
		return "password";
	}
	if($field == "password" && $lang == 2)
	{
		return "اكلمه المرور";
	}
}
// function language_message($field , $lang)
// {
// if($field == "email" && $lang == 1)
// 	{
// 		return "Email is required";
// 	}
// 	if($field == "email" && $lang == 2)
// 	{
// 		return "حقل البريد الالكترونى مطلوب";
// 	}
// 	if($field == "email_err" && $lang == 1)
// 	{
// 		return "Invalid email format";
// 	}
// 	if($field == "email_err" && $lang == 2)
// 	{
// 		return "برجاء كتابه الايميل بطريقه صحيحه";
// 	}
// }
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}













?>