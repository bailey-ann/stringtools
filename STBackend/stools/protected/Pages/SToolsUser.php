<?php
/*
 * Created on 12/nov/2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 Prado::using('System.Security.TDbUserManager');
 
 class SToolsUser extends TDbUser
{
    /**
     * Creates a BlogUser object based on the specified username.
     * This method is required by TDbUser. It checks the database
     * to see if the specified username is there. If so, a BlogUser
     * object is created and initialized.
     * @param string the specified username
     * @return BlogUser the user object, null if username is invalid.
     */
    public function createUser($username)
    {
        // use UserRecord Active Record to look for the specified username
        $userRecord=TblUsers::finder()->findBy_username($username);
        
        if($userRecord instanceof TblUsers) // if found
        {
        	$user=new SToolsUser($this->Manager);
	        $user->Name=$userRecord->name;
	        $user->Surname=$userRecord->surname;
	        $user->Active =$userRecord->active;
	        $userRecord->currency_unit = TblCurrencyUnit::finder()->findBy_id($userRecord->tbl_currency_unit_id);
	        $userRecord->weight_unit = TblWeightUnit::finder()->findBy_id($userRecord->tbl_weight_unit_id);
	        $user->UserDB = $userRecord;
	        $user->IsGuest=false;   // the user is not a guest
	        return $user;           
        }
        else
            return null;
    }
 
    /**
     * Checks if the specified (username, password) is valid.
     * This method is required by TDbUser.
     * @param string username
     * @param string password
     * @return boolean whether the username and password are valid.
     */
    public function validateUser($username,$password)
    {
        // use UserRecord Active Record to look for the (username, password) pair.
        return TblUsers::finder()->findBy_username_AND_password($username,md5($password));
    }
    
    public function getSurname()
	{
		return $this->getState('Surname','');
	}

	public function setSurname($value)
	{
		$this->setState('Surname',$value,'');
	}
	
	public function getActive()
	{
		return $this->getState('Active','');
	}

	public function setActive($value)
	{
		$this->setState('Active',$value,'');
	}
		
	public function getUserDB()
	{
		return $this->getState('UserDB','');
	}

	public function setUserDB($value)
	{
		$this->setState('UserDB',$value,'');
	}
}
?>
