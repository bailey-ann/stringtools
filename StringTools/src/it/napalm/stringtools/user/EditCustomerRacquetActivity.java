package it.napalm.stringtools.user;

import java.text.DecimalFormat;
import java.text.ParseException;
import java.util.ArrayList;
import java.util.Calendar;

import org.json.JSONException;

import it.napalm.stringtools.R;
import it.napalm.stringtools.globalobject.CustomerRacquet;
import it.napalm.stringtools.object.TblBrands;
import it.napalm.stringtools.object.TblGripSize;
import it.napalm.stringtools.object.TblRacquetsPattern;
import it.napalm.stringtools.object.TblRacquetsUser;
import it.napalm.stringtools.utils.Function;
import it.napalm.stringtools.utils.HttpFunctions;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.DatePickerDialog;
import android.app.Dialog;
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
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.TextView;

public class EditCustomerRacquetActivity extends Activity  {

	private HttpFunctions function;
	private CustomerRacquet customerRacquet;
	private ArrayList<TblBrands> listTblBrands;
	private ArrayList<TblRacquetsPattern> listTblRacquetsPattern;
	private ArrayList<TblGripSize> listTblGripSize;
	private Spinner spinBrandName ;
	private Spinner spinPattern ;
	private Spinner spinGripSize ;
	
