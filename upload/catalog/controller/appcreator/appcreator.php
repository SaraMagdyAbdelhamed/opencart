<?php
error_reporting(0);
ini_set("display_errors", 0);
//session_start();
if (!empty($_REQUEST['api_auth'])) {
header('Content-Type:application/json');
require(dirname(__FILE__).'/../../../config.php');

$request = (array)json_decode(file_get_contents('php://input'));
require_once 'trade.php';
  // echo($_REQUEST);
  // exit();
// var_dump((array)json_decode(file_get_contents("php://in‌​put"), true));
// $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";


 // var_dump($request['User']);
 // exit();
$action = (int)$request['Action_ID'];
//echo $action;
$list=array();

//DB Connection
$db_driver      = DB_DRIVER;   // Database driver name 
$db_host        = DB_HOSTNAME;     // Database host name 
$db_user        = DB_USERNAME;     // User for database authentication 
$db_pass        = DB_PASSWORD; // Password for database authentication 
$db_name        = DB_DATABASE;       // Database name 
$db_prefix      = DB_PREFIX; // Database prefix (may be empty) 
// Database prefix (if empty then remove prefixing double underscore)
$db_prefix      = (trim($db_prefix)=="") ? "":$db_prefix; 
if($db_driver == 'mysqli')
{
	$conn = new mysqli($db_host,$db_user,$db_pass,$db_name);
        $conn->query("SET NAMES 'utf8'");
}
else
{
	$conn = mysql_connect($db_host,$db_user,$db_pass,$db_name);
        mysql_query("SET NAMES 'utf8'");
}
switch($action) {
	case "2":
		//Products List (Search & Category)
        Product_List_View($conn,$db_prefix,$request);
	break;
	case "3":
        Store_Depts($conn,$db_prefix,$request);
    break;
	case "6":
        $product_id = $request['ID'];
        if(is_nan($product_id)) exit();
        Store_Display($conn,$db_prefix,$product_id,$request);
    break;
	case "7":
        Store_Product($conn,$db_prefix,$request);
    break;
	case "53":
        Store_Shops($conn,$db_prefix,$request);
    break;
    case "54":
        Store_View($conn,$db_prefix,$request);
    break;
	case "63":
        Store_Comment_List($conn,$db_prefix,$request);
    break;
        case "64":
            $result=signup($conn,$db_prefix,$request);
            echo $result;exit;
    break;
        case "65":
            // http_response_code(200);
            $result=signin($conn,$db_prefix,$request);
            echo $result;exit;
    break;
        case "22":
            $result=profile($conn,$db_prefix,$request);
            echo $result;exit;
    break;
        case "69":
            $result=Shopping_cart($conn,$db_prefix,$request);
            echo $result;exit;
    break;
        case "68":
            $result=Add_To_Shopping_Cart($conn,$db_prefix,$request);
            echo $result;exit;
    break;
        case "70":
           $result= Remove_From_Shopping_Cart($conn,$db_prefix,$request);
           echo $result;exit;
    break;
	}
}
?>