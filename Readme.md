Dashboard Metrics Module
========================

This module is designed to integrated Graphite and Deploynaut to surface some server metrics to the client.

First, define the metric, then add it to a metric set. Then you can assign a metric set to an environment and enable the ability to see the metrics. 

Here are some default queries to add in:

Load Average
------------
```
alias(avg(server.{cluster}.{stack}.{env}.web.*.loadavg.1min),'Load Average')
```
Number of Webservers
--------------------
```
alias(sumSeries(changed(server.skinny.skinny.prod.web.*.apache.uptime)),'sum')
```
Average Response Time
---------------------
```
alias(maxSeries(server.{cluster}.{stack}.{env}.web.*.apache.mysite.request.time_95),'95th Percentile');alias(averageSeries(server.{cluster}.{stack}.{env}.web.*.apache.mysite.request.time_95),'Average')
```
Requests Per Minute
-------------------
```
alias(sumSeries(server.{cluster}.{stack}.{env}.web.*.apache.mysite.request.req_per_min),'Apache (Dynamic)');alias(sumSeries(server.{cluster}.{stack}.{env}.web.*.nginx.mysite.request.req_per_min),'Nginx (Static)')
```
