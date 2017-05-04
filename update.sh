#!/bin/bash
set -e
pushd $(dirname "$0") > /dev/null

# get current version
[ ! -f  "VERSION" ] && echo "VERSION file is missing" && exit 1
ver=`cat VERSION`
echo Yass current version: $ver
    
# reset changes
git reset --hard

# back to latest
git checkout release

# pull for update
git pull
git submodule update --init
chmod a+x update.sh

# delete setup ?
read -p "Do you want to keep setup folder? [Y]es [N]o " yn
if [[ ! $yn =~ ^[Yy]$ ]]
then
   echo "Remove setup folder"
   rm -rf setup/
   rm setup.sql
   rm setup.conf
fi

# migration
php .migration $ver

# get current version
[ ! -f  "VERSION" ] && echo "VERSION file is missing" && exit 1
ver=`cat VERSION`
echo Yass new current version: $ver
