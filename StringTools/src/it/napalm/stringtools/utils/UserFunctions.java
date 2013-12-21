package it.napalm.stringtools.utils;

import it.napalm.stringtools.object.TblBrands;
import it.napalm.stringtools.object.TblCurrencyUnit;
import it.napalm.stringtools.object.TblGripSize;
import it.napalm.stringtools.object.TblRacquets;
import it.napalm.stringtools.object.TblRacquetsPattern;
import it.napalm.stringtools.object.TblRacquetsUser;
import it.napalm.stringtools.object.TblUsers;
import it.napalm.stringtools.object.TblWeightUnit;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.Locale;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.util.Log;

public class UserFunctions {
	private JSONParser jsonParser;
    
     
    private static String login_tag = "login";
    private static String getuser_tag = "getuser";
    private static String editdatauser = "editdatauser";
    private static String getTblWeightUnit = "getweightunit";
    private static String getTblCurrencyUnit  = "getcurrencyunit";
    private static String getListCustomers  = "listcustomers";
    private static String newCustomer  = "newcustomer";
    private static String getListCustomerRacquet  = "racquetcustomer";
    private static String getListRacquet  = "racquets";
    private static String getBrands  = "listbrand";
    private static String getRacquetsPattern  = "racquetspattern";
    private static String getGripSize  = "listgripsize";

     
    // constructor
    public UserFunctions(){
        jsonParser = new JSONParser();
    }
     
    /**
     * function make Login Request
     * @param username
     * @param password
     * */
    public JSONObject loginUser(String url, String username, String password){
        // Building Parameters
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", login_tag));
        params.add(new BasicNameValuePair("username", username));
        params.add(new BasicNameValuePair("password", password));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        // return json
        // Log.e("JSON", json.toString());
        return json;
    }
    
