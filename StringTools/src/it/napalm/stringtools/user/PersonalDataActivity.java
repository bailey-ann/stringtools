package it.napalm.stringtools.user;

import java.text.DecimalFormat;
import java.util.ArrayList;

import org.json.JSONException;

import it.napalm.stringtools.R;
import it.napalm.stringtools.object.TblCurrencyUnit;
import it.napalm.stringtools.object.TblUsers;
import it.napalm.stringtools.object.TblWeightUnit;
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
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.Spinner;

public class PersonalDataActivity extends Activity implements OnItemSelectedListener  {

	private Spinner spinnerWeightUnit;
	private Spinner spinnerCurrencyUnit;
	private ArrayList<TblWeightUnit> weightUnitList;
	private ArrayList<TblCurrencyUnit> currencyUnitList;
	private TblUsers user = null;
	private HttpFunctions function;
	private int position;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.personal_data);
		getActionBar().setDisplayHomeAsUpEnabled(true);
		
		StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
	    StrictMode.setThreadPolicy(policy);
	    	    
	    function = new HttpFunctions();
	    spinnerWeightUnit = (Spinner) findViewById(R.id.spinWeightUnit);
	    spinnerCurrencyUnit = (Spinner) findViewById(R.id.spinCurrencyUnit);
        weightUnitList = new ArrayList<TblWeightUnit>();
        currencyUnitList = new ArrayList<TblCurrencyUnit>();
        Intent intent=getIntent();
        user = (TblUsers)intent.getSerializableExtra("user");
        position = intent.getIntExtra("position", 0);
        
        
	    new GetDataForSpinner().execute();
	    setDataUser();
	}
	
	public void setDataUser(){
		((EditText) findViewById(R.id.email)).setText(user.getEmail());
		((EditText) findViewById(R.id.name)).setText(user.getName());
		((EditText) findViewById(R.id.surname)).setText(user.getSurname());
		((EditText) findViewById(R.id.telephone)).setText(user.getTelephone());
		((EditText) findViewById(R.id.mobile_telephone)).setText(user.getMobileTelephone());
		((EditText) findViewById(R.id.piva)).setText(user.getPiva());
		((EditText) findViewById(R.id.fax)).setText(user.getFax());
		((EditText) findViewById(R.id.stringing_cost)).setText(String.format( "%.2f", user.getCost() ));		
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
	    // Handle presses on the action bar items
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
		
		user.setEmail(((EditText) findViewById(R.id.email)).getText().toString());
		user.setName(((EditText) findViewById(R.id.name)).getText().toString());
		user.setSurname(((EditText) findViewById(R.id.surname)).getText().toString());		
		user.setTelephone(((EditText) findViewById(R.id.telephone)).getText().toString());
		user.setMobileTelephone(((EditText) findViewById(R.id.mobile_telephone)).getText().toString());
		user.setCost(0);
		try {
	        String eAm = ((EditText) findViewById(R.id.stringing_cost)).getText().toString();
	        DecimalFormat dF = new DecimalFormat("0.00");
	        Number num = dF.parse(eAm);
	        user.setCost(num.doubleValue());
	    } catch (Exception e) { }				
		
		TblWeightUnit tbl_weight_unit = (TblWeightUnit) spinnerWeightUnit.getItemAtPosition(spinnerWeightUnit.getSelectedItemPosition());		
		user.setTblWeightUnitId(tbl_weight_unit.getId());
		TblCurrencyUnit tbl_currency_unit = (TblCurrencyUnit) spinnerCurrencyUnit.getItemAtPosition(spinnerCurrencyUnit.getSelectedItemPosition());
		user.setTblCurrencyUnitId(tbl_currency_unit.getId());
		user.setPiva(((EditText) findViewById(R.id.piva)).getText().toString());
		user.setFax(((EditText) findViewById(R.id.fax)).getText().toString());
		
		new SaveDataUser().execute();
	}

	/**
	 * Async task to get all food categories
	 * */
	private class GetDataForSpinner extends AsyncTask<Void, Void, Void> {
	 
	    @Override
	    protected void onPreExecute() {
	        super.onPreExecute();
	        //pDialog = new ProgressDialog(MainActivity.this);
	        //pDialog.setMessage("Fetching food categories..");
	        //pDialog.setCancelable(false);
	        //pDialog.show();
	 
	    }
	 
	    @Override
	    protected Void doInBackground(Void... arg0) {
	    	
	    	
	    	try {
				weightUnitList = function.getTblWeightUnit(getResources().getString(R.string.URL));
				currencyUnitList = function.getTblCurrencyUnit(getResources().getString(R.string.URL));
			} catch (NotFoundException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
	    			

	 
	        return null;
	    }
	 
	    @Override
	    protected void onPostExecute(Void result) {
	        super.onPostExecute(result);
	        //if (pDialog.isShowing())
	        //    pDialog.dismiss();
	        populateSpinner();
	    }
	 
	}
	
	/**
	 * Adding spinner data
	 * */
	private void populateSpinner() {
		//spinner weight
	    ArrayAdapter<TblWeightUnit> spinnerAdapterWeightUnit = new ArrayAdapter<TblWeightUnit>(getApplicationContext(),
	            R.layout.simple_spinner_item, weightUnitList);
	    spinnerAdapterWeightUnit
	            .setDropDownViewResource(R.layout.simple_spinner_dropdown_item);
	    spinnerWeightUnit.setAdapter(spinnerAdapterWeightUnit);
	    
	    if(user != null){
		    for(int i = 0; i < spinnerAdapterWeightUnit.getCount(); i++) {
		        if (spinnerAdapterWeightUnit.getItem(i).getId() == user.getTblWeightUnitId() ){
		        	spinnerWeightUnit.setSelection(i);
		            break;
		        }
		    }
	    }
	    
	  //spinner currency
	    ArrayAdapter<TblCurrencyUnit> spinnerAdapterCurrencyUnit = new ArrayAdapter<TblCurrencyUnit>(getApplicationContext(),
	            R.layout.simple_spinner_item, currencyUnitList);
	    spinnerAdapterCurrencyUnit
	            .setDropDownViewResource(R.layout.simple_spinner_dropdown_item);
	    spinnerCurrencyUnit.setAdapter(spinnerAdapterCurrencyUnit);
	    
	    if(user != null){
		    for(int i = 0; i < spinnerAdapterCurrencyUnit.getCount(); i++) {
		        if (spinnerAdapterCurrencyUnit.getItem(i).getId() == user.getTblCurrencyUnitId() ){
		        	spinnerCurrencyUnit.setSelection(i);
		            break;
		        }
		    }
	    }
	}
	
	@Override
    public void onItemSelected(AdapterView<?> parent, View view, int position,
            long id) {

 
    }


	@Override
	public void onNothingSelected(AdapterView<?> arg0) {
		// TODO Auto-generated method stub
		
	}
	
	private class SaveDataUser extends AsyncTask<Void, Void, Void> {
		private ProgressDialog pDialog ;
		private String return_type = "";
	    @Override
	    protected void onPreExecute() {
	        super.onPreExecute();	
	        pDialog = new ProgressDialog(PersonalDataActivity.this);
	        pDialog.setMessage(getResources().getString(R.string.wait_save));
	        pDialog.setCancelable(false);
	        pDialog.show();
	    }
	 
	    @Override
	    protected Void doInBackground(Void... arg0) {
	    	try {
	    		return_type = function.saveDataUser(getResources().getString(R.string.URL), user);
			} catch (NotFoundException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			} catch (JSONException e) {
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
		        output.putExtra("user", user);
		        output.putExtra("position", position);
		        setResult(RESULT_OK, output);
		        finish();
	        }else{
	        	AlertDialog.Builder builder1 = new AlertDialog.Builder(PersonalDataActivity.this);
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
        output.putExtra("position", position);
        setResult(RESULT_CANCELED, output);
        finish();
	}
	
	@Override
	public void onBackPressed() {
	    backTo();
	}
}


