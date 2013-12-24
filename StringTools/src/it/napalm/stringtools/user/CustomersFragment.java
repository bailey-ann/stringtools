package it.napalm.stringtools.user;

import java.text.ParseException;
import java.util.ArrayList;

import org.json.JSONException;

import it.napalm.stringtools.MainActivity;
import it.napalm.stringtools.R;
import it.napalm.stringtools.adapter.CustomersAdapter;
import it.napalm.stringtools.object.PositionMenu;
import it.napalm.stringtools.object.TblUsers;
import it.napalm.stringtools.utils.HttpFunctions;
import android.app.Fragment;
import android.app.ProgressDialog;
import android.content.res.Resources.NotFoundException;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.EditText;
import android.widget.ListView;

public class CustomersFragment extends Fragment {
	
	int position;
	ListView list;
	ArrayList<TblUsers> listCustomers;
    CustomersAdapter adapter;
    HttpFunctions function;
    View rootView;
    EditText inputSearch;
    
	public CustomersFragment(){	}
	

	public static CustomersFragment newInstance
	(int position) {
		CustomersFragment fragment = new CustomersFragment();
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
		
		rootView = inflater.inflate(R.layout.customers_list, container, false);
        function = new HttpFunctions();
        new GetListCustomers().execute();
        
		return rootView;
    }
	
	private void populateList() {
		list = (ListView)rootView.findViewById(R.id.listCustomers);
		 
        adapter = new CustomersAdapter(getActivity(), listCustomers, this.position);
        list.setAdapter(adapter);
 
        // Click event for single list row
        list.setOnItemClickListener(new OnItemClickListener() {
 

			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
					long arg3) {
				// TODO Auto-generated method stub
				TblUsers userSelected = listCustomers.get(arg2);
				
				if(position == PositionMenu.CUSTOMERS){				
					TblUsers user = ((MainActivity)getActivity()).getUser();	
					if(userSelected.getId() == user.getId()){
						 ((MainActivity)getActivity()).callEditDataUser(true, PositionMenu.CUSTOMERS);
					}else{
						((MainActivity)getActivity()).callEditDataCustomer(userSelected);
					}
				}else if(position == PositionMenu.CUSTOMERS_LIST_RACQUET){				
					((MainActivity)getActivity()).callShowCustomerRacquet(false, userSelected);
				}			
			}
        });
	}
	
	public void onCreateOptionsMenu(Menu menu, MenuInflater inflater) {
		if(position == PositionMenu.CUSTOMERS)
			inflater.inflate(R.menu.menu_ah, menu);
		else if(position == PositionMenu.CUSTOMERS_LIST_RACQUET)
			inflater.inflate(R.menu.menu_h, menu);
        super.onCreateOptionsMenu(menu, inflater);
    }
	
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {


	    switch (item.getItemId()) {
	        case R.id.home:
	        	((MainActivity)getActivity()).backToHome();
	            return super.onOptionsItemSelected(item);
	        case R.id.newCustomer:
	        	((MainActivity)getActivity()).callEditDataCustomer(null);
	            return super.onOptionsItemSelected(item);
	        default:
	            return super.onOptionsItemSelected(item);
	    }
	}
	
	@Override
	public void onSaveInstanceState(Bundle outState) {
	    super.onSaveInstanceState(outState);
	}
	

	private class GetListCustomers extends AsyncTask<Void, Void, Void> {
	 
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
					listCustomers = function.getListCustomers(getResources().getString(R.string.URL), ((MainActivity)getActivity()).getUser().getId());
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
	        ((MainActivity) getActivity()).setTitleWithStringer(R.string.list_customers);
	        populateList();
	    }
	}
}
