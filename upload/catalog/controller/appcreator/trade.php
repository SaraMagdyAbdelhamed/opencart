<?php
// require_once '../upload/model/account/cutomer.php';
function json_encode_convert($outputs, $Key = "") {
    if (strstr($_SERVER['HTTP_USER_AGENT'], 'iPod') || strstr($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
        return json_encode($outputs);
    } else {
        $arr_Key["$Key"] = $outputs;
        return json_encode($arr_Key);
    }
}

function Product_List_View($conn, $db_prefix,$request) {
    $Product_List = array();
        
    
    $sql = "SELECT " . $db_prefix . "product.product_id as 'ID', date_added, viewed, image as 'Img'," . $db_prefix . "product.quantity as 'Quantity',name as 'Title',price as 'Price',description as 'Description' FROM " . $db_prefix . "product INNER JOIN " . $db_prefix . "product_description ON " . $db_prefix . "product_description.product_id = " . $db_prefix . "product.product_id ";
    if (isset($request['cat_id'])) {
        $sql .= " AND " . $db_prefix . "product.product_id IN (SELECT product_id FROM " . $db_prefix . "product_to_category WHERE category_id = $request[cat_id]) ";
    }
    if (!empty($request['product_name'])) {
        $sql .= " AND name LIKE '$request[product_name]%' ";
    }
    if (!empty($request['product_id'])) {
        $sql .= " AND " . $db_prefix . "product.product_id =$request[product_id] ";
    }
    if(isset($request['lang']))
    {
        $sql .= " AND " . $db_prefix . "product_description.language_id='{$request['lang']}'";
    }
    $sql .= " ORDER BY " . $db_prefix . "product.product_id DESC";
    if (DB_DRIVER == 'mysqli') {
        $Products = $conn->query($sql);
        while ($Product = $Products->fetch_assoc())
            $Product_List[] = $Product;
    } else {
        $Products = mysql_query($sql);
        while ($Product = mysql_fetch_assoc($Products))
            $Product_List[] = $Product;
    }
    $out = array();
    $temp = array();
    $Final_Json = array();
    foreach ($Product_List as $product) 
    {
        $temp['id'] = $product['ID'];
        $temp['name'] = $product['Title'];
        $image = HTTP_SERVER . "image/$product[Img]";
        $temp['image'] = $image;
        $temp['description'] = limit_string(strip_tags($product['Description']), 200);
        $temp['price'] = $product['Price'];
        $temp['Expire_In'] = ($product['Expire_In'] ? $product['Expire_In'] : "");
        $temp['visit_num'] = $product['viewed'];
        $temp['link_share'] = HTTP_SERVER . "index.php?route=product/product&product_id=" . $product['ID'];
        $q = "SELECT count(review_id) as 'c' FROM " . $db_prefix . "review WHERE product_id = '$product[ID]'";
        if (DB_DRIVER == 'mysqli') {
            $comments_count = $conn->query($q);
            $c = $comments_count->fetch_assoc();
        } else {
            $comments_count = mysql_query($q);
            $c = mysql_fetch_assoc($comments_count);
        }
        $temp['Comment_Num'] = $c['c']; 

        $sql_rate = "SELECT avg(rating) as 'rate' FROM " . $db_prefix . "review WHERE product_id = '$product[ID]'";
        if (DB_DRIVER == 'mysqli') {
            $rate_avg = $conn->query($sql_rate);
            $rate = $rate_avg->fetch_assoc();
        } else {
            $rate_avg = mysql_query($sql_rate);
            $rate = mysql_fetch_assoc($rate_avg);
        }
        $temp['rate']=$rate['rate'];
        
        $temp['Product_Published'] = (string) strtotime($product['date_added']);
        $q = "SELECT " . $db_prefix . "product_attribute.attribute_id as 'ID', name as 'Key', text as 'Value' FROM " . $db_prefix . "product_attribute INNER JOIN " . $db_prefix . "attribute_description ON " . $db_prefix . "attribute_description.attribute_id = " . $db_prefix . "product_attribute.attribute_id WHERE product_id = '$product[ID]'";
        if(isset($request['lang']))
    {
        $q .= " AND " . $db_prefix . "attribute_description.language_id='{$request['lang']}'";
    }
        $t = array();
        if (DB_DRIVER == 'mysqli') {
            $data = $conn->query($q);
            while ($c = $data->fetch_assoc())
                $t[] = $c;
        } else {
            $data = $conn->query($q);
            while ($c = mysql_fetch_assoc($data))
                $t[] = $c;
        }
        $temp['Data'] = $t;
        $q_images = "SELECT image as Image FROM " . $db_prefix . "product_image WHERE product_id='{$temp['ID']}'";
        $im_arr = array();
        if (DB_DRIVER == 'mysqli') {
            $data = $conn->query($q_images);
            while ($c = $data->fetch_assoc()) {
                $images['Image'] = HTTP_SERVER . "image/" . $c['Image'];
                $im_arr[] = $images;
            }
        } else {
            $data = $conn->query($q);
            while ($c = mysql_fetch_assoc($data)) {
                $images['Image'] = HTTP_SERVER . "image/" . $c['Image'];
                $im_arr[] = $images;
            }
        }
        $temp['Images'] = $im_arr;
        $q_shop = "SELECT store_id FROM " . $db_prefix . "product_to_store WHERE product_id='{$product['ID']}'";
        if (DB_DRIVER == 'mysqli') {
            $shop_res = $conn->query($q_shop);
            $shop = $shop_res->fetch_assoc();
        } else {
            $shop_res = mysql_query($q_shop);
            $shop = mysql_fetch_assoc($shop_res);
        }
        $temp['Key'] = "ID";
        $temp['Api'] = "&ID=" . $product['ID'];
        $q_color = "SELECT name, text FROM " . $db_prefix . "attribute_description inner join " . $db_prefix . "product_attribute on " . $db_prefix . "attribute_description.attribute_id=" . $db_prefix . "attribute_description.attribute_id WHERE product_id='{$product['ID']}' and name='color'";
        if(isset($request['lang']))
    {
        $q_color .= " AND " . $db_prefix . "attribute_description.language_id='{$request['lang']}'";
    }
        if (DB_DRIVER == 'mysqli') {
            $color_res = $conn->query($q_color);
            $color = $color_res->fetch_assoc();
        } else {
            $color_res = mysql_query($q_color);
            $color = mysql_fetch_assoc($color_res);
        }
        if (isset($color["text"])) {
            $temp["Color"] = $color["text"];
        } else {
            $temp["Color"] = "";
        }

        // $adv_data = disp_advanced_data();
        // foreach ($adv_data as $k => $v) {
        //     $temp[$k] = $v;
        // }
        // $footerData_arr = array();
        // $footer_data = disp_footer_data($footerData_arr);
        // $setting_data = disp_setting_data("0", "0", null, null, $temp['Key'], $temp['Api'], null, $temp['Color'], $footer_data);
        // foreach ($setting_data as $k => $v) {
        //     $temp[$k] = $v;
        // }

        $Arr_Json = Json_CData($temp,$temp['Data']);
        $Final_Json[] = $Arr_Json;
    }
    output($Final_Json);
}

function Store_Depts($conn, $db_prefix,$request) {
    $Product_List = array();
    
    $wh= "";
    if(isset($request['lang']))
    {
        $wh .= " AND " . $db_prefix . "category_description.language_id='{$request['lang']}'";
    }
    if (isset($request['ID']))
        $sql = "SELECT DISTINCT " . $db_prefix . "category.category_id as 'ID',name as 'Title',image as 'Pic',description as 'Des' FROM " . $db_prefix . "category INNER JOIN " . $db_prefix . "category_description ON " . $db_prefix . "category_description.category_id = " . $db_prefix . "category.category_id WHERE status=1 $wh AND " . $db_prefix . "category.category_id IN (SELECT category_id FROM " . $db_prefix . "category WHERE parent_id = '$request[ID]')";
    else
        $sql = "SELECT DISTINCT " . $db_prefix . "category.category_id as 'ID',name as 'Title',image as 'Pic',description as 'Des' FROM " . $db_prefix . "category INNER JOIN " . $db_prefix . "category_description ON " . $db_prefix . "category_description.category_id = " . $db_prefix . "category.category_id WHERE status=1 $wh";
    if (DB_DRIVER == 'mysqli') {
        $Products = $conn->query($sql);
        while ($Product = $Products->fetch_assoc())
            $Product_List[] = $Product;
    } else {
        $Products = mysql_query($sql);
        while ($Product = mysql_fetch_assoc($Products))
            $Product_List[] = $Product;
    }
    $out = array();
    $Final_Json = array();
    $temp = array();
    foreach ($Product_List as $product) {
        $temp['id'] = $product['ID'];
        $temp['name'] = $product['Title'];
        $temp['image'] = ($product['Pic'] ? HTTP_SERVER . "/image/" . $product['Pic'] : "");
        $temp['visit_num'] = 0;
        $temp['description'] = limit_string(strip_tags($product['Des']), 100);
        $temp['Key'] = "ID";
        $temp['Api'] = "&ID=" . $temp['ID'];
        $q = "SELECT category_id FROM " . $db_prefix . "category WHERE parent_id = " . $product['ID'] . " LIMIT 0,1";
        if (DB_DRIVER == 'mysqli') {
            $have = $conn->query($q);
            $c = $have->num_rows;
        } else {
            $have = mysql_query($q);
            $c = mysql_num_rows($have);
        }
        $temp['Havesub'] = ($c ? 1 : 0);
        $temp['link_share'] = HTTP_SERVER . "/index.php?route=product/category&path=" . $product['ID'];
        // $adv_data = disp_advanced_data();
        // foreach ($adv_data as $k => $v) {
        //     $temp[$k] = $v;
        // }
        // $footerData_arr = array();
        // $footer_data = disp_footer_data($footerData_arr);
        // $setting_data = disp_setting_data("0", "0", null, null, $temp['Key'], $temp['Api'], null, null, $footer_data);
        // foreach ($setting_data as $k => $v) {
        //     $temp[$k] = $v;
        // }
        $Arr_Json = Json_CData($temp);
        $Final_Json[] = $Arr_Json;
    }
    output($Final_Json);
    //echo json_encode_convert($out, "Products_Depts");
    //exit;
}

function Store_Display($conn, $db_prefix, $product_id,$request) {
    $Product_List = array();
    
    $sql = "SELECT " . $db_prefix . "product.product_ID as 'ID',image as 'Image',viewed,date_added," . $db_prefix . "product.quantity as 'Quantity',name as 'Title',price as 'Price',description as 'Description', location as 'Location' FROM " . $db_prefix . "product INNER JOIN " . $db_prefix . "product_description ON " . $db_prefix . "product_description.product_id = " . $db_prefix . "product.product_id AND " . $db_prefix . "product.product_id = '$product_id' ";
    
    if(isset($request['lang']))
    {
        $sql .= " AND " . $db_prefix . "category_description.language_id='{$request['lang']}'";
    }
    
    $u = "UPDATE " . $db_prefix . "product SELECT viewed = viewed+1 WHERE product_id='$request[ID]'";
    if (DB_DRIVER == 'mysqli') {
        $Products = $conn->query($sql);
        $Product_List = $Products->fetch_assoc();
        $conn->query($u);
    } else {
        $Products = mysql_query($sql);
        $Product_List = mysql_fetch_assoc($Products);
        mysql_query($u);
    }
    $out = array();
    $temp = array();
    $temp['id'] = $Product_List['ID'];
    $temp['User_ID'] = 1;
    $temp['name'] = "Administrator";
    $q = "SELECT category_id as 'Depts_ID',name as 'Dept_Title' FROM " . $db_prefix . "category_description WHERE category_id = (SELECT category_id FROM " . $db_prefix . "product_to_category WHERE product_id = " . $request['ID'] . " LIMIT 0,1) LIMIT 0,1";
    
    if(isset($request['lang']))
    {
        $q .= " AND " . $db_prefix . "category_description.language_id='{$request['lang']}'";
    }
    
    if (DB_DRIVER == 'mysqli') {
        $cat = $conn->query($q);
        $c = $cat->fetch_assoc();
    } else {
        $cat = mysql_query($q);
        $c = mysql_fetch_assoc($cat);
    }
    $temp['Depts_ID'] = $c['Depts_ID'];
    $temp['Dept_Title'] = $c['Dept_Title'];
    $q = "SELECT store_id as 'Shop_ID',name as 'Shop_Title' FROM " . $db_prefix . "store WHERE store_id = (SELECT store_id FROM " . $db_prefix . "product_to_store WHERE product_id = " . $request['ID'] . " LIMIT 0,1) LIMIT 0,1";
    if (DB_DRIVER == 'mysqli') {
        $cat = $conn->query($q);
        $c = $cat->fetch_assoc();
    } else {
        $cat = mysql_query($q);
        $c = mysql_fetch_assoc($cat);
    }
    $temp['Shop_ID'] = ($c['Shop_ID'] ? $c['Shop_ID'] : 0);
    $temp['Shop_Title'] = ($c['Shop_Title'] ? $c['Shop_Title'] : "");
    $temp['Title'] = $Product_List['Title'];
    $attributes = array();
    $attributes = getProductAttributes($conn, $db_prefix, $product_id);


    $Img = array();
    $Img = getProductImages($conn, $db_prefix, $product_id);
    $images = array(HTTP_SERVER . "image/" . $Product_List['Image']);

    foreach ($Img as $Im)
        $images[] = HTTP_SERVER . "image/" . $Im["image"];

    $temp["Pic"] = $images[0];
    $im_arr = array();
    foreach ($images as $im) {
        $arr["Image"] = $im;
        $im_arr[] = $arr;
    }
    $temp['Images'] = $im_arr;
    $temp['description'] = limit_string($Product_List['Description'], 200);
    $temp['link_share'] = HTTP_SERVER . "index.php?route=product/category&path=" . $Product_List['ID'];
    $temp['order'] = $Product_List['Price'];
    $temp['Expire_In'] = ($Product_List['Expire_In'] ? $Product_List['Expire_In'] : "");
    $temp['Hits'] = $Product_List['viewed'];
    $q = "SELECT count(review_id) as 'c' FROM " . $db_prefix . "review WHERE product_id = '$Product_List[ID]'";
    if (DB_DRIVER == 'mysqli') {
        $comments_count = $conn->query($q);
        $c = $comments_count->fetch_assoc();
    } else {
        $comments_count = mysql_query($q);
        $c = mysql_fetch_assoc($comments_count);
    }
    $temp['Comment_Num'] = $c['c'];
    $temp['Product_Published'] = (string) strtotime($Product_List['date_added']);
    $temp["Data"] = $attributes;
    $temp['Key'] = "ID";
    $temp['Api'] = "&ID=" . $temp['ID'];
    $q_color = "SELECT name, text FROM " . $db_prefix . "attribute_description inner join " . $db_prefix . "product_attribute on " . $db_prefix . "attribute_description.attribute_id=" . $db_prefix . "attribute_description.attribute_id WHERE product_id='{$Product_List['ID']}' and name='color'";
     if(isset($request['lang']))
    {
        $q_color .= " AND " . $db_prefix . "attribute_description.language_id='{$request['lang']}'";
    }
    if (DB_DRIVER == 'mysqli') {
        $color_res = $conn->query($q_color);
        $color = $color_res->fetch_assoc();
    } else {
        $color_res = mysql_query($q_color);
        $color = mysql_fetch_assoc($color_res);
    }
    if (isset($color["text"])) {
        $temp["Color"] = $color["text"];
    } else {
        $temp["Color"] = "";
    }
    $dept = ["ID" => $temp['Depts_ID'], "Title" => $temp['Dept_Title'], "Img" => ""];
    $shop = ["ID" => $temp['Shop_ID'], "Title" => $temp['Shop_Title'], "Img" => ""];
    // $adv_data = disp_advanced_data($dept, null, $shop);
    // foreach ($adv_data as $k => $v) {
    //     $temp[$k] = $v;
    // }
    // $footerData_arr = array();
    // $footer_data = disp_footer_data($footerData_arr);
    // $setting_data = disp_setting_data("0", "0", null, null, $temp['Key'], $temp['Api'], null, $temp['Color'], $footer_data);
    // foreach ($setting_data as $k => $v) {
    //     $temp[$k] = $v;
    // }

    $phone = checkProductAttrExist(getProductAttributes($conn, $db_prefix, $Product_List['ID']), "phone");
    $latitude = checkProductAttrExist(getProductAttributes($conn, $db_prefix, $Product_List['ID']), "latitude");
    $longitude = checkProductAttrExist(getProductAttributes($conn, $db_prefix, $Product_List['ID']), "longitude");
    $Others_Data['Product_Display'] = array("Price" => number_format($Product_List['Price'], 2), "Hits" => $Product_List['viewed'], "Comment_Hits" => $c['c'], "Latitude" => $latitude, "Longitude" => $longitude, "URL" => $temp['Link_Share'], "Phone1" => $phone, "Phone2" => "", "Address" => $Product_List['Location'], "Discount" => "");
    $Others_Data['Form_Choice']['Form'] = array();

    $Arr_Json = Json_CData($temp, $Others_Data, $temp['Data']);
    output($Arr_Json);
}

function Store_Product($conn, $db_prefix,$request) {
    $Product_List = array();
    $sql = "SELECT " . $db_prefix . "product.product_id as 'ID', date_added, viewed, image as 'Img'," . $db_prefix . "product.quantity as 'Quantity',name as 'Title',price as 'Price',description as 'Description' FROM " . $db_prefix . "product INNER JOIN " . $db_prefix . "product_description ON " . $db_prefix . "product_description.product_id = " . $db_prefix . "product.product_id WHERE " . $db_prefix . "product.product_id IN (SELECT product_id FROM " . $db_prefix . "product_to_store WHERE store_id = '$request[ID]')";
    if(isset($request['lang']))
    {
        $sql .= " AND " . $db_prefix . "product_description.language_id='{$request['lang']}'";
    }
    
    $sql .= " ORDER BY " . $db_prefix . "product.product_id DESC";
    if (DB_DRIVER == 'mysqli') {
        $Products = $conn->query($sql);
        while ($Product = $Products->fetch_assoc())
            $Product_List[] = $Product;
    } else {
        $Products = mysql_query($sql);
        while ($Product = mysql_fetch_assoc($Products))
            $Product_List[] = $Product;
    }
    $out = array();
    $temp = array();
    $Final_Json = array();

    foreach ($Product_List as $product) {
        $temp['ID'] = $product['ID'];
        $temp['Title'] = $product['Title'];
        $image = HTTP_SERVER . "/image/$product[Img]";
        $temp['image'] = $image;
        $temp['description'] = limit_string($product['Description'], 200);
        $temp['Price'] = $product['Price'];
        $temp['Expire_In'] = ($product['Expire_In'] ? $product['Expire_In'] : "");
        $temp['Num_Visit'] = $product['viewed'];
        $q = "SELECT count(review_id) as 'c' FROM " . $db_prefix . "review WHERE product_id = '$product[ID]'";
        if (DB_DRIVER == 'mysqli') {
            $comments_count = $conn->query($q);
            $c = $comments_count->fetch_assoc();
        } else {
            $comments_count = mysql_query($q);
            $c = mysql_fetch_assoc($comments_count);
        }
        $temp['Comment_Num'] = $c['c'];
        $temp['Product_Published'] = (string) strtotime($product['date_added']);
        $q = "SELECT " . $db_prefix . "product_attribute.attribute_id as 'ID', name as 'Key', text as 'Value' FROM " . $db_prefix . "product_attribute INNER JOIN " . $db_prefix . "attribute_description ON " . $db_prefix . "attribute_description.attribute_id = " . $db_prefix . "product_attribute.attribute_id WHERE product_id = '$product[ID]'";
        if(isset($request['lang']))
    {
        $sql .= " AND " . $db_prefix . "attribute_description.language_id='{$request['lang']}'";
    }
        
        $t = array();
        if (DB_DRIVER == 'mysqli') {
            $data = $conn->query($q);
            while ($c = $data->fetch_assoc())
                $t[] = $c;
        } else {
            $data = $conn->query($q);
            while ($c = mysql_fetch_assoc($data))
                $t[] = $c;
        }
        $temp['Data'] = $t;
        $q_images = "SELECT image as Image FROM " . $db_prefix . "product_image WHERE product_id='{$product['ID']}'";
        $im_arr = array();
        if (DB_DRIVER == 'mysqli') {
            $data = $conn->query($q_images);
            while ($c = $data->fetch_assoc()) {
                $images['Image'] = HTTP_SERVER . "image/" . $c['Image'];
                $im_arr[] = $images;
            }
        } else {
            $data = $conn->query($q);
            while ($c = mysql_fetch_assoc($data)) {
                $images['Image'] = HTTP_SERVER . "image/" . $c['Image'];
                $im_arr[] = $images;
            }
        }
        $temp['Images'] = $im_arr;
        $temp['Link_Share'] = HTTP_SERVER . "index.php?route=product/product&product_id=" . $product['ID'];
        $temp['Key'] = "ID";
        $temp['Api'] = "&ID=" . $product['ID'];

        $message = checkProductAttrExist(getProductAttributes($conn, $db_prefix, $product['ID']), "message");
        $phone = checkProductAttrExist(getProductAttributes($conn, $db_prefix, $product['ID']), "phone");
        $sms = checkProductAttrExist(getProductAttributes($conn, $db_prefix, $product['ID']), "sms");
        $latitude = checkProductAttrExist(getProductAttributes($conn, $db_prefix, $product['ID']), "latitude");
        $longitude = checkProductAttrExist(getProductAttributes($conn, $db_prefix, $product['ID']), "longitude");
        if ($latitude != false && $longitude != false) {
            $map = $latitude . "," . $longitude;
        } elseif (checkProductAttrExist(getProductAttributes($conn, $db_prefix, $product['ID']), "map") != false) {
            $map = checkProductAttrExist(getProductAttributes($conn, $db_prefix, $product['ID']), "map");
        } else {
            $map = "";
        }

        // $adv_data = disp_advanced_data();
        // foreach ($adv_data as $k => $v) {
        //     $temp[$k] = $v;
        // }
        // $footerData_arr = array();
        // $footer_data = disp_footer_data($footerData_arr);
        // $setting_data = disp_setting_data("0", "0", null, null, $temp['Key'], $temp['Api'], null, null, $footer_data);
        // foreach ($setting_data as $k => $v) {
        //     $temp[$k] = $v;
        // }

        //$out[] = $temp;
        $Others_Data['Content'] = $temp['Des'];
        $Arr_Json = Json_CData($temp, $Others_Data, $temp['Data']);
        $Final_Json[] = $Arr_Json;
    }
    output($Final_Json);
    //echo json_encode_convert($out, "Products_List");
    //exit;
}

function Store_Shops($conn, $db_prefix,$request) {
    $Product_List = array();
    $sql = "SELECT * FROM " . $db_prefix . "store";

    if (DB_DRIVER == 'mysqli') {
        $Products = $conn->query($sql);
        while ($Product = $Products->fetch_assoc())
            $Product_List[] = $Product;
    } else {
        $Products = mysql_query($sql);
        while ($Product = mysql_fetch_assoc($Products))
            $Product_List[] = $Product;
    }
    $out = array();
    $temp = array();
    $Final_Json = array();

    foreach ($Product_List as $product) {
        $temp['ID'] = $product['store_id'];
        $temp['Title'] = $product['name'];
        $temp['Des'] = "";
        $temp['Pic'] = "";
        $temp['Havesub'] = 0;
        $temp['Link_Share'] = $product['url'];

        $temp['Key'] = "ID";
        $temp['Api'] = "&ID=" . $product['store_id'];

        // $adv_data = disp_advanced_data();
        // foreach ($adv_data as $k => $v) {
        //     $temp[$k] = $v;
        // }
        // $footerData_arr = array();
        // $footer_data = disp_footer_data($footerData_arr);
        // $setting_data = disp_setting_data("0", "0", null, null, $temp['Key'], $temp['Api'], null, null, $footer_data);
        // foreach ($setting_data as $k => $v) {
        //     $temp[$k] = $v;
        // }

        $shop_owner = getSetting($conn, $db_prefix, $product['store_id'], "config_owner");
        $shop_tel = getSetting($conn, $db_prefix, $product['store_id'], "config_telephone");
        $shop_fax = getSetting($conn, $db_prefix, $product['store_id'], "config_fax");
        $shop_mail = getSetting($conn, $db_prefix, $product['store_id'], "config_email");
        $shop_address = getSetting($conn, $db_prefix, $product['store_id'], "config_address");

        $Others_Data['Shop'] = array("Content" => $product['name'], "Shop_Owner" => $shop_owner, "Shop_Phone" => $shop_tel,
            "Shop_Fax" => $shop_fax, "Shop_Mail" => $shop_mail, "Delivery_Time" => "", "Shop_Address" => $shop_address,
            "Pay_Way" => "", "Ship_Way" => "", "Shop_Hits" => "", "Facebook" => "", "Youtube" => "", "GooglePlus" => "",
            "Instagram" => "", "Twitter" => "", "Shop_Rating" => "0", "Number_Of_Rate" => "");

        $Store_Products = array();
        $sql = "SELECT image as 'Img', name as 'Title',price as 'Price',description as 'Description' FROM " . $db_prefix . "product INNER JOIN " . $db_prefix . "product_description ON " . $db_prefix . "product_description.product_id = " . $db_prefix . "product.product_id WHERE " . $db_prefix . "product.product_id IN (SELECT product_id FROM " . $db_prefix . "product_to_store WHERE store_id = '{$product['store_id']}')";
        $sql .= " ORDER BY " . $db_prefix . "product.product_id DESC";
        if (DB_DRIVER == 'mysqli') {
            $Products = $conn->query($sql);
            while ($Product = $Products->fetch_assoc())
                $Store_Products[] = $Product;
        } else {
            $Products = mysql_query($sql);
            while ($Product = mysql_fetch_assoc($Products))
                $Store_Products[] = $Product;
        }
        $prods = array();
        foreach ($Store_Products as $product) {
            //print_r($product);die;
            $arr["Title"] = $product["Title"];
            $arr["Img"] = HTTP_SERVER . "image/" . $product["Img"];
            $arr["Price"] = $product["Price"];
            $arr["Description"] = $product["Description"] == "" ? "" : limit_string($product['Description'], 200);

            $prods[] = $arr;
        }
        $Others_Data['Shop']['Product_List'] = $prods;
        $temp['Data'] = array();
        $Arr_Json = Json_CData($temp, $Others_Data, $temp['Data']);
        $Final_Json[] = $Arr_Json;
    }

    output($Final_Json);
    //echo json_encode_convert($out, "Shop_List");
    //exit;
}

function Store_View($conn, $db_prefix,$request) {
    $Product_List = array();
    $sql = "SELECT " . $db_prefix . "product.product_id as 'ID', date_added, viewed, image as 'Img'," . $db_prefix . "product.quantity as 'Quantity',name as 'Title',price as 'Price',description as 'Description' FROM " . $db_prefix . "product INNER JOIN " . $db_prefix . "product_description ON " . $db_prefix . "product_description.product_id = " . $db_prefix . "product.product_id WHERE " . $db_prefix . "product.product_id IN (SELECT product_id FROM " . $db_prefix . "product_to_store WHERE store_id = '$request[ID]')";
    if(isset($request['lang']))
    {
        $sql .= " AND " . $db_prefix . "product_description.language_id='{$request['lang']}'";
    }
    
    $sql .= " ORDER BY " . $db_prefix . "product.product_id DESC";
    if (DB_DRIVER == 'mysqli') {
        $Products = $conn->query($sql);
        while ($Product = $Products->fetch_assoc())
            $Product_List[] = $Product;
    } else {
        $Products = mysql_query($sql);
        while ($Product = mysql_fetch_assoc($Products))
            $Product_List[] = $Product;
    }
    $out = array();
    $temp = array();
    $Final_Json = [];
    foreach ($Product_List as $product) {
        $temp['ID'] = $product['ID'];
        $temp['Title'] = $product['Title'];
        $image = HTTP_SERVER . "/image/$product[Img]";
        $temp['Pic'] = $image;
        $temp['Des'] = limit_string($product['Description'], 200);
        $temp['Price'] = $product['Price'];
        $temp['Expire_In'] = ($product['Expire_In'] ? $product['Expire_In'] : "");
        $temp['Num_Visits'] = $product['viewed'];
        $temp["Key"] = "ID";
        $temp["Api"] = "&ID=" . $temp['ID'];
        $q = "SELECT count(review_id) as 'c' FROM " . $db_prefix . "review WHERE product_id = '$product[ID]'";
        if (DB_DRIVER == 'mysqli') {
            $comments_count = $conn->query($q);
            $c = $comments_count->fetch_assoc();
        } else {
            $comments_count = mysql_query($q);
            $c = mysql_fetch_assoc($comments_count);
        }
        $temp['Comment_Num'] = $c['c'];
        $temp['Product_Published'] = (string) strtotime($product['date_added']);
        $q = "SELECT " . $db_prefix . "product_attribute.attribute_id as 'ID', name as 'Key', text as 'Value' FROM " . $db_prefix . "product_attribute INNER JOIN " . $db_prefix . "attribute_description ON " . $db_prefix . "attribute_description.attribute_id = " . $db_prefix . "product_attribute.attribute_id WHERE product_id = '$product[ID]'";
        if(isset($request['lang']))
    {
        $q .= " AND " . $db_prefix . "attribute_description.language_id='{$request['lang']}'";
    }
        
        $t = array();
        if (DB_DRIVER == 'mysqli') {
            $data = $conn->query($q);
            while ($c = $data->fetch_assoc())
                $t[] = $c;
        } else {
            $data = $conn->query($q);
            while ($c = mysql_fetch_assoc($data))
                $t[] = $c;
        }
        $temp['Data'] = $t;

        // $adv_data = disp_advanced_data();
        // foreach ($adv_data as $k => $v) {
        //     $temp[$k] = $v;
        // }
        // $footerData_arr = array();
        // $footer_data = disp_footer_data($footerData_arr);
        // $setting_data = disp_setting_data("0", "0", null, null, $temp['Key'], $temp['Api'], null, null, $footer_data);
        // foreach ($setting_data as $k => $v) {
        //     $temp[$k] = $v;
        // }

        //$out[] = $temp;
        $Others_Data['Content'] = $temp['Des'];
        $Arr_Json = Json_CData($temp, $Others_Data, $temp['Data']);
        $Final_Json[] = $Arr_Json;
    }
    output($Final_Json);
    //echo json_encode_convert($out, "Products_List");
    //exit;
}

function Store_Comment_List($conn, $db_prefix,$request) {
    $Comments_List = array();
    $sql = "SELECT review_id as 'ID',customer_id as 'User_ID',product_id as 'Products_ID',text as 'Content',author as 'User_Name'," . $db_prefix . "review.date_added as 'DateTime'," . $db_prefix . "user.username," . $db_prefix . "user.image FROM " . $db_prefix . "review LEFT JOIN " . $db_prefix . "user ON " . $db_prefix . "user.user_id = " . $db_prefix . "review.customer_id WHERE product_id = $request[ID] AND " . $db_prefix . "review.status = '1' ";
    if (DB_DRIVER == 'mysqli') {
        $comments = $conn->query($sql);
        if ($comments) {
            while ($Comment = $comments->fetch_assoc())
                $Comments_List[] = $Comment;
        }
    } else {
        $comments = mysql_query($sql);
        if ($comments) {
            while ($Comment = mysql_fetch_assoc($comments))
                $Comments_List[] = $Comment;
        }
    }
    $out = array();
    $temp = array();
    $Final_Json = array();

    foreach ($Comments_List as $comment) {
        $temp['ID'] = $comment['ID'];
        $temp['User_ID'] = $comment['User_ID'];
        $temp['User_Name'] = ($comment['username'] ? $comment['username'] : $comment['User_Name']);
        $temp['User_Img'] = ($comment['image'] ? HTTP_SERVER . "/image/" . $comment['image'] : "");
        $temp['Products_ID'] = $comment['Products_ID'];
        $temp['Des'] = limit_string(strip_tags($comment['Content']), 100);
        $temp['DateTime'] = (string) strtotime($comment['DateTime']);
        $temp['Key'] = "ID";
        $temp['Api'] = "&ID=" . $temp['ID'];
        $user = ["ID" => $temp['User_ID'], "Title" => $temp['User_Name'], "Img" => $temp['User_Img']];

        // $adv_data = disp_advanced_data(null, null, null, null, $user);
        // foreach ($adv_data as $k => $v) {
        //     $temp[$k] = $v;
        // }
        // $footerData_arr = array();
        // $footer_data = disp_footer_data($footerData_arr);
        // $setting_data = disp_setting_data("0", "0", null, null, $temp['Key'], $temp['Api'], null, null, $footer_data);
        // foreach ($setting_data as $k => $v) {
        //     $temp[$k] = $v;
        // }
        $temp['Others_Data']["Product_ID"] = $temp['Products_ID'];
        //$out[] = $temp;
        $Arr_Json = Json_CData($temp, $temp['Others_Data']);
        $Final_Json[] = $Arr_Json;
    }
    output($Final_Json);
    //echo json_encode_convert($out, "Comments_List");
    //exit;
}

function signin($conn, $db_prefix,$request) {
    // var_dump($request['Pass']);exit;
    // $validator = new Validator;
    if (isset($request['email']) && !empty($request['email']) && isset($request['password']) && !empty($request['password']))
     {
        $email = $request['email'];
        // $password = $request['password'];
         // $salt="MBq9nRUFO";
         // return sha1($salt . sha1($salt . sha1($request['password'])));
         $password = html_entity_decode($request['password'], ENT_QUOTES, "utf-8");
         // return md5($password);
        // $sql = "SELECT * FROM " . $db_prefix . "user WHERE email = '" . $email ."' AND status=1 AND password= '".sha1($request['Pass'])."'";
        /*$sql = "SELECT * FROM " . $db_prefix . "customer WHERE email = '" . $email ."' AND status=1 AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $password . "'))))) OR password = '" . md5($password) . "')";*/
        $sql = "SELECT * FROM " .$db_prefix . "customer WHERE LOWER(email) = '" .utf8_strtolower($email) . "'AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $password . "'))))) OR password = '" . md5($password) . "')  AND status = '1'";

         $query = $conn->query($sql);
        // $q=$query->fetch_assoc();
        // // $salt = $q['salt'];
        // // die($salt);
        // $check_pass =password_verify (  $password, $q['password'] );
        // var_dump($check_pass);die;
        // return $query->num_rows;
        if ($query->num_rows > 0) {
            $token = "";
            $user_pass = "";
            $img = "";
            $user_id = "";
            while ($res = $query->fetch_assoc()) {
                $user_id = $res['customer_id'];
                $token = $res['salt'];
                $user_pass = $res['password'];
                $img = $res['image'];
                break;
            }
            // $userdata = array("status" => array("code"=>200,"message"=>"success","error_details"=>array()), "content" => array("userID" => $user_id, "Img" => $img));
            // session_start();
            $_SESSION['userData'] = array("is_logged" => 1, "user_id" => $user_id);
            // echo $_SESSION['userData']['is_logged'];
            // exit();
            // var_dump(http_response_code());die;
            // header("HTTP/1.1 200 OK");             
             // var_dump(http_response_code());die;


            /////check if api_token exist for this user so update it or insert new one if not
            $sql_token = "SELECT * FROM " . $db_prefix . "api_tokens WHERE user_id = '" . $user_id ."'";
        $query_token = $conn->query($sql_token);
        $token='';
        if ($query_token->num_rows == 1) 
        {
            $length=78;
            $token = bin2hex(random_bytes($length));
            $conn->query("UPDATE " . $db_prefix . "api_tokens SET api_token = '".$token."'  WHERE user_id = '" . $user_id . "'");
            // return 1;
        }
        else
        {
            $length=78;
            $token = bin2hex(random_bytes($length));
            $sql_api = "INSERT INTO " . $db_prefix . "api_tokens SET user_id = '" . $user_id . "', api_token = '".$token."'";

             if (DB_DRIVER == 'mysqli') 
             {

            $conn->query($sql_api);
           
            } 
        else
             {
            mysql_query($sql_api);
           
            }
            // return 2;
        }
            $userdata = array("status" => array("code"=>200,"message"=>"success","error_details"=>array()), "content" => array("user_id" => $user_id, "Img" => $img,"access_token"=>$token));
            return json_encode($userdata);
        } 
        else {
            $userdata = array("status" => array("code"=>204,"message"=>"No data","error_details"=>array("no data found")), "content" => array());
            return json_encode($userdata);
        }

    } 
    else {
        $userdata = array("status" => array("code"=>2,"message"=>"Validation failed","error_details"=>array("تسجيل الدخول خاطئ")), "content" => array());
        return json_encode($userdata);
    }
}

function signup($conn, $db_prefix,$request) {
    if (isset($request['email']) && !empty($request['email']) && isset($request['password']) && !empty($request['password'])) {
        // $this->load->model('account/cutomer');
        // $customer = $ModelAccountCustomer->addCustomer($request);
        $userdata = array();
        $data = array();
        $data['firstname'] = isset($request['first_name']) ? $request['first_name'] : "";
        $data['lastname'] = isset($request['last_name']) ? $request['last_name'] : "";
        $check_user = check_user_exists($conn, $db_prefix, $request['email']);
        if ($check_user != "") {
            $userdata = array("status" => array("code"=>2,"message"=>"error","error_details"=>array("هذا المستخدم موجود بالفعل")), "content" => array());
            return json_encode($userdata);
            // exit;
        }
        // $sql = "INSERT INTO `" . $db_prefix . "user` SET username = '" . $request['User'] . "', user_group_id = '10', salt = '" . $salt = token(9) . "', password = '" . sha1($request['Pass']) . "', firstname = '" . $data['first_name'] . "', lastname = '" . $data['last_name'] . "', email = '" . $request['email'] . "', status = '1', date_added = NOW()";
        $password = html_entity_decode($request['password'], ENT_QUOTES, "utf-8");
        $salt = token(9);
        $sql="INSERT INTO " . $db_prefix . "customer SET customer_group_id =  1 , store_id = 0 , language_id =  1 , firstname = '" . $data['firstname'] . "', lastname = '" . $data['lastname'] . "', email = '" . $request['email'] . "', telephone = '" . $request['telephone'] . "', custom_field = '', salt = '" . $salt . "', password =  SHA1(CONCAT('".$salt."', SHA1(CONCAT('".$salt."', SHA1('" . $password . "'))))), newsletter =  0 , ip = '::1', status = 1 , date_added = NOW()";
        if (DB_DRIVER == 'mysqli') {
            $conn->query($sql);
            $user_id = $conn->insert_id;
            // return sha1($salt . sha1($salt . sha1($request['password'])));
        } else {
            mysql_query($sql);
            $user_id = mysql_insert_id();
        }
        
       
        

    $userdata = array("status" => array("code"=>200,"message"=>"success","error_details"=>array()), "content" => array("user_id" => $user_id, "email" => ""));
        return json_encode($userdata);
    } else {
        $userdata = array("status" => array("code"=>2,"message"=>"Error!","error_details"=>array("تسجيل المستخدم خاطئ")), "content" => array());
        return json_encode($userdata);
    }
}

function profile($conn, $db_prefix,$request) {
    // session_start();
      // echo ($session);exit();
    // $userData = $_SESSION['userData'];
    $query_token = $conn->query("SELECT * FROM ". $db_prefix ."api_tokens WHERE api_token='".$request["api_token"]."'");
    if ($query_token->num_rows == 1) {
        // return $query_token->fetch_assoc()['user_id'];
    //     while ($row=mysqli_fetch_row($query_token))
    // {
    // printf ("%s (%s)\n",$row[0],$row[1]);die;
    // }
        // return mysql_insert_id();
        $user_id=$query_token->fetch_assoc()['user_id'];
        // $query = $conn->query("SELECT * FROM ". $db_prefix ."user WHERE user_id='".$user_id."'");
        $query = $conn->query("SELECT * FROM ". $db_prefix ."customer WHERE customer_id='".$user_id."'");
        if ($query) {
            // return 'h';
            while ($res = $query->fetch_assoc()) {
                 // return 'h';
                $arr['id'] = (string) $user_id;
                $arr['name'] = $res['firstname'] . " " . $res['lastname'];
                $arr['description'] = $res['telephone'];
                $arr['Key'] = "ID";
                $arr['Api'] = "&ID=" . $arr['ID'];

                $user = ["ID" => $arr['ID'], "Title" => $arr['Title'], "mobile" => $res['telephone']];

                // $adv_data = disp_advanced_data(null, null, null, null, $user);
                // // return $adv_data;
                // foreach ($adv_data as $k => $v) {
                //     $arr[$k] = $v;
                // }

                // $footerData_arr = array();
                // $footer_data = disp_footer_data($footerData_arr);
                // // return $footer_data;
                // $setting_data = disp_setting_data("0", "0", null, null, $arr['Key'], $arr['Api'], null, null, $footer_data);
                // // return $setting_data;
                // foreach ($setting_data as $k => $v) {
                //     $arr[$k] = $v;
                // }
                 // return $arr;
                $Arr_Json = Json_CData($arr, $arr['Others_Data']);
                // return $Arr_Json;
                output($Arr_Json);
            }
        }
    } else {
        $userdata =  array("status" => array("code"=>2,"message"=>"error","error_details"=>array( "برجاء تسجيل دخولك")), "content" => array());
        return json_encode($userdata);
    }
}

function Shopping_cart($conn, $db_prefix,$request) {
    // $userData = $_SESSION['userData'];
    // if (isset($userData['is_logged']) && $userData['is_logged'] == 1) {
     $query_token = $conn->query("SELECT * FROM ". $db_prefix ."api_tokens WHERE api_token='".$request["api_token"]."'");
    if ($query_token->num_rows == 1) {
        $user_id=$query_token->fetch_assoc()['user_id'];
        $sql = "SELECT * FROM {$db_prefix}cart ";
        $query = $conn->query($sql);
        if ($query->num_rows > 0 ) {
            $temp = array();
            $Final_Json = array();
            $i=0;
            $sub_total=0;
            while ($res = $query->fetch_assoc()) {
                $sql_product = $conn->query("SELECT " . $db_prefix . "product.product_id as 'ID', date_added, viewed, image as 'Img', quantity as 'Quantity',name as 'Title',price as 'Price',description as 'Description' FROM " . $db_prefix . "product INNER JOIN " . $db_prefix . "product_description ON " . $db_prefix . "product.product_id=" . $db_prefix . "product_description.product_id WHERE " . $db_prefix . "product.product_id = '{$res['product_id']}'");
                // $products=[];
                
                $items=mysqli_fetch_all($sql_product, MYSQLI_ASSOC);
                // echo count($items);
                foreach ($items as $product ) {
                    $products[$i]["cart_item_id"]=$res['cart_id'];
                    $products[$i]["product_id"]=$product['ID'];
                    $products[$i]["product_name"]=$product['Title'];
                    $products[$i]["product_description"]=$product['Description'];
                    $products[$i]["product_image"]=HTTP_SERVER . "/image/$product[Img]";
                    $products[$i]["count"]=$res['quantity'];
                    $products[$i]["item_price"]=$product['Price'];
                    $products[$i]["price_of_count"]=$res['quantity']*$product['Price'];
                    
                    $sub_total +=$res['quantity']*$product['Price'];
                    // echo $i;
                    $i++;

                }
            }
             $userdata =  array("status" => array("code"=>200,"message"=>"success","error_details"=>array()), "content" => array("products"=>$products,"sub_total"=>$sub_total,"shipping"=>0,"vat"=>0,"total"=>$sub_total,"restaurant_delivery_methods"=>array()));
        return json_encode($userdata);
        }
        else {
        $userdata =  array("status" => array("code"=>204,"message"=>"No data","error_details"=>array( "cart is empty")), "content" => array());
        return json_encode($userdata);
    }
    } else {
        $userdata =  array("status" => array("code"=>2,"message"=>"error","error_details"=>array( "برجاء تسجيل دخولك")), "content" => array());
        return json_encode($userdata);
    }
}

function Add_To_Shopping_Cart($conn, $db_prefix,$request) {
    // $userData = $_SESSION['userData'];
    // if (isset($userData['is_logged']) && $userData['is_logged'] == 1) {
     $query_token = $conn->query("SELECT * FROM ". $db_prefix ."api_tokens WHERE api_token='".$request["api_token"]."'");
    if ($query_token->num_rows == 1) {
        $user_id=$query_token->fetch_assoc()['user_id'];
        $count = isset($request['count']) && is_numeric($request['count']) ? $request['count'] : "";
        if (add_to_cart($conn, $db_prefix, $user_id , $request['item_id'], $count, array(), 0)) {
            if(isset($request['action_id']) && !empty($request['action_id']) && isset($request['item_id']) && !empty($request['item_id']))
        {
            $query_actions = $conn->query("INSERT INTO ". $db_prefix ."users_actions SET product_id='".$request["item_id"]."', action_id='".$request["action_id"]."' , user_id='".$user_id."'");
        }
        else
        {

            $userdata = array("status" => array("code"=>1,"message"=>"Error!","error_details"=>array("action id and item id required")), "content" => array());
                return json_encode($userdata);
        }
            $userdata = array("status" => array("code"=>200,"message"=>"success","error_details"=>array()),  "content" => array("product_id"=>$request['item_id']));
            return json_encode($userdata);
        } else {
            $userdata = array("status" => array("code"=>2,"message"=>"error","error_details"=>array( "فشلت العملية")), "content" => array());
            return json_encode($userdata);
        }
    } else {
        $userdata = array("status" => array("code"=>2,"message"=>"error","error_details"=>array( "برجاء تسجيل دخولك")), "content" => array());
        return json_encode($userdata);
    }
}

function Remove_From_Shopping_Cart($conn, $db_prefix,$request) {
    // $userData = $_SESSION['userData'];
    // if (isset($userData['is_logged']) && $userData['is_logged'] == 1) {
     $query_token = $conn->query("SELECT * FROM ". $db_prefix ."api_tokens WHERE api_token='".$request["api_token"]."'");
    if ($query_token->num_rows == 1) {
        $user_id=$query_token->fetch_assoc()['user_id'];
        if (remove_from_cart($conn, $db_prefix, $user_id,$request['item_id'])) {
            $userdata = array("status" => array("code"=>200,"message"=>"success","error_details"=>array()),  "content" => array());
            return json_encode($userdata);
        } else {
            $userdata = array("status" => array("code"=>404,"message"=>"invalid cart item ","error_details"=>array( "product not found!")), "content" => array());
            return json_encode($userdata);
        }
    } else {
        $userdata = array("status" => array("code"=>2,"message"=>"error","error_details"=>array( "برجاء تسجيل دخولك")), "content" => array());
        return json_encode($userdata);
    }
}
/////checkout
function checkout($conn,$db_prefix,$data)
{
    $query_token = $conn->query("SELECT * FROM ". $db_prefix ."api_tokens WHERE api_token='".$data["access_token"]."'");
    if ($query_token->num_rows == 1) {
        $user_id=$query_token->fetch_assoc()['user_id'];
         // var_dump(implode(',',$data['cart_items_ids']));die;
        $cart=$conn->query("SELECT * From ".$db_prefix."cart WHERE customer_id='".(int)$user_id."' AND cart_id IN ('".implode(',',$data['cart_items_ids'])."')");
          // var_dump($cart->fetch_assoc()['customer_id']);die;
        if( $cart->num_rows > 0  )
        {
            $user=$conn->query("SELECT * From ".$db_prefix."customer WHERE customer_id='".$user_id."'");
             // var_dump($user->num_rows);die;
            $customer=$user->fetch_assoc();
            $store_info=$conn->query("SELECT * From ".$db_prefix."store WHERE store_id='".(int)$data['store_id']."'");
            // var_dump($store_info->num_rows);die;
            $store=$store_info->fetch_assoc();
            $order="INSERT INTO `" . $db_prefix . "order` SET invoice_prefix ='INV-2018-00', store_id = '" . $data['store_id'] . "', store_name = '" . $store['name'] . "', store_url = '" . $store['url'] . "', customer_id = '" . $data['customer_id'] . "', customer_group_id = '" . $customer['customer_group_id'] . "', firstname = '" . $customer['firstname'] . "', lastname = '" . $customer['lastname'] . "', email = '" . $customer['email'] . "', telephone = '" . $customer['telephone'] . "', custom_field = '" .$customer['custom_field']  . "', payment_firstname = '" . $data['payment_firstname'] . "', payment_lastname = '" . $data['payment_lastname'] . "', payment_company = '" . $data['payment_company'] . "', payment_address_1 = '" . $data['payment_address_1'] . "', payment_address_2 = '" . $data['payment_address_2'] . "', payment_city = '" . $data['payment_city'] . "', payment_postcode = '" . $data['payment_postcode'] . "', payment_country = '" . $data['payment_country'] . "', payment_country_id = '" . $data['payment_country_id'] . "', payment_zone = '" . $data['payment_zone'] . "', payment_zone_id = '" . $data['payment_zone_id'] . "', payment_address_format = '" . $data['payment_address_format'] . "', payment_custom_field = 1 , payment_method = '" . $data['payment_method'] . "', payment_code = '" . $data['payment_code'] . "', shipping_firstname = '" . $data['shipping_firstname'] . "', shipping_lastname = '" . $data['shipping_lastname'] . "', shipping_company = '" . $data['shipping_company'] . "', shipping_address_1 = '" . $data['shipping_address_1'] . "', shipping_address_2 = '" . $data['shipping_address_2'] . "', shipping_city = '" . $data['shipping_city'] . "', shipping_postcode = '" . $data['shipping_postcode'] . "', shipping_country = '" . $data['shipping_country'] . "', shipping_country_id = '" . $data['shipping_country_id'] . "', shipping_zone = '" . $data['shipping_zone'] . "', shipping_zone_id = '" . $data['shipping_zone_id'] . "', shipping_address_format = '" . $data['shipping_address_format'] . "', shipping_custom_field = 1, shipping_method = '" . $data['shipping_method'] . "', shipping_code = '" . $data['shipping_code'] . "', comment = '" . $data['comment'] . "', total = '" . $data['total_price'] . "', language_id = '" . $customer['language_id'] . "', currency_code = '" . $data['currency_code'] . "',  ip = '" . $customer['ip'] . "', forwarded_ip = '" .  $customer['forwarded_ip'] . "', date_added = NOW(), date_modified = NOW()";
            $conn->query($order);
            $checkout_id = $conn->insert_id;
            $order_data = array("status" => array("code"=>200,"message"=>"success","error_details"=>array( "")), "content" => array("checkout_id"=>$checkout_id ,"user_name"=>$customer['firstname'],"total_price"=>$data['total_price'],"store"=>$store['name']));
        return json_encode($order_data);
        }
        else
        {
           $userdata = array("status" => array("code"=>422,"message"=>"cart items did not exist","error_details"=>array( "Error happened, please try again")), "content" => array());
        return json_encode($userdata); 
        }

        
    }
    else {
        $userdata = array("status" => array("code"=>2,"message"=>"error","error_details"=>array( "برجاء تسجيل دخولك")), "content" => array());
        return json_encode($userdata);
    }



}

//////get stores
function get_stores($conn,$db_prefix,$request)
{
    $query = $conn->query("SELECT * FROM " . $db_prefix . "store");

    if ($query->num_rows > 0) {
        // echo $query->fetch_assoc()['user_id'];die;
        // echo $query->num_rows;die;
        $store_list=mysqli_fetch_all($query, MYSQLI_ASSOC);
        foreach ($store_list as $store) {
        $temp['id'] = $store['store_id'];
        $temp['name'] = $store['name'];
        $image = HTTP_SERVER . "";
        $temp['image'] = $image;
        $temp['description'] = limit_string(strip_tags(""), 200);
        $temp['order'] = "";
        $temp['Expire_In'] = "";
        $temp['visit_num'] = "";
        $temp['link_share'] =$store['url'];
         $Arr_Json = Json_CData($temp);
        $Final_Json[] = $Arr_Json;
    }
    output($Final_Json);
        // $stores= array("status" => array("code"=>200,"message"=>"success","error_details"=>array()), "content" => array("stores" => mysqli_fetch_all($query, MYSQLI_ASSOC)));
        // return json_encode($stores);
    } else {
        $stores = array("status" => array("code"=>204,"message"=>"No data","error_details"=>array("no data found")), "content" => array());
            return json_encode($stores);
    }
}

function check_user_exists($conn, $db_prefix, $mail = '') {
    // $where1 = "username = '" . $username . "'";
    $where2 = "email = '" . $mail . "'";
    // $query = $conn->query("SELECT * FROM `" . $db_prefix . "user` WHERE $where1 OR $where2");
    $query = $conn->query("SELECT * FROM `" . $db_prefix . "customer` WHERE $where2");
    if ($query->num_rows == 1) {
        // echo $query->fetch_assoc()['user_id'];die;
        // echo $query->num_rows;die;
        return array("num_rows" => $query->num_rows);
    } else {
        return "";
    }
}

function add_to_cart($conn, $db_prefix, $customer_id,$product_id, $quantity , $option = array(), $recurring_id = 0) {
    $query = $conn->query("SELECT COUNT(*) AS total FROM " . $db_prefix . "cart WHERE customer_id = '" . $customer_id . "' AND product_id = '" . (int) $product_id . "' AND recurring_id = '" . (int) $recurring_id . "' AND `option` = '" . json_encode($option) . "'");
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            if (!$row['total']) {
                $conn->query("INSERT " . $db_prefix . "cart SET customer_id = '" . $customer_id . "', product_id = '" . (int) $product_id . "', recurring_id = '" . (int) $recurring_id . "', `option` = '" . json_encode($option) . "', quantity = '" . (int) $quantity . "', date_added = NOW()");
            } else {
               
                $conn->query("UPDATE " . $db_prefix . "cart SET quantity = " . (int) $quantity . " WHERE customer_id = '" . $customer_id . "' AND product_id = '" . (int) $product_id . "' AND recurring_id = '" . (int) $recurring_id . "' AND `option` = '" . json_encode($option) . "'");
            }
        }
        return true;
    } else {
        return false;
    }
}

