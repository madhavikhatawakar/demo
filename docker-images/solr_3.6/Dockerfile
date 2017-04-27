FROM ubuntu:14.04

# update packages
RUN apt-get -y update

# Install Oracle Java 7
#RUN apt-get -y install vim python-software-properties
#RUN add-apt-repository ppa:webupd8team/java
#RUN echo "oracle-java7-installer  shared/accepted-oracle-license-v1-1 boolean true" | debconf-set-selections
#RUN apt-get -y update && apt-get -y install oracle-java7-installer

RUN apt-get -y install default-jre

RUN apt-get -y install default-jdk

# Install Apache Solr
ENV SOLR_VERSION 3.6.2
ENV SOLR apache-solr-$SOLR_VERSION
ADD http://archive.apache.org/dist/lucene/solr/$SOLR_VERSION/$SOLR.tgz /opt/$SOLR.tgz
RUN tar -C /opt --extract --file /opt/$SOLR.tgz
RUN mv /opt/$SOLR /opt/solr

#COPY ./config /temp/solr-drupal-config

COPY ./conf_magento /temp/solr-drupal-config

RUN cd /temp/solr-drupal-config && cp -R . /opt/solr/example/solr/conf/

COPY apache-solr /etc/init.d/apache-solr

RUN chmod 755 /etc/init.d/apache-solr

COPY apache-solr-logs /etc/logrotate.d/apache-solr

RUN touch /var/log/apache-solr.log && \
chown root:adm /var/log/apache-solr.log && \
chmod 0640 /var/log/apache-solr.log

Add start.sh /start.sh
RUN chmod 777 start.sh
RUN  update-rc.d apache-solr defaults
# Run Apache Solr
WORKDIR /opt/solr
EXPOSE 8983

ENTRYPOINT ["/start.sh"]
#ENTRYPOINT service apache-solr start && bash
#CMD service apache-solr start -D FOREGROUND

