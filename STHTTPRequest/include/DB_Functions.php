<?php
/*
 * Created on 10/dic/2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class DB_Functions {
 
    private $db;
 
    //put your code here
    // constructor
    function __construct() {
        require_once 'DB_Connect.php';
        // connecting to database
        $this->db = new DB_Connect();
        $this->db->connect();
    }
 
    // destructor
    function __destruct() {
         
    }
 
    /**
     * Storing new user
     * returns user details
     */
    public function storeUser($name, $email, $password) {
        $uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
        $result = mysql_query("INSERT INTO users(unique_id, name, email, encrypted_password, salt, created_at) VALUES('$uuid', '$name', '$email', '$encrypted_password', '$salt', NOW())");
        // check for successful store
        if ($result) {
            // get user details 
            $uid = mysql_insert_id(); // last inserted id
            $result = mysql_query("SELECT * FROM users WHERE uid = $uid");
            // return user details
            return mysql_fetch_array($result);
        } else {
            return false;
        }
    }
 
    /**
     * Get user by email and password
     */
    public function getUserByEmailAndPassword($email, $password) {
        $result = mysql_query("SELECT * FROM users WHERE email = '$email'") or die(mysql_error());
        // check for result 
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysql_fetch_array($result);
            $salt = $result['salt'];
            $encrypted_password = $result['encrypted_password'];
            $hash = $this->checkhashSSHA($salt, $password);
            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                return $result;
            }
        } else {
            // user not found
            return false;
        }
    }
    
    /**
     * Get user by email and password
     */
    public function loginByUserAndPassword($username, $password) {
        $result = mysql_query("select * from tbl_users where username = '".$username."' and password = '".md5($password)."'") or die(mysql_error());
        // check for result 
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysql_fetch_array($result);
            return $result;
        } else {
            // user not found
            return false;
        }
    }
	
	public function getUserById($id) {
        
		$result = mysql_query("select * from tbl_users where id = " . $id) or die(mysql_error());
        // check for result 
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysql_fetch_array($result);
            return $result;
        } else {
            // user not found
            return false;
        }
    }
	
	public function getWeightUnit() {
        $response = array();
		$response["weightUnit"] = array();
     
		$result = mysql_query("select * from tbl_weight_unit") or die(mysql_error());

		while($row = mysql_fetch_array($result)){
			$tmp = array();
			$tmp["id"] = $row["id"];
			$tmp["description"] = $row["description"];
			array_push($response["weightUnit"], $tmp);
		}
		
        return $response;
    }
	
	public function getCurrencyUnit() {
        $response = array();
		$response["currencyUnit"] = array();
     
		$result = mysql_query("select * from tbl_currency_unit") or die(mysql_error());

		while($row = mysql_fetch_array($result)){
			$tmp = array();
			$tmp["id"] = $row["id"];
			$tmp["description"] = $row["description"];
			array_push($response["currencyUnit"], $tmp);
		}
		
        return $response;
    }
	
	public function updateDataUser($id, $name, $surname, $email, $telephone, $mobile_telephone, $cost, $tbl_weight_unit_id, $tbl_currency_unit_id, $piva, $fax){
		$query = "update tbl_users set name = '".$name."', surname = '".$surname."', email = '".$email."', telephone = '".$telephone."',  mobile_telephone = '".$mobile_telephone."', cost = '".$cost."', tbl_weight_unit_id = '".$tbl_weight_unit_id."', tbl_currency_unit_id = '".$tbl_currency_unit_id."', piva = '".$piva."', fax = '".$fax."', date_insert = now() where id = " . $id;
		$result = mysql_query($query) or die(mysql_error());
		return $result;
	}
	
	public function getListCustomers($id) {
        $response = array();
		$response["listCustomers"] = array();
     
		$result = mysql_query("select * from tbl_users INNER JOIN rel_stringer_customer ON rel_stringer_customer.id_customer = tbl_users.id where rel_stringer_customer.id_stringer = " .$id . " order by tbl_users.surname, tbl_users.name") or die(mysql_error());

		while($row = mysql_fetch_array($result)){
			$tmp = array();
			
			$tmp["id"] = $row["id"];
            $tmp["type_user_id"] = $row["type_user_id"];
            $tmp["username"] = $row["username"];
            $tmp["active"] = $row["active"];
            $tmp["confirm_code"] = $row["confirm_code"];
            $tmp["name"] = $row["name"];
            $tmp["surname"] = $row["surname"];
            $tmp["email"] = $row["email"];
            $tmp["telephone"] = $row["telephone"];
            $tmp["mobile_telephone"] = $row["mobile_telephone"];
            $tmp["cost"] = $row["cost"];
            $tmp["date_insert"] = $row["date_insert"];
            $tmp["tbl_currency_unit_id"] = $row["tbl_currency_unit_id"];
            $tmp["tbl_weight_unit_id"] = $row["tbl_weight_unit_id"];
            $tmp["piva"] = $row["piva"];
			$tmp["fax"] = $row["fax"];
			array_push($response["listCustomers"], $tmp);
		}
		
        return $response;
    }
    
    public function insertUser($value){
    	$query = "insert into tbl_users (id, type_user_id, username, password, active, confirm_code, name, surname, email, telephone, mobile_telephone, fax, cost, date_insert, tbl_currency_unit_id, tbl_weight_unit_id, piva) " .
    			"VALUES (null, ".$value["user_type"].",'".$value["username"]."','".$value["password"]."','".$value["active"]."','".$value["confirm_code"]."'," .
    					"'".$value["name"]."','".$value["surname"]."','".$value["email"]."','".$value["telephone"]."','".$value["mobile_telephone"]."','".$value["fax"]."','".$value["cost"]."', now(),'".$value["tbl_currency_unit_id"]."','".$value["tbl_weight_unit_id"]."','".$value["piva"]."')";
    	$result = mysql_query($query) or die(mysql_error());
		return $result;				
    					
    }
    
    public function newCustomer($value){
    	$retunValue = $this->insertUser($value);
    	if($retunValue){
    		$idNewCustomer = mysql_insert_id();
    		$query = "insert into rel_stringer_customer (id_stringer, id_customer) values(".$value["idStringer"].",".$idNewCustomer.")";
    		$result = mysql_query($query) or die(mysql_error());
			return $result;	
    	}else{
    		return $retunValue;
    	}
    }
    
    public function newUser($value){
    	$retunValue = $this->insertUser($value);
    	return $retunValue;
    }
    
    public function getListBrands($id) {
        $response = array();
		$response["listbrands"] = array();
		
		$query = "select * from tbl_brands order by description";
		if($id != 0)
			$query = "select * from tbl_brands  where id = " . $id;

		$result = mysql_query($query) or die(mysql_error());

		while($row = mysql_fetch_array($result)){
			$tmp = array();
			
			$tmp["id"] = $row["id"];
            $tmp["description"] = $row["description"];
			array_push($response["listbrands"], $tmp);
		}
		
        return $response;
    }
    
     public function getListGauges() {
        $response = array();
		$response["listgauges"] = array();
     
		$result = mysql_query("select * from FROM tbl_gauges order by id") or die(mysql_error());

		while($row = mysql_fetch_array($result)){
			$tmp = array();
			
			$tmp["id"] = $row["id"];
            $tmp["usa"] = $row["usa"];
            $tmp["diameter"] = $row["diameter"];
			array_push($response["listgauges"], $tmp);
		}
		
        return $response;
    }
    
    public function getListGripSize($id) {
        $response = array();
		$response["listgripsize"] = array();
		
		$query = "select * from tbl_grip_size order by id";
		if($id != 0)
			$query = "select * from tbl_grip_size  where id = " . $id;

		$result = mysql_query($query) or die(mysql_error());
     
		

		while($row = mysql_fetch_array($result)){
			$tmp = array();
			
			$tmp["id"] = $row["id"];
            $tmp["europe_size"] = $row["europe_size"];
            $tmp["usa_size"] = $row["usa_size"];
			array_push($response["listgripsize"], $tmp);
		}
		
        return $response;
    }
    
    public function getStringingMachineType() {
        $response = array();
		$response["stringingmachinetype"] = array();
     
		$result = mysql_query("select * from tbl_stringing_machine_type order by description") or die(mysql_error());

		while($row = mysql_fetch_array($result)){
			$tmp = array();
			$tmp["id"] = $row["id"];
			$tmp["description"] = $row["description"];
			array_push($response["stringingmachinetype"], $tmp);
		}
		
        return $response;
    }
    
     public function getStringingJobType() {
        $response = array();
		$response["stringingjobtype"] = array();
     
		$result = mysql_query("select * from tbl_stringing_job_type order by description") or die(mysql_error());

		while($row = mysql_fetch_array($result)){
			$tmp = array();
			$tmp["id"] = $row["id"];
			$tmp["description"] = $row["description"];
			array_push($response["stringingjobtype"], $tmp);
		}
		
        return $response;
    }
    
    public function getStringType() {
        $response = array();
		$response["stringtype"] = array();
     
		$result = mysql_query("select * from tbl_string_type order by description") or die(mysql_error());

		while($row = mysql_fetch_array($result)){
			$tmp = array();
			$tmp["id"] = $row["id"];
			$tmp["description"] = $row["description"];
			array_push($response["stringtype"], $tmp);
		}
		
        return $response;
    }
    
    public function getRacquetsPattern($id) {
        $response = array();
		$response["racquetspattern"] = array();
		
		$query = "select * from tbl_racquets_pattern order by description";
		if($id != 0)
			$query = "select * from tbl_racquets_pattern  where id = " . $id;

		$result = mysql_query($query) or die(mysql_error());
		
		while($row = mysql_fetch_array($result)){
			$tmp = array();
			$tmp["id"] = $row["id"];
			$tmp["description"] = $row["description"];
			array_push($response["racquetspattern"], $tmp);
		}
		
        return $response;
    }
    
    public function getStrings() {
        $response = array();
		$response["strings"] = array();
     
		$result = mysql_query("select * from tbl_strings order by id") or die(mysql_error());

		while($row = mysql_fetch_array($result)){
			$tmp = array();
			$tmp["id"] = $row["id"];
			$tmp["tbl_brands_id"] = $row["tbl_brands_id"];
			$tmp["tbl_gauges_id"] = $row["tbl_gauges_id"];
			$tmp["tbl_string_type_id"] = $row["tbl_string_type_id"];
			$tmp["model"] = $row["model"];
			$tmp["code"] = $row["code"];
			$tmp["exact_gauge"] = $row["exact_gauge"];
			array_push($response["strings"], $tmp);
		}
		
        return $response;
    }
    
    public function getStringingMachines() {
        $response = array();
		$response["stringingmachines"] = array();
     
		$result = mysql_query("select * from tbl_stringing_machines order by id") or die(mysql_error());

		while($row = mysql_fetch_array($result)){
			$tmp = array();
			$tmp["id"] = $row["id"];
			$tmp["tbl_brands_id"] = $row["tbl_brands_id"];
			$tmp["tbl_stringing_machine_type_id"] = $row["tbl_stringing_machine_type_id"];
			$tmp["model"] = $row["model"];
			array_push($response["stringingmachines"], $tmp);
		}
		
        return $response;
    }
    
    public function getOvergrips() {
        $response = array();
		$response["overgrips"] = array();
     
		$result = mysql_query("select * from tbl_overgrips order by id") or die(mysql_error());

		while($row = mysql_fetch_array($result)){
			$tmp = array();
			$tmp["id"] = $row["id"];
			$tmp["tbl_brands_id"] = $row["tbl_brands_id"];
			$tmp["tbl_gauges_id"] = $row["tbl_gauges_id"];
			$tmp["model"] = $row["model"];
			$tmp["price"] = $row["price"];
			$tmp["note"] = $row["note"];
			array_push($response["overgrips"], $tmp);
		}
		
        return $response;
    }
    
    public function getGrips() {
        $response = array();
		$response["grips"] = array();
     
		$result = mysql_query("select * from tbl_grips order by id") or die(mysql_error());

		while($row = mysql_fetch_array($result)){
			$tmp = array();
			$tmp["id"] = $row["id"];
			$tmp["tbl_brands_id"] = $row["tbl_brands_id"];
			$tmp["tbl_gauges_id"] = $row["tbl_gauges_id"];
			$tmp["model"] = $row["model"];
			$tmp["price"] = $row["price"];
			$tmp["note"] = $row["note"];
			array_push($response["grips"], $tmp);
		}
		
        return $response;
    }
    
    public function getRacquetCustomer($id) {
        $response = array();
		$response["racquetcustomer"] = array();
     
		$result = mysql_query("select * from tbl_racquets_user where tbl_users_id = " . $id . " and active = 1") or die(mysql_error());

		while($row = mysql_fetch_array($result)){
			$tmp = array();
			$tmp["id"] = $row["id"];
			$tmp["tbl_racquets_id"] = $row["tbl_racquets_id"];
			$tmp["tbl_users_id"] = $row["tbl_users_id"];
			$tmp["tbl_grip_size_id"] = $row["tbl_grip_size_id"];
			$tmp["serial"] = $row["serial"];
			$tmp["weight_unstrung"] = $row["weight_unstrung"];
			$tmp["weight_strung"] = $row["weight_strung"];
			$tmp["balance"] = $row["balance"];
			$tmp["swingweight"] = $row["swingweight"];
			$tmp["stiffness"] = $row["stiffness"];
			$tmp["date_buy"] = $row["date_buy"];
			$tmp["note"] = $row["note"];
			$tmp["active"] = $row["active"];
			array_push($response["racquetcustomer"], $tmp);
		}
		
        return $response;
    }
    
    public function getRacquets($id) {
        $response = array();
		$response["racquets"] = array();
		
		$query = "select * from tbl_racquets";
		if($id != 0)
			$query = "select * from tbl_racquets where id = " . $id;

		$result = mysql_query($query) or die(mysql_error());

		while($row = mysql_fetch_array($result)){
			$tmp = array();
			$tmp["id"] = $row["id"];
			$tmp["tbl_brands_id"] = $row["tbl_brands_id"];
			$tmp["tbl_racquets_pattern_id"] = $row["tbl_racquets_pattern_id"];
			$tmp["model"] = $row["model"];
			$tmp["head_size"] = $row["head_size"];
			$tmp["length"] = $row["length"];
			$tmp["weight_unstrung"] = $row["weight_unstrung"];
			$tmp["weight_strung"] = $row["weight_strung"];
			$tmp["balance"] = $row["balance"];
			$tmp["swingweight"] = $row["swingweight"];
			$tmp["stiffness"] = $row["stiffness"];
			$tmp["beam_width"] = $row["beam_width"];
			$tmp["note"] = $row["note"];
			$tmp["date_modify"] = $row["date_modify"];
			array_push($response["racquets"], $tmp);
		}
		
        return $response;
    }
    
    public function addRacquetCustomer($value){
    	$query = "insert into tbl_racquets_user (id, tbl_racquets_id, tbl_users_id, tbl_grip_size_id, serial, weight_unstrung, weight_strung, balance, swingweight, stiffness, date_buy, note, active) " .
    			"VALUES (null, ".$value["tbl_racquets_id"].",'".$value["tbl_users_id"]."','".$value["tbl_grip_size_id"]."','".$value["serial"]."','".$value["weight_unstrung"]."'," .
    					"'".$value["weight_strung"]."','".$value["balance"]."','".$value["swingweight"]."','".$value["stiffness"]."','".$value["date_buy"]."','".$value["note"]."', 1)";
    	$result = mysql_query($query) or die(mysql_error());
    	if($result){
    		$idRacquetCustomer = mysql_insert_id();
    		$query = "insert into log_racquet_user (id, id_tbl_racquet_user, weight_unstrung, weight_strung, balance, swingweight, stiffness, date_modify, note) values " .
    				"(null,".$idRacquetCustomer.", '".$value["weight_unstrung"]."', '".$value["weight_strung"]."', '".$value["balance"]."', '".$value["swingweight"]."', '".$value["stiffness"]."', '".$value["date_modify"]."', '".$value["note"]."')";
    		$result = mysql_query($query) or die(mysql_error());
			return $result;	
    	}else 
    		return $result;		
    					
    }
    
    public function updateRacquetCustomer($value){
    	$query = "update tbl_racquets_user set tbl_grip_size_id = ".$value["tbl_grip_size_id"].", " .
    			" serial = '".$value["serial"]."', " .
    			"weight_unstrung = '".$value["weight_unstrung"]."', " .
    			"weight_strung = '".$value["weight_strung"]."', " .
    			"balance = '".$value["balance"]."', " .
    			"swingweight = '".$value["swingweight"]."', " .
    			"stiffness = '".$value["stiffness"]."', " .
    			"date_buy = '".$value["date_buy"]."', " .
    			"note = '".$value["note"]."', " .
    			"active = '".$value["active"]."' " .
    			" where id = ". $value["id"];
    	$result = mysql_query($query) or die(mysql_error());
    	if($result){
    		$query = "insert into log_racquet_user (id, id_tbl_racquet_user, weight_unstrung, weight_strung, balance, swingweight, stiffness, date_modify, note) values " .
    				"(null,".$value["id"].", '".$value["weight_unstrung"]."', '".$value["weight_strung"]."', '".$value["balance"]."', '".$value["swingweight"]."', '".$value["stiffness"]."', now(), '".$value["note"]."')";
    		$result = mysql_query($query) or die(mysql_error());
			return $result;	
    	}else 
    		return $result;		
    					
    }
    
    public function removeRacquetCustomer($value){
    	$query = "update tbl_racquets_user set active = 0 " .
    			" where id = ". $value["id"];
    	$result = mysql_query($query) or die(mysql_error());
    	return $result;   		
    }

}
?>
