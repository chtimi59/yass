#!/bin/bash

#reset changes
git reset --hard

#pull for update
git pull
git submodule update --init
chmod a+x update.sh

#delete setup ?
read -p "Do you want to keep setup folder? [Y]es [N]o " yn
if [[ ! $yn =~ ^[Yy]$ ]]
then
   echo "Remove setup folder"
   rm -rf setup/
   rm setup.sql
   rm setup.conf
fi
