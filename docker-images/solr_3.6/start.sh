#!/bin/bash
service apache-solr start
tail -f /var/log/apache-solr.log
