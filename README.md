## Guzzle Async Performance Test

For guzzle's Promise @see [https://github.com/guzzle/promises](https://github.com/guzzle/promises)

This experiment was made to find the answer on my personal question, which was, will there be performance gain if we use async network io?

#### Experiment environment
- Amazon elasticbeanstalk t2.small instance
- Use http://httpbin.org/get to make network io

#### Run apache bench
```bash
~ $ ab -c 5 -n 100 -H "Accept: application/json" http://{host}/sync
# This will make 100 requests using 5 threads

~ $ ab -c 5 -n 100 -H "Accept: application/json" http://{host}/async
```

---

#### For local test

Clone and build
```bash
~ $ git clone git@github.com/appkr:quzzle-async-performance.git
~ $ cd quzzle-async-performance
~/quzzle-async-performance $ git clone git@github.com/appkr:quzzle-async-performance.git 
```

Run local server for the server and the client
> NOTE. PHP local server does not handle multiple request, meaning processes only one request at a time.
> So, it does not fit on the purpose of benchmark test. To do the benchmark, we must use production-level web server.
```bash
# server
~/quzzle-async-performance $ php -S localhost:8001 -t public

# client
~/quzzle-async-performance $ php -S localhost:8000  -t public
```

Make a request
```bash
# sync call to server
~ $ curl -s http://localhost:8000/sync

# async call to server
~ $ curl -s http://localhost:8000/async
```

Monitor log
```bash
# server
~/quzzle-async-performance $ tail -f logs/server.log

# client
~/quzzle-async-performance $ tail -f logs/client.log
```
