mysql -v -uwaffle -pwaffle waffle << EOF

select  distinct o.\`Connect Time\`,
RPAD(o.\`SNS User Email\`,40,' '),
RPAD(o.\`SNS User Name\`, 20, ' '),
o.\`Nation\` 
from ob_email o, ob_email_sent s  
where o.\`SNS User Email\` = s.email;

exit

EOF
