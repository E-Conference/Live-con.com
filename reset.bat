php app/console cache:clear
php app/console doctrine:database:drop --force
php app/console doctrine:database:create
php app/console doctrine:schema:update --force
php app/console assets:install web
php app/console wwwconf:database:init
php app/console livecon:admin:create admin admin@admin.fr admin
php app/console fos:user:promote admin ROLE_ADMIN

