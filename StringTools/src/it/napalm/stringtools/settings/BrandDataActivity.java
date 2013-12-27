package it.napalm.stringtools.settings;



import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.text.DecimalFormat;
import java.util.Random;

import org.json.JSONException;

import it.napalm.stringtools.R;
import it.napalm.stringtools.object.PositionMenu;
import it.napalm.stringtools.object.TblBrands;
import it.napalm.stringtools.object.TblUsers;
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
import android.view.View;
import android.view.inputmethod.InputMethodManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.EditText;
import android.widget.TextView;

public class BrandDataActivity extends Activity implements OnItemSelectedListener  {

	private TblBrands item = null;
	private HttpFunctions function;
	private int position;
	private boolean NEW = false;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.brand_data);
		getActionBar().setDisplayHomeAsUpEnabled(true);
		
		StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
	    StrictMode.setThreadPolicy(policy);
	    	    
	    function = new HttpFunctions();
        Intent intent=getIntent();
        item = (TblBrands)intent.getSerializableExtra("brand");
        position = intent.getIntExtra("position", 0);
        
        if(item != null){
        	setTitle(R.string.edit_brand, "");
        	setData();
        	NEW = false;
        }else{
        	setTitle(R.string.new_brand, "");
        	NEW = true;
        }
	}
	
	public void setData(){
		((EditText) findViewById(R.id.item)).setText(item.getDescription());	
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
			item = new TblBrands();
		}
		item.setDescription(((EditText) findViewById(R.id.item)).getText().toString());
		
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
	
	private class SaveData extends AsyncTask<Boolean, Void, Void> {
		private ProgressDialog pDialog ;
		private String return_type = "";
	    @Override
	    protected void onPreExecute() {
	        super.onPreExecute();	
	        pDialog = new ProgressDialog(BrandDataActivity.this);
	        pDialog.setMessage(getResources().getString(R.string.wait_save));
	        pDialog.setCancelable(false);
	        pDialog.show();
	    }
	 
	    @Override
	    protected Void doInBackground(Boolean... arg0) {
	    	try {
	    		if(!arg0[0])
					try {
						return_type = function.editDataBrand(getResources().getString(R.string.URL), item);
					} catch (JSONException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
				else
					try {
						return_type = function.newBrand(getResources().getString(R.string.URL), item);
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
	        	AlertDialog.Builder builder1 = new AlertDialog.Builder(BrandDataActivity.this);
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
		imm.hideSoftInputFromWindow(((EditText) findViewById(R.id.item)).getWindowToken(), 0);
		Intent output = new Intent();
        output.putExtra("position", PositionMenu.CUSTOMERS);
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


