Create Database Pilkington;
Use Pilkington;

CREATE TABLE items(
	Itemid MEDIUMINT NOT NULL AUTO_INCREMENT,
	Userid VARCHAR(16),
	Itemname VARCHAR(64),
	Sort VARCHAR(64),
	Location VARCHAR(46),
	Langd MEDIUMINT,
	Pris DOUBLE (6,2),
	Starttime TIMESTAMP,
	Endtime TIMESTAMP,
	Information TEXT,
	PRIMARY KEY(Itemid)
);