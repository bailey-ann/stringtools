package it.napalm.stringtools.adapter;

import java.util.ArrayList;

import it.napalm.stringtools.R;
import it.napalm.stringtools.globalobject.CustomerRacquet;
import it.napalm.stringtools.globalobject.Racquet;
import android.app.Activity;
import android.content.Context;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.Filter;
import android.widget.TextView;

public class RacquetAdapter extends BaseAdapter{
	
	private Activity activity;
	private ArrayList<Racquet> data;
	private ArrayList<Racquet> originalData;
	private static LayoutInflater inflater=null;
	
	public RacquetAdapter(Activity a, ArrayList<Racquet> list){
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
	public Object getItem(int arg0) {
		return arg0;
	}

	@Override
	public long getItemId(int arg0) {
		return arg0;
	}
	
	private void clear() {
		data = new ArrayList<Racquet>();
	}
	
	private void add(Racquet item) {
		// TODO Auto-generated method stub
		data.add(item);
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		View vi=convertView;
	    if(convertView==null)
	    	vi = inflater.inflate(R.layout.list_row_racquet, null);
	 
	    TextView firstLine = (TextView)vi.findViewById(R.id.firstLine); 
	    TextView secondLine = (TextView)vi.findViewById(R.id.secondLine); 
	    Racquet racquet = data.get(position); 
	    firstLine.setText(racquet.getTblBrands().getDescription() + " " + racquet.getTblRacquets().getModel());
	    secondLine.setText("");
        return vi;
	}
	
	public Filter getFilter(){
	   return new Filter(){

	        @Override
	        protected FilterResults performFiltering(CharSequence constraint) {
	             constraint = constraint.toString().toLowerCase();
	             FilterResults result = new FilterResults();
	             Log.i("Nomad", "CharSequence " + constraint);
	                if (constraint != null && constraint.toString().length() > 0) {
	                  ArrayList<Racquet> founded = new ArrayList<Racquet>();
	                  for(Racquet item: originalData){
	                	  String testo = item.getTblBrands().getDescription() + " " + item.getTblRacquets().getModel();
	                      if(testo.toString().toLowerCase().contains(constraint)){
	                    	  founded.add(item);
	                      }
	                    }

	                    result.values = founded;
	                    result.count = founded.size();
	            }
	            return result;


	    }

			@Override
			protected void publishResults(CharSequence arg0, FilterResults arg1) {
				clear();
		        for (Racquet item : (ArrayList<Racquet>) arg1.values) {
		        	add(item);
		        }
				notifyDataSetChanged();
			}

			

			
	   
	};
	}

}
