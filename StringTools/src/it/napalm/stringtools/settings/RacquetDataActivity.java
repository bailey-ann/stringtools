package it.napalm.stringtools.settings;

import java.text.DecimalFormat;
import java.text.ParseException;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Locale;

import org.json.JSONException;

import it.napalm.stringtools.R;
import it.napalm.stringtools.globalobject.CustomerRacquet;
import it.napalm.stringtools.object.TblBrands;
import it.napalm.stringtools.object.TblGripSize;
import it.napalm.stringtools.object.TblRacquets;
import it.napalm.stringtools.object.TblRacquetsPattern;
import it.napalm.stringtools.object.TblRacquetsUser;
import it.napalm.stringtools.utils.Function;
import it.napalm.stringtools.utils.HttpFunctions;
import android.R.bool;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.DatePickerDialog;
import android.app.Dialog;
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
import android.view.View;
import android.view.inputmethod.InputMethodManager;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.TextView;

public class RacquetDataActivity extends Activity  {

	private HttpFunctions function;
	private int id;
	private TblRacquets item;
	private boolean NEW;
	private ArrayList<TblBrands> listTblBrands;
	private ArrayList<TblRacquetsPattern> listTblRacquetsPattern;
	private Spinner spinBrandName ;
	private Spinner spinPattern ;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.racquet_data);
		getActionBar().setDisplayHomeAsUpEnabled(true);
		
		StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
	    StrictMode.setThreadPolicy(policy);
	    
	    spinBrandName = (Spinner) findViewById(R.id.spinBrandName);
	    spinPattern = (Spinner) findViewById(R.id.spinPattern);

	    function = new HttpFunctions();
	    listTblBrands = new ArrayList<TblBrands>();
	    listTblRacquetsPattern = new ArrayList<TblRacquetsPattern>();
	    
	    Intent intent=getIntent();
	    id = intent.getIntExtra("id", 0);
	    
	    if(id != 0){
        	setTitle(R.string.edit_racquet, "");        	
        	NEW = false;
        }else{
        	setTitle(R.string.new_racquet, "");
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
	        	saveRacquet();
	            return true;
	        default:
	            return super.onOptionsItemSelected(item);
	    }
	}
	
	private void saveRacquet(){
		if(NEW){
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
		
		new SaveData().execute();
	}

	private void backTo(){
		InputMethodManager imm = (InputMethodManager)getSystemService(Context.INPUT_METHOD_SERVICE);
		imm.hideSoftInputFromWindow(((EditText) findViewById(R.id.WeightStrung)).getWindowToken(), 0);
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
	        pDialog = new ProgressDialog(RacquetDataActivity.this);
	        pDialog.setMessage(getResources().getString(R.string.wait_load));
	        pDialog.setCancelable(false);
	        pDialog.show();
	 
	    }
	 
	    @Override
	    protected Void doInBackground(Void... arg0) {
	    	try {
	    		if(!NEW)
	    			item = function.getListRacquet(getResources().getString(R.string.URL), id).get(0);
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
	        return null;
	    }
	 
	    private void setData() {
	    	try{

	    		if(!NEW){
		    		EditText model = ((EditText) findViewById(R.id.Model));
		    		model.setText(item.getModel());
		    		EditText HeadSize = ((EditText) findViewById(R.id.HeadSize));
		    		HeadSize.setText(String.format(Locale.ENGLISH,  "%.2f", item.getHeadSize()));
		    		EditText Length = ((EditText) findViewById(R.id.Length));
		    		Length.setText(String.format(Locale.ENGLISH,  "%.2f", item.getLength()));
		    		
			    	((EditText) findViewById(R.id.WeightUnstrung)).setText(String.format(Locale.ENGLISH,  "%.2f", item.getWeightUnstrung()));
			    	((EditText) findViewById(R.id.WeightStrung)).setText(String.format(Locale.ENGLISH,  "%.2f", item.getWeightStrung()));
			    	((EditText) findViewById(R.id.Balance)).setText(String.format(Locale.ENGLISH,  "%.2f", item.getBalance()));
			    	((EditText) findViewById(R.id.Swingweight)).setText(String.format(Locale.ENGLISH,  "%.2f", item.getSwingweight()));
			    	((EditText) findViewById(R.id.Stiffness)).setText(String.format(Locale.ENGLISH,  "%.2f", item.getStiffness()));
			    	EditText BeamWidth = ((EditText) findViewById(R.id.BeamWidth));
		    		BeamWidth.setText(item.getBeamWidth());
		    		
			    	((EditText) findViewById(R.id.Note)).setText(item.getNote());
	    		}
		    	
		    	populateSpinner();
		    	
	    	}catch(Exception e){}
		}
	    
	
		private void populateSpinner() {
			
			ArrayAdapter<TblBrands> spinnerAdapterTblBrands= new ArrayAdapter<TblBrands>(RacquetDataActivity.this,
		            R.layout.simple_spinner_item, listTblBrands);
			spinnerAdapterTblBrands.setDropDownViewResource(R.layout.simple_spinner_dropdown_item);
		    spinBrandName.setAdapter(spinnerAdapterTblBrands);
		    int id = item.getTblBrands();
		    for(int i = 0; i < spinnerAdapterTblBrands.getCount(); i++) {
		    	if (spinnerAdapterTblBrands.getItem(i).getId() == id){
		    		spinBrandName.setSelection(i);
					break;
				}
			}
		    spinBrandName.setEnabled(false);
			
		    ArrayAdapter<TblRacquetsPattern> spinnerTblRacquetsPattern= new ArrayAdapter<TblRacquetsPattern>(RacquetDataActivity.this,
		            R.layout.simple_spinner_item, listTblRacquetsPattern);
		    spinnerTblRacquetsPattern.setDropDownViewResource(R.layout.simple_spinner_dropdown_item);
		    spinPattern.setAdapter(spinnerTblRacquetsPattern);
		    id = item.getTblRacquetsPattern();
		    for(int i = 0; i < spinnerTblRacquetsPattern.getCount(); i++) {
		    	if (spinnerTblRacquetsPattern.getItem(i).getId() == id){
		    		spinPattern.setSelection(i);
					break;
				}
			}
		    spinPattern.setEnabled(false);
		    
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
	        pDialog = new ProgressDialog(RacquetDataActivity.this);
	        pDialog.setMessage(getResources().getString(R.string.wait_save));
	        pDialog.setCancelable(false);
	        pDialog.show();
	    }
	 
	    @Override
	    protected Void doInBackground(Void... arg0) {
	    	try {
	    		if(!NEW)
	    			return_type = function.editRacquet(getResources().getString(R.string.URL), item);
	    		else
	    			return_type = function.saveRacquet(getResources().getString(R.string.URL), item);
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
		        setResult(RESULT_OK, output);
		        finish();
	        }else{
	        	AlertDialog.Builder builder1 = new AlertDialog.Builder(RacquetDataActivity.this);
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


