#!/bin/bash

RED=$(tput setaf 1)
OFF=$(tput sgr0)

if [ ! -d colby ]
then
 echo "${RED}This doesn't appear to be a colby web root directory.${OFF}"
 exit
fi

if [ ! -f .htaccess ]
then
    echo "creating '.htaccess'"
    cat > .htaccess << 'EOF'
<IfModule mod_rewrite.c>

RewriteEngine On
RewriteBase /

#
# pass URLS matching the following patterns through
# without any further modification
#

RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule . /index.php [L]

#
# if the request is for an existing file whose path contains the string '.git'
# rewrite to index.php (do not show the file)
#

RewriteRule \.git /index.php [L]

#
# if the request is for an existing php file (other than index.php)
# rewrite to index.php (do not show the file)
#

RewriteRule \.php$ /index.php [L]

</IfModule>
EOF
else
    echo "${RED}.htaccess already exists, no changes made${OFF}"
fi


