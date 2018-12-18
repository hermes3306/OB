echo "START generating email receipients" >> /tmp/OB.log
echo `date` >> /tmp/OB.log

mysql -v -uroot -pWaffleB2BProject waffle <  /root/OB/sql/cr_obemail.sql >> /tmp/OB.log
#mysql -v -uwaffle -pwaffle waffle <  /home/pi/code/OB/sql/ob_email.sql >> /tmp/OB.log

echo `date` >> /tmp/OB.log
echo "END" >> /tmp/OB.log
echo "" >> /tmp/OB.log
echo "" >> /tmp/OB.log
echo "" >> /tmp/OB.log