function remove_from_cart($conn, $db_prefix,$user_id, $product_id) {
    $query = $conn->query("DELETE FROM " . $db_prefix . "cart WHERE customer_id = '" . $user_id . "' AND product_id='" . (int) $product_id . "';");
    if ($query) {
        return true;
    }
    return false;
}

function getProductAttributes($conn, $db_prefix, $product_id) {
    $product_attribute_group_data = array();
    $sql = "SELECT a.attribute_id as 'ID', ad.name as 'Key' , pa.text as 'Value' FROM " . $db_prefix . "product_attribute pa INNER JOIN " . $db_prefix . "attribute a ON (pa.attribute_id = a.attribute_id) INNER JOIN " . $db_prefix . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int) $product_id . "' ORDER BY a.sort_order, ad.name";

    if (DB_DRIVER == 'mysqli') {
        $prod = $conn->query($sql);
        while ($pro = $prod->fetch_assoc())
            $product_attribute_group_data[] = $pro;
    } else {
        $prod = mysql_query($sql);
        while ($pro = mysql_fetch_assoc($prod))
            $product_attribute_group_data[] = $pro;
    }
    return $product_attribute_group_data;
}

function checkProductAttrExist($product_attrs, $attr) {
    $check = false;
    foreach ($product_attrs as $k => $v) {
        if ($v['Key'] == $attr) {
            $check = $v['Value'];
            break;
        }
    }
    return $check;
}

