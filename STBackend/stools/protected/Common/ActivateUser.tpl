<com:TPanel CssClass="login" >
	<p><%= Prado::localize('ACTIVATE_ROW_1') %></p>
	<p><%= Prado::localize('ACTIVATE_ROW_2') %></p>

	<com:TImageButton ID="SendEmailButton"
		OnClick="sendEmailClicked" />
</com:TPanel>