package it.napalm.stringtools.settings;



import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.text.DecimalFormat;
import java.text.ParseException;
import java.util.ArrayList;
import java.util.Random;

import org.json.JSONException;

import it.napalm.stringtools.R;
import it.napalm.stringtools.object.PositionMenu;
import it.napalm.stringtools.object.TblBrands;
import it.napalm.stringtools.object.TblGripSize;
import it.napalm.stringtools.object.TblGrips;
import it.napalm.stringtools.object.TblUsers;
import it.napalm.stringtools.user.NewCustomerRacquetActivity;
import it.napalm.stringtools.utils.Function;
import it.napalm.stringtools.utils.HttpFunctions;
import android.R.integer;
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
import android.util.Log;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.inputmethod.InputMethodManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.TextView;

public class GripDataActivity extends Activity implements OnItemSelectedListener  {

	private TblGrips item = null;
	private int id = 0;
	private HttpFunctions function;
	private int position;
	private boolean NEW = false;
	private ArrayList<TblBrands> listTblBrands;
	private Spinner spinBrandName ;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.grip_data);
		getActionBar().setDisplayHomeAsUpEnabled(true);
		
		StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
	    StrictMode.setThreadPolicy(policy);
	    	    
	    function = new HttpFunctions();
        Intent intent=getIntent();
        id = intent.getIntExtra("id", 0);
        position = intent.getIntExtra("position", 0);
        spinBrandName = (Spinner) findViewById(R.id.spinBrandName);
        Log.d("id", id+"");
        if(id != 0){
        	setTitle(R.string.edit_grip, "");
        	
        	NEW = false;
        }else{
        	setTitle(R.string.new_grip, "");
        	NEW = true;
        }
        
        new SetGui().execute();
	}
	
	public void setData(){
		ArrayAdapter<TblBrands> spinnerAdapterTblBrands= new ArrayAdapter<TblBrands>(GripDataActivity.this,
	            R.layout.simple_spinner_item, listTblBrands);
		spinnerAdapterTblBrands.setDropDownViewResource(R.layout.simple_spinner_dropdown_item);
	    spinBrandName.setAdapter(spinnerAdapterTblBrands);
	    if(!NEW){
		    int id = item.getIdTblBrands();
		    for(int i = 0; i < spinnerAdapterTblBrands.getCount(); i++) {
		    	if (spinnerAdapterTblBrands.getItem(i).getId() == id){
		    		spinBrandName.setSelection(i);
					break;
				}
			}
		    
		    ((EditText) findViewById(R.id.model)).setText(item.getModel());	
		    ((EditText) findViewById(R.id.cost)).setText(String.format( "%.2f", item.getPrice()));
		    ((EditText) findViewById(R.id.Note)).setText(item.getNote());
	    }
		
		
		
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
	            saveData();
	            return true;
	        case android.R.id.home:
	        	backTo();
	            return true;
	        default:
	            return super.onOptionsItemSelected(item);
	    }
	}
	
	private void saveData() {
		
		if(NEW){
			item = new TblGrips();
		}
		TblBrands tblBrands = (TblBrands) spinBrandName.getItemAtPosition(spinBrandName.getSelectedItemPosition());
		item.setIdTblBrands(tblBrands.getId());
		item.setModel(((EditText) findViewById(R.id.model)).getText().toString());
		item.setPrice( Function.stringToDouble(((EditText) findViewById(R.id.cost)).getText().toString()) );
		item.setNote(((EditText) findViewById(R.id.Note)).getText().toString());
		
		new SaveData().execute(NEW);
	}


	@Override
    public void onItemSelected(AdapterView<?> parent, View view, int position,
            long id) {

 
    }


	@Override
	public void onNothingSelected(AdapterView<?> arg0) {
		// TODO Auto-generated method stub
		
	}
	
	private class SetGui extends AsyncTask<Void, Void, Void> {
		private ProgressDialog pDialog ;
	    @Override
	    protected void onPreExecute() {
	        super.onPreExecute();	
	        pDialog = new ProgressDialog(GripDataActivity.this);
	        pDialog.setMessage(getResources().getString(R.string.wait_load));
	        pDialog.setCancelable(false);
	        pDialog.show();
	    }
	 
	    @Override
	    protected Void doInBackground(Void... arg0) {
	    	try {
				item = function.getListGrips(getResources().getString(R.string.URL), id).get(0);
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
	        return null;
	    }
	 
	    @Override
	    protected void onPostExecute(Void result) {
	        super.onPostExecute(result);
	        Log.d("item", item.getModel());
	        setData();
	        if (pDialog.isShowing())
	            pDialog.dismiss();
	    }
	 
	}
	
	private class SaveData extends AsyncTask<Boolean, Void, Void> {
		private ProgressDialog pDialog ;
		private String return_type = "";
	    @Override
	    protected void onPreExecute() {
	        super.onPreExecute();	
	        pDialog = new ProgressDialog(GripDataActivity.this);
	        pDialog.setMessage(getResources().getString(R.string.wait_save));
	        pDialog.setCancelable(false);
	        pDialog.show();
	    }
	 
	    @Override
	    protected Void doInBackground(Boolean... arg0) {
	    	try {
	    		if(!arg0[0])
					try {
						return_type = function.editDataGrip(getResources().getString(R.string.URL), item);
					} catch (JSONException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
				else
					try {
						return_type = function.newGrip(getResources().getString(R.string.URL), item);
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
		        output.putExtra("position", position);
		        setResult(RESULT_OK, output);
		        finish();
	        }else{
	        	AlertDialog.Builder builder1 = new AlertDialog.Builder(GripDataActivity.this);
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
		InputMethodManager imm = (InputMethodManager)getSystemService(Context.INPUT_METHOD_SERVICE);
		imm.hideSoftInputFromWindow(((EditText) findViewById(R.id.model)).getWindowToken(), 0);
		Intent output = new Intent();
        output.putExtra("position", PositionMenu.GRIPS_LIST);
        setResult(RESULT_CANCELED, output);
        finish();
	}
	
	@Override
	public void onBackPressed() {
	    backTo();
	}
	
	public void setTitle(int title,CharSequence subTitle) {
        getActionBar().setTitle(title);
        getActionBar().setSubtitle(subTitle);
    }
}