function getProductImages($conn, $db_prefix, $product_id) {
    $product_image_group_data = array();
    $sql = "SELECT image FROM " . $db_prefix . "product_image WHERE product_id = " . $product_id;

    if (DB_DRIVER == 'mysqli') {
        $prod = $conn->query($sql);
        while ($pro = $prod->fetch_assoc())
            $product_image_group_data[] = $pro;
    } else {
        $prod = mysql_query($sql);
        while ($pro = mysql_fetch_assoc($prod))
            $product_image_group_data[] = $pro;
    }
    return $product_image_group_data;
}

function getSetting($conn, $db_prefix, $store_id, $key) {
    $sql = "SELECT value FROM " . $db_prefix . "setting WHERE store_id='$store_id' and key='$key'";
    $setting_data = array();
    if (DB_DRIVER == 'mysqli') {
        $res = $conn->query($sql);
        if ($res) {
            while ($pro = $res->fetch_assoc())
                $setting_data = $pro;
        }
    } else {
        $res = mysql_query($sql);
        if ($res) {
            while ($pro = mysql_fetch_assoc($res))
                $setting_data = $pro;
        }
    }
    return isset($setting_data["value"]) ? $setting_data["value"] : "";
}

// function Json_Arr_Setting($Target_Action_ID, $Target_Layout_ID, $Havesub, $Api, $Key, $Dialog, $Color, $Footer) {
//     $Setting = array();
//     $Setting['Target_Action_ID'] = "$Target_Action_ID";
//     $Setting['Target_Layout_ID'] = "$Target_Layout_ID";
//     $Setting['Havesub'] = "$Havesub";
//     $Setting['Key'] = "$Key";
//     $Setting['Api'] = "$Api";
//     $Setting['Dialog'] = "$Dialog";
//     $Setting['Color'] = "$Color";
//     $Setting['Footer'] = $Footer;

