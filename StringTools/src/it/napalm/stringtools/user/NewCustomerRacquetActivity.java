package it.napalm.stringtools.user;

import java.text.DecimalFormat;
import java.text.ParseException;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;

import org.json.JSONException;

import it.napalm.stringtools.R;
import it.napalm.stringtools.globalobject.CustomerRacquet;
import it.napalm.stringtools.globalobject.Racquet;
import it.napalm.stringtools.object.TblBrands;
import it.napalm.stringtools.object.TblGripSize;
import it.napalm.stringtools.object.TblRacquets;
import it.napalm.stringtools.object.TblRacquetsPattern;
import it.napalm.stringtools.object.TblRacquetsUser;
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
import android.util.Log;
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

public class NewCustomerRacquetActivity extends Activity  {

	private HttpFunctions function;
	private TblUsers customer;
	private TblRacquets racquet;
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
	    customer = (TblUsers)intent.getSerializableExtra("customer");
	    try {
			racquet = function.getListRacquet(getResources().getString(R.string.URL), (int)intent.getIntExtra("idracquet", 0)).get(0);
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
	    
	    getActionBar().setTitle(getResources().getString(R.string.newcustomerracquet));
        getActionBar().setSubtitle("");
	    
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
	        case R.id.save:
	        	saveRacquetCustomer();
	            return true;
	        default:
	            return super.onOptionsItemSelected(item);
	    }
	}
	
	private void saveRacquetCustomer(){
		TblRacquetsUser racquetUser = new TblRacquetsUser();
		racquetUser.setTblRacquets(racquet.getId());
		racquetUser.setTblUsers(customer.getId());
		TblGripSize tblGripSize = (TblGripSize) spinGripSize.getItemAtPosition(spinGripSize.getSelectedItemPosition());
		racquetUser.setTblGripSize(tblGripSize.getId());
		racquetUser.setSerial(((EditText) findViewById(R.id.Serial)).getText().toString());
		racquetUser.setWeightUnstrung(Function.stringToDouble( ((EditText) findViewById(R.id.WeightUnstrung)).getText().toString()));	
		racquetUser.setWeightStrung(Function.stringToDouble( ((EditText) findViewById(R.id.WeightStrung)).getText().toString()));
		racquetUser.setBalance(Function.stringToDouble( ((EditText) findViewById(R.id.Balance)).getText().toString()));
		racquetUser.setSwingweight(Function.stringToDouble( ((EditText) findViewById(R.id.Swingweight)).getText().toString()));
		racquetUser.setStiffness(Function.stringToDouble( ((EditText) findViewById(R.id.Stiffness)).getText().toString()));
		racquetUser.setDateBuy(Function.stringToDateShort(((TextView) findViewById(R.id.DateBuy)).getText().toString()));		
		racquetUser.setNote(((EditText) findViewById(R.id.Note)).getText().toString());
		racquetUser.setActive(1);
		new SaveDataRacquet().execute(racquetUser);
	}
	
	
	private void backTo(){
		InputMethodManager imm = (InputMethodManager)getSystemService(Context.INPUT_METHOD_SERVICE);
		imm.hideSoftInputFromWindow(((EditText) findViewById(R.id.Serial)).getWindowToken(), 0);
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
	        pDialog = new ProgressDialog(NewCustomerRacquetActivity.this);
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
	    		model.setText(racquet.getModel());
	    		model.setEnabled(false);
	    		EditText HeadSize = ((EditText) findViewById(R.id.HeadSize));
	    		HeadSize.setText(format.format(racquet.getHeadSize()));
	    		HeadSize.setEnabled(false);
	    		EditText Length = ((EditText) findViewById(R.id.Length));
	    		Length.setText(format.format(racquet.getLength()));
	    		Length.setEnabled(false);
	    		
		    	((EditText) findViewById(R.id.Serial)).setText("");
		    	((EditText) findViewById(R.id.WeightUnstrung)).setText(format.format(racquet.getWeightUnstrung()));
		    	((EditText) findViewById(R.id.WeightStrung)).setText(format.format(racquet.getWeightStrung()));
		    	((EditText) findViewById(R.id.Balance)).setText(format.format(racquet.getBalance()));
		    	((EditText) findViewById(R.id.Swingweight)).setText(format.format(racquet.getSwingweight()));
		    	((EditText) findViewById(R.id.Stiffness)).setText(format.format(racquet.getStiffness()));
		    	EditText BeamWidth = ((EditText) findViewById(R.id.BeamWidth));
	    		BeamWidth.setText(racquet.getBeamWidth());
	    		BeamWidth.setEnabled(false);
	    		
		    	((EditText) findViewById(R.id.Note)).setText("");
		    	
		    	Calendar calendar = Calendar.getInstance();  
		        calendar.setTime(new Date());  
		        mYear = calendar.get(Calendar.YEAR);
		        mMonth = calendar.get(Calendar.MONTH);
		        mDay = calendar.get(Calendar.DAY_OF_MONTH);
		        
		        updateDateDisplay();
		    	
		    	
				try {
					listTblBrands = function.getBrands(getResources().getString(R.string.URL), 0);
					listTblRacquetsPattern = function.getRacquetsPattern(getResources().getString(R.string.URL), 0);
					listTblGripSize = function.getGripSize(getResources().getString(R.string.URL), 0);
				} catch (NotFoundException e) {
					Log.d("racquet",e.getMessage());
					e.printStackTrace();
				} catch (JSONException e) {
					Log.d("racquet",e.getMessage());
					e.printStackTrace();
				} catch (ParseException e) {
					Log.d("racquet",e.getMessage());
					e.printStackTrace();
				}
		    	
	    	}catch(Exception e){
	    		Log.d("racquet",e.getMessage());
	    	}
		}
	    
	
		private void populateSpinner() {
			
			ArrayAdapter<TblBrands> spinnerAdapterTblBrands= new ArrayAdapter<TblBrands>(NewCustomerRacquetActivity.this,
		            R.layout.simple_spinner_item, listTblBrands);
			spinnerAdapterTblBrands.setDropDownViewResource(R.layout.simple_spinner_dropdown_item);
		    spinBrandName.setAdapter(spinnerAdapterTblBrands);
		    int id = racquet.getTblBrands();
		    for(int i = 0; i < spinnerAdapterTblBrands.getCount(); i++) {
		    	if (spinnerAdapterTblBrands.getItem(i).getId() == id){
		    		spinBrandName.setSelection(i);
					break;
				}
			}
		    spinBrandName.setEnabled(false);
			
		    ArrayAdapter<TblRacquetsPattern> spinnerTblRacquetsPattern= new ArrayAdapter<TblRacquetsPattern>(NewCustomerRacquetActivity.this,
		            R.layout.simple_spinner_item, listTblRacquetsPattern);
		    spinnerTblRacquetsPattern.setDropDownViewResource(R.layout.simple_spinner_dropdown_item);
		    spinPattern.setAdapter(spinnerTblRacquetsPattern);
		    id = racquet.getTblRacquetsPattern();
		    for(int i = 0; i < spinnerTblRacquetsPattern.getCount(); i++) {
		    	if (spinnerTblRacquetsPattern.getItem(i).getId() == id){
		    		spinPattern.setSelection(i);
					break;
				}
			}
		    spinPattern.setEnabled(false);
		    
		    ArrayAdapter<TblGripSize> spinnerTblGripSize= new ArrayAdapter<TblGripSize>(NewCustomerRacquetActivity.this,
		            R.layout.simple_spinner_item, listTblGripSize);
		    spinnerTblGripSize.setDropDownViewResource(R.layout.simple_spinner_dropdown_item);
		    spinGripSize.setAdapter(spinnerTblGripSize);
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
	        pDialog = new ProgressDialog(NewCustomerRacquetActivity.this);
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
	        	AlertDialog.Builder builder1 = new AlertDialog.Builder(NewCustomerRacquetActivity.this);
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
	
	private class DeleteRacquet extends AsyncTask<TblRacquetsUser, Void, Void> {
		private ProgressDialog pDialog ;
		private String return_type = "";
	    @Override
	    protected void onPreExecute() {
	        super.onPreExecute();	
	        pDialog = new ProgressDialog(NewCustomerRacquetActivity.this);
	        pDialog.setMessage(getResources().getString(R.string.wait_save));
	        pDialog.setCancelable(false);
	        pDialog.show();
	    }
	 
	    @Override
	    protected Void doInBackground(TblRacquetsUser... arg0) {
	    	try {
	    		return_type = function.removeRacquetCustomer(getResources().getString(R.string.URL), arg0[0]);
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
	        	AlertDialog.Builder builder1 = new AlertDialog.Builder(NewCustomerRacquetActivity.this);
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


