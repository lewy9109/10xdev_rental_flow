####
RUN Server:
```
docker compose up -d 
```


Check the status of the server with the following command:
need cli: `jq | watch | curl`
```
watch -n1  -d "curl -s 0:8080 | jq '.'"
```

```
composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition=2.4.6 src
```