//     return $Setting;
// }

function Json_Basic_Data($ID, $Title, $Des, $Pic, $Link_Share, $DateTime, $Links, $ArrImg = array(), $ArrVideo = array(), $Value = 0 ,$Price , $Rate , $Price_Currency,$Others_Data) {

    $Arr['id'] = "$ID";
    $Arr['name'] = "$Title";
    $Arr['description'] = "$Des";
    $Arr['image'] = "$Pic";
    $Arr['content']="";
    $Arr['Key'] = "$Value";
    $Arr['link_share'] = "$Link_Share";
    $Arr['created_at'] = "$DateTime";
    $Arr['duration']="";
    $Arr['price']="$Price";
    $Arr['rate']="$Rate";
    $Arr['price_currency']="$Price_Currency";
    $Arr['Links'] = "$Links";
    
    if (count($ArrImg) == 0)
        $ArrImg = array();
    if (count($ArrVideo) == 0)
        $ArrVideo = array();
    $Arr['media'] = $ArrImg;
    $Arr['media'] += $ArrVideo;
    $Arr['more'] =$Others_Data;
    return $Arr;
}

function Json_Others_Data($Others_Data) {
    // return $Others_Data;
    $array['key']='keyvalue';
    $array['group_name']="Key Value Fields";
    if(count($Others_Data) > 0 )
    {
        foreach ($Others_Data as $key => $value) {
        $array['value'][]=array("id"=>$value['ID'],"parameter"=>"","name"=>$value['Key'],"value"=>array("value_type"=>"normal","value_string"=>$value['Value']),"dkv_id"=>"","title"=>"","des"=>"","setting_id"=>"");
    }
    }
    else
    {
        $array['value']=array(array("id"=>0,"parameter"=>"","name"=>"","value"=>array("value_type"=>"normal","value_string"=>""),"dkv_id"=>"","title"=>"","des"=>"","setting_id"=>""));
    }
    
    // $array['value']=array(array("id"=>1,"parameter"=>"","name"=>"","value"=>array("value_type"=>"","value_string"=>""),"dkv_id"=>"","title"=>"","des"=>"","setting_id"=>""));
    return array($array);
}

