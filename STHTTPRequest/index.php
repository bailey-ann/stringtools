<?php
/*
 * Created on 10/dic/2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 /**
 * File to handle all API requests
 * Accepts GET and POST
 * 
 * Each request will be identified by TAG
 * Response will be JSON data
 
  /**
 * check for POST request 
 */
if (isset($_POST['tag']) && $_POST['tag'] != '') {
    // get tag
    $tag = $_POST['tag'];
 
    // include db handler
    require_once 'include/DB_Functions.php';
    $db = new DB_Functions();
 
    // response Array
    $response = array("tag" => $tag, "success" => 0, "error" => 0);
 
    // check for tag type
    if ($tag == 'login') {
        // Request type is check Login
        $username = $_POST['username'];
        $password = $_POST['password'];
 
        // check for user
        $user = $db->loginByUserAndPassword($username, $password);
        if ($user != false) {
            // user found
            // echo json with success = 1
            $response["success"] = 1;
            $response["id"] = $user["id"];
			$response["user"]["id"] = $user["id"];
            $response["user"]["type_user_id"] = $user["type_user_id"];
            $response["user"]["username"] = $user["username"];
            $response["user"]["active"] = $user["active"];
            $response["user"]["confirm_code"] = $user["confirm_code"];
            $response["user"]["name"] = $user["name"];
            $response["user"]["surname"] = $user["surname"];
            $response["user"]["email"] = $user["email"];
            $response["user"]["telephone"] = $user["telephone"];
            $response["user"]["mobile_telephone"] = $user["mobile_telephone"];
            $response["user"]["cost"] = $user["cost"];
            $response["user"]["date_insert"] = $user["date_insert"];
            $response["user"]["tbl_currency_unit_id"] = $user["tbl_currency_unit_id"];
            $response["user"]["tbl_weight_unit_id"] = $user["tbl_weight_unit_id"];
            $response["user"]["piva"] = $user["piva"];
			$response["user"]["fax"] = $user["fax"];
            if($user["active"] == 0){
            	$response["error"] = 1;
            	$response["error_msg"] = "Utente non attivo!";
            	echo json_encode($response);
            }else
           		echo json_encode($response);
        } else {
            // user not found
            // echo json with error = 1
            $response["error"] = 1;
            $response["error_msg"] = "Incorrect username or password!";
            echo json_encode($response);
        }
    } else if ($tag == 'getuser') { 
		//$username = $_POST['username']; // DA INSERIRE
        //$password = $_POST['password']; // DA INSERIRE
		$idUser = $_POST['idUser'];
		
		// check for user
        $user = $db->getUserById($idUser);
		if ($user != false) {
            // user found
            // echo json with success = 1
            $response["success"] = 1;
            $response["id"] = $user["id"];
			$response["user"]["id"] = $user["id"];
            $response["user"]["type_user_id"] = $user["type_user_id"];
            $response["user"]["username"] = $user["username"];
            $response["user"]["active"] = $user["active"];
            $response["user"]["confirm_code"] = $user["confirm_code"];
            $response["user"]["name"] = $user["name"];
            $response["user"]["surname"] = $user["surname"];
            $response["user"]["email"] = $user["email"];
            $response["user"]["telephone"] = $user["telephone"];
            $response["user"]["mobile_telephone"] = $user["mobile_telephone"];
            $response["user"]["cost"] = $user["cost"];
            $response["user"]["date_insert"] = $user["date_insert"];
            $response["user"]["tbl_currency_unit_id"] = $user["tbl_currency_unit_id"];
            $response["user"]["tbl_weight_unit_id"] = $user["tbl_weight_unit_id"];
            $response["user"]["piva"] = $user["piva"];
			$response["user"]["fax"] = $user["fax"];
            echo json_encode($response);
        } else {
            // user not found
            // echo json with error = 1
            $response["error"] = 1;
            $response["error_msg"] = "Incorrect username or password!";
            echo json_encode($response);
        }
	
	} else if ($tag == 'getweightunit') { 
		//$username = $_POST['username']; // DA INSERIRE
        //$password = $_POST['password']; // DA INSERIRE
	
        $response = $db->getWeightUnit();
		echo json_encode($response);
	} else if ($tag == 'getcurrencyunit') { 
		//$username = $_POST['username']; // DA INSERIRE
        //$password = $_POST['password']; // DA INSERIRE
	
        $response = $db->getCurrencyUnit();
		echo json_encode($response);
	} else if ($tag == 'editdatauser') {
        //$username = $_POST['username']; // DA INSERIRE
        //$password = $_POST['password']; // DA INSERIRE
		$result = $db->updateDataUser($_POST['id'], $_POST['name'],$_POST['surname'],$_POST['email'],$_POST['telephone'],$_POST['mobile_telephone'],$_POST['cost'],$_POST['tbl_weight_unit_id'],$_POST['tbl_currency_unit_id'],$_POST['piva'],$_POST['fax']);
        $response["result"] = $result."";
    	echo json_encode($response);
    } else if ($tag == 'listcustomers') {
        //$username = $_POST['username']; // DA INSERIRE
        //$password = $_POST['password']; // DA INSERIRE
		$idUser = $_POST['idUser'];
		$response = $db->getListCustomers($idUser);
		echo json_encode($response);
    }else if ($tag == 'newcustomer') {
        //$username = $_POST['username']; // DA INSERIRE
        //$password = $_POST['password']; // DA INSERIRE
		$result = $db->newCustomer($_POST);
		$response["result"] = $result."";
		echo json_encode($response);
    }else if ($tag == 'listbrand') {
		$response = $db->getListBrands($_POST['id']);
		echo json_encode($response);
    }else if ($tag == 'listgauges') {
		$response = $db->getListGauges();
		echo json_encode($response);
    }else if ($tag == 'listgripsize') {
		$response = $db->getListGripSize($_POST['id']);
		echo json_encode($response);
    }else if ($tag == 'stringingmachinetype') {
		$response = $db->getStringingMachineType();
		echo json_encode($response);
    }else if ($tag == 'stringingjobtype') {
		$response = $db->getStringingJobType();
		echo json_encode($response);
    }else if ($tag == 'stringtype') {
		$response = $db->getStringType();
		echo json_encode($response);
    }else if ($tag == 'racquetspattern') {
		$response = $db->getRacquetsPattern($_POST['id']);
		echo json_encode($response);
    }else if ($tag == 'strings') {
		$response = $db->getStrings($_POST['id']);
		echo json_encode($response);
    }else if ($tag == 'stringingmachines') {
		$response = $db->getStringingMachines($_POST['id']);
		echo json_encode($response);
    }else if ($tag == 'overgrips') {
		$response = $db->getOvergrips($_POST['id']);
		echo json_encode($response);
    }else if ($tag == 'grips') {
		$response = $db->getGrips($_POST['id']);
		echo json_encode($response);
    }else if ($tag == 'racquetcustomer') {
		$response = $db->getRacquetCustomer($_POST['id']);
		echo json_encode($response);
    }else if ($tag == 'racquets') {
		$response = $db->getRacquets($_POST['id']);
		echo json_encode($response);
    }else if ($tag == 'racquetstext') {
		$response = $db->getRacquetsText();
		echo json_encode($response);
    }else if ($tag == 'editracquetcustomer') {
		$result = $db->updateRacquetCustomer($_POST);
        $response["result"] = $result."";
    	echo json_encode($response);
    }else if ($tag == 'addracquetcustomer') {
		$result = $db->addRacquetCustomer($_POST);
        $response["result"] = $result."";
    	echo json_encode($response);
    }else if ($tag == 'removeracquetcustomer') {
		$result = $db->removeRacquetCustomer($_POST);
        $response["result"] = $result."";
    	echo json_encode($response);
    }else if ($tag == 'editdatabrand') {
		$result = $db->editDataBrand($_POST);
		$response["result"] = $result."";
		echo json_encode($response);
    }else if ($tag == 'newbrand') {
		$result = $db->newBrand($_POST);
		$response["result"] = $result."";
		echo json_encode($response);
    }else if ($tag == 'gripstext') {
		$response = $db->getGripsText();
		echo json_encode($response);
    }else if ($tag == 'editdatagrip') {
		$result = $db->editDataGrip($_POST);
		$response["result"] = $result."";
		echo json_encode($response);
    }else if ($tag == 'newbrand') {
		$result = $db->newGrip($_POST);
		$response["result"] = $result."";
		echo json_encode($response);
    }else {
        echo "Invalid Request";
    }
} else {
    echo "Access Denied";
}
?>
