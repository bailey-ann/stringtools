package it.napalm.stringtools.globalobject;

public class StringingMachinesText implements java.io.Serializable{

	private static final long serialVersionUID = 1210049051786834919L;
	

	private int id;
	private String description;
		
	public StringingMachinesText(){}
	
	public int getId() {
		return this.id;
	}

	public void setId(int id) {
		this.id = id;
	}
	
	public String getDescription() {
		return this.description;
	}

	public void setDescription(String description) {
		this.description = description;
	}	
}
