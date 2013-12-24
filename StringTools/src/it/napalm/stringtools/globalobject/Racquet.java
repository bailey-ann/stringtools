package it.napalm.stringtools.globalobject;

import it.napalm.stringtools.object.TblBrands;
import it.napalm.stringtools.object.TblRacquets;
import it.napalm.stringtools.object.TblRacquetsPattern;

public class Racquet implements java.io.Serializable{

	private static final long serialVersionUID = 1210049051786834919L;
	

	private TblRacquets tblRacquets;
		private TblBrands tblBrands;
		private TblRacquetsPattern tblRacquetsPattern;
		
	public Racquet(){}

	public Racquet(TblRacquets tblRacquets, TblBrands tblBrands, TblRacquetsPattern tblRacquetsPattern){
			this.tblRacquets = tblRacquets;
				this.tblBrands = tblBrands;
				this.tblRacquetsPattern = tblRacquetsPattern;
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
}
