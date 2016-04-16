#!/usr/bin/env bash

function d-kill {
    docker kill $(docker ps | awk {'print $1'} | tail -n 1)
}

function d-build {
    docker build -t plagiarism .
}

function d-shell {
    docker exec -i -t $(docker ps | awk {'print $1'} | tail -n 1) /bin/bash
}

function d-run {
    docker run -v $(pwd)/api:/plagiarism/api -v $(pwd)/web:/plagiarism/web -v $(pwd)/logs:/logs -p 80:80 -p 15672:15672 plagiarism
}