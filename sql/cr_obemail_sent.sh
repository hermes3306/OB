awk '/^A/ {print substr($5,3)}' /tmp/OB.log > OB2.log
awk -f d.awk OB2.log > ins.sql

mysql -v -uroot -pWaffleB2BProject waffle << EOF

DROP TABLE IF EXISTS ob_email_sent;
CREATE TABLE ob_email_sent (
  email longtext,
  yymmdd varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

exit
EOF

mysql -v -uroot -pWaffleB2BProject waffle < ins.sql

mysql -v -uroot -pWaffleB2BProject waffle << EOF

	select count(*) from ob_email_sent;

exit
EOF

mysql -v -uwaffle -pwaffle waffle << EOF

select 
o.\`SNS User Email\` as email ,
o.\`SNS User Name\` as name ,
o.\`Birthday\` as birth ,
o.\`Nation\` as nation 
from ob_email o, ob_email_sent s where o.\`SNS User Email\` = s.email;

exit

EOF



