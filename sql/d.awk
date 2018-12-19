BEGIN {
}

{
	print "insert into ob_email_sent(email,yymmdd) values('" $1 "', '181218');"
}

END {
}
