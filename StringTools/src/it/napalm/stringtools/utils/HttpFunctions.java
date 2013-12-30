package it.napalm.stringtools.utils;

import it.napalm.stringtools.globalobject.GripText;
import it.napalm.stringtools.globalobject.OvergripText;
import it.napalm.stringtools.globalobject.RacquetText;
import it.napalm.stringtools.globalobject.StringText;
import it.napalm.stringtools.object.TblBrands;
import it.napalm.stringtools.object.TblCurrencyUnit;
import it.napalm.stringtools.object.TblGauges;
import it.napalm.stringtools.object.TblGripSize;
import it.napalm.stringtools.object.TblGrips;
import it.napalm.stringtools.object.TblOvergrips;
import it.napalm.stringtools.object.TblRacquets;
import it.napalm.stringtools.object.TblRacquetsPattern;
import it.napalm.stringtools.object.TblRacquetsUser;
import it.napalm.stringtools.object.TblStringType;
import it.napalm.stringtools.object.TblStrings;
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

public class HttpFunctions {
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
    private static String editRacquetCustomer  = "editracquetcustomer";
    private static String removeRacquetCustomer  = "removeracquetcustomer";
    private static String getListRacquetText  = "racquetstext";
    private static String editDataBrand = "editdatabrand";
    private static String newBrand = "newbrand";
    private static String getListGrips  = "grips";
    private static String getGripsText  = "gripstext";
    private static String editDataGrip = "editdatagrip";
    private static String newGrip= "newgrip";
    private static String getListOvergrips  = "overgrips";
    private static String getOvergripsText  = "overgripstext";
    private static String editDataOvergrip = "editdataovergrip";
    private static String newOvergrip = "newovergrip";
    private static String saveRacquet = "saveracquet";
    private static String editRacquet = "editracquet";
    private static String getListStringText  = "stringstext";
    private static String getListStrings  = "strings";
    private static String getGauges  = "gauges";
    private static String getStringType  = "stringtype";
    
