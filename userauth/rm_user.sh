#!/bin/bash
#export ORACLE_SID=koncept
#. oraenv
sqlplus -s mkr/mkr@tora1 <<-END
delete from userauth where username='$1';
END

