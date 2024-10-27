# 概要

## 事前準備

```bash
docker-compose up -d
docker exec -it docker-php bash
cd php_libs/
composer require piece/stagehand-testrunner
```

```bash
curl http://localhost:8080/test.json
```