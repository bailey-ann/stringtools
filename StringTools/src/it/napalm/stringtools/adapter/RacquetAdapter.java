package it.napalm.stringtools.adapter;

import java.util.ArrayList;

import it.napalm.stringtools.R;
import it.napalm.stringtools.globalobject.CustomerRacquet;
import it.napalm.stringtools.globalobject.Racquet;
import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

public class RacquetAdapter extends BaseAdapter{
	
	private Activity activity;
	private ArrayList<Racquet> data;
	private static LayoutInflater inflater=null;
	
	public RacquetAdapter(Activity a, ArrayList<Racquet> list){
		activity = a;
		data = list;
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

}
