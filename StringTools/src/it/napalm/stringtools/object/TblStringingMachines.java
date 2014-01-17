package it.napalm.stringtools.object;
// Generated 12-dic-2013 10.48.19 by Hibernate Tools 3.4.0.CR1

import java.util.Date;
import java.util.HashSet;
import java.util.Set;

/**
 * TblStringingMachines generated by hbm2java
 */
public class TblStringingMachines implements java.io.Serializable {

	private Integer id;
	private int tblBrands;
	private int tblStringingMachineType;
	private String model;
	
	private String serial;
	private Date dateBuy;
	private Date dateCalibration;
	private String note;


	public TblStringingMachines() {
	}

	public TblStringingMachines(int tblBrands,
			int tblStringingMachineType) {
		this.tblBrands = tblBrands;
		this.tblStringingMachineType = tblStringingMachineType;
	}

	public TblStringingMachines(int tblBrands,
			int tblStringingMachineType, String model) {
		this.tblBrands = tblBrands;
		this.tblStringingMachineType = tblStringingMachineType;
		this.model = model;
	}

	public Integer getId() {
		return this.id;
	}

	public void setId(Integer id) {
		this.id = id;
	}

	public int getTblBrands() {
		return this.tblBrands;
	}

	public void setTblBrands(int tblBrands) {
		this.tblBrands = tblBrands;
	}

	public int getTblStringingMachineType() {
		return this.tblStringingMachineType;
	}

	public void setTblStringingMachineType(
			int tblStringingMachineType) {
		this.tblStringingMachineType = tblStringingMachineType;
	}

	public String getModel() {
		return this.model;
	}

	public void setModel(String model) {
		this.model = model;
	}
	
	public String getSerial() {
		return this.serial;
	}

	public void setSerial(String serial) {
		this.serial = serial;
	}
	
	public String getNote() {
		return this.note;
	}

	public void setNote(String note) {
		this.note = note;
	}
	
	public Date getDateBuy() {
		return this.dateBuy;
	}

	public void setDateBuy(Date dateBuy) {
		this.dateBuy = dateBuy;
	}
	
	public Date getDateCalibration() {
		return this.dateCalibration;
	}

	public void setDateCalibration(Date dateCalibration) {
		this.dateCalibration = dateCalibration;
	}


}