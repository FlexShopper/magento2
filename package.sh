#!/bin/sh
tar --exclude='./flexshopper.tar.gz' --exclude='./bitbucket-pipelines.yml' --exclude='./package.sh' --exclude='.git'  -zcvf ./flexshopper.tar.gz .