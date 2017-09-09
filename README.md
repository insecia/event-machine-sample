# event-machine-sample
Small sample application built on top of 
[Event Machine](https://github.com/proophsoftware/event-machine) and 
[Event Machine Skeleton](https://github.com/proophsoftware/event-machine-skeleton).

## Installation
Please make sure you have installed [Docker](https://docs.docker.com/engine/installation/ "Install Docker") and [Docker Compose](https://docs.docker.com/compose/install/ "Install Docker Compose").

```bash
$ docker run --rm -it -v $(pwd):/app prooph/composer:7.1 create-project insecia/event-machine-sample
$ cd event-machine-sample
$ sudo chown $(id -u -n):$(id -g -n) . -R
$ docker-compose up -d
$ docker-compose run php php scripts/create_event_stream.php
```
