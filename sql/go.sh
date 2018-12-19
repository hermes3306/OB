awk '/^A/ {print substr($5,3)}' OB.log > OB2.log
awk -f d.awk OB2.log > ins.sql
mysql -v -uwaffle -pwaffle waffle < ins.sql

