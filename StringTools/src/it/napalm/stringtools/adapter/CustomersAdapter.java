package it.napalm.stringtools.adapter;

import java.util.ArrayList;

import it.napalm.stringtools.R;
import it.napalm.stringtools.object.PositionMenu;
import it.napalm.stringtools.object.TblUsers;
import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

public class CustomersAdapter extends BaseAdapter{
	
	private Activity activity;
	private ArrayList<TblUsers> data;
	private static LayoutInflater inflater=null;
	private int position;
	
	public CustomersAdapter(Activity a, ArrayList<TblUsers> list, int position){
		activity = a;
		data = list;
		inflater = (LayoutInflater)activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
		this.position = position;
	}
	
	@Override
	public int getCount() {
		return data.size();
	}

	@Override
	public Object getItem(int arg0) {
		return arg0;
	}

	@Override
	public long getItemId(int arg0) {
		return arg0;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		View vi=convertView;
		if(this.position == PositionMenu.CUSTOMERS){
	        if(convertView==null)
	            vi = inflater.inflate(R.layout.list_row_customers, null);
	 
	        TextView name = (TextView)vi.findViewById(R.id.name); 
	        TblUsers user = data.get(position); 
	        name.setText(user.getName() + " " + user.getSurname());
		}else if(this.position == PositionMenu.CUSTOMERS_LIST_RACQUET){
	        if(convertView==null)
	            vi = inflater.inflate(R.layout.list_row_customers_forward, null);
	 
	        TextView name = (TextView)vi.findViewById(R.id.name); 
	        TblUsers user = data.get(position); 
	        name.setText(user.getName() + " " + user.getSurname());
		}
        return vi;
	}

}
