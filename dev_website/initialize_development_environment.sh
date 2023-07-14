#!/usr/bin/env sh

# NOTE 20230708
# Matt Calkins
#
#   This script is a set of commands that I would like to have in the Dockerfile
#   in the development stage. However, they don't currently work correctly when
#   run inside the Dockefile, but do work correctly if run inside the finished
#   development container.
#
#   If we can find a way to get the to work inside the Dockerfile that would be
#   great.



# Run these commands manually in a VSCode terminal before running this script.
#
#   apache2ctl start
#
#       Do not use sudo to run this command. The container is set up to run as
#       devuser, if you run as root it won't have the environment variables set
#       by Docker build. Also if you run using sudo and the switch a session
#       file in the /tmp directory will have to be deleted.
#
#       https://github.com/docker-library/php/blob/master/8.0/bullseye/apache/Dockerfile
#
#       The PHP docker image uses "apache2-foreground" to run Apache which has
#       an odd behavior of quitting when "the terminal" is resized. You will
#       cause the terminal to resize if you open this project in VSCode.
#
#       The command "apache2ctl start" does not have this issue. They give a
#       reason for not using this command by default.
#
#   gh auth login
#
#       When you run this in the VSCode terminal you can accept the default
#       answers to the questions they ask. VSCode will open a web browser on the
#       host (MacOS or Windows) so the process is very easy.
#
#   git config --global user.email "you@example.com"
#   git config --global user.name "Your Name"



composer require mattcalkins/colby
