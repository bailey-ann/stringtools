package it.napalm.stringtools;

import org.json.JSONObject;

import it.napalm.stringtools.utils.HttpFunctions;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.os.StrictMode;
import android.preference.PreferenceManager;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

@SuppressLint("NewApi")
public class LoginActivity extends Activity {
	Button btnLogin;
    Button btnLinkToRegister;
    EditText inputUsername;
    EditText inputPassword;
    TextView loginErrorMsg;
    
    // JSON Response node names
    private static String KEY_SUCCESS = "success";
    

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_login);
		
		
		
		StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
	    StrictMode.setThreadPolicy(policy);
		
		// Importing all assets like buttons, text fields
		inputUsername = (EditText) findViewById(R.id.loginUsername);
        inputPassword = (EditText) findViewById(R.id.loginPassword);
        loginErrorMsg = (TextView) findViewById(R.id.login_error);
        inputUsername.setText("napalm");
        inputPassword.setText("gabber");
        
        btnLogin = (Button) findViewById(R.id.btnLogin);
        //btnLinkToRegister = (Button) findViewById(R.id.btnLinkToRegisterScreen);
        //loginErrorMsg = (TextView) findViewById(R.id.login_error);
        
        // Login button Click Event
        btnLogin.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View view) {
				String username = inputUsername.getText().toString();
                String password = inputPassword.getText().toString();

                HttpFunctions userFunction = new HttpFunctions();
                JSONObject json = userFunction.loginUser(getResources().getString(R.string.URL), username, password);
                
                try{
                	if (json.getString(KEY_SUCCESS) != null) {

                        String res = json.getString(KEY_SUCCESS); 
                        Log.d("res", res+"");
                        if(Integer.parseInt(res) == 1){

                            JSONObject json_user = json.getJSONObject("user");
                            
                            if(json_user.getInt("active") == 0){
                            	loginErrorMsg.setText(R.string.no_active);
                            }else{
                            	SharedPreferences prefs = 
                            			PreferenceManager.getDefaultSharedPreferences(getBaseContext());
                                
                                SharedPreferences.Editor editor = prefs.edit();
                                editor.putInt("id", json_user.getInt("id")); // value to store
                                editor.commit();
                                
                                Intent main = new Intent(getApplicationContext(), MainActivity.class);
                                main.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                                startActivity(main);
                                // Closing dashboard screen
                                finish();
                            }

                        }else{
                            loginErrorMsg.setText(R.string.wrong_login);
                        }
                    }
                }catch (Exception e) {
                	//Log.d("error", e.getMessage());
				}
				
			}
        	
        });
		
	}

}
