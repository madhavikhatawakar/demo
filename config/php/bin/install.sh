set -e

#####################################
# Update the Magento Installation
# Arguments:
#   None
# Returns:
#   None
#####################################
function updateMagento() {
	cd /var/www/html
	composer update
}


#####################################
# A never-ending while loop (which keeps the installer container alive)
# Arguments:
#   None
# Returns:
#   None
#####################################
function runForever() {
	while :
	do
		sleep 1
	done
}

#####################################
# Fix the filesystem permissions for the magento root.
# Arguments:
#   None
# Returns:
#   None
#####################################
function fixFilesystemPermissions() {
	chmod -R go+rw $MAGENTO_ROOT
}
# Fix the www-folder permissions
chgrp -R 33 /var/www/html


# Check if the MAGENTO_ROOT direcotry has been specified
if [ -z "$MAGENTO_ROOT" ]
then
	echo "Please specify the root directory of Magento via the environment variable: MAGENTO_ROOT"
	exit 1
fi

# Check if the specified MAGENTO_ROOT direcotry exists
if [ ! -d "$MAGENTO_ROOT" ]
then
	mkdir -p $MAGENTO_ROOT
fi

# Check if there is alreay an index.php. If yes, abort the installation process.
#if [ -e "$MAGENTO_ROOT/index.php" ]
#then
#	echo "Magento is already installed."
#	echo "Updating Magento"
#	updateMagento
#
##	echo "Fixing filesystem permissions"
#	fixFilesystemPermissions


#	exit 0
#fi


echo "Installing Magento"
updateMagento

echo "Fixing file system permission"
fixFilesystemPermissions

echo "server is up and running"

runForever
exit 0
