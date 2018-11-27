#!/bin/bash

exec ab -c5 -n5 http://127.0.0.1:8080/
