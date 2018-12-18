#!/bin/bash

#!/bin/bash


echo "START sending email... " >> /tmp/OB.log
echo `date` >> /tmp/OB.log
echo "" >> /tmp/OB.log
echo "" >> /tmp/OB.log

php /root/OB/Mail/OB.php
#php /home/pi/code/OB/Mail/OB.php

echo `date` >> /tmp/OB.log
echo "END" >> /tmp/OB.log
echo "" >> /tmp/OB.log
echo "" >> /tmp/OB.log
echo "" >> /tmp/OB.log

