#!/bin/bash
#export ORACLE_SID=koncept
#. oraenv
sqlplus -s mkr/mkr@tora1 <<-END 
drop table userauth;
create table userauth ( username VARCHAR2(64) NOT NULL, password VARCHAR2(64) NOT NULL, settings CLOB, creation_date DATE default CURRENT_DATE not null, lastlogin DATE, lastmod DATE, confirmed INT default 0, CONSTRAINT userauth_pk PRIMARY KEY (username));
END
