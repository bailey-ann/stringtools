package it.napalm.stringtools.settings;

import java.text.ParseException;
import java.util.ArrayList;

import org.json.JSONException;

import it.napalm.stringtools.MainActivity;
import it.napalm.stringtools.R;
import it.napalm.stringtools.adapter.RacquetAdapter;
import it.napalm.stringtools.globalobject.RacquetText;
import it.napalm.stringtools.utils.HttpFunctions;
import android.app.Fragment;
import android.app.ProgressDialog;
import android.content.res.Resources.NotFoundException;
import android.os.AsyncTask;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.SearchView.OnQueryTextListener;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.SearchView;

public class RacquetsFragment extends Fragment {
	
	protected static final int MOD_DATA = 0;
	int position;
	ListView list;
	ArrayList<RacquetText> listItem;
	RacquetAdapter adapter;
    HttpFunctions function;
    View rootView;
    EditText inputSearch;
    
	public RacquetsFragment(){	}
	

	public static RacquetsFragment newInstance
	(int position) {
		RacquetsFragment fragment = new RacquetsFragment();
	    fragment.position = position;
	    return fragment;
	}
	
	
	
	@Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setHasOptionsMenu(true); 
    }
	
	@Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState) {
		
		rootView = inflater.inflate(R.layout.racquet_list, container, false);
        function = new HttpFunctions();
        new GetList().execute();
        
		return rootView;
    }
	
	private void populateList() {
		list = (ListView)rootView.findViewById(R.id.listRacquet);
		 
        adapter = new RacquetAdapter(getActivity(), listItem);
        list.setAdapter(adapter);
        adapter.getFilter().filter("");
 
        // Click event for single list row
        list.setOnItemClickListener(new OnItemClickListener() {
 

			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
					long arg3) {
				// TODO Auto-generated method stub
				RacquetText item = (RacquetText)adapter.getItem(arg2);
				
				((MainActivity)getActivity()).callEditDataRacquets(item.getId());
			}
        });
	}
	
	public void onCreateOptionsMenu(Menu menu, MenuInflater inflater) {
		inflater.inflate(R.menu.menu_racquets_sah, menu);
		SearchView searchView = (SearchView) menu.findItem(R.id.search_racquets).getActionView();
	    searchView.setOnQueryTextListener(listener);
	    searchView.setIconifiedByDefault(false); // Do not iconify the widget; expand it by default
        super.onCreateOptionsMenu(menu, inflater);
    }
	
	private OnQueryTextListener listener = new OnQueryTextListener(){

		@Override
		public boolean onQueryTextChange(String newText) {
			try{
				if (TextUtils.isEmpty(newText)) {
			        adapter.getFilter().filter("");
			    } else {
			        adapter.getFilter().filter(newText.toString());
			        list.setFilterText(newText.toString());
			    }
			}catch(Exception e){}
		    return true;
		}

		@Override
		public boolean onQueryTextSubmit(String query) {
			// TODO Auto-generated method stub
			return false;
		}
		
	};
	
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {


	    switch (item.getItemId()) {
	        case R.id.home:
	        	((MainActivity)getActivity()).backToHome();
	            return super.onOptionsItemSelected(item);
	        case R.id.newCustomer:
	        	((MainActivity)getActivity()).callEditDataRacquets(0);
	            return super.onOptionsItemSelected(item);
	        default:
	            return super.onOptionsItemSelected(item);
	    }
	}
	
	@Override
	public void onSaveInstanceState(Bundle outState) {
	    super.onSaveInstanceState(outState);
	}
	

	private class GetList extends AsyncTask<Void, Void, Void> {
	 
		ProgressDialog pDialog;
		
	    @Override
	    protected void onPreExecute() {
	        super.onPreExecute();
	        pDialog = new ProgressDialog(getActivity());
	        pDialog.setMessage(getResources().getString(R.string.wait_load));
	        pDialog.setCancelable(false);
	        pDialog.show();
	 
	    }
	 
	    @Override
	    protected Void doInBackground(Void... arg0) {
	    	
	    	
	    	try {
	    		try {
					listItem = function.getListRacquetText(getResources().getString(R.string.URL));
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
	        ((MainActivity) getActivity()).setTitle(R.string.list_racquets, "");
	        populateList();
	        if (pDialog.isShowing())
	            pDialog.dismiss();
	        
	    }
	}
}
