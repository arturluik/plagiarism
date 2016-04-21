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
    if [ $(docker ps | wc -l) -gt 1 ]; then
        d-kill
    fi
        docker run -v $(pwd):/plagiarism/ -v $(pwd)/logs:/logs -v $(pwd)/data:/data -p 80:80 -p 15672:15672 -p 5432:5432 plagiarism
}

function d-buildrun {
    d-build
    d-run
}
