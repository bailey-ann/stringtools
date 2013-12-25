package it.napalm.stringtools.user;

import java.text.ParseException;
import java.util.ArrayList;

import org.json.JSONException;

import it.napalm.stringtools.R;
import it.napalm.stringtools.adapter.RacquetAdapter;
import it.napalm.stringtools.globalobject.RacquetText;
import it.napalm.stringtools.object.TblUsers;
import it.napalm.stringtools.utils.HttpFunctions;
import android.app.Activity;
import android.app.ProgressDialog;
import android.app.SearchManager;
import android.content.Context;
import android.content.Intent;
import android.content.res.Resources.NotFoundException;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.StrictMode;
import android.text.TextUtils;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.SearchView;
import android.widget.SearchView.OnQueryTextListener;

public class SelectRacquetActivity extends Activity implements OnItemSelectedListener {

	protected static final int NEW_RACQUET_CUSTOMER = 1;
	private TblUsers customer;
	private ArrayList<RacquetText> listRacquets;
	private HttpFunctions function;
	private ListView list;
	private RacquetAdapter adapter;
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.racquet_list);
		getActionBar().setDisplayHomeAsUpEnabled(true);
		
		StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
	    StrictMode.setThreadPolicy(policy);
	    
	    function = new HttpFunctions();
	    Intent intent=getIntent();
	    customer = (TblUsers)intent.getSerializableExtra("customer");
	    
	    getActionBar().setTitle(getResources().getString(R.string.select_racquet_toadd));
	   
	    
	    new GetListRacquet().execute();
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
	    // Inflate the menu items for use in the action bar
	    MenuInflater inflater = getMenuInflater();
	    inflater.inflate(R.menu.menu_sh, menu);
	    
	 // Get the SearchView and set the searchable configuration
	    //SearchManager searchManager = (SearchManager) getSystemService(Context.SEARCH_SERVICE);
	    SearchView searchView = (SearchView) menu.findItem(R.id.grid_default_search).getActionView();
	    // Assumes current activity is the searchable activity
	    searchView.setOnQueryTextListener(listener);
	    searchView.setIconifiedByDefault(false); // Do not iconify the widget; expand it by default

	    return super.onCreateOptionsMenu(menu);
	}
	
	private OnQueryTextListener listener = new OnQueryTextListener(){

		@Override
		public boolean onQueryTextChange(String newText) {
			if (TextUtils.isEmpty(newText)) {
		        adapter.getFilter().filter("");
		    } else {
		        adapter.getFilter().filter(newText.toString());
		        list.setFilterText(newText.toString());
		    }
		    return true;
		}

		@Override
		public boolean onQueryTextSubmit(String query) {
			// TODO Auto-generated method stub
			return false;
		}
		
	};
	
	
	@Override
	public void onItemSelected(AdapterView<?> arg0, View arg1, int arg2,
			long arg3) {
		// TODO Auto-generated method stub
		
	}

	@Override
	public void onNothingSelected(AdapterView<?> arg0) {
		// TODO Auto-generated method stub
		
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
	
	private class GetListRacquet extends AsyncTask<Void, Void, Void> {
		 
		ProgressDialog pDialog;
		
	    @Override
	    protected void onPreExecute() {
	        super.onPreExecute();
	        pDialog = new ProgressDialog(SelectRacquetActivity.this);
	        pDialog.setMessage(getResources().getString(R.string.wait_load));
	        pDialog.setCancelable(false);
	        pDialog.show();
	 
	    }
	 
	    @Override
	    protected Void doInBackground(Void... arg0) {
	    	
	    	
	    	try {
	    		try {
	    			listRacquets  = function.getListRacquetText(getResources().getString(R.string.URL));
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
	
	private void populateList() {
		list = (ListView)findViewById(R.id.listRacquet);
		 
        adapter = new RacquetAdapter(SelectRacquetActivity.this, listRacquets);
        list.setAdapter(adapter);
 
        // Click event for single list row
        list.setOnItemClickListener(new OnItemClickListener() {
 

			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
					long arg3) {
				// TODO Auto-generated method stub
				RacquetText racquetText = (RacquetText)adapter.getItem(arg2);
				Intent newCustomerRacquet = new Intent(SelectRacquetActivity.this, NewCustomerRacquetActivity.class);
				newCustomerRacquet.putExtra("customer", customer);
				newCustomerRacquet.putExtra("idracquet", racquetText.getId());
				startActivityForResult(newCustomerRacquet, NEW_RACQUET_CUSTOMER);
			}
        });
	}
	
	protected void onActivityResult(int requestCode, int resultCode, Intent data){
        switch (requestCode) {
            case NEW_RACQUET_CUSTOMER:                      	
            	
            	if (resultCode == RESULT_OK){
            		finish();
            		//rigenerare la lista racchette
                } else if (resultCode == RESULT_CANCELED){
            		//notting to do
                } 
            default:
                break;
        }
    }


}