// function Json_Advanced_Data($Dept = array(), $Source = array(), $Shop = array(), $Model = array(), $User = array(), $Content_Json = array(), $Author = array()) {
//     $More_Data['Dept'] = $Dept;
//     $More_Data['Source'] = $Source;
//     $More_Data['Shop'] = $Shop;
//     $More_Data['Model'] = $Model;
//     $More_Data['User'] = $User;
//     $More_Data['Author'] = $Author;
//     $More_Data['Content_Json'] = $Content_Json;

//     return $More_Data;
// }

// function Json_Stat_Data($Num_Visit, $Num_Comment) {
//     $Stat = array();
//     $Stat['Num_Visit'] = "$Num_Visit";
//     $Stat['Comment_Num'] = "$Num_Comment";
//     return $Stat;
// }

function Json_Action_Creat($Arr_Basc_Data, $Arr_Advanced_Data = array(), $Arr_Setting_Data = array(), $Arr_Stat_Data = array(), $Arr_Others_Data = array(), $Key_Value = array()) {
    // $array=[];
    

    // foreach (array($Arr_Basc_Data) as $key => $value) {
    //     $value['more']=(array)$Arr_Others_Data;
    //     $array[]=$value;
       
    // }
    // $Arr['Basc_Data'] = $Arr_Basc_Data;
    // $Arr['Advanced_Data'] = $Arr_Advanced_Data;
    // $Arr['Setting_Data'] = $Arr_Setting_Data;
    // $Arr['Stat_Data'] = $Arr_Stat_Data;
    // $Arr['more'] = $Arr_Others_Data;
    // $Arr['Key_Value'] = $Key_Value;
    return $Arr_Basc_Data;
}

