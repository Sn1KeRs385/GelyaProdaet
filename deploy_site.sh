git fetch
git reset --hard origin/master

docker compose build site-api
docker compose up -d site-api
