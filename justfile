test:
    HURL_host=http://localhost:8080/api/v1 hurl ./tests --test --color

try:
    HURL_host=http://localhost:8080/api/v1 hurl ./tests --very-verbose | jq