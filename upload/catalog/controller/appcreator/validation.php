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
function validate_profile($request)
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
if(!isset($request['first_name']))
{
	$error[$i]=language_error("first_name" ,$lang );
$validation_array[$i]['field']=language_field("first_name" , $lang);
$validation_array[$i]['message']=language_error("first_name" , $lang);
$i++;
}
if(!isset($request['family_name']))
{
	$error[$i]=language_error("family_name" ,$lang );
$validation_array[$i]['field']=language_field("family_name" , $lang);
$validation_array[$i]['message']=language_error("family_name" , $lang);
$i++;
}
if(!isset($request['phone']))
{
	$error[$i]=language_error("phone" ,$lang );
$validation_array[$i]['field']=language_field("phone" , $lang);
$validation_array[$i]['message']=language_error("phone" , $lang);
$i++;
}
if(!isset($request['address']))
{
	$error[$i]=language_error("address" ,$lang );
$validation_array[$i]['field']=language_field("address" , $lang);
$validation_array[$i]['message']=language_error("address" , $lang);
$i++;
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
	if($field == "first_name" && $lang == 1)
	{
		return "first_name is required";
	}
	if($field == "first_name" && $lang == 2)
	{
		return "حقل الاسم مطلوب ";
	}
	if($field == "family_name" && $lang == 1)
	{
		return "family_name is required";
	}
	if($field == "family_name" && $lang == 2)
	{
		return "حقل اسم العائله مطلوب ";
	}
	if($field == "phone" && $lang == 1)
	{
		return "phone is required";
	}
	if($field == "phone" && $lang == 2)
	{
		return "حقل رقم التليفون مطلوب  ";
	}
	if($field == "address" && $lang == 1)
	{
		return "address is required";
	}
	if($field == "address" && $lang == 2)
	{
		return "حقل العنوان مطلوب  ";
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
	if($field == "first_name" && $lang == 1)
	{
		return "first_name";
	}
	if($field == "first_name" && $lang == 2)
	{
		return "االاسم";
	}
	if($field == "family_name" && $lang == 1)
	{
		return "family_name";
	}
	if($field == "family_name" && $lang == 2)
	{
		return "اسم العائله";
	}
	if($field == "phone" && $lang == 1)
	{
		return "phone";
	}
	if($field == "phone" && $lang == 2)
	{
		return "رقم التليفون ";
	}
	if($field == "address" && $lang == 1)
	{
		return "address";
	}
	if($field == "address" && $lang == 2)
	{
		return "العنوان ";
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