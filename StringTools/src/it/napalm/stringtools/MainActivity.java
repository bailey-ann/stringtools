package it.napalm.stringtools;


import it.napalm.stringtools.object.PositionMenu;
import it.napalm.stringtools.object.TblBrands;
import it.napalm.stringtools.object.TblGrips;
import it.napalm.stringtools.object.TblUsers;
import it.napalm.stringtools.settings.BrandDataActivity;
import it.napalm.stringtools.settings.BrandsFragment;
import it.napalm.stringtools.settings.GripDataActivity;
import it.napalm.stringtools.settings.GripsFragment;
import it.napalm.stringtools.user.CustomerDataActivity;
import it.napalm.stringtools.user.CustomerRacquetActivity;
import it.napalm.stringtools.user.CustomersFragment;
import it.napalm.stringtools.user.PersonalDataActivity;
import it.napalm.stringtools.utils.HttpFunctions;
import android.os.Bundle;
import android.os.StrictMode;
import android.preference.PreferenceManager;
import android.app.Activity;
import android.app.Fragment;
import android.app.FragmentManager;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.Configuration;
import android.support.v4.app.ActionBarDrawerToggle;
import android.support.v4.view.GravityCompat;
import android.support.v4.widget.DrawerLayout;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ListView;

public class MainActivity extends Activity {

	
	private static final int MOD_DATA_USER = 1;
	private static final int MOD_DATA_BRAND = 2;
	private static final int MOD_DATA_GRIPS = 3;
	private ListView mDrawerList;
	private DrawerLayout mDrawerLayout;
	private String[] mTitles;
	private ActionBarDrawerToggle mDrawerToggle;
	private TblUsers user;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		
		StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
	    StrictMode.setThreadPolicy(policy);
		
		SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(getBaseContext());
		int idUser  = prefs.getInt("id", 0);
		
		HttpFunctions userFunction = new HttpFunctions();
		user = userFunction.getUser(getResources().getString(R.string.URL), "","", idUser);

		// enable ActionBar app icon to behave as action to toggle nav drawer
		getActionBar().setDisplayHomeAsUpEnabled(true);
		getActionBar().setHomeButtonEnabled(true);
		
		setTitle(R.string.Welcome, user.getName() + " " + user.getSurname());

		
        mTitles = getResources().getStringArray(R.array.menu_items);
        mDrawerLayout = (DrawerLayout) findViewById(R.id.drawer_layout);
        mDrawerList = (ListView) findViewById(R.id.left_drawer);

        // set a custom shadow that overlays the main content when the drawer opens
        mDrawerLayout.setDrawerShadow(R.drawable.drawer_shadow, GravityCompat.START);
        // set up the drawer's list view with items and click listener
        mDrawerList.setAdapter(new ArrayAdapter<String>(this,
                R.layout.drawer_list_item, mTitles));
        mDrawerList.setOnItemClickListener(new DrawerItemClickListener());

        // ActionBarDrawerToggle ties together the the proper interactions
        // between the sliding drawer and the action bar app icon
        mDrawerToggle = new ActionBarDrawerToggle(
                this,                  /* host Activity */
                mDrawerLayout,         /* DrawerLayout object */
                R.drawable.ic_drawer,  /* nav drawer image to replace 'Up' caret */
                R.string.menu_open,  /* "open drawer" description for accessibility */
                R.string.menu_close  /* "close drawer" description for accessibility */
                ) {
            public void onDrawerClosed(View view) {
                invalidateOptionsMenu(); // creates call to onPrepareOptionsMenu()
            }

            public void onDrawerOpened(View drawerView) {
                invalidateOptionsMenu(); // creates call to onPrepareOptionsMenu()
            }
        };
        mDrawerLayout.setDrawerListener(mDrawerToggle);
        
