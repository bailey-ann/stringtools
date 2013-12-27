package it.napalm.stringtools.utils;


import java.text.DecimalFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;

public class Function {

	/*public static double stringToDouble(String value){
		double returnValue = 0;
		try {
	        DecimalFormat dF = new DecimalFormat("0.00");
	        Number num = dF.parse(value);
	        returnValue = num.doubleValue();
	    } catch (Exception e) { }		
		return returnValue;
	}*/
	
	public static double stringToDouble(String value){
		double returnValue = 0;
		try {
			returnValue = Double.valueOf(value);
	    } catch (Exception e) { }		
		return returnValue;
	}
	
	public static Date stringToDateShort(String value){
		Date returnValue = new Date();
		try {
			returnValue = new SimpleDateFormat("dd-MM-yyyy", Locale.getDefault()).parse(value);
		} catch (ParseException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}		
		return returnValue;
	}
}
