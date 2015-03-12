#!/bin/bash
#
# SlaxWeb BaseController install script
#
# Copies over the config files for it
#

# Create dir if it does not exist
mkdir -p config/slaxweb/

# Copy the config files
cp -r vendor/slaxweb/ci-basecontroller/SlaxWeb/BaseController/install/slaxweb/ config/slaxweb
