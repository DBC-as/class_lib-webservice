#!/bin/bash
echo "#!/bin/bash"
echo "sqlplus -s mkr/mkr@tora1 <<-END"
for i in `seq 1000 6000`;
do
echo "insert into userauth(username,password,settings,creation_date,lastmod) VALUES ('testuser$i@testdomain$i.com','a09337d88d5c91ef0e73adc5b583$i','',SYSDATE,SYSDATE);"
echo "update userauth SET confirmed='1',lastmod=SYSDATE WHERE username='testuser$i@testdomain$i.com';"
echo "update userauth SET settings='a:2:{s:9:\"bibdk_fav\";s:10:\"\";s:9:\"bibdk_opt\";s:56:\"artikel5Ya:2:{s:9:\"bibdk_fav\";s:10:\"\";s:9:\"bibdk_opt\";s:56:\"artikel5Ya:2:{s:9:\"bibdk_fav\";s:10:\"\";s:9:\"bibdk_opt\";s:56:\"artikel5Ya:2:{s:9:\"bibdk_fav\";s:10:\"\";s:9:\"bibdk_opt\";s:56:\"artikel5Ya:2:{s:9:\"bibdk_fav\";s:10:\"\";s:9:\"bibdk_opt\";s:56:\"artikel5Y',lastmod=SYSDATE WHERE username='testuser$i@testdomain$i.com';"
done

echo "END"
