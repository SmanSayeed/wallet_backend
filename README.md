
## WDMS 
Wallet and Denomination Management System

Used technologies:

Backend: Laravel 10 
[(Backend GITHUB)](https://github.com/SmanSayeed/wallet_backend)


Frontend: Next JS 14
[(Frontend GITHUB)](https://github.com/SmanSayeed/wallet-frontend)

Database: MySQL

## API Documentation

[API Documentation](https://www.postman.com/universal-shuttle-184021/workspace/wdms/collection/10943642-cec55610-56fd-444f-b851-a91776964783?action=share&creator=10943642&active-environment=10943642-b997e5f9-2168-4728-8512-2278cd4bd7a5)


## System Design Planning and Flow Chart

[Open  System Design Planning and Flow Chart](https://alert-sidewalk-474.notion.site/317c4b194a754e1caa2b1e9c4e3d549c?v=44e88655acb1457d945f8bcabb731ee2&pvs=4)

## Task progress:

[Open Task progress google sheet link](https://docs.google.com/spreadsheets/d/1BTnx7c_VrUTk_ZFVzF7VGoxXEa7UwFhHq0XzuN9QNZw/edit?usp=sharing)



### 1# Install backend
1. clone the repository
``` 
git clone https://github.com/SmanSayeed/wallet_backend.git
```
2. Update composer
```
composer update
```
3. Copy .env.example to .env, setup database and use following commands:
```
php artisan key generate
php artisan queue:table
php artisan migrate --seed
php artisan passport::install
php artisan queue:listen 
```
4. Run backend api:
```
php artisan serve
```
