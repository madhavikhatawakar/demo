# Pull base image.
FROM ubuntu:14.04

# Install following packages

RUN apt-get update && apt-get -y install redis-server vim php5-cli php5-common php5-mysql php5-xdebug libapache2-mod-php5 apache2 nano \
software-properties-common --force-yes -y

RUN add-apt-repository ppa:ondrej/php && apt-get update && apt-get install php5 curl php5-curl php5-mcrypt php5-gd --force-yes -y

# Copy source code to /var/www/html/

RUN mkdir -p /var/www/html/magento/
# Install magerun
RUN curl -o magerun https://raw.githubusercontent.com/netz98/n98-magerun/master/n98-magerun.phar && \
    chmod +x ./magerun && \
    cp ./magerun /usr/local/bin/ && \
    rm ./magerun && \
    apt-get update && \
    apt-get install -qy mysql-client

RUN php5enmod mcrypt		
#RUN service apache2 reload

COPY php.ini /usr/local/etc/php/php.ini

RUN rm -f /etc/apache2/sites-available/000-default.conf
COPY 000-default.conf /etc/apache2/sites-available/

#RUN rm -f /etc/default/varnish
#COPY varnish /etc/default/

#RUN rm -f /etc/varnish/default.vcl
#COPY default.vcl /etc/varnish/

RUN rm -f /etc/apache2/ports.conf
COPY ports.conf /etc/apache2/


EXPOSE 8080

#EXPOSE 80

#RUN service apache2 restart

#RUN service varnish restart

#CMD /bin/bash -c "start.sh"

CMD /usr/sbin/apache2ctl -D FOREGROUND
