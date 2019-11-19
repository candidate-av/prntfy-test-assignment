## Deployment

Clone, install, create & populate DB
~~~
git clone https://github.com/candidate-av/prntfy-test-assignment.git
cd prntfy-test-assignment
// Adjust .env DATABASE_URL
composer install
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate
bin/console doctrine:fixtures:load

bin/console doctrine:database:create --env=test
bin/console doctrine:migrations:migrate --env=test
~~~

Run server
~~~
bin/console server:run  
~~~

## Run tests
~~~
 php bin/phpunit
~~~

## Example API calls

Add Product
~~~
curl --request POST \
  --url http://127.0.0.1:8000/products \
  --header 'content-type: application/json' \
  --data '{"price": 9.15,"colorCode": "gr","sizeCode": "xl","typeCode": "srt"}'
~~~

Add Order
~~~
curl --request POST \
  --url http://127.0.0.1:8000/orders \
  --header 'content-type: application/json' \
  --data '{"products": [{"productId": 1, "quantity": 2}, {"productId": 2,"quantity": 1}]}'
~~~

Get all Orders
~~~
curl --request GET --url http://127.0.0.1:8000/orders 
~~~

Get Orders by type
~~~
curl --request GET --url 'http://127.0.0.1:8000/orders?typeCode=srt' 
~~~