function Json_CData($output, $Others_Data = array(), $Key_Value = array()) {
    $Arr_Basc_Data = Json_Basic_Data($output['id'], $output['name'], $output['description'], $output['image'], $output['link_share'], $output['DateTime'], $output['Links'], $output['Images'], $output['Videos'], $output['Key'],$output['price'],$output['rate'],$output['price_currency'],Json_Others_Data($Others_Data));
    // $Arr_Setting_Data = Json_Arr_Setting($output['Target_Action_ID'], $output['Target_Layout_ID'], $output['Havesub'], $output['Api'], $output['Key'], $output['Dialog'], $output['Color'], $output['Footer']);
    // $Arr_Stat_Data = Json_Stat_Data($output['Visit_Num'], $output['Comment_Num']);
    // $Arr_Advanced_Data = Json_Advanced_Data($output['Dept'], $output['Source'], $output['Shop'], $output['Model'], $output['User'], $output['Content_Json'], $output['Author']);
    if (empty($Others_Data))
        $Others_Data = new Jsonobjects;

    $Arr_Json = Json_Action_Creat($Arr_Basc_Data, $Arr_Advanced_Data, $Arr_Setting_Data, $Arr_Stat_Data, $Others_Data, $Key_Value);
    return $Arr_Json;
}

function limit_string($string, $charlimit) {
    if (strlen($string) < $charlimit) {
        return $string . '...';
    } elseif (substr($string, $charlimit - 1, 1) != ' ') {
        $string = substr($string, '0', $charlimit);
        $array = explode(' ', $string);
        array_pop($array);
        $new_string = implode(' ', $array);

        return $new_string . '...';
    } else {

        return substr($string, '0', $charlimit - 1) . '...';
    }
}

function Json_Help_Data($Key = 0, $KTable = "") {
    $Help_Arr = new Jsonobjects;
    if ($Key != '') {
        $Help_Arr = $this->connection->fetchOne('SELECT ID,Img,Title  FROM ' . $KTable . '  WHERE ID=' . $Key, Phalcon\Db::FETCH_ASSOC);
        $Help_Arr['Img'] = IMAGE_URL . $Help_Arr['Img'];
    }
    return $Help_Arr;
}

function Json_GetHelp_Data($output, $KTable, $Setting_Json = 0) {
    if ($Setting_Json == 1)
        $output['Json'] = json_decode($output['Json'], true);
    else
        $output['Json'] = array();

    if ($KTable != "") {
        $output['Dept'] = Json_Help_Data($output['Dept_ID'], $KTable . "_depts");

        $output['Source'] = Json_Help_Data($output['Source_ID'], $KTable . "_source");
        $output['Shop'] = Json_Help_Data($output['Shop_ID'], $KTable . "_models");
        $output['Model'] = Json_Help_Data($output['Model_ID'], $KTable . "_shops");
        $output['User'] = Json_Help_Data($output['User_ID'], "users");
        $output['Author'] = Json_Help_Data($output['Author_ID'], $KTable . "_author");
    } else {
        $output['Dept'] = new Jsonobjects;
        $output['Source'] = new Jsonobjects;
        $output['Shop'] = new Jsonobjects;
        $output['Model'] = new Jsonobjects;
        $output['User'] = new Jsonobjects;
        $output['Author'] = new Jsonobjects;
    }
    return $output;
}

//depts for all module
function Json_DataType_Str($KTable, $TypeHelp, $ID = 0) {
    if ($ID == '')
        $ID = 0;
    $outputs = array();
    $results = $this->connection->fetchAll('SELECT Target_Layout_ID,Target_Action_ID,ID,Title,Des,`Img`,`Visit_Num` FROM ' . $KTable . '_' . $TypeHelp . ' WHERE `Main`=' . $ID . ' AND Application_ID=' . Application_ID . ' AND Module_ID=' . Module_ID . ' Order by ID ASC');
    foreach ($results as $output) {

        $output['Link_Share'] = SITEURL . 'dept_news.php?ID=' . $output['ID'];
        $output['Pic'] = IMAGE_URL . $output['Img'];
        $temp = $this->connection->fetchOne('SELECT count(ID) AS "Havesub" FROM ' . $KTable . '_' . $TypeHelp . ' WHERE Main=' . $output['ID'], Phalcon\Db::FETCH_ASSOC);
        $output['Havesub'] = $temp['Havesub'];
        $Arr_Setting_Data = $this->Json_Arr_Setting($output['Target_Action_ID'], $output['Target_Layout_ID'], $arr_Havesub['Havesub'], "");
        $output['Des'] = $this->limit_string($output['Des'], 100);
        $output = $this->Json_GetHelp_Data($output, $KTable);
        $Arr_Json = $this->Json_CData($output);
        $Final_Json[] = $Arr_Json;
    }
    return $Final_Json;
}

