FROM        ubuntu:14.04.1
MAINTAINER  madhavikh "madhavikh@cybage.com"

RUN apt-get update -qq && \
  apt-get upgrade -yqq && \
  apt-get -yqq install varnish curl && \
  apt-get -yqq clean

# Make our custom VCLs available on the container
ADD default.vcl /etc/varnish/default.vcl
ADD varnish /etc/default/varnish
ENV VARNISH_PORT 80

# Expose port 80
EXPOSE 80

ADD start.sh /start.sh
CMD ["/start.sh"]
