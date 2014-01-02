package it.napalm.stringtools.settings;

import java.text.ParseException;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Locale;

import org.json.JSONException;

import it.napalm.stringtools.R;
import it.napalm.stringtools.object.TblBrands;
import it.napalm.stringtools.object.TblGauges;
import it.napalm.stringtools.object.TblStringType;
import it.napalm.stringtools.object.TblStringingMachineType;
import it.napalm.stringtools.object.TblStringingMachines;
import it.napalm.stringtools.object.TblStrings;
import it.napalm.stringtools.object.TblUsers;
import it.napalm.stringtools.utils.Function;
import it.napalm.stringtools.utils.HttpFunctions;
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

public class StringingMachineDataActivity extends Activity  {

	private HttpFunctions function;
	private int id;
	private TblUsers stringer;
	private TblStringingMachines item;
	private boolean NEW;
	private ArrayList<TblBrands> listTblBrands;
	private ArrayList<TblStringingMachineType> listTblStringingMachineType;
	private Spinner spinTblBrands;
	private Spinner spinTblStringingMachineType;
	
	protected TextView mDBDisplay;
    protected Button mPickDB;
    protected int mYearDB;
    protected int mMonthDB;
    protected int mDayDB;
    
    protected TextView mDCDisplay;
    protected Button mPickDC;
    protected int mYearDC;
    protected int mMonthDC;
    protected int mDayDC;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.stringing_machine_data);
		getActionBar().setDisplayHomeAsUpEnabled(true);
		
		StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
	    StrictMode.setThreadPolicy(policy);
	    
	    spinTblBrands = (Spinner) findViewById(R.id.spinBrandName);
	    spinTblStringingMachineType = (Spinner) findViewById(R.id.spinType);

	    function = new HttpFunctions();
	    listTblBrands = new ArrayList<TblBrands>();
	    listTblStringingMachineType = new ArrayList<TblStringingMachineType>();
	    
	    Intent intent=getIntent();
	    id = intent.getIntExtra("id", 0);
	    stringer = (TblUsers)intent.getSerializableExtra("stringer");
	    
	    if(id != 0){
        	setTitle(R.string.edit_stringing_machine, "");        	
        	NEW = false;
        }else{
        	setTitle(R.string.new_stringing_machine, "");
        	NEW = true;
        }
	    
	    mDBDisplay = (TextView) findViewById(R.id.DateBuy);
        mPickDB = (Button) findViewById(R.id.buttonDateBuy);
        
        mPickDB.setOnClickListener(new View.OnClickListener() {
            @SuppressWarnings("deprecation")
			public void onClick(View v) {
                showDialog(0);
            }
        });
        
        mDCDisplay = (TextView) findViewById(R.id.DateCalibration);
        mPickDC = (Button) findViewById(R.id.buttonDateCalibration);
        
        mPickDC.setOnClickListener(new View.OnClickListener() {
            @SuppressWarnings("deprecation")
			public void onClick(View v) {
                showDialog(0);
            }
        });

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
			item = new TblStrings();
			item.setId(0);
		}
		TblBrands tblBrands = (TblBrands) spinTblBrands.getItemAtPosition(spinTblBrands.getSelectedItemPosition());
		item.setTblBrands(tblBrands.getId());
		TblGauges tlGauges = (TblGauges) spinTblGauges.getItemAtPosition(spinTblGauges.getSelectedItemPosition());
		item.setTblGauges(tlGauges.getId());
		TblStringType tblStringType = (TblStringType) spinTblStringType.getItemAtPosition(spinTblStringType.getSelectedItemPosition());
		item.setTblStringType(tblStringType.getId());
		item.setModel(((EditText) findViewById(R.id.Model)).getText().toString());
		item.setCode(((EditText) findViewById(R.id.Code)).getText().toString());		
		item.setExactGauge(Function.stringToDouble( ((EditText) findViewById(R.id.ExactGauges)).getText().toString()));
		item.setPrice(Function.stringToDouble( ((EditText) findViewById(R.id.Price)).getText().toString()));
		
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
	        pDialog = new ProgressDialog(StringingMachineDataActivity.this);
	        pDialog.setMessage(getResources().getString(R.string.wait_load));
	        pDialog.setCancelable(false);
	        pDialog.show();
	 
	    }
	 
	    @Override
	    protected Void doInBackground(Void... arg0) {
	    	try {
	    		if(!NEW)
	    			item = function.getListStringMachines(getResources().getString(R.string.URL), stringer.getId(), id).get(0);
				listTblBrands = function.getBrands(getResources().getString(R.string.URL), 0);
				listTblStringingMachineType = function.getStriningMachineType(getResources().getString(R.string.URL), 0);
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
		    		EditText serial = ((EditText) findViewById(R.id.Serial));
		    		serial.setText(item.getSerial());
		    		EditText note = ((EditText) findViewById(R.id.Note));
		    		note.setText(item.getNote());
		    		
		    		Calendar calendar = Calendar.getInstance();  
			        calendar.setTime(item.getDateBuy());  
			        mYearDB = calendar.get(Calendar.YEAR);
			        mMonthDB = calendar.get(Calendar.MONTH);
			        mDayDB = calendar.get(Calendar.DAY_OF_MONTH);
			        
			        updateDBDisplay();
			        
			        calendar = Calendar.getInstance();  
			        calendar.setTime(item.getDateCalibration());  
			        mYearDC = calendar.get(Calendar.YEAR);
			        mMonthDC = calendar.get(Calendar.MONTH);
			        mDayDC = calendar.get(Calendar.DAY_OF_MONTH);
			        
			        updateDCDisplay();
	    		}
		    	
		    	populateSpinner();
		    	
	    	}catch(Exception e){}
		}
	    
	
		private void populateSpinner() {
			
			ArrayAdapter<TblBrands> spinnerAdapterTblBrands= new ArrayAdapter<TblBrands>(StringingMachineDataActivity.this,
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
			
		    ArrayAdapter<TblStringingMachineType> spinnerTblStringingMachineType = new ArrayAdapter<TblStringingMachineType>(StringingMachineDataActivity.this,
		            R.layout.simple_spinner_item, listTblStringingMachineType);
		    spinnerTblStringingMachineType.setDropDownViewResource(R.layout.simple_spinner_dropdown_item);
		    spinTblStringingMachineType.setAdapter(spinnerTblStringingMachineType);
		    id = item.getTblStringingMachineType();
		    for(int i = 0; i < spinnerTblStringingMachineType.getCount(); i++) {
		    	if (spinnerTblStringingMachineType.getItem(i).getId() == id){
		    		spinTblStringingMachineType.setSelection(i);
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
	        pDialog = new ProgressDialog(StringingMachineDataActivity.this);
	        pDialog.setMessage(getResources().getString(R.string.wait_save));
	        pDialog.setCancelable(false);
	        pDialog.show();
	    }
	 
	    @Override
	    protected Void doInBackground(Void... arg0) {
	    	/*try{
	    		if(NEW)
		    		return_type = function.saveString(getResources().getString(R.string.URL), item, stringer.getId());
		    	else
		    		return_type = function.editString(getResources().getString(R.string.URL), item, stringer.getId());
	    	}catch(Exception e){}*/
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
	        	AlertDialog.Builder builder1 = new AlertDialog.Builder(StringingMachineDataActivity.this);
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
	
	protected void updateDBDisplay() {
        mDBDisplay.setText(
            new StringBuilder()
            		.append(mDayDB).append("-")
                    .append(mMonthDB + 1).append("-")                   
                    .append(mYearDB).append(""));
    }
	
	protected void updateDCDisplay() {
        mDCDisplay.setText(
            new StringBuilder()
            		.append(mDayDC).append("-")
                    .append(mMonthDC + 1).append("-")                   
                    .append(mYearDC).append(""));
    }
	
	protected Dialog onCreateDialogDB(int id) {
        return new DatePickerDialog(this,
                    mDBSetListener,
                    mYearDB, mMonthDB, mDayDB);
	}
	
	protected Dialog onCreateDialogDC(int id) {
        return new DatePickerDialog(this,
                    mDCSetListener,
                    mYearDC, mMonthDC, mDayDC);
	}
	
	 protected DatePickerDialog.OnDateSetListener mDBSetListener =
		        new DatePickerDialog.OnDateSetListener() {


					@Override
					public void onDateSet(DatePicker view, int year,
							int monthOfYear, int dayOfMonth) {
						mYearDB = year;
		                mMonthDB = monthOfYear;
		                mDayDB = dayOfMonth;
		                updateDBDisplay();
						
					}
		    };
		    
		    protected DatePickerDialog.OnDateSetListener mDCSetListener =
			        new DatePickerDialog.OnDateSetListener() {


						@Override
						public void onDateSet(DatePicker view, int year,
								int monthOfYear, int dayOfMonth) {
							mYearDC = year;
			                mMonthDC = monthOfYear;
			                mDayDC = dayOfMonth;
			                updateDCDisplay();
							
						}
			    };
		    
}


