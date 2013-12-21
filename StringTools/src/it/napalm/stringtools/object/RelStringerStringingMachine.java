package it.napalm.stringtools.object;
import java.util.Date;

/**
 * RelStringerStringingMachine generated by hbm2java
 */
public class RelStringerStringingMachine implements java.io.Serializable {

	private RelStringerStringingMachineId id;
	private String serial;
	private Date dateBuy;
	private Date dateCalibration;
	private String note;

	public RelStringerStringingMachine() {
	}

	public RelStringerStringingMachine(RelStringerStringingMachineId id) {
		this.id = id;
	}

	public RelStringerStringingMachine(RelStringerStringingMachineId id,
			String serial, Date dateBuy, Date dateCalibration, String note) {
		this.id = id;
		this.serial = serial;
		this.dateBuy = dateBuy;
		this.dateCalibration = dateCalibration;
		this.note = note;
	}

	public RelStringerStringingMachineId getId() {
		return this.id;
	}

	public void setId(RelStringerStringingMachineId id) {
		this.id = id;
	}

	public String getSerial() {
		return this.serial;
	}

	public void setSerial(String serial) {
		this.serial = serial;
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

	public String getNote() {
		return this.note;
	}

	public void setNote(String note) {
		this.note = note;
	}

}