    // constructor
    public HttpFunctions(){
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
        params.add(new BasicNameValuePair("cost",String.format( "%.2f", user.getCost() ).replace(',', '.')));
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
        params.add(new BasicNameValuePair("cost",String.format( "%.2f", user.getCost() ).replace(',', '.')));
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
    
    public ArrayList<RacquetText> getListRacquetText(String url) throws JSONException, ParseException{
        // Building for TblRacquetsUser
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", getListRacquetText));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        JSONArray firstList = json.getJSONArray("racquets"); 
        
        ArrayList<RacquetText> racquetList = new ArrayList<RacquetText>();
        for (int i = 0; i < firstList.length(); i++) {
            JSONObject catObj = (JSONObject) firstList.get(i);
            RacquetText racquet = new RacquetText();
            racquet.setId(catObj.getInt("id"));
            racquet.setDescription(catObj.getString("description"));
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
    
    public String editRacquetCustomer(String url, TblRacquetsUser value) throws JSONException{
        // Building Parameters
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", editRacquetCustomer));
        params.add(new BasicNameValuePair("tbl_grip_size_id", value.getTblGripSize()+""));
        params.add(new BasicNameValuePair("serial", value.getSerial().toString()));
        params.add(new BasicNameValuePair("weight_unstrung", String.format( "%.2f", value.getWeightUnstrung())));
        params.add(new BasicNameValuePair("weight_strung", String.format( "%.2f", value.getWeightStrung())));
        params.add(new BasicNameValuePair("balance", String.format( "%.2f", value.getBalance())));
        params.add(new BasicNameValuePair("swingweight", String.format( "%.2f", value.getSwingweight())));
        params.add(new BasicNameValuePair("stiffness",String.format( "%.2f", value.getStiffness() )));
        SimpleDateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss.SSS", Locale.getDefault()); 
        params.add(new BasicNameValuePair("date_buy", dateFormat.format(value.getDateBuy().getTime())));
        params.add(new BasicNameValuePair("note", value.getNote()));
        params.add(new BasicNameValuePair("active", value.getActive()+""));
        params.add(new BasicNameValuePair("id", value.getId()+""));
        params.add(new BasicNameValuePair("id_tbl_racquet_user", value.getId()+""));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        return json.getString("result");
    }
    
    public String removeRacquetCustomer(String url, TblRacquetsUser value) throws JSONException{
        // Building Parameters
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", removeRacquetCustomer));
        params.add(new BasicNameValuePair("id", value.getId()+""));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        return json.getString("result");
    }
    
    public String editDataBrand(String url, TblBrands item) throws JSONException{
        // Building Parameters
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", editDataBrand));
        params.add(new BasicNameValuePair("id", item.getId().toString()));
        params.add(new BasicNameValuePair("description", item.getDescription()));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        return json.getString("result");
    }
    
    public String newBrand(String url, TblBrands item) throws JSONException{
        // Building Parameters
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", newBrand));
        params.add(new BasicNameValuePair("description", item.getDescription()));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        return json.getString("result");
    }
    
    public ArrayList<TblGrips> getListGrips(String url, int id) throws JSONException, ParseException{
        // Building for TblRacquetsUser
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", getListGrips));
        params.add(new BasicNameValuePair("id", id+""));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        JSONArray firstList = json.getJSONArray("grips"); 
        
        ArrayList<TblGrips> listInside = new ArrayList<TblGrips>();
        for (int i = 0; i < firstList.length(); i++) {
            JSONObject catObj = (JSONObject) firstList.get(i);
            TblGrips element = new TblGrips();
            element.setId(catObj.getInt("id"));
            element.setIdTblBrands(catObj.getInt("tbl_brands_id"));
            element.setModel(catObj.getString("model"));
            element.setPrice(catObj.getDouble("price"));
            element.setNote(catObj.getString("note"));
			listInside.add(element);
        }
        return listInside;
    }
    
    public ArrayList<GripText> getGripsText(String url, int id) throws JSONException, ParseException{
        // Building for TblRacquetsUser
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", getGripsText));
        params.add(new BasicNameValuePair("id", id+""));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        JSONArray firstList = json.getJSONArray("grips"); 
        
        ArrayList<GripText> listInside = new ArrayList<GripText>();
        for (int i = 0; i < firstList.length(); i++) {
            JSONObject catObj = (JSONObject) firstList.get(i);
            GripText element = new GripText();
            element.setId(catObj.getInt("id"));
            element.setDescription(catObj.getString("description"));
			listInside.add(element);
        }
        return listInside;
    }
    
    public String editDataGrip(String url, TblGrips item) throws JSONException{
        // Building Parameters
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", editDataGrip));
        params.add(new BasicNameValuePair("id", item.getId().toString()));
        params.add(new BasicNameValuePair("tbl_brands_id", item.getIdTblBrands()+""));
        params.add(new BasicNameValuePair("model", item.getModel()));        
        params.add(new BasicNameValuePair("price", String.format( "%.2f", item.getPrice()).replace(',', '.')));
        params.add(new BasicNameValuePair("note", item.getNote()));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        return json.getString("result");
    }
    
    public String newGrip(String url, TblGrips item) throws JSONException{
        // Building Parameters
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", newGrip));
        params.add(new BasicNameValuePair("tbl_brands_id", item.getIdTblBrands()+""));
        params.add(new BasicNameValuePair("model", item.getModel()));
        params.add(new BasicNameValuePair("price", String.format( "%.2f", item.getPrice()).replace(',', '.')));
        params.add(new BasicNameValuePair("note", item.getNote()));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        return json.getString("result");
    }
    
    public ArrayList<TblOvergrips> getListOvergrips(String url, int id) throws JSONException, ParseException{
        // Building for TblRacquetsUser
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", getListOvergrips));
        params.add(new BasicNameValuePair("id", id+""));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        JSONArray firstList = json.getJSONArray("overgrips"); 
        
        ArrayList<TblOvergrips> listInside = new ArrayList<TblOvergrips>();
        for (int i = 0; i < firstList.length(); i++) {
            JSONObject catObj = (JSONObject) firstList.get(i);
            TblOvergrips element = new TblOvergrips();
            element.setId(catObj.getInt("id"));
            element.setIdTblBrands(catObj.getInt("tbl_brands_id"));
            element.setModel(catObj.getString("model"));
            element.setPrice(catObj.getDouble("price"));
            element.setNote(catObj.getString("note"));
			listInside.add(element);
        }
        return listInside;
    }
    
    public ArrayList<OvergripText> getOvergripsText(String url, int id) throws JSONException, ParseException{
        // Building for TblRacquetsUser
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", getOvergripsText));
        params.add(new BasicNameValuePair("id", id+""));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        JSONArray firstList = json.getJSONArray("overgrips"); 
        
        ArrayList<OvergripText> listInside = new ArrayList<OvergripText>();
        for (int i = 0; i < firstList.length(); i++) {
            JSONObject catObj = (JSONObject) firstList.get(i);
            OvergripText element = new OvergripText();
            element.setId(catObj.getInt("id"));
            element.setDescription(catObj.getString("description"));
			listInside.add(element);
        }
        return listInside;
    }
    
    public String editDataOvergrip(String url, TblOvergrips item) throws JSONException{
        // Building Parameters
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", editDataOvergrip));
        params.add(new BasicNameValuePair("id", item.getId().toString()));
        params.add(new BasicNameValuePair("tbl_brands_id", item.getIdTblBrands()+""));
        params.add(new BasicNameValuePair("model", item.getModel()));        
        params.add(new BasicNameValuePair("price", String.format( "%.2f", item.getPrice()).replace(',', '.')));
        params.add(new BasicNameValuePair("note", item.getNote()));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        return json.getString("result");
    }
    
    public String newOvergrip(String url, TblOvergrips item) throws JSONException{
        // Building Parameters
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", newOvergrip));
        params.add(new BasicNameValuePair("tbl_brands_id", item.getIdTblBrands()+""));
        params.add(new BasicNameValuePair("model", item.getModel()));
        params.add(new BasicNameValuePair("price", String.format( "%.2f", item.getPrice()).replace(',', '.')));
        params.add(new BasicNameValuePair("note", item.getNote()));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        return json.getString("result");
    }
    
    public String saveRacquet(String url, TblRacquets value) throws JSONException{
        // Building Parameters
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", saveRacquet));
        params.add(new BasicNameValuePair("tbl_brands_id", value.getTblBrands()+""));
        params.add(new BasicNameValuePair("tbl_racquets_pattern_id", value.getTblRacquetsPattern()+""));
        params.add(new BasicNameValuePair("model", value.getModel()));
        params.add(new BasicNameValuePair("head_size", String.format( "%.2f", value.getHeadSize()).replace(',', '.')));
        params.add(new BasicNameValuePair("length", String.format( "%.2f", value.getLength()).replace(',', '.')));
        params.add(new BasicNameValuePair("weight_unstrung", String.format( "%.2f", value.getWeightUnstrung()).replace(',', '.')));
        params.add(new BasicNameValuePair("weight_strung", String.format( "%.2f", value.getWeightStrung()).replace(',', '.')));
        params.add(new BasicNameValuePair("balance", String.format( "%.2f", value.getBalance()).replace(',', '.')));
        params.add(new BasicNameValuePair("swingweight", String.format( "%.2f", value.getSwingweight()).replace(',', '.')));
        params.add(new BasicNameValuePair("stiffness",String.format( "%.2f", value.getStiffness() ).replace(',', '.')));
        params.add(new BasicNameValuePair("beam_width", value.getBeamWidth()));
        params.add(new BasicNameValuePair("note", value.getNote()));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        return json.getString("result");
    }
    
    public String editRacquet(String url, TblRacquets value) throws JSONException{
        // Building Parameters
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", editRacquet));
        params.add(new BasicNameValuePair("id", value.getId()+""));
        params.add(new BasicNameValuePair("tbl_brands_id", value.getTblBrands()+""));
        params.add(new BasicNameValuePair("tbl_racquets_pattern_id", value.getTblRacquetsPattern()+""));
        params.add(new BasicNameValuePair("model", value.getModel()));
        params.add(new BasicNameValuePair("head_size", String.format( "%.2f", value.getHeadSize()).replace(',', '.')));
        params.add(new BasicNameValuePair("length", String.format( "%.2f", value.getLength()).replace(',', '.')));
        params.add(new BasicNameValuePair("weight_unstrung", String.format( "%.2f", value.getWeightUnstrung()).replace(',', '.')));
        params.add(new BasicNameValuePair("weight_strung", String.format( "%.2f", value.getWeightStrung()).replace(',', '.')));
        params.add(new BasicNameValuePair("balance", String.format( "%.2f", value.getBalance()).replace(',', '.')));
        params.add(new BasicNameValuePair("swingweight", String.format( "%.2f", value.getSwingweight()).replace(',', '.')));
        params.add(new BasicNameValuePair("stiffness",String.format( "%.2f", value.getStiffness() ).replace(',', '.')));
        params.add(new BasicNameValuePair("beam_width", value.getBeamWidth()));
        params.add(new BasicNameValuePair("note", value.getNote()));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        return json.getString("result");
    }
    
    public ArrayList<StringText> getListStringText(String url) throws JSONException, ParseException{
        // Building for TblRacquetsUser
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", getListStringText));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        JSONArray firstList = json.getJSONArray("strings"); 
        
        ArrayList<StringText> listInside = new ArrayList<StringText>();
        for (int i = 0; i < firstList.length(); i++) {
            JSONObject catObj = (JSONObject) firstList.get(i);
            StringText element = new StringText();
            element.setId(catObj.getInt("id"));
            element.setDescription(catObj.getString("description"));
			listInside.add(element);
        }
        return listInside;
    }
    
    public ArrayList<TblStrings> getListStrings(String url, int idUser, int id) throws JSONException, ParseException{
        // Building for TblRacquetsUser
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", getListStrings));
        params.add(new BasicNameValuePair("idUser", idUser+""));
        params.add(new BasicNameValuePair("id", id+""));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        JSONArray firstList = json.getJSONArray("strings"); 
        
        ArrayList<TblStrings> listInside = new ArrayList<TblStrings>();
        for (int i = 0; i < firstList.length(); i++) {
            JSONObject catObj = (JSONObject) firstList.get(i);
            TblStrings element = new TblStrings();
            element.setId(catObj.getInt("id"));
            element.setTblBrands(catObj.getInt("tbl_brands_id"));
            element.setTblGauges(catObj.getInt("tbl_gauges_id"));
            element.setTblStringType(catObj.getInt("tbl_string_type_id"));
            element.setModel(catObj.getString("model"));
            element.setCode(catObj.getString("code"));
            element.setExactGauge(catObj.getDouble("exact_gauge"));
            element.setPrice(catObj.getDouble("price"));
			listInside.add(element);
        }
        return listInside;
    }
    
    public ArrayList<TblGauges> getGauges(String url, int id) throws JSONException, ParseException{
        // Building for TblRacquetsUser
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", getGauges));
        params.add(new BasicNameValuePair("id", id+""));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        JSONArray firstList = json.getJSONArray("gauges"); 
        
        ArrayList<TblGauges> listInside = new ArrayList<TblGauges>();
        for (int i = 0; i < firstList.length(); i++) {
            JSONObject catObj = (JSONObject) firstList.get(i);
            TblGauges element = new TblGauges();
            element.setId(catObj.getInt("id"));
            element.setUsa(catObj.getString("usa"));
            element.setDiameter(catObj.getString("diameter"));
			listInside.add(element);
        }
        return listInside;
    }
    
    public ArrayList<TblStringType> getStringType(String url, int id) throws JSONException, ParseException{
        // Building for TblRacquetsUser
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        params.add(new BasicNameValuePair("tag", getStringType));
        params.add(new BasicNameValuePair("id", id+""));
        JSONObject json = jsonParser.getJSONFromUrl(url, params);
        JSONArray firstList = json.getJSONArray("stringtype"); 
        
        ArrayList<TblStringType> listInside = new ArrayList<TblStringType>();
        for (int i = 0; i < firstList.length(); i++) {
            JSONObject catObj = (JSONObject) firstList.get(i);
            TblStringType element = new TblStringType();
            element.setId(catObj.getInt("id"));
            element.setDescription(catObj.getString("description"));
			listInside.add(element);
        }
        return listInside;
    }
}
