package it.napalm.stringtools.object;
// Generated 12-dic-2013 10.48.19 by Hibernate Tools 3.4.0.CR1

import java.math.BigDecimal;

/**
 * RelStringPrice generated by hbm2java
 */
public class RelStringPrice implements java.io.Serializable {

	private RelStringPriceId id;
	private TblUsers tblUsers;
	private TblStrings tblStrings;
	private BigDecimal price;

	public RelStringPrice() {
	}

	public RelStringPrice(RelStringPriceId id, TblUsers tblUsers,
			TblStrings tblStrings, BigDecimal price) {
		this.id = id;
		this.tblUsers = tblUsers;
		this.tblStrings = tblStrings;
		this.price = price;
	}

	public RelStringPriceId getId() {
		return this.id;
	}

	public void setId(RelStringPriceId id) {
		this.id = id;
	}

	public TblUsers getTblUsers() {
		return this.tblUsers;
	}

	public void setTblUsers(TblUsers tblUsers) {
		this.tblUsers = tblUsers;
	}

	public TblStrings getTblStrings() {
		return this.tblStrings;
	}

	public void setTblStrings(TblStrings tblStrings) {
		this.tblStrings = tblStrings;
	}

	public BigDecimal getPrice() {
		return this.price;
	}

	public void setPrice(BigDecimal price) {
		this.price = price;
	}

}
