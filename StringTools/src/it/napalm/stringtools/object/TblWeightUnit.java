package it.napalm.stringtools.object;

/**
 * TblWeightUnit generated by hbm2java
 */
public class TblWeightUnit implements java.io.Serializable {

	private Integer id;
	private String description;

	public TblWeightUnit() {
	}

	public TblWeightUnit(Integer id, String description) {
		this.id = id;
		this.description = description;
	}

	public Integer getId() {
		return this.id;
	}

	public void setId(Integer id) {
		this.id = id;
	}

	public String getDescription() {
		return this.description;
	}

	public void setDescription(String description) {
		this.description = description;
	}
	
	@Override
    public String toString() {
        return this.description;
    }

}
