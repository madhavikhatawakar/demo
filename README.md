Dockering the magento environment
-PHP
-Redis
-Solr
-magento
-Varnish

COMMANDS TO BE EXECUTED
1. for starting redis, solr container
docker-compose -p "dev" -f "docker-compose-common.yml" up

2. for starting php and installer

docker-compose -p "developer_name" up