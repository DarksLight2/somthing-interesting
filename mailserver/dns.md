# Main server

**Type** - **Name** - **Val**
A - mail - IP
MX - @ - <subdomain>.<domain>
TXT - @ - v=spf1 mx ~all
TXT - _dmark - v=DMARC1; p=quarantine; rua=mailto:postmaster@"
