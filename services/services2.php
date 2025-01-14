<?php

  header('Access-Control-Allow-Origin: *');
  header("Access-Control-Allow-Credentials: true");
  header('Access-Control-Allow-Headers: X-Requested-With');
  header('Access-Control-Allow-Headers: Content-Type');
  header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT'); // http://stackoverflow.com/a/7605119/578667
// header('Access-Control-Max-Age: 86400');


// DB bağlantısı PHP ile kurulacak


  $host = 'mysql11.turhost.com';
  $user = 'cafeApp';
  $pass = 'Gokhan12356.';
  $data = 'app_db';
  $service_type = $_GET['service_type'];
  //echo $service_type;

  try {
      $pdo = new PDO('mysql:host='.$host.';dbname='.$data.';charset=utf8', $user, $pass);
     // print '</br> --- </br>'."Sunucuya bağlanıldı..".'</br> --- </br>';
  } catch (PDOException $e) {
      print "Error!: " . $e->getMessage();
  }

//-----------------------------------------------------------------
switch($service_type){
  case register_user:
    echo "registera girdi. DEneme".'</br> --- </br>';
    register_user($pdo);
    break;
  case login_user:
    //echo "logine girdik.".'</br> --- </br>';
    login_user($pdo);
    break;
  case forgot_password:
    echo "forgot_password girdi".'</br> --- </br>';
    forgat_password($pdo);
    break;
  case if_exist:
	  echo "if_exit girdi".'</br> --- </br>';
	  if_exist($pdo);
    break;
  case load_home:
    echo "load_home girdi".'</br> --- </br>';
    load_home($pdo);
    break;
  case buton_click:
    echo "buton_click girdi".'</br> --- </br>';
    buton_click($pdo);
    break;
  case get_branches:
    //echo "get_branches girdi".'</br> --- </br>';
    get_branches($pdo);
    break;
  case get_products:
    //echo "get_products girdi". </br> --- </br>;
    get_products($pdo);
    break;
  case contact;
    echo "contact girdi";
    contact($pdo);
    break;

  //-----KASA İŞLEMLERİ ----//

  case update_barcode:
    echo "update_barcode girdi".'</br> --- </br>';
    update_barcode($pdo);
    break;
  case depleted_products:
    echo "depleted_products girdi".'</br> --- </br>';
    depleted_products($pdo);
    break;
  default:
    echo "0";
}


