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