        selectItem(PositionMenu.HOME);

	}
	
	
	@Override
    public boolean onCreateOptionsMenu(Menu menu) {
        MenuInflater inflater = getMenuInflater();
        inflater.inflate(R.menu.main, menu);
        return super.onCreateOptionsMenu(menu);
    }

    /* Called whenever we call invalidateOptionsMenu() */
    @Override
    public boolean onPrepareOptionsMenu(Menu menu) {
        // If the nav drawer is open, hide action items related to the content view
        //boolean drawerOpen = mDrawerLayout.isDrawerOpen(mDrawerList);
        //menu.findItem(R.id.action_websearch).setVisible(!drawerOpen);
        return super.onPrepareOptionsMenu(menu);
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
         // The action bar home/up action should open or close the drawer.
         // ActionBarDrawerToggle will take care of this.
        if (mDrawerToggle.onOptionsItemSelected(item)) {
            return true;
        }
        // Handle action buttons
        switch(item.getItemId()) {
        
        default:
            return super.onOptionsItemSelected(item);
        }
    }

    /* The click listner for ListView in the navigation drawer */
    private class DrawerItemClickListener implements ListView.OnItemClickListener {
        @Override
        public void onItemClick(AdapterView<?> parent, View view, int position, long id) {            
        	
        	selectItem(position);
        }
    }

    private void selectItem(int position) {
    	mDrawerList.setItemChecked(position, true);
    	mDrawerLayout.closeDrawer(mDrawerList);
    	Fragment fragment = null;
    	if(position == PositionMenu.HOME){
    		fragment = new HomeFragment();
    	}
    	else if(position == PositionMenu.PERSONALE_DATA){
    		callEditDataUser(false, position);
    	}
    	else if(position == PositionMenu.CUSTOMERS){
    		fragment = CustomersFragment.newInstance(position);
    	}
    	else if(position == PositionMenu.CUSTOMERS_LIST_RACQUET){
    		fragment = CustomersFragment.newInstance(position);
    	}
    	else if(position == PositionMenu.BRANDS_LIST){
    		fragment = BrandsFragment.newInstance(position);
    	}
    	else if(position == PositionMenu.GRIPS_LIST){
    		fragment = GripsFragment.newInstance(position);
    	}
    	else if(position == PositionMenu.LOGOUT){
    		SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(getBaseContext());
        	SharedPreferences.Editor editor = prefs.edit();
            editor.putInt("id", 0); // value to store
            editor.commit();
            Intent login = new Intent(getApplicationContext(), LoginActivity.class);
            login.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
            startActivity(login);
            // Closing dashboard screen
            finish();
    	}
    	
    	if (fragment != null) {
            FragmentManager fragmentManager = getFragmentManager();
            fragmentManager.beginTransaction()
                    .replace(R.id.content_frame, fragment).commit();

        }
    }

    /**
     * When using the ActionBarDrawerToggle, you must call it during
     * onPostCreate() and onConfigurationChanged()...
     */

    @Override
    protected void onPostCreate(Bundle savedInstanceState) {
        super.onPostCreate(savedInstanceState);
        // Sync the toggle state after onRestoreInstanceState has occurred.
        mDrawerToggle.syncState();
    }

    @Override
    public void onConfigurationChanged(Configuration newConfig) {
        super.onConfigurationChanged(newConfig);
        // Pass any configuration change to the drawer toggls
        mDrawerToggle.onConfigurationChanged(newConfig);
    }
    
    @Override
    public void setTitle(CharSequence title) {
        getActionBar().setTitle(title);
    }
    
    public void setTitle(int title,CharSequence subTitle) {
        getActionBar().setTitle(title);
        getActionBar().setSubtitle(subTitle);
    }
    
    public void setTitleWithStringer(int title) {
        getActionBar().setTitle(title);
        getActionBar().setSubtitle(user.getName() + " " + user.getSurname());
    }
    
    protected void onActivityResult(int requestCode, int resultCode, Intent data){
        // See which child activity is calling us back.
    	
        switch (requestCode) {
            case MOD_DATA_USER:
            	int position = data.getIntExtra("position", 0);
            	
            	if (resultCode == RESULT_OK){
                	if(position == PositionMenu.PERSONALE_DATA){
                		selectItem(PositionMenu.HOME);
                		user = (TblUsers)data.getSerializableExtra("user");
                    	setTitle(R.string.Welcome, user.getName() + " " + user.getSurname());
                	}else if(position == PositionMenu.CUSTOMERS){
                		selectItem(PositionMenu.CUSTOMERS);
                	}
                } else  if (resultCode == RESULT_CANCELED){                	
                	if(position == PositionMenu.PERSONALE_DATA){
                	}else if(position == PositionMenu.CUSTOMERS){
                		selectItem(PositionMenu.CUSTOMERS);
                	}
                } 
            case MOD_DATA_BRAND:
            	selectItem(PositionMenu.BRANDS_LIST);
            case MOD_DATA_GRIPS:
            	selectItem(PositionMenu.GRIPS_LIST);
            default:
                break;
        }
    }
    
    public void backToHome(){
    	setTitle(R.string.Welcome, user.getName() + " " + user.getSurname());
    	selectItem(PositionMenu.HOME);
    	mDrawerList.setItemChecked(-1, true);
    	mDrawerLayout.closeDrawer(mDrawerList);
    }
    
    public TblUsers getUser(){
    	return user;
    }
    
    public void callEditDataUser(Boolean close, int position){
    	if(close){
    		mDrawerList.setItemChecked(-1, true);
        	mDrawerLayout.closeDrawer(mDrawerList);
		}
    	Intent personalData = new Intent(this, PersonalDataActivity.class);
		personalData.putExtra("user", user);
		personalData.putExtra("position", position);
		startActivityForResult(personalData, MOD_DATA_USER);
		
    }
    
    public void callEditDataCustomer(TblUsers customer){
    	mDrawerList.setItemChecked(-1, true);
        mDrawerLayout.closeDrawer(mDrawerList);
    	Intent personalData = new Intent(this, CustomerDataActivity.class);
		personalData.putExtra("customer", customer);
		personalData.putExtra("stringer", user);
		personalData.putExtra("position", PositionMenu.CUSTOMERS);
		startActivityForResult(personalData, MOD_DATA_USER);
		
    }
    
    public void callShowCustomerRacquet(Boolean close, TblUsers customer){
    	if(close){
    		mDrawerList.setItemChecked(-1, true);
        	mDrawerLayout.closeDrawer(mDrawerList);
		}
    	Intent customerRacquet = new Intent(this, CustomerRacquetActivity.class);
    	customerRacquet.putExtra("customer", customer);
    	customerRacquet.putExtra("position", PositionMenu.CUSTOMERS_LIST_RACQUET);
		startActivity(customerRacquet);		
    }
    
    public void callEditDataBrand(TblBrands item){
    	mDrawerList.setItemChecked(-1, true);
        mDrawerLayout.closeDrawer(mDrawerList);
    	Intent personalData = new Intent(this, BrandDataActivity.class);
		personalData.putExtra("brand", item);
		personalData.putExtra("position", PositionMenu.BRANDS_LIST);
		startActivityForResult(personalData, MOD_DATA_BRAND);
		
    }
    
    public void callEditDataGrips(int id){
    	mDrawerList.setItemChecked(-1, true);
        mDrawerLayout.closeDrawer(mDrawerList);
    	Intent personalData = new Intent(this, GripDataActivity.class);
		personalData.putExtra("id", id);
		personalData.putExtra("position", PositionMenu.GRIPS_LIST);
		startActivityForResult(personalData, MOD_DATA_GRIPS);
		
    }
    
    
    public void selectItemFormOutside(int position){
    	selectItem(position);
    }

}
