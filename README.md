## Guzzle Async Performance Test

The main question here is "will there be performance gain if we use async/non-blocking network io?"
The answer is YES!!! **Guzzle `sendAsync()` shows better performance than `send()`**

`sendAsync()` returns `PromiseInterface`. For guzzle's Promise @see [https://github.com/guzzle/promises](https://github.com/guzzle/promises)

<!--ts-->
## Table of Contents
* [1 Experiment environment](#1-experiment-environment)
* [2 Run apache bench](#2-run-apache-bench)
* [3 For local test](#3-for-local-test)
* [4 AWS Elasticbeanstalk Provisioning Logs](#4-aws-elasticbeanstalk-provisioning-logs)
<!--te-->

#### 1 Experiment environment
- Amazon elasticbeanstalk t2.small instance, PHP 7.3 running on 64bit Amazon Linux/2.9.4
- Two scenarios 

#### 2 Run apache bench
> <small>scenario #1</small> **`SERVER_URI=httpbin.org/get`**<br/>
> macbook -(curl call)-> http://quzzle-async-performance.ap-northeast-2.elasticbeanstalk.com/sync -(guzzle call)-> httpbin.org/get

-c 5 -n 100|Time taken for tests|Requests per second
---|---|---
sync|11.022 seconds|9.07 [#/sec] (mean)
async|10.910 seconds|9.17 [#/sec] (mean)

> <small>scenario #2</small> **`SERVER_URI=http://quzzle-async-performance.ap-northeast-2.elasticbeanstalk.com`**<br/>
>  macbook -(curl call)-> http://quzzle-async-performance.ap-northeast-2.elasticbeanstalk.com/sync -(guzzle call)-> http://quzzle-async-performance.ap-northeast-2.elasticbeanstalk.com

-c 10 -n 300|Time taken for tests|Requests per second
---|---|---
sync|4.509 seconds|66.53 [#/sec] (mean)
async|2.428 seconds|123.57 [#/sec] (mean)

<details>
<summary>Experiment Logs</summary>

```bash
# ------------------------------------------------------------------------------
# SERVER_URI=httpbin.org/get
# ------------------------------------------------------------------------------

# This command will make 100 requests using 5 threads
~ $ ab -c 5 -n 100 -H "Accept: application/json" http://quzzle-async-performance.ap-northeast-2.elasticbeanstalk.com/sync

# Concurrency Level:      5
# Time taken for tests:   11.022 seconds
# Complete requests:      100
# Failed requests:        0
# Total transferred:      42900 bytes
# HTML transferred:       25100 bytes
# Requests per second:    9.07 [#/sec] (mean)
# Time per request:       551.119 [ms] (mean)
# Time per request:       110.224 [ms] (mean, across all concurrent requests)
# Transfer rate:          3.80 [Kbytes/sec] received
# 
# Connection Times (ms)
#               min  mean[+/-sd] median   max
# Connect:        7   10   2.0      9      17
# Processing:   398  501 104.6    496     876
# Waiting:      397  500 104.4    496     875
# Total:        406  511 105.3    506     884
# 
# Percentage of the requests served within a certain time (ms)
#   50%    506
#   66%    611
#   75%    613
#   80%    614
#   90%    615
#   95%    711
#   98%    715
#   99%    884
#  100%    884 (longest request)

~ $ ab -c 5 -n 100 -H "Accept: application/json" http://quzzle-async-performance.ap-northeast-2.elasticbeanstalk.com/async
# Concurrency Level:      5
# Time taken for tests:   10.910 seconds
# Complete requests:      100
# Failed requests:        0
# Total transferred:      42900 bytes
# HTML transferred:       25100 bytes
# Requests per second:    9.17 [#/sec] (mean)
# Time per request:       545.513 [ms] (mean)
# Time per request:       109.103 [ms] (mean, across all concurrent requests)
# Transfer rate:          3.84 [Kbytes/sec] received
# 
# Connection Times (ms)
#               min  mean[+/-sd] median   max
# Connect:        8   10   3.0     10      26
# Processing:   399  511  90.3    502     655
# Waiting:      399  511  90.2    502     655
# Total:        407  522  90.2    510     667
# 
# Percentage of the requests served within a certain time (ms)
#   50%    510
#   66%    613
#   75%    614
#   80%    614
#   90%    650
#   95%    663
#   98%    666
#   99%    667
#  100%    667 (longest request)

# ------------------------------------------------------------------------------
# SERVER_URI=http://quzzle-async-performance.ap-northeast-2.elasticbeanstalk.com
# ------------------------------------------------------------------------------

~ $ ab -c 10 -n 300 -H "Accept: application/json" http://quzzle-async-performance.ap-northeast-2.elasticbeanstalk.com/sync
# Concurrency Level:      10
# Time taken for tests:   4.509 seconds
# Complete requests:      300
# Failed requests:        0
# Total transferred:      57000 bytes
# HTML transferred:       3900 bytes
# Requests per second:    66.53 [#/sec] (mean)
# Time per request:       150.308 [ms] (mean)
# Time per request:       15.031 [ms] (mean, across all concurrent requests)
# Transfer rate:          12.34 [Kbytes/sec] received
# 
# Connection Times (ms)
#               min  mean[+/-sd] median   max
# Connect:        7    9   1.2      9      22
# Processing:    36  135 485.3     45    2833
# Waiting:       36  135 485.3     45    2833
# Total:         45  145 485.0     54    2841
# 
# Percentage of the requests served within a certain time (ms)
#   50%     54
#   66%     56
#   75%     58
#   80%     58
#   90%     64
#   95%     73
#   98%   2740
#   99%   2746
#  100%   2841 (longest request)

~ $ ab -c 10 -n 300 -H "Accept: application/json" http://quzzle-async-performance.ap-northeast-2.elasticbeanstalk.com/async
# Concurrency Level:      10
# Time taken for tests:   2.428 seconds
# Complete requests:      300
# Failed requests:        0
# Total transferred:      57000 bytes
# HTML transferred:       3900 bytes
# Requests per second:    123.57 [#/sec] (mean)
# Time per request:       80.925 [ms] (mean)
# Time per request:       8.093 [ms] (mean, across all concurrent requests)
# Transfer rate:          22.93 [Kbytes/sec] received
# 
# Connection Times (ms)
#               min  mean[+/-sd] median   max
# Connect:        8   11   3.5     11      37
# Processing:    36   65 100.7     45     608
# Waiting:       36   65 100.7     44     608
# Total:         47   77 100.9     55     619
# 
# Percentage of the requests served within a certain time (ms)
#   50%     55
#   66%     58
#   75%     60
#   80%     62
#   90%     77
#   95%    100
#   98%    614
#   99%    619
#  100%    619 (longest request)
```
</details>

#### 3 For local test

Clone and build
```bash
~ $ git clone git@github.com:appkr/quzzle-async-performance.git
~ $ cd quzzle-async-performance
~/quzzle-async-performance $ cp .env.example .env
~/quzzle-async-performance $ composer install 
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

#### 4 AWS Elasticbeanstalk Provisioning Logs

<details>
<summary>AWS Elasticbeanstalk Provisioning Logs</summary>

Create eb(elasticbeanstalk) application
```bash
~/quzzle-async-performance $ eb init --profile appkr
# 10) ap-northeast-2 : Asia Pacific (Seoul)

# Enter Application Name
# (default is "quzzle-async-performance"):

# It appears you are using PHP. Is this correct?
# (Y/n):

# Select a platform version.
# 2) PHP 7.3

# Do you wish to continue with CodeCommit? (y/N) (default is n): N

# Do you want to set up SSH for your instances?
# (Y/n):

# Select a keypair.
# 1) aws-eb
```

Create eb(elasticbeanstalk) environment
```bash
~/quzzle-async-performance $ eb create benchmark -c quzzle-async-performance -i t2.small --envvars SERVER_URI=http://httpbin.org/get --profile appkr --region ap-northeast-2
# Creating application version archive "app-65cf-200426_105847".
# Uploading quzzle-async-performance/app-65cf-200426_105847.zip to S3. This may take a while.
# Upload Complete.
# Environment details for: benchmark
#   Application name: quzzle-async-performance
#   Region: ap-northeast-2
#   Deployed Version: app-65cf-200426_105847
#   Environment ID: e-uzfan9cb4a
#   Platform: arn:aws:elasticbeanstalk:ap-northeast-2::platform/PHP 7.3 running on 64bit Amazon Linux/2.9.4
#   Tier: WebServer-Standard-1.0
#   CNAME: quzzle-async-performance.ap-northeast-2.elasticbeanstalk.com
#   Updated: 2020-04-26 01:58:49.361000+00:00
# Printing Status:
# 2020-04-26 01:58:48    INFO    createEnvironment is starting.
# 2020-04-26 01:58:49    INFO    Using elasticbeanstalk-ap-northeast-2-628988759087 as Amazon S3 storage bucket for environment data.
# 2020-04-26 01:59:08    INFO    Created security group named: sg-0b06d7c51bff42e54
# 2020-04-26 01:59:10    INFO    Created load balancer named: awseb-e-u-AWSEBLoa-BKFL3XYL0Z2T
# 2020-04-26 01:59:25    INFO    Created security group named: awseb-e-uzfan9cb4a-stack-AWSEBSecurityGroup-161HNNCY6AHI3
# 2020-04-26 01:59:25    INFO    Created Auto Scaling launch configuration named: awseb-e-uzfan9cb4a-stack-AWSEBAutoScalingLaunchConfiguration-ICF5JCKMF71H
# 2020-04-26 02:00:27    INFO    Created Auto Scaling group named: awseb-e-uzfan9cb4a-stack-AWSEBAutoScalingGroup-O6FEUW3O1T0X
# 2020-04-26 02:00:27    INFO    Waiting for EC2 instances to launch. This may take a few minutes.
# 2020-04-26 02:00:27    INFO    Created Auto Scaling group policy named: arn:aws:autoscaling:ap-northeast-2:628988759087:scalingPolicy:e46a5851-fa37-46ae-bd35-0ddc9f11ad75:autoScalingGroupName/awseb-e-uzfan9cb4a-stack-AWSEBAutoScalingGroup-O6FEUW3O1T0X:policyName/awseb-e-uzfan9cb4a-stack-AWSEBAutoScalingScaleDownPolicy-1RCT7YQECGWW0
# 2020-04-26 02:00:27    INFO    Created Auto Scaling group policy named: arn:aws:autoscaling:ap-northeast-2:628988759087:scalingPolicy:5e95b072-5c6d-4063-8973-622f6f4e70e2:autoScalingGroupName/awseb-e-uzfan9cb4a-stack-AWSEBAutoScalingGroup-O6FEUW3O1T0X:policyName/awseb-e-uzfan9cb4a-stack-AWSEBAutoScalingScaleUpPolicy-1AFRE9R89DSNE
# 2020-04-26 02:00:28    INFO    Created CloudWatch alarm named: awseb-e-uzfan9cb4a-stack-AWSEBCloudwatchAlarmHigh-7LSRCD6OU5OT
# 2020-04-26 02:00:28    INFO    Created CloudWatch alarm named: awseb-e-uzfan9cb4a-stack-AWSEBCloudwatchAlarmLow-1UB1UNQAXQPO
# 2020-04-26 02:00:59    INFO    Application available at quzzle-async-performance.ap-northeast-2.elasticbeanstalk.com.
# 2020-04-26 02:01:00    INFO    Successfully launched environment: benchmark
```

Deploy a new version
```bash
~/quzzle-async-performance $ eb deploy benchmark  --label="v0.0.5" --verbose --timeout=10 --profile appkr --region ap-northeast-2
# INFO: Deploying code to benchmark in region ap-northeast-2
# Creating application version archive "v0.0.5".
# INFO: creating zip using git archive HEAD
# INFO: git archive output: .ebextensions/
# .ebextensions/01-command.config
# .env.example
# .gitignore
# README.md
# composer.json
# composer.lock
# logs/
# logs/.gitignore
# public/
# public/.htaccess
# public/index.php
# src/
# src/Client/
# src/Client/AsyncController.php
# src/Client/DomainService.php
# src/Client/SyncController.php
# src/Common/
# src/Common/Logger.php
# src/Server/
# src/Server/FooBarController.php
# INFO: Uploading archive to s3 location: quzzle-async-performance/v0.0.5.zip
# Uploading quzzle-async-performance/v0.0.5.zip to S3. This may take a while.
# Upload Complete.
# INFO: Creating AppVersion v0.0.5
# 2020-04-26 03:20:27    INFO    Environment update is starting.
# 2020-04-26 03:21:06    INFO    Deploying new version to instance(s).
# 2020-04-26 03:21:36    INFO    New application version was deployed to running EC2 instances.
# 2020-04-26 03:21:36    INFO    Environment update completed successfully.
```
</details>
