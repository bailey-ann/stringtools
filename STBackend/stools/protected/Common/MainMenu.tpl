<a href="<%=$this->Service->constructUrl('Home') %>" >HOME</a> |
<com:THyperLink
	NavigateUrl="<%=$this->Service->constructUrl('UserLogin') %>"
	Text="LOGIN"
	Visible="<%= $this->User->IsGuest %>"
	/>
<com:THyperLink
	NavigateUrl="<%=$this->Service->constructUrl('User.Register') %>"
	Text=" | <%= Prado::localize('REGISTRATI') %>"
	Visible="<%= $this->User->IsGuest %>"
	/>
<com:THyperLink
	NavigateUrl="<%=$this->Service->constructUrl('User.LostPassword') %>"
	Text="<%= Prado::localize('LOST_PWD') %> | "
	Visible="<%= !$this->User->IsGuest %>"
	/>
<com:TLinkButton
	Text="LOGOUT"
	Visible="<%= !$this->User->IsGuest %>"
	OnClick="logout"
	/>
