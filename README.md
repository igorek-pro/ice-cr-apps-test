# ice-cr-apps-test

## Project setup
1. go to project directory (cd ice-cr-apps-test)
2. docker build php -t php74test
3. docker run --name php74test -d -p 8000:8000 -v $(pwd)/:/var/www php74test
4. DONE! go to localhost:8000 in your browser

Igor Detskin [detskinigor@gmail.com]