    /**
     * function get user object
     * @param username
     * @param password
     * */
    public TblUsers getUser(String url, String username, String password, int idUser){
        // Building Parameters
    	TblUsers user = new TblUsers();
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", getuser_tag));
        //params.add(new BasicNameValuePair("username", username));
        //params.add(new BasicNameValuePair("password", password));
        params.add(new BasicNameValuePair("idUser", idUser+""));
        Log.e("getUser idUser", idUser+"");
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        try {
			JSONObject json_user = json.getJSONObject("user");
			user.setId(json_user.getInt("id"));
			//user.setTblTypeUser(json_user.getInt("type_user_id"));
			user.setUsername(json_user.getString("username"));
			user.setActive(json_user.getInt("active"));
			user.setConfirmCode(json_user.getString("confirm_code"));
			user.setName(json_user.getString("name"));
			user.setSurname(json_user.getString("surname"));
			user.setEmail(json_user.getString("email"));
			user.setTelephone(json_user.getString("telephone"));
			user.setMobileTelephone(json_user.getString("mobile_telephone"));
			user.setCost(json_user.getDouble("cost"));
			user.setTblWeightUnitId(json_user.getInt("tbl_weight_unit_id"));
			user.setTblCurrencyUnitId(json_user.getInt("tbl_currency_unit_id"));
			String dateStr = json_user.getString("date_insert");
			Date date = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault()).parse(dateStr);
			user.setDateInsert(date);
			user.setPiva(json_user.getString("piva"));
			user.setFax(json_user.getString("fax"));
		} catch (JSONException e) {

			e.printStackTrace();
			Log.e("getUser", e.getMessage());
		} catch (ParseException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			Log.e("getUser", e.getMessage());
		}
        return user;
    }
    
    /**
     * function make TblWeightUnit Request
     * @throws JSONException 
     * */
    public ArrayList<TblWeightUnit> getTblWeightUnit(String url) throws JSONException{
        // Building Parameters
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", getTblWeightUnit));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        JSONArray weightUnit = json.getJSONArray("weightUnit"); 
        
        ArrayList<TblWeightUnit> weightUnitList = new ArrayList<TblWeightUnit>();
        for (int i = 0; i < weightUnit.length(); i++) {
            JSONObject catObj = (JSONObject) weightUnit.get(i);
            TblWeightUnit cat = new TblWeightUnit(catObj.getInt("id"),
                    catObj.getString("description"));
            weightUnitList.add(cat);
        }
        return weightUnitList;
    }
    
    /**
     * function make TblCurrencyUnit Request
     * @throws JSONException 
     * */
    public ArrayList<TblCurrencyUnit> getTblCurrencyUnit(String url) throws JSONException{
        // Building Parameters
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", getTblCurrencyUnit));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        JSONArray currencyUnit = json.getJSONArray("currencyUnit"); 
        
        ArrayList<TblCurrencyUnit> currencyUnitList = new ArrayList<TblCurrencyUnit>();
        for (int i = 0; i < currencyUnit.length(); i++) {
            JSONObject catObj = (JSONObject) currencyUnit.get(i);
            TblCurrencyUnit cat = new TblCurrencyUnit(catObj.getInt("id"),
                    catObj.getString("description"));
            currencyUnitList.add(cat);
        }
        return currencyUnitList;
    }
     
    /**
     * function save data user
     * @param username
     * @param password
     * @throws JSONException 
     * */
    public String saveDataUser(String url, TblUsers user) throws JSONException{
        // Building Parameters
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", editdatauser));
        params.add(new BasicNameValuePair("id", user.getId().toString()));
        params.add(new BasicNameValuePair("name", user.getName().toString()));
        params.add(new BasicNameValuePair("surname", user.getSurname().toString()));
        params.add(new BasicNameValuePair("email", user.getEmail().toString()));
        params.add(new BasicNameValuePair("telephone", user.getTelephone().toString()));
        params.add(new BasicNameValuePair("mobile_telephone", user.getMobileTelephone().toString()));
        params.add(new BasicNameValuePair("cost",String.format( "%.2f", user.getCost() )));
        params.add(new BasicNameValuePair("tbl_weight_unit_id", user.getTblWeightUnitId()+""));
        params.add(new BasicNameValuePair("tbl_currency_unit_id", user.getTblCurrencyUnitId()+""));
        params.add(new BasicNameValuePair("piva", user.getPiva().toString()));
        params.add(new BasicNameValuePair("fax", user.getFax().toString()));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        return json.getString("result");
    }
    
    /**
     * function make TblWeightUnit Request
     * @throws JSONException 
     * @throws ParseException 
     * */
    public ArrayList<TblUsers> getListCustomers(String url, int id) throws JSONException, ParseException{
        // Building Parameters
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", getListCustomers));
        params.add(new BasicNameValuePair("idUser", id+""));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        JSONArray customers = json.getJSONArray("listCustomers"); 
        
        ArrayList<TblUsers> customersList = new ArrayList<TblUsers>();
        for (int i = 0; i < customers.length(); i++) {
            JSONObject catObj = (JSONObject) customers.get(i);
            TblUsers user = new TblUsers();
            user.setId(catObj.getInt("id"));
			//user.setTblTypeUser(json_user.getInt("type_user_id"));
			user.setUsername(catObj.getString("username"));
			user.setActive(catObj.getInt("active"));
			user.setConfirmCode(catObj.getString("confirm_code"));
			user.setName(catObj.getString("name"));
			user.setSurname(catObj.getString("surname"));
			user.setEmail(catObj.getString("email"));
			user.setTelephone(catObj.getString("telephone"));
			user.setMobileTelephone(catObj.getString("mobile_telephone"));
			user.setCost(catObj.getDouble("cost"));
			user.setTblWeightUnitId(catObj.getInt("tbl_weight_unit_id"));
			user.setTblCurrencyUnitId(catObj.getInt("tbl_currency_unit_id"));
			String dateStr = catObj.getString("date_insert");
			Date date = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault()).parse(dateStr);
			user.setDateInsert(date);
			user.setPiva(catObj.getString("piva"));
			user.setFax(catObj.getString("fax"));
            customersList.add(user);
        }
        return customersList;
    }
    
    
    /**
     * function save data customer
     * @param username
     * @param password
     * @throws JSONException 
     * */
    public String newCustomer(String url, TblUsers user, int idStringer) throws JSONException{
        // Building Parameters
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", newCustomer));
        params.add(new BasicNameValuePair("idStringer", idStringer + ""));
        params.add(new BasicNameValuePair("user_type", user.getTblTypeUser()+""));
        params.add(new BasicNameValuePair("username", user.getUsername().toString()));
        params.add(new BasicNameValuePair("password", user.getPassword().toString()));
        params.add(new BasicNameValuePair("active", user.getActive()+""));
        params.add(new BasicNameValuePair("confirm_code", user.getConfirmCode().toString()));        
        params.add(new BasicNameValuePair("name", user.getName().toString()));
        params.add(new BasicNameValuePair("surname", user.getSurname().toString()));
        params.add(new BasicNameValuePair("email", user.getEmail().toString()));
        params.add(new BasicNameValuePair("telephone", user.getTelephone().toString()));
        params.add(new BasicNameValuePair("mobile_telephone", user.getMobileTelephone().toString()));
        params.add(new BasicNameValuePair("cost",String.format( "%.2f", user.getCost() )));
        params.add(new BasicNameValuePair("tbl_weight_unit_id", user.getTblWeightUnitId()+""));
        params.add(new BasicNameValuePair("tbl_currency_unit_id", user.getTblCurrencyUnitId()+""));
        params.add(new BasicNameValuePair("piva", user.getPiva().toString()));
        params.add(new BasicNameValuePair("fax", user.getFax().toString()));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        return json.getString("result");
    }
    
    public ArrayList<TblRacquetsUser> getListCustomerRacquet(String url, int id) throws JSONException, ParseException{
        // Building for TblRacquetsUser
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", getListCustomerRacquet));
        params.add(new BasicNameValuePair("id", id+""));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        JSONArray firstList = json.getJSONArray("racquetcustomer"); 
        
        ArrayList<TblRacquetsUser> customerRacquetList = new ArrayList<TblRacquetsUser>();
        for (int i = 0; i < firstList.length(); i++) {
            JSONObject catObj = (JSONObject) firstList.get(i);
            TblRacquetsUser racquet = new TblRacquetsUser();
            racquet.setId(catObj.getInt("id"));
            racquet.setTblRacquets(catObj.getInt("tbl_racquets_id"));
            racquet.setTblUsers(catObj.getInt("tbl_users_id"));
            racquet.setTblGripSize(catObj.getInt("tbl_grip_size_id"));
            racquet.setSerial(catObj.getString("serial"));
            racquet.setWeightUnstrung(catObj.getDouble("weight_unstrung"));
            racquet.setWeightStrung(catObj.getDouble("weight_strung"));
			racquet.setBalance(catObj.getDouble("balance"));
			racquet.setSwingweight(catObj.getDouble("swingweight"));
			racquet.setStiffness(catObj.getDouble("stiffness"));
			String dateStr = catObj.getString("date_buy");
			Date date = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault()).parse(dateStr);
			racquet.setDateBuy(date);
			racquet.setNote(catObj.getString("note"));
			racquet.setActive(catObj.getInt("active"));
			customerRacquetList.add(racquet);
        }
        return customerRacquetList;
    }
    
    public ArrayList<TblRacquets> getListRacquet(String url, int id) throws JSONException, ParseException{
        // Building for TblRacquetsUser
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", getListRacquet));
        params.add(new BasicNameValuePair("id", id+""));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        JSONArray firstList = json.getJSONArray("racquets"); 
        
        ArrayList<TblRacquets> racquetList = new ArrayList<TblRacquets>();
        for (int i = 0; i < firstList.length(); i++) {
            JSONObject catObj = (JSONObject) firstList.get(i);
            TblRacquets racquet = new TblRacquets();
            racquet.setId(catObj.getInt("id"));
            racquet.setTblBrands(catObj.getInt("tbl_brands_id"));
            racquet.setTblRacquetsPattern(catObj.getInt("tbl_racquets_pattern_id"));
            racquet.setModel(catObj.getString("model"));
            racquet.setHeadSize(catObj.getDouble("head_size"));
            racquet.setLength(catObj.getDouble("length"));
            racquet.setWeightUnstrung(catObj.getDouble("weight_unstrung"));
            racquet.setWeightStrung(catObj.getDouble("weight_strung"));
			racquet.setBalance(catObj.getDouble("balance"));
			racquet.setSwingweight(catObj.getDouble("swingweight"));
			racquet.setStiffness(catObj.getDouble("stiffness"));
			racquet.setBeamWidth(catObj.getString("beam_width"));
			racquet.setNote(catObj.getString("note"));
			String dateStr = catObj.getString("date_modify");
			Date date = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault()).parse(dateStr);
			racquet.setDateModify(date);
			racquetList.add(racquet);
        }
        return racquetList;
    }
    
    public ArrayList<TblBrands> getBrands(String url, int id) throws JSONException, ParseException{
        // Building for TblRacquetsUser
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", getBrands));
        params.add(new BasicNameValuePair("id", id+""));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        JSONArray firstList = json.getJSONArray("listbrands"); 
        
        ArrayList<TblBrands> listInside = new ArrayList<TblBrands>();
        for (int i = 0; i < firstList.length(); i++) {
            JSONObject catObj = (JSONObject) firstList.get(i);
            TblBrands element = new TblBrands();
            element.setId(catObj.getInt("id"));
            element.setDescription(catObj.getString("description"));
			listInside.add(element);
        }
        return listInside;
    }
    
    public ArrayList<TblRacquetsPattern> getRacquetsPattern(String url, int id) throws JSONException, ParseException{
        // Building for TblRacquetsUser
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", getRacquetsPattern));
        params.add(new BasicNameValuePair("id", id+""));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        JSONArray firstList = json.getJSONArray("racquetspattern"); 
        
        ArrayList<TblRacquetsPattern> listInside = new ArrayList<TblRacquetsPattern>();
        for (int i = 0; i < firstList.length(); i++) {
            JSONObject catObj = (JSONObject) firstList.get(i);
            TblRacquetsPattern element = new TblRacquetsPattern();
            element.setId(catObj.getInt("id"));
            element.setDescription(catObj.getString("description"));
			listInside.add(element);
        }
        return listInside;
    }
    
    public ArrayList<TblGripSize> getGripSize(String url, int id) throws JSONException, ParseException{
        // Building for TblRacquetsUser
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", getGripSize));
        params.add(new BasicNameValuePair("id", id+""));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        JSONArray firstList = json.getJSONArray("listgripsize"); 
        
        ArrayList<TblGripSize> listInside = new ArrayList<TblGripSize>();
        for (int i = 0; i < firstList.length(); i++) {
            JSONObject catObj = (JSONObject) firstList.get(i);
            TblGripSize element = new TblGripSize();
            element.setId(catObj.getInt("id"));
            element.setEuropeSize(catObj.getString("europe_size"));
            element.setUsaSize(catObj.getString("usa_size"));
			listInside.add(element);
        }
        return listInside;
    }
}
