#!/usr/bin/env bash

# Install Backdrop CLI tool bee:
wget -qO bee.zip https://github.com/backdrop-contrib/bee/archive/refs/heads/1.x-1.x.zip
unzip -q bee.zip && rm bee.zip
sudo mv bee-1.x-1.x /usr/local/bin/bee