## Redis Caching Setup

This project uses Redis as the cache layer for Laravel API aggregation endpoints. Redis reduces repeated PostgreSQL aggregation queries by temporarily storing API responses.

### Running Redis with Docker

Start the Redis container:

```
docker start redis
```

If the Redis container has not been created yet, run:

```
docker run -d --name redis -p 6379:6379 redis:latest
```

Verify that Redis is running:
```
docker ps
```

### Laravel Redis Configuration
The Laravel `.env` file should include the following cache and Redis settings:
```
CACHE_STORE=redis

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

After updating `.env`, clear Laravel's cached configuration:
```
php artisan optimize:clear
```

### Cached API Endpoints
The analytics API endpoints use Laravel's `Cache::remember()` with a 5-minute time-to-live.

Ex:
```
$result = Cache::remember('kpis', 300, function () {
    return DB::select("
        SELECT ...
        FROM delivery_records;
    ");
});
```
This means Laravel first checks Redis for an existing cached response. If the cache exists, Laravel returns it immediately. 
If not, Laravel runs the SQL query, stores the result in Redis for 300 seconds, and returns the response.

### Checking Redis Cache Keys

Open the Redis CLI inside the Docker container:
```
docker exec -it redis redis-cli
```
Select the Redis database in which the cached values are stored. In this case, and by default, it is stored in db 1:
```
SELECT 1
KEYS *
```

To check the remaining lifetime of a cached key:
```
TTL laravel-database-laravel-cache-kpis
```

### Clearing Cache
To clear all Laravel cache entries:
```
php artisan cache:clear
```