function register_user($pdo){

  $name_user=$_GET['name_user'];
  $username = $_GET['username'];
  $password_user =$_GET['password_user'];
  $school=$_GET['school'];
  $email_address=$_GET['email_address'];
  $phone_number=$_GET['phone_number'];
  $company_id=$_GET['company_id'];
  //gokhanbirkin.net/services.php?service_type=register&name_user=batuhan&username=batuerdemir&password_user=1234&school=sabancı üniversitesi&email_address=batuerdemir@gmail.com&phone_number=564123651&company_id=1
  //id+1

  if( $pdo->exec('INSERT INTO user ( name_user, username,password_user,school,email_address,phone_number,company_id)
  VALUES ("'.$name_user.'","'.$username.'","'.$password_user.'","'.$school.'","'.$email_address.'","'.$phone_number.'","'.$company_id.'")')){
    echo "kayıt eklendi";
  }
}

//localStorage->
function login_user($pdo){

    $username = $_GET['username'];
    $password_user =$_GET['password_user'];

    $stmt = $pdo->prepare("SELECT name_user, username,school,email_address,phone_number,company_id
    from user where username=:username and password_user=:password_user");
    //Localstorage -> name_user, username,school,email_address,phone_number,company_id
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password_user', $password_user, PDO::PARAM_STR);
    $stmt->execute();

 		$gelenuser = $stmt->fetchAll(PDO::FETCH_ASSOC); //tüm gelenleri atıyor
		$json_data=json_encode($gelenuser,JSON_UNESCAPED_UNICODE); //json'a döüştürüyor
    if($gelenuser){
      // $jsonArray = json_decode($json_data,true);
      // return $jsonArray;
      print $json_data;

    }else{
      return false;
    }
}

//SMS MAİL <---
function forgot_password($pdo){
  $username = $_GET['username'];
  $stmt = $pdo->prepare("SELECT phone_number,email_address FROM user WHERE username=:username");
  $stmt->bindParam(':username', $username, PDO::PARAM_STR);
  $stmt->execute();
  $gelenuser = $stmt->fetchAll(PDO::FETCH_ASSOC); //tüm gelenleri atıyor
  $json_data=json_encode($gelenuser,JSON_UNESCAPED_UNICODE); //json'a döüştürüyor
  if($gelenuser){
    print $json_data;
  }else{
    echo "0";
  }
}

// function if_exist($pdo){
// 	$fieldName = $_GET['fieldName'];
// 	$value = $_GET['value'];
// 	$stmt = $pdo->prepare("SELECT user_id FROM user WHERE ".$fieldName."=:".$value);
// 	$stmt->bindParam(':'.$value, $value, PDO::PARAM_STR);
// 	$stmt->execute();
// 	$gelenuser = $stmt->fetchAll(PDO::FETCH_ASSOC); //tüm gelenleri atıyor
// 	$json_data=json_encode($gelenuser,JSON_UNESCAPED_UNICODE); //json'a döüştürüyor
// 	if($gelenuser){
//     print $json_data;
//   }else{
//     echo "0";
//   }
// }

//Veritabanından sorgu bekleniyor..
function load_home($pdo){
  $user_id = $_GET['user_id'];
  //$product_id = $_GET['product_id'];
  $stmt = $pdo->prepare('SELECT consumption.product_id, consumption.count FROM consumption WHERE consumption.user_id =:user_id');
  //SELECT(JOIN)(Product Name,Product ID,Product Image,Campaign Code ID, (campaign)Product ID)
  //Localstorage -> name_user, school
  $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
  //$stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
  $stmt->execute();

  $gelenuser = $stmt->fetchAll(PDO::FETCH_ASSOC); //tüm gelenleri atıyor
  $json_data=json_encode($gelenuser,JSON_UNESCAPED_UNICODE); //json'a döüştürüyor
  if($gelenuser){
    return $json_data;
  }else{
    echo "0";
  }
}

//front-end'den buton_id gelcek
function buton_click($pdo){
  //$buton_id =$_GET['buton_id'];

  $stmt = $pdo->prepare("SELECT campaign_id,campaign_code  FROM campaign WHERE product_id=1 AND validation !=0 LIMIT 1");
  // $stmt->bindParam(':buton_id', $buton_id, PDO::PARAM_STR);
  $stmt->execute();

  $gelendata = $stmt->fetchAll(PDO::FETCH_ASSOC); //tüm gelenleri atıyor
  $json_data=json_encode($gelendata,JSON_UNESCAPED_UNICODE); //json'a döüştürüyor
  print $gelendata[id];

  //$jsonArray = json_decode($json_data,true);
  //$key =  $jsonArray[0]['id'];


  if($gelendata){
    print $json_data;
  }else{
    echo "0";
  }
}

function contact($pdo){
  $branch_id = $_GET['branch_id'];

  $stmt = $pdo->prepare("SELECT branch.location, branch.phone_number, branch.image,company.name_company
  FROM company, branch
  WHERE branch.branch_id =:branch_id AND branch.company_id = company.company_id");
  $stmt->bindParam(':branch_id', $branch_id, PDO::PARAM_STR);
  $stmt->execute();
  $gelenData = $stmt->fetchAll(PDO::FETCH_ASSOC); //tüm gelenleri atıyor
	$json_data=json_encode($gelenData,JSON_UNESCAPED_UNICODE); //json'a döüştürüyor
	if($gelenData){
    print $json_data;
  }else{
    echo "0";
  }
}

function get_branches($pdo){
  //local->company_id
  $company_id = $_GET['company_id'];
  $stmt = $pdo->prepare("SELECT branch.branch_id, branch.location, branch.image  FROM branch WHERE branch.company_id =:company_id");
  $stmt->bindParam(':company_id', $company_id, PDO::PARAM_STR);
	$stmt->execute();
	$gelenuser = $stmt->fetchAll(PDO::FETCH_ASSOC); //tüm gelenleri atıyor
	$json_data=json_encode($gelenuser,JSON_UNESCAPED_UNICODE); //json'a döüştürüyor
	if($gelenuser){
    print $json_data;
  }else{
    echo "0";
  }
}

function get_products($pdo){
  $branch_id = $_GET['branch_id'];
  $stmt = $pdo->prepare("SELECT product.product_id, product.name_product, product.price, product.image, product.stock FROM product WHERE product.branch_id =:branch_id");
  $stmt->bindParam(':branch_id', $branch_id, PDO::PARAM_STR);
	$stmt->execute();
	$gelenProducts = $stmt->fetchAll(PDO::FETCH_ASSOC); //tüm gelenleri atıyor
	$json_data=json_encode($gelenProducts,JSON_UNESCAPED_UNICODE); //json'a döüştürüyor
	if($gelenProducts){
    print $json_data;
  }else{
    echo "0";
  }
}
//KASA İŞLEMLERİ
//-----------------------------------------------------------------------------------------
function if_exist_func($pdo,$value,$fieldName,$tableName){

	$stmt = $pdo->prepare("SELECT * FROM ".$tableName." WHERE ".$fieldName."=:".$value);
	$stmt->bindParam(':'.$value, $value, PDO::PARAM_STR);
	$stmt->execute();
	$gelenuser = $stmt->fetchAll(PDO::FETCH_ASSOC); //tüm gelenleri atıyor
  $json_data=json_encode($gelenuser,JSON_UNESCAPED_UNICODE); //json'a döüştürüyor
  $return_value = false;
	if($gelenuser){
    $return_value = true;
  }
  return $return_value;
}

function if_exist_func_two($pdo,$value,$fieldName,$tableName,$value2,$fieldName2){

	$stmt = $pdo->prepare("SELECT * FROM ".$tableName." WHERE ".$fieldName."=:".$value." and ".$fieldName2." = ".$value2);
	$stmt->bindParam(':'.$value, $value, PDO::PARAM_STR);
	$stmt->execute();
	$gelenuser = $stmt->fetchAll(PDO::FETCH_ASSOC); //tüm gelenleri atıyor
  $json_data=json_encode($gelenuser,JSON_UNESCAPED_UNICODE); //json'a döüştürüyor
  $return_value = false;
	if($gelenuser){
    $return_value = true;
  }
  return $return_value;
}

function update_barcode($pdo){
  $user_id = $_GET['user_id'];
  $product_id = $_GET['product_id'];
  $campaign_id = $_GET['campaign_id'];
  //$campaign_code = $_GET['campaign_code'];
  //qrcode_service->oluşturulan_barcode(user_id,product_id,campaign_id,campaign_code)
  //  $pdo->stmt('UPDATE campaign SET validation=0 WHERE campaign_id=:campaign_id ');
  $user_exist = if_exist_func($pdo,$user_id,"user_id","consumption");
  if($user_exist==false){
    echo "Kullanıcı yok".'</br> --- </br>';
    $stmt = $pdo->prepare('INSERT INTO consumption (user_id,product_id,count) VALUES ('.$user_id.','.$product_id.', 1)');
    $stmt->bindParam(':user_id',$user_id,PDO::PARAM_STR);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
    $stmt->execute();
    $stmt2 = $pdo->prepare('UPDATE  campaign SET campaign.validation=0, campaign.user_id =:user_id WHERE campaign.campaign_id=:campaign_id ');
    $stmt2->bindParam(':campaign_id',$campaign_id,PDO::PARAM_STR);
    $stmt2->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $stmt2->execute();
  }else{
    $product_exist = if_exist_func_two($pdo,$product_id,"product_id","consumption",$user_id,"user_id");
    // $stmt3 = $pdo-> prepare('SELECT campaign.validation FROM campaign WHERE  campaign_id =: campaign_id ');
    // $stmt3->bindParam(':campaign_id',$campaign_id,PDO::PARAM_STR);
    // $stmt3->execute();

    // $gelendata = $stmt3->fetchAll(PDO::FETCH_ASSOC); //tüm gelenleri atıyor
    // echo $gelendata;
    // json_encode($gelendata,JSON_UNESCAPED_UNICODE); //json'a döüştürüyor
    // print $gelendata[id];
    // echo '</br> --- </br>';
    if($product_exist==false){
      echo "Kullanıcı var ürün yok";
      $stmt = $pdo->prepare('INSERT INTO consumption (user_id,product_id,count) VALUES ('.$user_id.','.$product_id.', 1) ');
      $stmt->bindParam(':user_id',$user_id,PDO::PARAM_STR);
      $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
      $stmt->execute();

    }else{
        echo "Kullanıcı ve ürün var";
        $stmt = $pdo->prepare('UPDATE campaign, consumption SET campaign.validation=0, consumption.count=consumption.count+1,
        campaign.user_id =:user_id WHERE campaign.campaign_id=:campaign_id and consumption.user_id =:user_id and consumption.product_id=:product_id');
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindParam(':campaign_id', $campaign_id, PDO::PARAM_STR);
        // $stmt->bindParam(':campaign_code', $campaign_code, PDO::PARAM_STR);
        $stmt->execute();
        // UPDATE campaign, consumption SET campaign.validation=0, consumption.count=consumption.count+1,
        // campaign.user_id =1 WHERE campaign.campaign_id=7 and consumption.user_id =1 and consumption.product_id = 3
        $count = $stmt->rowCount();

        if($count =='0'){
            echo "Failed !";
        }
        else{
            echo "Success !";
        }
    }
  }


}

function depleted_products($pdo){
  $product_id=$_GET['product_id'];
  $user_id = $_GET['user_id'];
  $branch_id =$_GET['branch_id'];
  $company_id=$_GET['company_id'];

  $stmt = $pdo->prepare('INSERT INTO  depleted_products(product_id,user_id,branch_id,company_id)
  VALUES('.$product_id.','.$user_id.','.$branch_id.','.$company_id.')');
  $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
  $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
  $stmt->bindParam(':branch_id', $branch_id, PDO::PARAM_STR);
  $stmt->bindParam(':company_id', $company_id, PDO::PARAM_STR);
  $stmt->execute();
}

function qrcode_generator($pdo){

}

?>
