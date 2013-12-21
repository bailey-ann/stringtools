package it.napalm.stringtools.user;


import java.text.ParseException;
import java.util.ArrayList;

import org.json.JSONException;

import it.napalm.stringtools.R;
import it.napalm.stringtools.adapter.CustomerRacquetAdapter;
import it.napalm.stringtools.globalobject.CustomerRacquet;
import it.napalm.stringtools.object.PositionMenu;
import it.napalm.stringtools.object.TblRacquets;
import it.napalm.stringtools.object.TblRacquetsUser;
import it.napalm.stringtools.object.TblUsers;
import it.napalm.stringtools.utils.UserFunctions;
import android.app.Activity;
import android.app.ProgressDialog;
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
import android.widget.AdapterView.OnItemClickListener;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ListView;

public class CustomerRacquetActivity extends Activity implements OnItemSelectedListener  {

	private static final int MOD_DATA_RACQUET = 1;
	
	private UserFunctions function;
	private TblUsers customer;
	private ArrayList<CustomerRacquet> listRacquet;
	private ListView list;
	private CustomerRacquetAdapter adapter;
	private int position;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.customer_racquet_list);
		getActionBar().setDisplayHomeAsUpEnabled(true);
		
		StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
	    StrictMode.setThreadPolicy(policy);
	    
	    Intent intent=getIntent();
	    	    
	    function = new UserFunctions();
	    customer = (TblUsers)intent.getSerializableExtra("customer");
	    position = intent.getIntExtra("position", PositionMenu.CUSTOMERS_LIST_RACQUET);
	    
	    getActionBar().setTitle(getResources().getString(R.string.list_customer_racquet));
        getActionBar().setSubtitle(customer.getName() + " " + customer.getSurname());
	    
	    new GetListCustomerRacquet().execute();
	   
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
	    // Inflate the menu items for use in the action bar
	    MenuInflater inflater = getMenuInflater();
	    inflater.inflate(R.menu.menu_ah, menu);
	    return super.onCreateOptionsMenu(menu);
	}
	
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
	    // Handle presses on the action bar items
	    switch (item.getItemId()) {
	        case android.R.id.home:
	        	finish();
	            return true;
	        default:
	            return super.onOptionsItemSelected(item);
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
	
	private void populateList() {
		list = (ListView)findViewById(R.id.listCustomerRacquet);
		 
        adapter = new CustomerRacquetAdapter(CustomerRacquetActivity.this, listRacquet, this.position);
        list.setAdapter(adapter);
 
        // Click event for single list row
        list.setOnItemClickListener(new OnItemClickListener() {
 

			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
					long arg3) {
				// TODO Auto-generated method stub
				CustomerRacquet customerRacquet = listRacquet.get(arg2);
				Intent editCustomerRacquet = new Intent(CustomerRacquetActivity.this, EditCustomerRacquetActivity.class);
				editCustomerRacquet.putExtra("customerracquet", customerRacquet);
				editCustomerRacquet.putExtra("position", PositionMenu.CUSTOMERS_LIST_RACQUET);
				startActivityForResult(editCustomerRacquet, MOD_DATA_RACQUET);
			}
        });
	}
	
	private class GetListCustomerRacquet extends AsyncTask<Void, Void, Void> {
		 
		ProgressDialog pDialog;
		
	    @Override
	    protected void onPreExecute() {
	        super.onPreExecute();
	        pDialog = new ProgressDialog(CustomerRacquetActivity.this);
	        pDialog.setMessage(getResources().getString(R.string.wait_load));
	        pDialog.setCancelable(false);
	        pDialog.show();
	 
	    }
	 
	    @Override
	    protected Void doInBackground(Void... arg0) {
	    	
	    	
	    	try {
	    		try {
	    			listRacquet = new ArrayList<CustomerRacquet>();
	    			ArrayList<TblRacquetsUser> listCustomerRacquet = 
	    					function.getListCustomerRacquet(getResources().getString(R.string.URL), customer.getId());
	    			
	    			for (TblRacquetsUser racquetUser : listCustomerRacquet){
	    				CustomerRacquet customerRacquet = new CustomerRacquet();
	    				customerRacquet.setTblRacquetsUser(racquetUser);
	    				TblRacquets tblRacquet = function.getListRacquet(getResources().getString(R.string.URL), racquetUser.getTblRacquets()).get(0);
	    				customerRacquet.setTblRacquets(tblRacquet);
	    				customerRacquet.setTblBrands(function.getBrands(getResources().getString(R.string.URL), tblRacquet.getTblBrands()).get(0));
	    				customerRacquet.setTblRacquetsPattern(function.getRacquetsPattern(getResources().getString(R.string.URL), tblRacquet.getTblRacquetsPattern()).get(0));
	    				customerRacquet.setTblGripSize(function.getGripSize(getResources().getString(R.string.URL), racquetUser.getTblGripSize()).get(0));
	    				listRacquet.add(customerRacquet);
	    			}
				} catch (ParseException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
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
	        
	        populateList();
	    }
	}

	protected void onActivityResult(int requestCode, int resultCode, Intent data){
        switch (requestCode) {
            case MOD_DATA_RACQUET:                      	
            	if (resultCode == RESULT_OK){
            		new GetListCustomerRacquet().execute();
                } 
            default:
                break;
        }
    }
}


