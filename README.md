### Getting Started

The repo is using Laravel Sail and has been developed on Unbuntu but as Laravel Sail uses docker technology the repo should work on all operating systems. 


1. Install Docker
2. Clone Repo
3. Cd in repo
4. Execute script

``docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs``
    
    
### Operations

1. ```./vendor/bin/sail up```
2. ``` ./vendor/bin/sail composer install```

    
### Troubleshooting
 
 Stop all containers, other containers may be sharing same port not allowing the application to run
 
 1. ``docker stop $(docker ps -aq)``
 
**Error : service "laravel.test" is not running container #1**

1. Run ```docker ps -a```
2. Get container id of ``sail-8.2/app``
3. run ```docker start <CONTAINER_ID HERE>```

### Overview

Please look at CSVReader and CSVReaderTest. 

Please put your CSV file in ```storage/app/csv```

Please put your JSON file in ```storage/app/output```

Also, please make sure the CSV file is UTF-8 :)

Thanks.

### Summary

The task completed is now done in the GitHub repo using docker, so the environment should be the same for every developer. Also, now that it is in the private repo, it should be easier to test, as the other developers can drop the CSV file into the application and add it to the test case to test the output. The previous solution may have needed to be fixed as the wrong import may have been used or not included when the function was used in a different environment, or the OS encoded the CSV data in another format.




