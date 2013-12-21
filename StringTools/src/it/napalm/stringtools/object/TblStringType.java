package it.napalm.stringtools.object;
// Generated 12-dic-2013 10.48.19 by Hibernate Tools 3.4.0.CR1

import java.util.HashSet;
import java.util.Set;

/**
 * TblStringType generated by hbm2java
 */
public class TblStringType implements java.io.Serializable {

	private Integer id;
	private String description;
	private Set tblStringses = new HashSet(0);

	public TblStringType() {
	}

	public TblStringType(String description) {
		this.description = description;
	}

	public TblStringType(String description, Set tblStringses) {
		this.description = description;
		this.tblStringses = tblStringses;
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

	public Set getTblStringses() {
		return this.tblStringses;
	}

	public void setTblStringses(Set tblStringses) {
		this.tblStringses = tblStringses;
	}

}
