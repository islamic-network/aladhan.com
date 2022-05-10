## بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ

[![CircleCI](https://circleci.com/gh/islamic-network/aladhan.com.svg?style=shield)](https://circleci.com/gh/islamic-network/aladhan.com)
[![](https://img.shields.io/docker/pulls/islamicnetwork/aladhan.com.svg)](https://cloud.docker.com/u/islamicnetwork/repository/docker/islamicnetwork/aladhan.com)
[![](https://img.shields.io/github/release/islamic-network/aladhan.com.svg)](https://github.com/islamic-network/aladhan.com/releases)
[![](https://img.shields.io/github/license/islamic-network/aladhan.com.svg)](https://github.com/islamic-network/aladhan.com/blob/master/LICENSE)
![GitHub All Releases](https://img.shields.io/github/downloads/islamic-network/aladhan.com/total)

# Al Adhan App

This is the code you see deployed on https://aladhan.com. This documentation explains how you can get setup
to deploy your own instance and contribute code.

## Technology Stack and Requirements
* PHP 8.0
* Composer - See composer.json for other dependencies
* Slim Framework v4
* Bootstrap 3
* JQuery
* Bootstrap Multiselect
* Docker

## Getting Started

### Running the App

The app is fully Dockerised. You **just need docker** to spin it up.

A production ready Docker image of the app is published as:

* quay.io/islamic-network/aladhan.com on QUAY
* islamicnetwork/aladhan.com on Docker Hub

To get your own instance up, simply run:

```
docker run -it -p 8082:8080  islamicnetwork/aladhan.com:latest
``` 

You can now visit http://localhost:8082/ and start using it.

### Build and Contribute

**Please note that the Dockerfile included builds a production ready container which has opcache switched on and xdebug turned off, so you will only see your changes every 5 minutes if you are developing. To actively develop, change the ```FROM islamicnetwork/php:8.0-apache``` line to ```islamicnetwork/php:8.0-apache-dev```.**

1. Clone this repository
2. Run ```docker build . -t islamicnetwork/aladhan.com```. This will build an image with production dependencies only.
3. Run ```docker run -it -p 8081:8080  -v $(pwd)/.:/var/www islamicnetwork/aladhan.com``` to spin up the built image.
3. Run ```composer install``` to add development dependencies.
6. Make sure you have internet connectivity so the app can connect to https://api.aladhan.com.
7. Open your browser and browse to http://localhost:8081/.
8. Any changes you make will be synced to the Docker container. You just refresh the page in the browser to see them.

## Scaling and Sizing

This app takes 37 MB per apache process / worker and is set to have a maximum of 10 Apache workers.

A single instance should be sized with a maximum of 400 MB RAM, after which you should scale it horizontally.

## Contributing Code

You can contribute code by raising a pull request.

There's a backlog of stuff under issues for things that potentially need to be worked on, so please feel free to pick something up from there or contribute your own improvements.

You can also join the community at https://community.islamic.network/ or the Islamic Network Discord Server to discuss any of the apps or APIs @ https://discord.gg/FwUy69M.