	protected TextView mDateDisplay;
    protected Button mPickDate;
    protected int mYear;
    protected int mMonth;
    protected int mDay;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.customer_racquet_data);
		getActionBar().setDisplayHomeAsUpEnabled(true);
		
		StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
	    StrictMode.setThreadPolicy(policy);
	    
	    spinBrandName = (Spinner) findViewById(R.id.spinBrandName);
	    spinPattern = (Spinner) findViewById(R.id.spinPattern);
	    spinGripSize = (Spinner) findViewById(R.id.spinGripSize);
	    function = new HttpFunctions();
	    listTblBrands = new ArrayList<TblBrands>();
	    listTblRacquetsPattern = new ArrayList<TblRacquetsPattern>();
	    listTblGripSize = new ArrayList<TblGripSize>();
	    
	    Intent intent=getIntent();
	    customerRacquet = (CustomerRacquet)intent.getSerializableExtra("customerracquet");
	    
	    getActionBar().setTitle(getResources().getString(R.string.editcustomerracquet));
        getActionBar().setSubtitle(customerRacquet.getTblRacquets().getModel() + " " + customerRacquet.getTblRacquetsUser().getSerial());
	    
        mDateDisplay = (TextView) findViewById(R.id.DateBuy);
        mPickDate = (Button) findViewById(R.id.buttonSelectTime);
        
        mPickDate.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {
                showDialog(0);
            }
        });
        
        
	    
	    
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
	        	updateRacquetCustomer();
	            return true;
	        case R.id.remove:
	            //setResult(RESULT_OK, null);
	        	//finish();
	            return true;
	        default:
	            return super.onOptionsItemSelected(item);
	    }
	}
	
	private void updateRacquetCustomer(){
		TblRacquetsUser racquet = customerRacquet.getTblRacquetsUser();
		TblGripSize tblGripSize = (TblGripSize) spinGripSize.getItemAtPosition(spinGripSize.getSelectedItemPosition());
		racquet.setTblGripSize(tblGripSize.getId());
		racquet.setSerial(((EditText) findViewById(R.id.Serial)).getText().toString());
		racquet.setWeightUnstrung(Function.stringToDouble( ((EditText) findViewById(R.id.WeightUnstrung)).getText().toString()));	
		racquet.setWeightStrung(Function.stringToDouble( ((EditText) findViewById(R.id.WeightStrung)).getText().toString()));
		racquet.setBalance(Function.stringToDouble( ((EditText) findViewById(R.id.Balance)).getText().toString()));
		racquet.setSwingweight(Function.stringToDouble( ((EditText) findViewById(R.id.Swingweight)).getText().toString()));
		racquet.setStiffness(Function.stringToDouble( ((EditText) findViewById(R.id.Stiffness)).getText().toString()));
		racquet.setDateBuy(Function.stringToDateShort(((TextView) findViewById(R.id.DateBuy)).getText().toString()));		
		racquet.setNote(((EditText) findViewById(R.id.Note)).getText().toString());
		
		new SaveDataRacquet().execute(racquet);
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
	    		TextView lblBrandName = ((TextView) findViewById(R.id.lblBrandName));
	    		lblBrandName.setText(lblBrandName.getText() + "   " + getResources().getString(R.string.onlyread));
	    		TextView lblPattern = ((TextView) findViewById(R.id.lblPattern));
	    		lblPattern.setText(lblPattern.getText() + "   " + getResources().getString(R.string.onlyread));
	    		TextView lblModel = ((TextView) findViewById(R.id.lblModel));
	    		lblModel.setText(lblModel.getText() + "   " + getResources().getString(R.string.onlyread));
	    		TextView lblHeadSize = ((TextView) findViewById(R.id.lblHeadSize));
	    		lblHeadSize.setText(lblHeadSize.getText() + "   " + getResources().getString(R.string.onlyread));
	    		TextView lblLength = ((TextView) findViewById(R.id.lblLength));
	    		lblLength.setText(lblLength.getText() + "   " + getResources().getString(R.string.onlyread));
	    		TextView lblBeamWidth = ((TextView) findViewById(R.id.lblBeamWidth));
	    		lblBeamWidth.setText(lblBeamWidth.getText() + "   " + getResources().getString(R.string.onlyread));
	    		
	    		EditText model = ((EditText) findViewById(R.id.Model));
	    		model.setText(customerRacquet.getTblRacquets().getModel());
	    		model.setEnabled(false);
	    		EditText HeadSize = ((EditText) findViewById(R.id.HeadSize));
	    		HeadSize.setText(format.format(customerRacquet.getTblRacquets().getHeadSize()));
	    		HeadSize.setEnabled(false);
	    		EditText Length = ((EditText) findViewById(R.id.Length));
	    		Length.setText(format.format(customerRacquet.getTblRacquets().getLength()));
	    		Length.setEnabled(false);
	    		
		    	((EditText) findViewById(R.id.Serial)).setText(customerRacquet.getTblRacquetsUser().getSerial());
		    	((EditText) findViewById(R.id.WeightUnstrung)).setText(format.format(customerRacquet.getTblRacquetsUser().getWeightUnstrung()));
		    	((EditText) findViewById(R.id.WeightStrung)).setText(format.format(customerRacquet.getTblRacquetsUser().getWeightStrung()));
		    	((EditText) findViewById(R.id.Balance)).setText(format.format(customerRacquet.getTblRacquetsUser().getBalance()));
		    	((EditText) findViewById(R.id.Swingweight)).setText(format.format(customerRacquet.getTblRacquetsUser().getSwingweight()));
		    	((EditText) findViewById(R.id.Stiffness)).setText(format.format(customerRacquet.getTblRacquetsUser().getStiffness()));
		    	EditText BeamWidth = ((EditText) findViewById(R.id.BeamWidth));
	    		BeamWidth.setText(customerRacquet.getTblRacquets().getBeamWidth());
	    		BeamWidth.setEnabled(false);
	    		
		    	((EditText) findViewById(R.id.Note)).setText(customerRacquet.getTblRacquetsUser().getNote());
		    	
		    	Calendar calendar = Calendar.getInstance();  
		        calendar.setTime(customerRacquet.getTblRacquetsUser().getDateBuy());  
		        mYear = calendar.get(Calendar.YEAR);
		        mMonth = calendar.get(Calendar.MONTH);
		        mDay = calendar.get(Calendar.DAY_OF_MONTH);
		        
		        updateDateDisplay();
		    	
		    	
				try {
					listTblBrands = function.getBrands(getResources().getString(R.string.URL), 0);
					listTblRacquetsPattern = function.getRacquetsPattern(getResources().getString(R.string.URL), 0);
					listTblGripSize = function.getGripSize(getResources().getString(R.string.URL), 0);
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
		    spinBrandName.setEnabled(false);
			
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
		    spinPattern.setEnabled(false);
		    
		    ArrayAdapter<TblGripSize> spinnerTblGripSize= new ArrayAdapter<TblGripSize>(EditCustomerRacquetActivity.this,
		            R.layout.simple_spinner_item, listTblGripSize);
		    spinnerTblGripSize.setDropDownViewResource(R.layout.simple_spinner_dropdown_item);
		    spinGripSize.setAdapter(spinnerTblGripSize);
		    id = customerRacquet.getTblRacquetsUser().getTblGripSize();
		    for(int i = 0; i < spinnerTblGripSize.getCount(); i++) {
		    	if (spinnerTblGripSize.getItem(i).getId() == id){
		    		spinGripSize.setSelection(i);
					break;
				}
			}
		    spinGripSize.setEnabled(true);
		}
		
		@Override
	    protected void onPostExecute(Void result) {
	        super.onPostExecute(result);
	        populateSpinner();
	        if (pDialog.isShowing())
	            pDialog.dismiss();
	        
	    }
	}
	
	private class SaveDataRacquet extends AsyncTask<TblRacquetsUser, Void, Void> {
		private ProgressDialog pDialog ;
		private String return_type = "";
	    @Override
	    protected void onPreExecute() {
	        super.onPreExecute();	
	        pDialog = new ProgressDialog(EditCustomerRacquetActivity.this);
	        pDialog.setMessage(getResources().getString(R.string.wait_save));
	        pDialog.setCancelable(false);
	        pDialog.show();
	    }
	 
	    @Override
	    protected Void doInBackground(TblRacquetsUser... arg0) {
	    	try {
	    		return_type = function.editRacquetCustomer(getResources().getString(R.string.URL), arg0[0]);
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
	        	AlertDialog.Builder builder1 = new AlertDialog.Builder(EditCustomerRacquetActivity.this);
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
	
	
	protected void updateDateDisplay() {
        mDateDisplay.setText(
            new StringBuilder()
            		.append(mDay).append("-")
                    .append(mMonth + 1).append("-")                   
                    .append(mYear).append(""));
    }
	
	protected Dialog onCreateDialog(int id) {
        return new DatePickerDialog(this,
                    mDateSetListener,
                    mYear, mMonth, mDay);
	}
	
	 protected DatePickerDialog.OnDateSetListener mDateSetListener =
		        new DatePickerDialog.OnDateSetListener() {


					@Override
					public void onDateSet(DatePicker view, int year,
							int monthOfYear, int dayOfMonth) {
						mYear = year;
		                mMonth = monthOfYear;
		                mDay = dayOfMonth;
		                updateDateDisplay();
						
					}
		    };
}