function output($result) {
    header('Content-Type: application/json; charset=utf-8');
    if (strstr($_SERVER['HTTP_USER_AGENT'], 'iPod') || strstr($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
        foreach ($result as $key => $value) {
            $product = array("status" => array("code"=>200,"message"=>"success","error_details"=>array()), "content" => $value);
            echo json_encode($product, 256);
            break;
        }
    } else {
        $product = array("status" => array("code"=>200,"message"=>"success","error_details"=>array()), "content" => $result);
        echo json_encode($product, 256);
    }
    exit();
}

function disp_advanced_data($dept = "", $source = "", $shop = "", $model = "", $user = "", $author = "", $content_json = "") {
    return [
        "Dept" => (empty($dept) ? new Jsonobjects : $dept),
        "Source" => (empty($source) ? new Jsonobjects : $source),
        "Shop" => (empty($shop) ? new Jsonobjects : $shop),
        "Model" => (empty($model) ? new Jsonobjects : $model),
        "User" => (empty($user) ? new Jsonobjects : $user),
        "Author" => (empty($author) ? new Jsonobjects : $author),
        "Content_Json" => (empty($content_json) ? array() : $content_json)
    ];
}

function disp_setting_data($target_action_id = "0", $target_layout_id = "0", $target_module_id = "0", $havesub = "", $key = "", $api = "", $dialog = "", $color = "", $footer = array()) {
    return [
        "Target_Action_ID" => $target_action_id,
        "Target_Layout_ID" => $target_layout_id,
        "Target_Module_ID" => $target_module_id,
        "Havesub" => $havesub,
        "Key" => $key,
        "Api" => $api,
        "Dialog" => $dialog,
        "Color" => $color,
        "Footer" => $footer
    ];
}

function disp_footer_data($data = array()) {
    extract($data);
    $arr = [
        "Control" => [
            "Map" => [
            ],
            "Comment" => [
            ],
            "Share" => [
            ],
            "Message" => [
            ],
            "Zoom" => [
            ],
            "Fav" => [
            ],
            "Call" => [
            ],
            "Sms" => [
            ],
            "Shopping" => [
            ],
            "Reminder" => [
            ],
            "Navigate" => [
            ]
        ],
        "Layout_ID" => isset($Layout_ID) ? $Layout_ID : "1"
    ];
    $keys = ["Target_Action_ID" => "0", "Target_Module_ID" => "0", "Target_Layout_ID" => "0", "Title" => "0", "Image" => "0", "Top" => "0", "Footer" => "0", "Data" => "0"];
    foreach ($keys as $k => $v) {
        $arr["Control"]["Map"][$k] = (isset(${"Map_$k"}) ? ${"Map_$k"} : $v);
        $arr["Control"]["Button"][$k] = (isset(${"Button_$k"}) ? ${"Button_$k"} : $v);
        $arr["Control"]["Comment"][$k] = (isset(${"Comment_$k"}) ? ${"Comment_$k"} : $v);
        $arr["Control"]["Share"][$k] = (isset(${"Share_$k"}) ? ${"Share_$k"} : $v);
        $arr["Control"]["Message"][$k] = (isset(${"Message_$k"}) ? ${"Message_$k"} : $v);
        $arr["Control"]["Zoom"][$k] = (isset(${"Zoom_$k"}) ? ${"Zoom_$k"} : $v);
        $arr["Control"]["Fav"][$k] = (isset(${"Fav_$k"}) ? ${"Fav_$k"} : $v);
        $arr["Control"]["Call"][$k] = (isset(${"Call_$k"}) ? ${"Call_$k"} : $v);
        $arr["Control"]["Sms"][$k] = (isset(${"Sms_$k"}) ? ${"Sms_$k"} : $v);
        $arr["Control"]["Shopping"][$k] = (isset(${"Shopping_$k"}) ? ${"Shopping_$k"} : $v);
        $arr["Control"]["Reminder"][$k] = (isset(${"Reminder_$k"}) ? ${"Reminder_$k"} : $v);
        $arr["Control"]["Navigate"][$k] = (isset(${"Navigate_$k"}) ? ${"Navigate_$k"} : $v);
    }

    return $arr;
}

class Jsonobjects {

    function do_objects() {
        echo "Doing foo.";
    }

}

function change_password($conn, $db_prefix,$request)
{
$query_token = $conn->query("SELECT * FROM ". $db_prefix ."api_tokens WHERE api_token='".$request["api_token"]."'");
    if ($query_token->num_rows > 0) 
    {
        $user_id=$query_token->fetch_assoc()['user_id'];
         $query = $conn->query("UPDATE " . $db_prefix . "user SET  salt = '" . $salt = token(9) . "', password = '" . sha1($salt . sha1($salt . sha1($request['password']))) . "' WHERE user_id='".$user_id."'");
    if ($query) {

        $userdata = array("status" => array("code"=>200,"message"=>"success","error_details"=>array()), "content" => array("user_id" => $user_id));
            return json_encode($userdata);
    }
    return false;

    }
    else
    {
        $userdata = array("status" => array("code"=>1,"message"=>"Error!","error_details"=>array("please login and try again!")), "content" => array());
            return json_encode($userdata);
    }
}
function logout($conn, $db_prefix,$request)
{
    $query_token = $conn->query("SELECT * FROM ". $db_prefix ."api_tokens WHERE api_token='".$request["api_token"]."'");
    if ($query_token->num_rows > 0) 
    {
        $user_id=$query_token->fetch_assoc()['user_id'];
         $query = $conn->query("DELETE FROM " . $db_prefix . "api_tokens WHERE api_token = '" . $request["api_token"] . "' ");
    if ($query) {

        $userdata = array("status" => array("code"=>200,"message"=>"success","error_details"=>array()), "content" => array("user_id" => $user_id));
            return json_encode($userdata);
    }
    return false;

    }
    else
    {
        $userdata = array("status" => array("code"=>1,"message"=>"Error!","error_details"=>array("please login and try again!")), "content" => array());
            return json_encode($userdata);
    }
    
}


function forget_password($conn,$db_prefix,$request)
{
return send_mail($request['email']);
}

function send_mail($email_to)
{
    
$mail = new Mail();

$mail->protocol = $this->config->get('config_mail_protocol');
$mail->parameter = $this->config->get('config_mail_parameter');
$mail->hostname = $this->config->get('config_smtp_host');
$mail->username = $this->config->get('config_smtp_username');
$mail->password = $this->config->get('config_smtp_password');
$mail->port = $this->config->get('config_smtp_port');
$mail->timeout = $this->config->get('config_smtp_timeout');            
$mail->setTo($email_to);
$mail->setFrom("info@avocatoapp.net");
$mail->setSender("info@avocatoapp.net");
$mail->setSubject("test send mail opencart");
$mail->setText("welcome to opencart");

if($mail->send())
{
    $userdata = array("status" => array("code"=>200,"message"=>"success","error_details"=>array()), "content" => array());
            return json_encode($userdata);
}
else
{
    $userdata = array("status" => array("code"=>1,"message"=>"Error!","error_details"=>array("please error sending email")), "content" => array());
     return json_encode($userdata);
}
}

function Product_Action($conn, $db_prefix,$request)
{
    $query_token = $conn->query("SELECT * FROM ". $db_prefix ."api_tokens WHERE api_token='".$request["api_token"]."'");
    if ($query_token->num_rows > 0) 
    {
        $user_id=$query_token->fetch_assoc()['user_id'];
        if(isset($request['action_id']) && !empty($request['action_id']) && isset($request['product_id']) && !empty($request['product_id']))
        {
           $query = $conn->query("INSERT INTO ". $db_prefix ."push_notifications_from_api SET product_id='".$request["product_id"]."', action_id='".$request["action_id"]."' , user_id='".$user_id."'");
           $query_actions = $conn->query("INSERT INTO ". $db_prefix ."users_actions SET product_id='".$request["product_id"]."', action_id='".$request["action_id"]."' , user_id='".$user_id."'");
            if($query)
            { 
                $userdata = array("status" => array("code"=>200,"message"=>"success","error_details"=>array()), "content" => array("user_id" => $user_id));
                return json_encode($userdata);
            }
            else
            {
               $userdata = array("status" => array("code"=>1,"message"=>"Error!","error_details"=>array("error while insert data")), "content" => array());
                return json_encode($userdata);
            } 
        }
        else
        {
            $userdata = array("status" => array("code"=>1,"message"=>"Error!","error_details"=>array("action id and product id required")), "content" => array());
                return json_encode($userdata);
        }
        


    }
    else
    {
        $userdata = array("status" => array("code"=>1,"message"=>"Error!","error_details"=>array("product id and action id are required")), "content" => array());
            return json_encode($userdata);
    }

}

function Category_Product($conn,$db_prefix,$request)
{
 $Category_List = array();
        
    
    $sql = "SELECT " . $db_prefix . "category.category_id as 'ID', date_added, image as 'Img',name as 'Title',description as 'Description' FROM " . $db_prefix . "category INNER JOIN " . $db_prefix . "category_description ON " . $db_prefix . "category_description.category_id = " . $db_prefix . "category.category_id ";
    if (isset($request['cat_id'])) {
        $sql .= " WHERE " . $db_prefix . "category.parent_id  = $request[cat_id] ";
    }
    else
    {
        $sql .= " WHERE " . $db_prefix . "category.parent_id  = 0 ";
    }
    if (!empty($request['category_name'])) {
        $sql .= " AND name LIKE '%$request[category_name]%' ";
    }
    if(isset($request['lang']))
    {
        $sql .= " AND " . $db_prefix . "category_description.language_id='{$request['lang']}'";
    }
    $sql .= " ORDER BY " . $db_prefix . "category.category_id DESC";
    if (DB_DRIVER == 'mysqli') {
        $Categories = $conn->query($sql);
        while ($Category = $Categories->fetch_assoc())
            $Category_List[] = $Category;
    } else {
        $Categories = mysql_query($sql);
        while ($Category = mysql_fetch_assoc($Categories))
            $Category_List[] = $Category;
    }
    $out = array();
    $temp = array();
    $Final_Json = array();
    foreach ($Category_List as $Category) 
    {
        $temp['id'] = $Category['ID'];
        $temp['name'] = $Category['Title'];
        $image = HTTP_SERVER . "image/$Category[Img]";
        $temp['image'] = $image;
        $temp['description'] = limit_string(strip_tags($Category['Description']), 200);
        // $temp['order'] = $product['Price'];
        // $temp['Expire_In'] = ($product['Expire_In'] ? $product['Expire_In'] : "");
        // $temp['visit_num'] = $product['viewed'];
        $temp['link_share'] = HTTP_SERVER . "index.php?route=product/category&path=" . $Category['ID'];
        // $q = "SELECT count(review_id) as 'c' FROM " . $db_prefix . "review WHERE category_id = '$Category[ID]'";
        // if (DB_DRIVER == 'mysqli') {
        //     $comments_count = $conn->query($q);
        //     $c = $comments_count->fetch_assoc();
        // } else {
        //     $comments_count = mysql_query($q);
        //     $c = mysql_fetch_assoc($comments_count);
        // }
        // $temp['Comment_Num'] = $c['c'];
        // $temp['Product_Published'] = (string) strtotime($Category['date_added']);
        // $q = "SELECT " . $db_prefix . "product_attribute.attribute_id as 'ID', name as 'Key', text as 'Value' FROM " . $db_prefix . "product_attribute INNER JOIN " . $db_prefix . "attribute_description ON " . $db_prefix . "attribute_description.attribute_id = " . $db_prefix . "product_attribute.attribute_id WHERE product_id = '$product[ID]'";
    //     if(isset($request['lang']))
    // {
    //     $q .= " AND " . $db_prefix . "attribute_description.language_id='{$request['lang']}'";
    // }
    //     $t = array();
    //     if (DB_DRIVER == 'mysqli') {
    //         $data = $conn->query($q);
    //         while ($c = $data->fetch_assoc())
    //             $t[] = $c;
    //     } else {
    //         $data = $conn->query($q);
    //         while ($c = mysql_fetch_assoc($data))
    //             $t[] = $c;
    //     }
    //     $temp['Data'] = $t;
    //     $q_images = "SELECT image as Image FROM " . $db_prefix . "product_image WHERE product_id='{$temp['ID']}'";
    //     $im_arr = array();
    //     if (DB_DRIVER == 'mysqli') {
    //         $data = $conn->query($q_images);
    //         while ($c = $data->fetch_assoc()) {
    //             $images['Image'] = HTTP_SERVER . "image/" . $c['Image'];
    //             $im_arr[] = $images;
    //         }
    //     } else {
    //         $data = $conn->query($q);
    //         while ($c = mysql_fetch_assoc($data)) {
    //             $images['Image'] = HTTP_SERVER . "image/" . $c['Image'];
    //             $im_arr[] = $images;
    //         }
    //     }
    //     $temp['Images'] = $im_arr;
    //     $q_shop = "SELECT store_id FROM " . $db_prefix . "product_to_store WHERE product_id='{$product['ID']}'";
    //     if (DB_DRIVER == 'mysqli') {
    //         $shop_res = $conn->query($q_shop);
    //         $shop = $shop_res->fetch_assoc();
    //     } else {
    //         $shop_res = mysql_query($q_shop);
    //         $shop = mysql_fetch_assoc($shop_res);
    //     }
    //     $temp['Key'] = "ID";
    //     $temp['Api'] = "&ID=" . $product['ID'];
    //     $q_color = "SELECT name, text FROM " . $db_prefix . "attribute_description inner join " . $db_prefix . "product_attribute on " . $db_prefix . "attribute_description.attribute_id=" . $db_prefix . "attribute_description.attribute_id WHERE product_id='{$product['ID']}' and name='color'";
    //     if(isset($request['lang']))
    // {
    //     $q_color .= " AND " . $db_prefix . "attribute_description.language_id='{$request['lang']}'";
    // }
    //     if (DB_DRIVER == 'mysqli') {
    //         $color_res = $conn->query($q_color);
    //         $color = $color_res->fetch_assoc();
    //     } else {
    //         $color_res = mysql_query($q_color);
    //         $color = mysql_fetch_assoc($color_res);
    //     }
    //     if (isset($color["text"])) {
    //         $temp["Color"] = $color["text"];
    //     } else {
    //         $temp["Color"] = "";
    //     }

        // $adv_data = disp_advanced_data();
        // foreach ($adv_data as $k => $v) {
        //     $temp[$k] = $v;
        // }
        // $footerData_arr = array();
        // $footer_data = disp_footer_data($footerData_arr);
        // $setting_data = disp_setting_data("0", "0", null, null, $temp['Key'], $temp['Api'], null, $temp['Color'], $footer_data);
        // foreach ($setting_data as $k => $v) {
        //     $temp[$k] = $v;
        // }

        $Arr_Json = Json_CData($temp);
        $Final_Json[] = $Arr_Json;
    }
    output($Final_Json);
}
function information($conn,$db_prefix,$request)
{

        
    
    $sql = "SELECT * From " . $db_prefix . "information_description WHERE information_id='{$request['id']}' ";
    
    if(isset($request['lang']))
    {
        $sql .= " AND language_id='{$request['lang']}'";
    }
    
    
        $informations = $conn->query($sql);
        $information = $informations->fetch_assoc();
            
    
    $out = array();
    $temp = array();
    $Final_Json = array();
    
        $temp['id'] = $information['information_id'];
        $temp['name'] = $information['title'];
        $image = "";
        $temp['image'] = $image;
        $temp['description'] = limit_string(strip_tags($information['description']), 200);
        $temp['content'] = limit_string(strip_tags($information['description']), 200);
        // $temp['order'] = $product['Price'];
        // $temp['Expire_In'] = ($product['Expire_In'] ? $product['Expire_In'] : "");
        // $temp['visit_num'] = $product['viewed'];
        $temp['link_share'] = HTTP_SERVER . "index.php?route=information/information&information_id=" . $temp['id'];
    $Arr_Json = Json_CData($temp);
        $Final_Json[] = $Arr_Json;
    
    output($Final_Json);
}


function submit_review($conn,$db_prefix,$request)
{
    if(isset($request['api_token']))
    {
       $query_token = $conn->query("SELECT * FROM ". $db_prefix ."api_tokens WHERE api_token='".$request["api_token"]."'");
        if ($query_token->num_rows > 0) 
        {
            $user_id=$query_token->fetch_assoc()['user_id'];

        } 
    }
   else
    {
        $user_id=0;

    }
    $user_id=0;
    $sql_review = $conn->query("INSERT INTO " . $db_prefix . "review SET customer_id = '" . $user_id . "', product_id = '".$request['product_id']."',text = '".$request['review']."',rating = '".$request['rating']."'");

         
            $id = $conn->insert_id;
         
        
 
             $userdata = array("status" => array("code"=>200,"message"=>"success","error_details"=>array()), "content" => array("id" => $id,"user_id"=>$user_id,"rating"=>$request['rating'],"review"=>$request['review'],"status_id"=>"","created_at"=>""));
                return json_encode($userdata);

}
function contactus($conn,$db_prefix,$request)
{
    if(isset($request['api_token']))
    {
       $query_token = $conn->query("SELECT * FROM ". $db_prefix ."api_tokens WHERE api_token='".$request["api_token"]."'");
        if ($query_token->num_rows > 0) 
        {
            $user_id=$query_token->fetch_assoc()['user_id'];
            $customer=$conn->query("SELECT * FROM ". $db_prefix ."customer WHERE customer_id='".$user_id."'");
            $name=$customer->fetch_assoc()['firstname'];
            $email=$customer->fetch_assoc()['email'];
        } 
    }
   else
    {
        // if(isset($request['name']) && $request['name'] != "" && isset($request['name']) && $request['name'] != "")
        // {
        //     $name=$request['name'];
        //     $email=$request['email'];
        // }
        // else
        // {
        //     $userdata = array("status" => array("code"=>1,"message"=>"Validation error","error_details"=>array(" name and email required")), "content" => array());
        //     return json_encode($userdata);
        // }
        $userdata =  array("status" => array("code"=>2,"message"=>"error","error_details"=>array( "برجاء تسجيل دخولك")), "content" => array());
        return json_encode($userdata);

    }
    $userdata = array("status" => array("code"=>200,"message"=>"success","error_details"=>array()), "content" => array());
            return json_encode($userdata);
}

function add_to_wishlist($conn,$db_prefix,$request)
{
    $query_token = $conn->query("SELECT * FROM ". $db_prefix ."api_tokens WHERE api_token='".$request["api_token"]."'");
        if ($query_token->num_rows > 0) 
        {
            $user_id=$query_token->fetch_assoc()['user_id'];
           $wishlist= "INSERT INTO ". $db_prefix ."customer_wishlist SET customer_id='".$user_id."' , product_id= '".$request['product_id']."'";
            if (DB_DRIVER == 'mysqli') {
            $conn->query($sql);
            $id = $conn->wishlist;
            
        } else {
            mysql_query($wishlist);
            $id = mysql_insert_id();
        }
        
            $userdata =  array("status" => array("code"=>200,"message"=>"success","error_details"=>array()), "content" => array("id"=>$id));
        return json_encode($userdata);
        }
        else
        {
            $userdata =  array("status" => array("code"=>2,"message"=>"error","error_details"=>array( "برجاء تسجيل دخولك")), "content" => array());
        return json_encode($userdata);
        }

}
function wishlist($conn,$db_prefix,$request)
{
    $query_token = $conn->query("SELECT * FROM ". $db_prefix ."api_tokens WHERE api_token='".$request["api_token"]."'");
    if ($query_token->num_rows == 1) {
        $user_id=$query_token->fetch_assoc()['user_id'];
        $sql = "SELECT * FROM {$db_prefix}customer_wishlist ";
        $query = $conn->query($sql);
        if ($query->num_rows > 0 ) {
            $temp = array();
            $Final_Json = array();
            $i=0;
            $sub_total=0;
            while ($res = $query->fetch_assoc()) {
                $sql_product = $conn->query("SELECT " . $db_prefix . "product.product_id as 'ID', date_added, viewed, image as 'Img', quantity as 'Quantity',name as 'Title',price as 'Price',description as 'Description' FROM " . $db_prefix . "product INNER JOIN " . $db_prefix . "product_description ON " . $db_prefix . "product.product_id=" . $db_prefix . "product_description.product_id WHERE " . $db_prefix . "product.product_id = '{$res['product_id']}'");
                // $products=[];
                
                $items=mysqli_fetch_all($sql_product, MYSQLI_ASSOC);
                // echo count($items);
                foreach ($items as $product ) {
                    
                    $products[$i]["product_id"]=$product['ID'];
                    $products[$i]["product_name"]=$product['Title'];
                    $products[$i]["product_description"]=$product['Description'];
                    $products[$i]["product_image"]=HTTP_SERVER . "/image/$product[Img]";
                    
                    // echo $i;
                    $i++;

                }
            }
             $userdata =  array("status" => array("code"=>200,"message"=>"success","error_details"=>array()), "content" => array("products"=>$products));
        return json_encode($userdata);
        }
        else {
        $userdata =  array("status" => array("code"=>204,"message"=>"No data","error_details"=>array( "cart is empty")), "content" => array());
        return json_encode($userdata);
    }
    } else {
        $userdata =  array("status" => array("code"=>2,"message"=>"error","error_details"=>array( "برجاء تسجيل دخولك")), "content" => array());
        return json_encode($userdata);
    }
}
function remove_from_wishlist($conn,$db_prefix,$request)
{
    $query_token = $conn->query("SELECT * FROM ". $db_prefix ."api_tokens WHERE api_token='".$request["api_token"]."'");
        if ($query_token->num_rows > 0) 
        {
            $user_id=$query_token->fetch_assoc()['user_id'];
           $query = $conn->query("DELETE FROM " . $db_prefix . "customer_wishlist WHERE customer_id = '" . $user_id . "' AND product_id='" . (int) $request['product_id'] . "';");
    if ($query) {
         $userdata =  array("status" => array("code"=>200,"message"=>"success","error_details"=>array()), "content" => array());
        return json_encode($userdata);
    }
     else { 
            $userdata = array("status" => array("code"=>404,"message"=>"invalid wishlist item ","error_details"=>array( "product not found!")), "content" => array());
            return json_encode($userdata);
        }
        
           
        }
        else
        {
            $userdata =  array("status" => array("code"=>2,"message"=>"error","error_details"=>array( "برجاء تسجيل دخولك")), "content" => array());
        return json_encode($userdata);
        }

}

function product_offers($conn,$db_prefix,$request)
{
    $query_token = $conn->query("SELECT * FROM ". $db_prefix ."api_tokens WHERE api_token='".$request["api_token"]."'");
        if ($query_token->num_rows > 0) 
        {
            $Product_List = array();
        
    
        $sql = "SELECT " . $db_prefix . "product.product_id as 'ID', date_added, viewed, image as 'Img'," . $db_prefix . "product.quantity as 'Quantity',name as 'Title',price as 'Price',description as 'Description' 
            FROM " . $db_prefix . "product 
            INNER JOIN " . $db_prefix . "product_description ON " . $db_prefix . "product_description.product_id = " . $db_prefix . "product.product_id  
            ";
         if (DB_DRIVER == 'mysqli') {
        $Products = $conn->query($sql);
        while ($Product = $Products->fetch_assoc())
            $Product_List[] = $Product;
    } else {
        $Products = mysql_query($sql);
        while ($Product = mysql_fetch_assoc($Products))
            $Product_List[] = $Product;
    }
    $temp = array();
    $Final_Json = array();
    foreach ($Product_List as $product) 
    {
        $discount = $conn->query("SELECT * FROM " . $db_prefix . "product_discount WHERE product_id = '$product[ID]'");
        if($discount->num_rows >0  )
        {
            $temp['id'] = $product['ID'];
        $temp['name'] = $product['Title'];
        $image = HTTP_SERVER . "image/$product[Img]";
        $temp['image'] = $image;
        $temp['description'] = limit_string(strip_tags($product['Description']), 200);
        $temp['price'] = $product['Price'];
        $temp['Expire_In'] = ($product['Expire_In'] ? $product['Expire_In'] : "");
        $temp['visit_num'] = $product['viewed'];
        $temp['link_share'] = HTTP_SERVER . "index.php?route=product/product&product_id=" . $product['ID'];
        
        $c=mysqli_fetch_all($discount, MYSQLI_ASSOC);
        // $temp['Data']=$c;
        $temp['discount'];
        $i=0;
        foreach ($c as $key => $value) {
             $temp['discount'][$i]['percentage']=($product['Price']*$value['price'])/100 .'%';
             $temp['discount'][$i]['quantity']=$value['quantity'];
             $temp['discount'][$i]['priority']=$value['priority'];
             $temp['discount'][$i]['date_start']=$value['date_start'];
             $temp['discount'][$i]['date_end']=$value['date_end'];
             $i++;
        }
        // return $c;
         $Arr_Json = $temp;
         $Final_Json[] = $Arr_Json; 
        }
       
    }
     output($Final_Json);
    // return json_encode($Product_List); 
        }
        else
        {
           $userdata =  array("status" => array("code"=>2,"message"=>"error","error_details"=>array( "برجاء تسجيل دخولك")), "content" => array());
        return json_encode($userdata); 
        }

}
function product_search($conn,$db_prefix,$request)
{
     $Product_List = array();
        
    
        $sql = "SELECT " . $db_prefix . "product.product_id as 'ID',name as 'Title' 
            FROM " . $db_prefix . "product 
            INNER JOIN " . $db_prefix . "product_description ON " . $db_prefix . "product_description.product_id = " . $db_prefix . "product.product_id  
            ";
    if (!empty($request['product_name'])) {
        $sql .= " AND name LIKE '$request[product_name]%' ";
    }
         if (DB_DRIVER == 'mysqli') {
        $Products = $conn->query($sql);
        while ($Product = $Products->fetch_assoc())
            $Product_List[] = $Product;
    } else {
        $Products = mysql_query($sql);
        while ($Product = mysql_fetch_assoc($Products))
            $Product_List[] = $Product;
    }
    $products =  array("status" => array("code"=>200,"message"=>"success","error_details"=>array()), "content" => array("products"=>$Product_List));
        return json_encode($products);

}
function product_filters($conn,$db_prefix,$request)
{
    $Product_List = array();
        
    
    $sql = "SELECT " . $db_prefix . "product.product_id as 'ID', date_added, viewed, image as 'Img'," . $db_prefix . "product.quantity as 'Quantity',name as 'Title',price as 'Price',description as 'Description' 
        FROM " . $db_prefix . "product 
        INNER JOIN " . $db_prefix . "product_description ON " . $db_prefix . "product_description.product_id = " . $db_prefix . "product.product_id ";
    // if(isset($request['has_offer']) && $request['has_offer'] == 1)
    // {
    //     $sql .=" INNER JOIN " . $db_prefix . "product_discount ON " . $db_prefix . "product_discount.product_id = " . $db_prefix . "product.product_id ";
    // }
    // else if(isset($request['has_offer']) && $request['has_offer'] == 0)
    // {
    //     $sql .=" LEFT JOIN " . $db_prefix . "product_discount ON " . $db_prefix . "product_discount.product_id = " . $db_prefix . "product.product_id";
    // }
    if(isset($request['price']) && $request['price'] != "")
    {
        // return $request['price_operator'];
        $sql .="AND price  = '". $request['price']."'";
    } 
    if (DB_DRIVER == 'mysqli') {
        $Products = $conn->query($sql);
        while ($Product = $Products->fetch_assoc())
            $Product_List[] = $Product;
    } else {
        $Products = mysql_query($sql);
        while ($Product = mysql_fetch_assoc($Products))
            $Product_List[] = $Product;
    }
    $out = array();
    $temp = array();
    $Final_Json = array();
    foreach ($Product_List as $product) 
    {
        if(isset($request['rating']) && $request['rating']!= "")
        {
            $sql_rate = "SELECT avg(rating) as 'rate' FROM " . $db_prefix . "review WHERE product_id = '$product[ID]'";
        if (DB_DRIVER == 'mysqli') {
            $rate_avg = $conn->query($sql_rate);
            $rate = $rate_avg->fetch_assoc();
        } else {
            $rate_avg = mysql_query($sql_rate);
            $rate = mysql_fetch_assoc($rate_avg);
        }
        $temp['rate']=$rate['rate'];
        if($temp['rate'] == $request['rating'])
        {
           $temp['id'] = $product['ID'];
        $temp['name'] = $product['Title'];
        $image = HTTP_SERVER . "image/$product[Img]";
        $temp['image'] = $image;
        $temp['description'] = limit_string(strip_tags($product['Description']), 200);
        $temp['price'] = $product['Price'];
        $temp['Expire_In'] = ($product['Expire_In'] ? $product['Expire_In'] : "");
        $temp['visit_num'] = $product['viewed'];
        $temp['link_share'] = HTTP_SERVER . "index.php?route=product/product&product_id=" . $product['ID']; 
        }
        }
        else
        {
            $temp['id'] = $product['ID'];
        $temp['name'] = $product['Title'];
        $image = HTTP_SERVER . "image/$product[Img]";
        $temp['image'] = $image;
        $temp['description'] = limit_string(strip_tags($product['Description']), 200);
        $temp['price'] = $product['Price'];
        $temp['Expire_In'] = ($product['Expire_In'] ? $product['Expire_In'] : "");
        $temp['visit_num'] = $product['viewed'];
        $temp['link_share'] = HTTP_SERVER . "index.php?route=product/product&product_id=" . $product['ID'];
        }
        
         

    $Arr_Json = Json_CData($temp);
        $Final_Json[] = $Arr_Json;
    }
    output($Final_Json);
}
?>