package it.napalm.stringtools.user;

import java.text.DecimalFormat;
import java.text.ParseException;
import java.util.ArrayList;
import java.util.List;

import org.json.JSONException;



import it.napalm.stringtools.R;
import it.napalm.stringtools.adapter.CustomerRacquetAdapter;
import it.napalm.stringtools.globalobject.CustomerRacquet;
import it.napalm.stringtools.object.PositionMenu;
import it.napalm.stringtools.object.TblBrands;
import it.napalm.stringtools.object.TblCurrencyUnit;
import it.napalm.stringtools.object.TblRacquets;
import it.napalm.stringtools.object.TblRacquetsPattern;
import it.napalm.stringtools.object.TblRacquetsUser;
import it.napalm.stringtools.object.TblUsers;
import it.napalm.stringtools.object.TblWeightUnit;
import it.napalm.stringtools.utils.UserFunctions;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.content.res.Resources.NotFoundException;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.StrictMode;
import android.util.Log;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.Spinner;
import android.widget.Toast;

public class EditCustomerRacquetActivity extends Activity  {

	private UserFunctions function;
	private CustomerRacquet customerRacquet;
	private ArrayList<CustomerRacquet> listRacquet;
	private ArrayList<TblBrands> listTblBrands;
	private ArrayList<TblRacquetsPattern> listTblRacquetsPattern;
	private ListView list;
	private CustomerRacquetAdapter adapter;
	private int position;
	private Spinner spinBrandName ;
	private Spinner spinPattern ;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.customer_racquet_data);
		getActionBar().setDisplayHomeAsUpEnabled(true);
		
		StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
	    StrictMode.setThreadPolicy(policy);
	    
	    spinBrandName = (Spinner) findViewById(R.id.spinBrandName);
	    spinPattern = (Spinner) findViewById(R.id.spinPattern);
	    function = new UserFunctions();
	    listTblBrands = new ArrayList<TblBrands>();
	    listTblRacquetsPattern = new ArrayList<TblRacquetsPattern>();
	    
	    Intent intent=getIntent();
	    customerRacquet = (CustomerRacquet)intent.getSerializableExtra("customerracquet");
	    position = intent.getIntExtra("position", PositionMenu.CUSTOMERS_LIST_RACQUET);
	    
	    getActionBar().setTitle(getResources().getString(R.string.editcustomerracquet));
        getActionBar().setSubtitle(customerRacquet.getTblRacquets().getModel() + " " + customerRacquet.getTblRacquetsUser().getSerial());
	    
	    
	    
	    new CreateGui().execute();
        
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
	    // Inflate the menu items for use in the action bar
	    MenuInflater inflater = getMenuInflater();
	    inflater.inflate(R.menu.menu_rs, menu);
	    return super.onCreateOptionsMenu(menu);
	}
	
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
	    // Handle presses on the action bar items
	    switch (item.getItemId()) {
		    case android.R.id.home:
		    	backTo();
	            return true;
	        case R.id.save:
	            setResult(RESULT_OK, null);
	        	finish();
	            return true;
	        case R.id.remove:
	            //setResult(RESULT_OK, null);
	        	//finish();
	            return true;
	        default:
	            return super.onOptionsItemSelected(item);
	    }
	}
	
	private void backTo(){
        setResult(RESULT_CANCELED, null);
        finish();
	}
	
	@Override
	public void onBackPressed() {
	    backTo();
	}
	
	private class CreateGui extends AsyncTask<Void, Void, Void> {
		 
		ProgressDialog pDialog;
		
	    @Override
	    protected void onPreExecute() {
	        super.onPreExecute();
	        pDialog = new ProgressDialog(EditCustomerRacquetActivity.this);
	        pDialog.setMessage(getResources().getString(R.string.wait_load));
	        pDialog.setCancelable(false);
	        pDialog.show();
	 
	    }
	 
	    @Override
	    protected Void doInBackground(Void... arg0) {
	    	
	        setData();
	        
	        return null;
	    }
	 
	    private void setData() {
	    	DecimalFormat format = new DecimalFormat();
	        format.setDecimalSeparatorAlwaysShown(false);
	    	try{
		    	((EditText) findViewById(R.id.Model)).setText(customerRacquet.getTblRacquets().getModel());
		    	((EditText) findViewById(R.id.HeadSize)).setText(format.format(customerRacquet.getTblRacquets().getHeadSize()));
		    	((EditText) findViewById(R.id.Length)).setText(format.format(customerRacquet.getTblRacquets().getLength()));
		    	((EditText) findViewById(R.id.Serial)).setText(customerRacquet.getTblRacquetsUser().getSerial());
		    	((EditText) findViewById(R.id.WeightUnstrung)).setText(format.format(customerRacquet.getTblRacquetsUser().getWeightUnstrung()));
		    	((EditText) findViewById(R.id.WeightStrung)).setText(format.format(customerRacquet.getTblRacquetsUser().getWeightStrung()));
		    	((EditText) findViewById(R.id.Balance)).setText(format.format(customerRacquet.getTblRacquetsUser().getBalance()));
		    	((EditText) findViewById(R.id.Swingweight)).setText(format.format(customerRacquet.getTblRacquetsUser().getSwingweight()));
		    	((EditText) findViewById(R.id.Stiffness)).setText(format.format(customerRacquet.getTblRacquetsUser().getStiffness()));
		    	((EditText) findViewById(R.id.BeamWidth)).setText(customerRacquet.getTblRacquets().getBeamWidth());
		    	((EditText) findViewById(R.id.Note)).setText(customerRacquet.getTblRacquetsUser().getNote());
		    	
		    	
				try {
					listTblBrands = function.getBrands(getResources().getString(R.string.URL), 0);
					listTblRacquetsPattern = function.getRacquetsPattern(getResources().getString(R.string.URL), 0);
				} catch (NotFoundException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				} catch (JSONException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				} catch (ParseException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
		    	
	    	}catch(Exception e){}
		}
	    
	
		private void populateSpinner() {
			//spinBrandName
			/*Spinner spinBrandName = (Spinner) findViewById(R.id.spinBrandName);
	    	List<TblBrands> listTblBrands = null;
			try {
				listTblBrands = function.getBrands(getResources().getString(R.string.URL), 0);
			} catch (NotFoundException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			} catch (ParseException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		    ArrayAdapter<TblBrands> spinnerAdapterTblBrands = new ArrayAdapter<TblBrands>(getApplicationContext(),
		            R.layout.simple_spinner_item, listTblBrands);
		    spinnerAdapterTblBrands
		            .setDropDownViewResource(R.layout.simple_spinner_dropdown_item);
		    spinBrandName.setAdapter(spinnerAdapterTblBrands);
		    Log.d("TEST", "Your message:"+listTblBrands.size());

		    for(int i = 0; i < spinnerAdapterTblBrands.getCount(); i++) {
				if (spinnerAdapterTblBrands.getItem(i).getId() == customerRacquet.getTblBrands().getId()){
					spinBrandName.setSelection(i);
					break;
				}
			}*/
			
			ArrayAdapter<TblBrands> spinnerAdapterTblBrands= new ArrayAdapter<TblBrands>(EditCustomerRacquetActivity.this,
		            R.layout.simple_spinner_item, listTblBrands);
			spinnerAdapterTblBrands.setDropDownViewResource(R.layout.simple_spinner_dropdown_item);
		    spinBrandName.setAdapter(spinnerAdapterTblBrands);
		    int id = customerRacquet.getTblBrands().getId();
		    for(int i = 0; i < spinnerAdapterTblBrands.getCount(); i++) {
		    	if (spinnerAdapterTblBrands.getItem(i).getId() == id){
		    		spinBrandName.setSelection(i);
					break;
				}
			}
			
			
		    ArrayAdapter<TblRacquetsPattern> spinnerTblRacquetsPattern= new ArrayAdapter<TblRacquetsPattern>(EditCustomerRacquetActivity.this,
		            R.layout.simple_spinner_item, listTblRacquetsPattern);
		    spinnerTblRacquetsPattern.setDropDownViewResource(R.layout.simple_spinner_dropdown_item);
		    spinPattern.setAdapter(spinnerTblRacquetsPattern);
		    id = customerRacquet.getTblRacquetsPattern().getId();
		    for(int i = 0; i < spinnerTblRacquetsPattern.getCount(); i++) {
		    	if (spinnerTblRacquetsPattern.getItem(i).getId() == id){
		    		spinPattern.setSelection(i);
					break;
				}
			}
		}
		
		@Override
	    protected void onPostExecute(Void result) {
	        super.onPostExecute(result);
	        populateSpinner();
	        if (pDialog.isShowing())
	            pDialog.dismiss();
	        
	    }
	}
}


