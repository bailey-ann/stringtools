
<com:TPanel CssClass="login" DefaultButton="LoginButton">
	<h4><%= Prado::localize('Login to Site') %></h4>
	<com:TLabel
		ForControl="Username" 
		Text="User Name" 
		CssClass="label"/>
	<com:TTextBox ID="Username" 
		AccessKey="u" 
		ValidationGroup="login"
		CssClass="textbox"/>
	<com:TRequiredFieldValidator 
		ControlToValidate="Username" 
		ValidationGroup="login"
		Display="Dynamic"
		ErrorMessage="*"/>

	<com:TLabel
		ForControl="Password" 
		Text="Password" 
		CssClass="label"/>
	<com:TTextBox ID="Password" 
		AccessKey="p" 
		CssClass="textbox" 
		ValidationGroup="login"
		TextMode="Password"/>
	<com:TCustomValidator
		ControlToValidate="Password"
		ValidationGroup="login"
		Text="<%= Prado::localize('...invalid') %>"
		Display="Dynamic"
		OnServerValidate="validateUser" />

	<div>
	<com:TCheckBox ID="RememberMe" Text="<%= Prado::localize('Remember me next time') %>"/>
	</div>

	<com:TImageButton ID="LoginButton"
		OnClick="loginButtonClicked"
		ValidationGroup="login"
		CssClass="button"/>
		<%= Prado::localize('or') %>
	<a href="<%=$this->Service->constructUrl('User.LostPassword')%>" class="button"><img src="<%=$this->Page->Theme->BaseUrl.'/images/it_IT/lost-password.gif'%>" alt="<%= Prado::localize('Lost Password') %>"/></a>
	<%= Prado::localize('or') %>
	<a href="<%=$this->Service->constructUrl('User.Register')%>" class="button"><img src="<%=$this->Page->Theme->BaseUrl.'/images/it_IT/register.gif'%>" alt="<%= Prado::localize('Create a new account') %>"/></a>
</com:TPanel>