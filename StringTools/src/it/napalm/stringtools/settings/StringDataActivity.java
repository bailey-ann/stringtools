package it.napalm.stringtools.settings;

import java.text.ParseException;
import java.util.ArrayList;
import java.util.Locale;

import org.json.JSONException;

import it.napalm.stringtools.R;
import it.napalm.stringtools.object.TblBrands;
import it.napalm.stringtools.object.TblGauges;
import it.napalm.stringtools.object.TblRacquets;
import it.napalm.stringtools.object.TblRacquetsPattern;
import it.napalm.stringtools.object.TblStringType;
import it.napalm.stringtools.object.TblStrings;
import it.napalm.stringtools.object.TblUsers;
import it.napalm.stringtools.utils.Function;
import it.napalm.stringtools.utils.HttpFunctions;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.res.Resources.NotFoundException;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.StrictMode;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.inputmethod.InputMethodManager;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.Spinner;

public class StringDataActivity extends Activity  {

	private HttpFunctions function;
	private int id;
	private TblUsers stringer;
	private TblStrings item;
	private boolean NEW;
	private ArrayList<TblBrands> listTblBrands;
	private ArrayList<TblGauges> listTblGauges;
	private ArrayList<TblStringType> listTblStringType;
	private Spinner spinTblBrands;
	private Spinner spinTblGauges;
	private Spinner spinTblStringType;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.string_data);
		getActionBar().setDisplayHomeAsUpEnabled(true);
		
		StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
	    StrictMode.setThreadPolicy(policy);
	    
	    spinTblBrands = (Spinner) findViewById(R.id.spinBrandName);
	    spinTblGauges = (Spinner) findViewById(R.id.spinGauges);
	    spinTblStringType = (Spinner) findViewById(R.id.spinStringType);

	    function = new HttpFunctions();
	    listTblBrands = new ArrayList<TblBrands>();
	    listTblGauges = new ArrayList<TblGauges>();
	    listTblStringType = new ArrayList<TblStringType>();
	    
	    Intent intent=getIntent();
	    id = intent.getIntExtra("id", 0);
	    stringer = (TblUsers)intent.getSerializableExtra("stringer");
	    
	    if(id != 0){
        	setTitle(R.string.edit_string, "");        	
        	NEW = false;
        }else{
        	setTitle(R.string.new_string, "");
        	NEW = true;
        }

	    new SetGui().execute();
        
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
		    case android.R.id.home:
		    	backTo();
	            return true;
	        case R.id.action_save:
	        	save();
	            return true;
	        default:
	            return super.onOptionsItemSelected(item);
	    }
	}
	
	private void save(){
		/*if(NEW){
			item = new TblRacquets();
			item.setId(0);
		}
		TblBrands tblBrands = (TblBrands) spinBrandName.getItemAtPosition(spinBrandName.getSelectedItemPosition());
		item.setTblBrands(tblBrands.getId());
		TblRacquetsPattern tblRacquetsPattern = (TblRacquetsPattern) spinPattern.getItemAtPosition(spinPattern.getSelectedItemPosition());
		item.setTblRacquetsPattern(tblRacquetsPattern.getId());
		item.setModel(((EditText) findViewById(R.id.Model)).getText().toString());
		item.setHeadSize(Function.stringToDouble( ((EditText) findViewById(R.id.HeadSize)).getText().toString()));
		item.setLength(Function.stringToDouble( ((EditText) findViewById(R.id.Length)).getText().toString()));
		item.setWeightUnstrung(Function.stringToDouble( ((EditText) findViewById(R.id.WeightUnstrung)).getText().toString()));	
		item.setWeightStrung(Function.stringToDouble( ((EditText) findViewById(R.id.WeightStrung)).getText().toString()));
		item.setBalance(Function.stringToDouble( ((EditText) findViewById(R.id.Balance)).getText().toString()));
		item.setSwingweight(Function.stringToDouble( ((EditText) findViewById(R.id.Swingweight)).getText().toString()));
		item.setStiffness(Function.stringToDouble( ((EditText) findViewById(R.id.Stiffness)).getText().toString()));	
		item.setBeamWidth(((EditText) findViewById(R.id.BeamWidth)).getText().toString());
		item.setNote(((EditText) findViewById(R.id.Note)).getText().toString());
		
		new SaveData().execute();*/
	}

	private void backTo(){
		InputMethodManager imm = (InputMethodManager)getSystemService(Context.INPUT_METHOD_SERVICE);
		imm.hideSoftInputFromWindow(((EditText) findViewById(R.id.Model)).getWindowToken(), 0);
        setResult(RESULT_CANCELED, null);
        finish();
	}
	
	@Override
	public void onBackPressed() {
	    backTo();
	}
	
	private class SetGui extends AsyncTask<Void, Void, Void> {
		 
		ProgressDialog pDialog;
		
	    @Override
	    protected void onPreExecute() {
	        super.onPreExecute();
	        pDialog = new ProgressDialog(StringDataActivity.this);
	        pDialog.setMessage(getResources().getString(R.string.wait_load));
	        pDialog.setCancelable(false);
	        pDialog.show();
	 
	    }
	 
	    @Override
	    protected Void doInBackground(Void... arg0) {
	    	try {
	    		if(!NEW)
	    			item = function.getListStrings(getResources().getString(R.string.URL), stringer.getId(), id).get(0);
				listTblBrands = function.getBrands(getResources().getString(R.string.URL), 0);
				listTblGauges = function.getGauges(getResources().getString(R.string.URL), 0);
				listTblStringType = function.getStringType(getResources().getString(R.string.URL), 0);
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
	        return null;
	    }
	 
	    private void setData() {
	    	try{

	    		if(!NEW){
		    		EditText model = ((EditText) findViewById(R.id.Model));
		    		model.setText(item.getModel());
		    		EditText code = ((EditText) findViewById(R.id.Code));
		    		code.setText(item.getCode());
		    		EditText ExactGauges = ((EditText) findViewById(R.id.ExactGauges));
		    		ExactGauges.setText(String.format(Locale.ENGLISH,  "%.2f", item.getExactGauge()));
		    		EditText Price = ((EditText) findViewById(R.id.Price));
		    		Price.setText(String.format(Locale.ENGLISH,  "%.2f", item.getPrice()));
	    		}
		    	
		    	populateSpinner();
		    	
	    	}catch(Exception e){}
		}
	    
	
		private void populateSpinner() {
			
			ArrayAdapter<TblBrands> spinnerAdapterTblBrands= new ArrayAdapter<TblBrands>(StringDataActivity.this,
		            R.layout.simple_spinner_item, listTblBrands);
			spinnerAdapterTblBrands.setDropDownViewResource(R.layout.simple_spinner_dropdown_item);
			spinTblBrands.setAdapter(spinnerAdapterTblBrands);
		    int id = item.getTblBrands();
		    for(int i = 0; i < spinnerAdapterTblBrands.getCount(); i++) {
		    	if (spinnerAdapterTblBrands.getItem(i).getId() == id){
		    		spinTblBrands.setSelection(i);
					break;
				}
			}
			
		    ArrayAdapter<TblGauges> spinnerTblGauges = new ArrayAdapter<TblGauges>(StringDataActivity.this,
		            R.layout.simple_spinner_item, listTblGauges);
		    spinnerTblGauges.setDropDownViewResource(R.layout.simple_spinner_dropdown_item);
		    spinTblGauges.setAdapter(spinnerTblGauges);
		    id = item.getTblGauges();
		    for(int i = 0; i < spinnerTblGauges.getCount(); i++) {
		    	if (spinnerTblGauges.getItem(i).getId() == id){
		    		spinTblGauges.setSelection(i);
					break;
				}
			}
		    
		    ArrayAdapter<TblStringType> spinnerStringType = new ArrayAdapter<TblStringType>(StringDataActivity.this,
		            R.layout.simple_spinner_item, listTblStringType);
		    spinnerStringType.setDropDownViewResource(R.layout.simple_spinner_dropdown_item);
		    spinTblStringType.setAdapter(spinnerStringType);
		    id = item.getTblStringType();
		    for(int i = 0; i < spinnerStringType.getCount(); i++) {
		    	if (spinnerStringType.getItem(i).getId() == id){
		    		spinTblStringType.setSelection(i);
					break;
				}
			}
		    
		}
		
		@Override
	    protected void onPostExecute(Void result) {
	        super.onPostExecute(result);
	        setData();
	        if (pDialog.isShowing())
	            pDialog.dismiss();
	        
	    }
	}
	
	private class SaveData extends AsyncTask<Void, Void, Void> {
		private ProgressDialog pDialog ;
		private String return_type = "";
	    @Override
	    protected void onPreExecute() {
	        super.onPreExecute();	
	        pDialog = new ProgressDialog(StringDataActivity.this);
	        pDialog.setMessage(getResources().getString(R.string.wait_save));
	        pDialog.setCancelable(false);
	        pDialog.show();
	    }
	 
	    @Override
	    protected Void doInBackground(Void... arg0) {

	        return null;
	    }
	 
	    @Override
	    protected void onPostExecute(Void result) {
	        super.onPostExecute(result);
	        if (pDialog.isShowing())
	            pDialog.dismiss();
	        if(return_type.equals("1")){
		        Intent output = new Intent();
		        setResult(RESULT_OK, output);
		        finish();
	        }else{
	        	AlertDialog.Builder builder1 = new AlertDialog.Builder(StringDataActivity.this);
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

	public void setTitle(int title,CharSequence subTitle) {
        getActionBar().setTitle(title);
        getActionBar().setSubtitle(subTitle);
    }	    
		    
}


