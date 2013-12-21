package it.napalm.stringtools.globalobject;

import it.napalm.stringtools.object.TblBrands;
import it.napalm.stringtools.object.TblGripSize;
import it.napalm.stringtools.object.TblRacquets;
import it.napalm.stringtools.object.TblRacquetsPattern;
import it.napalm.stringtools.object.TblRacquetsUser;

public class CustomerRacquet implements java.io.Serializable{

	private static final long serialVersionUID = 1210049051786834919L;
	
	private TblRacquetsUser tblRacquetsUser;
		private TblRacquets tblRacquets;
			private TblBrands tblBrands;
			private TblRacquetsPattern tblRacquetsPattern;
		private TblGripSize tblGripSize;
		
	public CustomerRacquet(){}

	public CustomerRacquet(TblRacquetsUser tblRacquetsUser, TblRacquets tblRacquets, 
			TblBrands tblBrands, TblRacquetsPattern tblRacquetsPattern, TblGripSize tblGripSize){
		this.tblRacquetsUser = tblRacquetsUser;
			this.tblRacquets = tblRacquets;
				this.tblBrands = tblBrands;
				this.tblRacquetsPattern = tblRacquetsPattern;
			this.tblGripSize = tblGripSize;
	}
	
	public TblRacquetsUser getTblRacquetsUser() {
		return this.tblRacquetsUser;
	}

	public void setTblRacquetsUser(TblRacquetsUser tblRacquetsUser) {
		this.tblRacquetsUser = tblRacquetsUser;
	}
	
	public TblRacquets getTblRacquets() {
		return this.tblRacquets;
	}

	public void setTblRacquets(TblRacquets tblRacquets) {
		this.tblRacquets = tblRacquets;
	}
	
	public TblBrands getTblBrands() {
		return this.tblBrands;
	}

	public void setTblBrands(TblBrands tblBrands) {
		this.tblBrands = tblBrands;
	}
	
	public TblRacquetsPattern getTblRacquetsPattern() {
		return this.tblRacquetsPattern;
	}

	public void setTblRacquetsPattern(TblRacquetsPattern tblRacquetsPattern) {
		this.tblRacquetsPattern = tblRacquetsPattern;
	}
	
	public TblGripSize getTblGripSize() {
		return this.tblGripSize;
	}

	public void setTblGripSize(TblGripSize tblGripSize) {
		this.tblGripSize = tblGripSize;
	}
	
}
