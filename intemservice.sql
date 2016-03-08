Create Database Pilkington;
Use Pilkington;

CREATE TABLE items(
	itemid 		MEDIUMINT NOT NULL AUTO_INCREMENT,
	userid 		VARCHAR(16),
	itemname 	VARCHAR(64),
	kind 			VARCHAR(64),
	location 	VARCHAR(46),
	size 			MEDIUMINT,
	cost 			DOUBLE (6,2),
	starttime TIMESTAMP,
	endtime 	TIMESTAMP,
	info 			TEXT,
	PRIMARY KEY(itemid)
);