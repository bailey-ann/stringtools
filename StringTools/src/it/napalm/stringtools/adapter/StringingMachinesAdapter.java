package it.napalm.stringtools.adapter;

import java.util.ArrayList;
import java.util.Locale;

import it.napalm.stringtools.R;
import it.napalm.stringtools.globalobject.StringingMachinesText;
import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.Filter;
import android.widget.TextView;

public class StringingMachinesAdapter extends BaseAdapter{
	
	private Activity activity;
	private ArrayList<StringingMachinesText> data;
	private ArrayList<StringingMachinesText> originalData;
	private static LayoutInflater inflater=null;
	
	public StringingMachinesAdapter(Activity a, ArrayList<StringingMachinesText> list){
		activity = a;
		data = list;
		originalData = list;
		inflater = (LayoutInflater)activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
	}
	
	@Override
	public int getCount() {
		return data.size();
	}

	@Override
	public Object getItem(int position) {
	    return data.get(position);
	}

	@Override
	public long getItemId(int arg0) {
		return arg0;
	}
	
	
	
	public void clear() {
		data = new ArrayList<StringingMachinesText>();
	}
	
	private void add(StringingMachinesText item) {
		// TODO Auto-generated method stub
		data.add(item);
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		View vi=convertView;
	    if(convertView==null)
	    	vi = inflater.inflate(R.layout.list_row_string, null);
	 
	    TextView firstLine = (TextView)vi.findViewById(R.id.firstLine); 
	    TextView secondLine = (TextView)vi.findViewById(R.id.secondLine); 
	    StringingMachinesText racquet = data.get(position); 
	    firstLine.setText(racquet.getDescription());
	    secondLine.setText("");
        return vi;
	}
	
	public Filter getFilter(){
	   return new Filter(){

	        @Override
	        protected FilterResults performFiltering(CharSequence constraint) {
	             constraint = constraint.toString().toLowerCase(Locale.ENGLISH);
	             FilterResults result = new FilterResults();
	                if (constraint != null && constraint.toString().length() > 0) {
	                  ArrayList<StringingMachinesText> founded = new ArrayList<StringingMachinesText>();
	                  for(StringingMachinesText item: originalData){
	                	  String testo = item.getDescription();
	                      if(testo.toString().toLowerCase(Locale.ENGLISH).contains(constraint)){
	                    	  founded.add(item);
	                      }
	                    }

	                    result.values = founded;
	                    result.count = founded.size();
	            }else {
                    result.values = originalData;
                    result.count = originalData.size();
                }
	            return result;


	    }

			@SuppressWarnings("unchecked")
			@Override
			protected void publishResults(CharSequence arg0, FilterResults arg1) {
				clear();
		        for (StringingMachinesText item : (ArrayList<StringingMachinesText>) arg1.values) {
		        	add(item);
		        }
				notifyDataSetChanged();
			}

			

			
	   
	};
	}

}
