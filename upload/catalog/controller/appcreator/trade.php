<?php

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
    if (isset($request['Depts_ID'])) {
        $sql .= " AND " . $db_prefix . "product.product_id IN (SELECT product_id FROM " . $db_prefix . "product_to_category WHERE category_id = $request[Depts_ID]) ";
    }
    if (!empty($request['q'])) {
        $sql .= " AND name LIKE '%$request[q]%' ";
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
    foreach ($Product_List as $product) {
        $temp['ID'] = $product['ID'];
        $temp['Title'] = $product['Title'];
        $image = HTTP_SERVER . "image/$product[Img]";
        $temp['Pic'] = $image;
        $temp['Des'] = limit_string(strip_tags($product['Description']), 200);
        $temp['Price'] = $product['Price'];
        $temp['Expire_In'] = ($product['Expire_In'] ? $product['Expire_In'] : "");
        $temp['Num_Visit'] = $product['viewed'];
        $temp['Link_Share'] = HTTP_SERVER . "index.php?route=product/product&product_id=" . $product['ID'];
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

        $adv_data = disp_advanced_data();
        foreach ($adv_data as $k => $v) {
            $temp[$k] = $v;
        }
        $footerData_arr = array();
        $footer_data = disp_footer_data($footerData_arr);
        $setting_data = disp_setting_data("0", "0", null, null, $temp['Key'], $temp['Api'], null, $temp['Color'], $footer_data);
        foreach ($setting_data as $k => $v) {
            $temp[$k] = $v;
        }

        $Arr_Json = Json_CData($temp);
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
        $temp['ID'] = $product['ID'];
        $temp['Title'] = $product['Title'];
        $temp['Pic'] = ($product['Pic'] ? HTTP_SERVER . "/image/" . $product['Pic'] : "");
        $temp['Visit_Num'] = 0;
        $temp['Des'] = limit_string(strip_tags($product['Des']), 100);
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
        $temp['Link_Share'] = HTTP_SERVER . "/index.php?route=product/category&path=" . $product['ID'];
        $adv_data = disp_advanced_data();
        foreach ($adv_data as $k => $v) {
            $temp[$k] = $v;
        }
        $footerData_arr = array();
        $footer_data = disp_footer_data($footerData_arr);
        $setting_data = disp_setting_data("0", "0", null, null, $temp['Key'], $temp['Api'], null, null, $footer_data);
        foreach ($setting_data as $k => $v) {
            $temp[$k] = $v;
        }
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
    $temp['ID'] = $Product_List['ID'];
    $temp['User_ID'] = 1;
    $temp['User_Title'] = "Administrator";
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
    $temp['Des'] = limit_string($Product_List['Description'], 200);
    $temp['Link_Share'] = HTTP_SERVER . "index.php?route=product/category&path=" . $Product_List['ID'];
    $temp['Price'] = $Product_List['Price'];
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
    $adv_data = disp_advanced_data($dept, null, $shop);
    foreach ($adv_data as $k => $v) {
        $temp[$k] = $v;
    }
    $footerData_arr = array();
    $footer_data = disp_footer_data($footerData_arr);
    $setting_data = disp_setting_data("0", "0", null, null, $temp['Key'], $temp['Api'], null, $temp['Color'], $footer_data);
    foreach ($setting_data as $k => $v) {
        $temp[$k] = $v;
    }

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
        $temp['Pic'] = $image;
        $temp['Des'] = limit_string($product['Description'], 200);
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

        $adv_data = disp_advanced_data();
        foreach ($adv_data as $k => $v) {
            $temp[$k] = $v;
        }
        $footerData_arr = array();
        $footer_data = disp_footer_data($footerData_arr);
        $setting_data = disp_setting_data("0", "0", null, null, $temp['Key'], $temp['Api'], null, null, $footer_data);
        foreach ($setting_data as $k => $v) {
            $temp[$k] = $v;
        }

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

        $adv_data = disp_advanced_data();
        foreach ($adv_data as $k => $v) {
            $temp[$k] = $v;
        }
        $footerData_arr = array();
        $footer_data = disp_footer_data($footerData_arr);
        $setting_data = disp_setting_data("0", "0", null, null, $temp['Key'], $temp['Api'], null, null, $footer_data);
        foreach ($setting_data as $k => $v) {
            $temp[$k] = $v;
        }

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

        $adv_data = disp_advanced_data();
        foreach ($adv_data as $k => $v) {
            $temp[$k] = $v;
        }
        $footerData_arr = array();
        $footer_data = disp_footer_data($footerData_arr);
        $setting_data = disp_setting_data("0", "0", null, null, $temp['Key'], $temp['Api'], null, null, $footer_data);
        foreach ($setting_data as $k => $v) {
            $temp[$k] = $v;
        }

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

        $adv_data = disp_advanced_data(null, null, null, null, $user);
        foreach ($adv_data as $k => $v) {
            $temp[$k] = $v;
        }
        $footerData_arr = array();
        $footer_data = disp_footer_data($footerData_arr);
        $setting_data = disp_setting_data("0", "0", null, null, $temp['Key'], $temp['Api'], null, null, $footer_data);
        foreach ($setting_data as $k => $v) {
            $temp[$k] = $v;
        }
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
    if (isset($request['Email']) && !empty($request['Email']) && isset($request['Pass']) && !empty($request['Pass']))
     {
        $email = $request['Email'];
        $password = html_entity_decode($request['Pass'], ENT_QUOTES, "utf-8");
        $sql = "SELECT * FROM " . $db_prefix . "user WHERE email = '" . $email . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $password . "'))))) OR password = '" . md5($password) . "') AND status = '1'";
        $query = $conn->query($sql);
        if ($query->num_rows == 1) {
            $token = "";
            $user_pass = "";
            $img = "";
            $user_id = "";
            while ($res = $query->fetch_assoc()) {
                $user_id = $res['user_id'];
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
            $userdata = array("status" => array("code"=>202,"message"=>"No Content","error_details"=>array("no data found")), "content" => array());
            return json_encode($userdata);
        }

    } 
    else {
        $userdata = array("status" => array("code"=>2,"message"=>"Validation failed","error_details"=>array("تسجيل الدخول خاطئ")), "content" => array());
        return json_encode($userdata);
    }
}

function signup($conn, $db_prefix,$request) {
    if (isset($request['User']) && !empty($request['User']) && isset($request['Pass']) && !empty($request['Pass'])) {
        $userdata = array();
        $data = array();
        $data['first_name'] = isset($request['first_name']) ? $request['first_name'] : "";
        $data['last_name'] = isset($request['last_name']) ? $request['last_name'] : "";
        $check_user = check_user_exists($conn, $db_prefix, $request['User'], $request['e
            mail']);
        if ($check_user != "") {
            $userdata = array("status" => array("code"=>2,"message"=>"error","error_details"=>array("هذا المستخدم موجود بالفعل")), "content" => array());
            return json_encode($userdata);
            exit;
        }
        $sql = "INSERT INTO `" . $db_prefix . "user` SET username = '" . $request['User'] . "', user_group_id = '10', salt = '" . $salt = token(9) . "', password = '" . sha1($salt . sha1($salt . sha1($request['Pass']))) . "', firstname = '" . $data['first_name'] . "', lastname = '" . $data['last_name'] . "', email = '" . $request['Mail'] . "', status = '1', date_added = NOW()";
        if (DB_DRIVER == 'mysqli') {
            $conn->query($sql);
            $user_id = $conn->insert_id;
        } else {
            mysql_query($sql);
            $user_id = mysql_insert_id();
        }
        
       
        

    $userdata = array("status" => array("code"=>200,"message"=>"success","error_details"=>array()), "content" => array("user_id" => $user_id, "Img" => ""));
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
    if ($query_token->num_rows > 0) {
        // return $query_token->fetch_assoc()['user_id'];
    //     while ($row=mysqli_fetch_row($query_token))
    // {
    // printf ("%s (%s)\n",$row[0],$row[1]);die;
    // }
        // return mysql_insert_id();
        $user_id=$query_token->fetch_assoc()['user_id'];
        $query = $conn->query("SELECT * FROM ". $db_prefix ."user WHERE user_id='".$user_id."'");
        if ($query) {
            // return 'h';
            while ($res = $query->fetch_assoc()) {
                // return 'h';
                $arr['ID'] = (string) $user_id;
                $arr['Title'] = $res['firstname'] . " " . $res['lastname'];
                $arr['Pic'] = $res['image'];
                $arr['Key'] = "ID";
                $arr['Api'] = "&ID=" . $arr['ID'];

                $user = ["ID" => $arr['ID'], "Title" => $arr['Title'], "Img" => $res['image']];

                $adv_data = disp_advanced_data(null, null, null, null, $user);
                // return $adv_data;
                foreach ($adv_data as $k => $v) {
                    $arr[$k] = $v;
                }

                $footerData_arr = array();
                $footer_data = disp_footer_data($footerData_arr);

                $setting_data = disp_setting_data("0", "0", null, null, $arr['Key'], $arr['Api'], null, null, $footer_data);
                
                foreach ($setting_data as $k => $v) {
                    $arr[$k] = $v;
                }
                // return $arr['Others_Data'];
                $Arr_Json = $this->Json_CData($arr, $arr['Others_Data']);
                // return $Arr_Json;
                $this->output($Arr_Json);
            }
        }
    } else {
        $userdata =  array("status" => array("code"=>2,"message"=>"error","error_details"=>array( "برجاء تسجيل دخولك")), "content" => array());
        return json_encode($userdata);
    }
}

function Shopping_cart($conn, $db_prefix,$request) {
    $userData = $_SESSION['userData'];
    if (isset($userData['is_logged']) && $userData['is_logged'] == 1) {
        $sql = "SELECT * FROM {$db_prefix}cart";
        $query = $conn->query($sql);
        if ($query) {
            $temp = array();
            $Final_Json = array();
            while ($res = $query->fetch_assoc()) {
                $sql_product = $conn->query("SELECT " . $db_prefix . "product.product_id as 'ID', date_added, viewed, image as 'Img', quantity as 'Quantity',name as 'Title',price as 'Price',description as 'Description' FROM " . $db_prefix . "product INNER JOIN " . $db_prefix . "product_description ON " . $db_prefix . "product.product_id=" . $db_prefix . "product_description.product_id WHERE " . $db_prefix . "product.product_id = '{$res['product_id']}'");

                while ($product = $sql_product->fetch_assoc()) {
                    $temp['ID'] = $product['ID'];
                    $temp['Title'] = $product['Title'];
                    $image = HTTP_SERVER . "/image/$product[Img]";
                    $temp['Pic'] = $image;
                    $temp['Des'] = limit_string($product['Description'], 200);
                    $temp['Price'] = $product['Price'];
                    $temp['Expire_In'] = ($product['Expire_In'] ? $product['Expire_In'] : "");
                    $temp['Num_Visit'] = $product['viewed'];
                    $temp['Key'] = "ID";
                    $temp['Api'] = "&ID=" . $temp['ID'];
                    $temp['Product_Published'] = (string) strtotime($product['date_added']);
                    $temp['Link_Share'] = HTTP_SERVER . "index.php?route=product/product&product_id=" . $product['ID'];

                    $adv_data = disp_advanced_data();
                    foreach ($adv_data as $k => $v) {
                        $temp[$k] = $v;
                    }
                    $footerData_arr = array();
                    $footer_data = disp_footer_data($footerData_arr);
                    $setting_data = disp_setting_data("0", "0", null, null, $temp['Key'], $temp['Api'], null, null, $footer_data);
                    foreach ($setting_data as $k => $v) {
                        $temp[$k] = $v;
                    }

                    $Others_Data['Count'] = $res['quantity'];
                    $Others_Data['ProductID'] = $res['product_id'];
                    $Others_Data['Price'] = $temp['Price'];

                    $Arr_Json = Json_CData($temp, $Others_Data);
                    $Final_Json[] = $Arr_Json;
                }
            }
            output($Final_Json);
        }
    } else {
        $userdata =  array("status" => array("code"=>2,"message"=>"error","error_details"=>array( "برجاء تسجيل دخولك")), "content" => array());
        return json_encode($userdata);
    }
}

function Add_To_Shopping_Cart($conn, $db_prefix,$request) {
    $userData = $_SESSION['userData'];
    if (isset($userData['is_logged']) && $userData['is_logged'] == 1) {
        $Add = isset($_GET['Add']) && is_numeric($_GET['Add']) ? $_GET['Add'] : "";
        if (add_to_cart($conn, $db_prefix, $_GET['ID'], 1, array(), 0, $Add)) {
            $userdata = array("status" => array("code"=>200,"message"=>"success","error_details"=>array()),  "content" => array());
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
    $userData = $_SESSION['userData'];
    if (isset($userData['is_logged']) && $userData['is_logged'] == 1) {
        if (remove_from_cart($conn, $db_prefix, $_GET['ID'])) {
            $userdata = array("status" => array("code"=>200,"message"=>"success","error_details"=>array()),  "content" => array());
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

function check_user_exists($conn, $db_prefix, $username = '', $mail = '') {
    $where1 = "username = '" . $username . "'";
    $where2 = "email = '" . $mail . "'";
    $query = $conn->query("SELECT * FROM `" . $db_prefix . "user` WHERE $where1 OR $where2");
    if ($query->num_rows == 1) {
        return array("num_rows" => $query->num_rows);
    } else {
        return "";
    }
}

function add_to_cart($conn, $db_prefix, $product_id, $quantity = 1, $option = array(), $recurring_id = 0, $Add = "") {
    $query = $conn->query("SELECT COUNT(*) AS total FROM " . $db_prefix . "cart WHERE session_id = '" . session_id() . "' AND product_id = '" . (int) $product_id . "' AND recurring_id = '" . (int) $recurring_id . "' AND `option` = '" . json_encode($option) . "'");
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            if (!$row['total']) {
                $conn->query("INSERT " . $db_prefix . "cart SET session_id = '" . session_id() . "', product_id = '" . (int) $product_id . "', recurring_id = '" . (int) $recurring_id . "', `option` = '" . json_encode($option) . "', quantity = '" . (int) $quantity . "', date_added = NOW()");
            } else {
                if ($Add) {
                    $quantity = $Add;
                }
                $conn->query("UPDATE " . $db_prefix . "cart SET quantity = (quantity + " . (int) $quantity . ") WHERE session_id = '" . session_id() . "' AND product_id = '" . (int) $product_id . "' AND recurring_id = '" . (int) $recurring_id . "' AND `option` = '" . json_encode($option) . "'");
            }
        }
        return true;
    } else {
        return false;
    }
}

function remove_from_cart($conn, $db_prefix, $product_id) {
    $query = $conn->query("DELETE FROM " . $db_prefix . "cart WHERE session_id = '" . session_id() . "' AND product_id='" . (int) $product_id . "';");
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

function Json_Arr_Setting($Target_Action_ID, $Target_Layout_ID, $Havesub, $Api, $Key, $Dialog, $Color, $Footer) {
    $Setting = array();
    $Setting['Target_Action_ID'] = "$Target_Action_ID";
    $Setting['Target_Layout_ID'] = "$Target_Layout_ID";
    $Setting['Havesub'] = "$Havesub";
    $Setting['Key'] = "$Key";
    $Setting['Api'] = "$Api";
    $Setting['Dialog'] = "$Dialog";
    $Setting['Color'] = "$Color";
    $Setting['Footer'] = $Footer;

    return $Setting;
}

function Json_Basic_Data($ID, $Title, $Des, $Pic, $Link_Share, $DateTime, $Links, $ArrImg = array(), $ArrVideo = array(), $Value = 0) {

    $Arr['ID'] = "$ID";
    $Arr['Title'] = "$Title";
    $Arr['Des'] = "$Des";
    $Arr['Pic'] = "$Pic";
    $Arr['Key'] = "$Value";
    $Arr['Link_Share'] = "$Link_Share";
    $Arr['DateTime'] = "$DateTime";
    $Arr['Links'] = "$Links";
    if (count($ArrImg) == 0)
        $ArrImg = array();
    if (count($ArrVideo) == 0)
        $ArrVideo = array();
    $Arr['Images'] = $ArrImg;
    $Arr['Videos'] = $ArrVideo;
    return $Arr;
}

function Json_Others_Data($Others_Data) {
    return $Others_Data;
}

function Json_Advanced_Data($Dept = array(), $Source = array(), $Shop = array(), $Model = array(), $User = array(), $Content_Json = array(), $Author = array()) {
    $More_Data['Dept'] = $Dept;
    $More_Data['Source'] = $Source;
    $More_Data['Shop'] = $Shop;
    $More_Data['Model'] = $Model;
    $More_Data['User'] = $User;
    $More_Data['Author'] = $Author;
    $More_Data['Content_Json'] = $Content_Json;

    return $More_Data;
}

function Json_Stat_Data($Num_Visit, $Num_Comment) {
    $Stat = array();
    $Stat['Num_Visit'] = "$Num_Visit";
    $Stat['Comment_Num'] = "$Num_Comment";
    return $Stat;
}

function Json_Action_Creat($Arr_Basc_Data, $Arr_Advanced_Data = array(), $Arr_Setting_Data = array(), $Arr_Stat_Data = array(), $Arr_Others_Data = array(), $Key_Value = array()) {
    $Arr['Basc_Data'] = $Arr_Basc_Data;
    $Arr['Advanced_Data'] = $Arr_Advanced_Data;
    $Arr['Setting_Data'] = $Arr_Setting_Data;
    $Arr['Stat_Data'] = $Arr_Stat_Data;
    $Arr['Others_Data'] = $Arr_Others_Data;
    $Arr['Key_Value'] = $Key_Value;
    return $Arr;
}

function Json_CData($output, $Others_Data = array(), $Key_Value = array()) {
    $Arr_Basc_Data = Json_Basic_Data($output['ID'], $output['Title'], $output['Des'], $output['Pic'], $output['Link_Share'], $output['DateTime'], $output['Links'], $output['Images'], $output['Videos'], $output['Key']);
    $Arr_Setting_Data = Json_Arr_Setting($output['Target_Action_ID'], $output['Target_Layout_ID'], $output['Havesub'], $output['Api'], $output['Key'], $output['Dialog'], $output['Color'], $output['Footer']);
    $Arr_Stat_Data = Json_Stat_Data($output['Visit_Num'], $output['Comment_Num']);
    $Arr_Advanced_Data = Json_Advanced_Data($output['Dept'], $output['Source'], $output['Shop'], $output['Model'], $output['User'], $output['Content_Json'], $output['Author']);
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
            echo json_encode($value, 256);
            break;
        }
    } else {
        echo json_encode($result, 256);
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
         $query = $conn->query("UPDATE " . $db_prefix . "user SELECT viewed = viewed+1 WHERE product_id='$request[ID]'");
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

?>