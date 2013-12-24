<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >

<com:THead Title=<%= Prado::localize('SITE_NAME') %> > 
	<meta name="description" content="<%= Prado::localize('SITE_NAME') %> " />
	<meta name="keywords" content="string, racquet, tools, stringing,
 			head, babolat, wilson" />
 <%% include("analyticstracking.php"); %>
 </com:THead>

<body>

<com:TForm>

<div class="header">

<h2><com:TLabel
Visible="<%= !$this->User->IsGuest %>"
	Text="<%= Prado::localize('Welcome') %> <%= $this->User->Name %> <%= $this->User->Surname %>"
	
	/></h2>

<div class="mainmenu">
<com:MainMenu />
</div>

</div>

<div class="shim column"></div>

<div class="page" id="home">

<div id="sidebar">
<com:TContentPlaceHolder ID="sidebar"/>
</div>
<div id="rightbar">
<com:TContentPlaceHolder ID="rightbar"/>
</div>

<div id="content">
<com:TContentPlaceHolder ID="content"/>
<com:TContentPlaceHolder ID="reg_ok"/>
<com:TContentPlaceHolder ID="zone_label"/>
<com:TContentPlaceHolder ID="zone_list_racquets_customer"/>
<com:TContentPlaceHolder ID="zone_list_add_racquets_customer"/>
<com:TContentPlaceHolder ID="zone_list_jobs"/>
<com:TContentPlaceHolder ID="editable"/>
</div>


</div>




<div class="footerbg">
<div class="footer">
<com:MainMenu />
<br/>
Copyright &copy; 2013 Luigi Piccione<br/>
</div>
</div>



</com:TForm>
</body>
</html>