# Plagiarism service


## Run
docker build -t plagiarism .
docker run -v $(pwd)/api:/plagiarism/api -v $(pwd)/web:/plagiarism/web -v $(pwd)/logs:/logs -p 80:80 plagiarism