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
        $result=Product_List_View($conn,$db_prefix,$request);
        echo $result;exit;
	break;
	case "3":
        $result=Store_Depts($conn,$db_prefix,$request);
        echo $result;exit;
    break;
    case "4":
        $result=Product_Action($conn,$db_prefix,$request);
        echo $result;exit;
    break;
    case "5":
        $result=get_stores($conn,$db_prefix,$request);
        echo $result;exit;
    break;
	case "6":
        $product_id = $request['ID'];
        if(is_nan($product_id)) exit();
        $result=Store_Display($conn,$db_prefix,$product_id,$request);
        echo $result;exit;
    break;
	case "7":
        $result=Store_Product($conn,$db_prefix,$request);
        echo $result;exit;
    break;
    case "8":
        $result=Category_Product($conn,$db_prefix,$request);
        echo $result;exit;
    break;
	case "53":
        $result=Store_Shops($conn,$db_prefix,$request);
        echo $result;exit;
    break;
    case "54":
        $result=Store_View($conn,$db_prefix,$request);
        echo $result;exit;
    break;
	case "63":
        $result=Store_Comment_List($conn,$db_prefix,$request);
         echo $result;exit;
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
    case "60":
            // http_response_code(200);
            $result=change_password($conn,$db_prefix,$request);
            echo $result;exit;
    break;
    case "61":
            // http_response_code(200);
            $result=logout($conn,$db_prefix,$request);
            echo $result;exit;
    break;
     case "62":
            // http_response_code(200);
            $result=forget_password($conn,$db_prefix,$request);
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
    case "71":
           $result= checkout($conn,$db_prefix,$request);
           echo $result;exit;
    break;
    case "72":
           $result= information($conn,$db_prefix,$request);
           echo $result;exit;
    break;
    case "73":
           $result= submit_review($conn,$db_prefix,$request);
           echo $result;exit;
    break;
    case "74":
           $result= contactus($conn,$db_prefix,$request);
           echo $result;exit;
    break;
    case "75":
           $result= add_to_wishlist($conn,$db_prefix,$request);
           echo $result;exit;
    break;
    case "76":
           $result= wishlist($conn,$db_prefix,$request);
           echo $result;exit;
    break;
    case "77":
           $result= remove_from_wishlist($conn,$db_prefix,$request);
           echo $result;exit;
    break;
    case "78":
           $result= product_offers($conn,$db_prefix,$request);
           echo $result;exit;
    break;
    case "79":
           $result= product_filters($conn,$db_prefix,$request);
           echo $result;exit;
    break;
    case "80":
           $result= product_search($conn,$db_prefix,$request);
           echo $result;exit;
    break;
	}
}
?>