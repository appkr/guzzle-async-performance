## Guzzle Async Performance Test

For guzzle's Promise @see [https://github.com/guzzle/promises](https://github.com/guzzle/promises)

This experiment was made to find the answer on my personal question, which was, will there be performance gain if we use async network io?

#### Experiment environment
- Amazon elasticbeanstalk t2.small instance, PHP 7.3 running on 64bit Amazon Linux/2.9.4
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

---

#### AWS Elasticbeanstalk Provisioning Logs

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

