package it.napalm.stringtools.user;



import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.text.DecimalFormat;
import java.util.Random;

import org.json.JSONException;

import it.napalm.stringtools.R;
import it.napalm.stringtools.object.PositionMenu;
import it.napalm.stringtools.object.TblUsers;
import it.napalm.stringtools.utils.HttpFunctions;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.res.Resources.NotFoundException;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.StrictMode;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.EditText;
import android.widget.TextView;

public class CustomerDataActivity extends Activity implements OnItemSelectedListener  {

	private TblUsers customer = null;
	private TblUsers stringer = null;
	private HttpFunctions function;
	private int position;
	private boolean NEW_CUSTOMER = false;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.customer_data);
		getActionBar().setDisplayHomeAsUpEnabled(true);
		
		StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
	    StrictMode.setThreadPolicy(policy);
	    	    
	    function = new HttpFunctions();
        Intent intent=getIntent();
        customer = (TblUsers)intent.getSerializableExtra("customer");
        stringer = (TblUsers)intent.getSerializableExtra("stringer");
        position = intent.getIntExtra("position", 0);
        
        if(customer != null){
        	setTitle(R.string.edit_customer, customer.getName() + " " + customer.getSurname());
        	setDataCustomer();
        	NEW_CUSTOMER = false;
        	((EditText) findViewById(R.id.username)).setVisibility(View.GONE);
        	((EditText) findViewById(R.id.password)).setVisibility(View.GONE);
        	((TextView) findViewById(R.id.lblusername)).setVisibility(View.GONE);
        	((TextView) findViewById(R.id.lblpassword)).setVisibility(View.GONE);
        }else{
        	setTitle(R.string.new_customer, "");
        	NEW_CUSTOMER = true;
        	((EditText) findViewById(R.id.username)).setVisibility(View.VISIBLE);
        	((EditText) findViewById(R.id.password)).setVisibility(View.VISIBLE);
        	((TextView) findViewById(R.id.lblusername)).setVisibility(View.VISIBLE);
        	((TextView) findViewById(R.id.lblpassword)).setVisibility(View.VISIBLE);
        }
	}
	
	public void setDataCustomer(){
		((EditText) findViewById(R.id.username)).setText(customer.getUsername());
		((EditText) findViewById(R.id.password)).setText(customer.getPassword());
		((EditText) findViewById(R.id.email)).setText(customer.getEmail());
		((EditText) findViewById(R.id.name)).setText(customer.getName());
		((EditText) findViewById(R.id.surname)).setText(customer.getSurname());
		((EditText) findViewById(R.id.telephone)).setText(customer.getTelephone());
		((EditText) findViewById(R.id.mobile_telephone)).setText(customer.getMobileTelephone());
		((EditText) findViewById(R.id.piva)).setText(customer.getPiva());
		((EditText) findViewById(R.id.fax)).setText(customer.getFax());
		((EditText) findViewById(R.id.stringing_cost)).setText(String.format( "%.2f", customer.getCost() ));		
	}
	

	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
	    // Inflate the menu items for use in the action bar
	    MenuInflater inflater = getMenuInflater();
	    inflater.inflate(R.menu.menu_save, menu);
	    return super.onCreateOptionsMenu(menu);
	}
	
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
	    switch (item.getItemId()) {
	        case R.id.action_save:
	            saveDataUSer();
	            return true;
	        case android.R.id.home:
	        	backTo();
	            return true;
	        default:
	            return super.onOptionsItemSelected(item);
	    }
	}
	
	private void saveDataUSer() {
		
		if(NEW_CUSTOMER){
			customer = new TblUsers();
			customer.setTblTypeUser(3);
			customer.setUsername(((EditText) findViewById(R.id.username)).getText().toString());
			customer.setPassword(md5(((EditText) findViewById(R.id.password)).getText().toString()));
			customer.setActive(1);
			customer.setConfirmCode(getMyNumber()+"");
		}
		customer.setEmail(((EditText) findViewById(R.id.email)).getText().toString());
		customer.setName(((EditText) findViewById(R.id.name)).getText().toString());
		customer.setSurname(((EditText) findViewById(R.id.surname)).getText().toString());		
		customer.setTelephone(((EditText) findViewById(R.id.telephone)).getText().toString());
		customer.setMobileTelephone(((EditText) findViewById(R.id.mobile_telephone)).getText().toString());
		customer.setCost(0);
		try {
	        String eAm = ((EditText) findViewById(R.id.stringing_cost)).getText().toString();
	        DecimalFormat dF = new DecimalFormat("0.00");
	        Number num = dF.parse(eAm);
	        customer.setCost(num.doubleValue());
	    } catch (Exception e) { }				
		
		customer.setTblWeightUnitId(1);
		customer.setTblCurrencyUnitId(1);
		customer.setPiva(((EditText) findViewById(R.id.piva)).getText().toString());
		customer.setFax(((EditText) findViewById(R.id.fax)).getText().toString());
		
		new SaveDataUser().execute(NEW_CUSTOMER);
	}


	@Override
    public void onItemSelected(AdapterView<?> parent, View view, int position,
            long id) {

 
    }


	@Override
	public void onNothingSelected(AdapterView<?> arg0) {
		// TODO Auto-generated method stub
		
	}
	
	private class SaveDataUser extends AsyncTask<Boolean, Void, Void> {
		private ProgressDialog pDialog ;
		private String return_type = "";
	    @Override
	    protected void onPreExecute() {
	        super.onPreExecute();	
	        pDialog = new ProgressDialog(CustomerDataActivity.this);
	        pDialog.setMessage(getResources().getString(R.string.wait_save));
	        pDialog.setCancelable(false);
	        pDialog.show();
	    }
	 
	    @Override
	    protected Void doInBackground(Boolean... arg0) {
	    	try {
	    		if(!arg0[0])
					try {
						return_type = function.saveDataUser(getResources().getString(R.string.URL), customer);
					} catch (JSONException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
				else
					try {
						return_type = function.newCustomer(getResources().getString(R.string.URL), customer, stringer.getId());
					} catch (JSONException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
			} catch (NotFoundException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
	        return null;
	    }
	 
	    @Override
	    protected void onPostExecute(Void result) {
	        super.onPostExecute(result);
	        if (pDialog.isShowing())
	            pDialog.dismiss();
	        if(return_type.equals("1")){
		        Intent output = new Intent();
		        output.putExtra("user", stringer);
		        output.putExtra("position", position);
		        setResult(RESULT_OK, output);
		        finish();
	        }else{
	        	AlertDialog.Builder builder1 = new AlertDialog.Builder(CustomerDataActivity.this);
	        	builder1.setMessage(getResources().getString(R.string.wrong_save))
	        		.setTitle(getResources().getString(R.string.attention));
	            builder1.setCancelable(true);
	            builder1.setPositiveButton("Ok",
	                    new DialogInterface.OnClickListener() {
	                public void onClick(DialogInterface dialog, int id) {
	                    dialog.cancel();
	                }
	            });

	            AlertDialog alert11 = builder1.create();
	            alert11.show();
	        }
	    }
	 
	}
	
	private void backTo(){
		Intent output = new Intent();
        output.putExtra("position", PositionMenu.CUSTOMERS);
        setResult(RESULT_CANCELED, output);
        finish();
	}
	
	@Override
	public void onBackPressed() {
	    backTo();
	}
	
	public String md5(String s) {
	    try {
	        // Create MD5 Hash
	        MessageDigest digest = java.security.MessageDigest.getInstance("MD5");
	        digest.update(s.getBytes());
	        byte messageDigest[] = digest.digest();

	        // Create Hex String
	        StringBuffer hexString = new StringBuffer();
	        for (int i=0; i<messageDigest.length; i++)
	            hexString.append(Integer.toHexString(0xFF & messageDigest[i]));
	        return hexString.toString();

	    } catch (NoSuchAlgorithmException e) {
	        e.printStackTrace();
	    }
	    return "";
	}
	
	public int getMyNumber()
	{

		int min = 100000000;
		int max = 999999999;
		Random r = new Random();
		int i = r.nextInt(max - min + 1) + min;
	
		return i;

	}
	
	public void setTitle(int title,CharSequence subTitle) {
        getActionBar().setTitle(title);
        getActionBar().setSubtitle(subTitle);
    }
}


