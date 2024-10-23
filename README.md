# ca-web-scrapper 
Coding Assignment 2024: Web Scrapper

## Requirements

- Docker >= 24.*
- git *(optional)*


## Deployment

- First run

  ```docker compose up -d --build```


- Subsequent runs

  ```docker compose down -v && docker compose up -d```


- Stop and remove docker containers and connected volumes

  ```docker compose down -v```


## Access

- **API**
    - https://localhost:8080/api

- **Horizon dashboard**
    - https://localhost:8080/horizon


## Testing

### Test case 1

Expect to succeed

**Data**

```
{
  "urls": [
    "https://pigu.lt/lt/buitine-technika-ir-elektronika/pramogos-namuose/zaidimai-kompiuteriams",
    "https://pigu.lt/lt/buitine-technika-ir-elektronika/pramogos-namuose/zaidimai-kompiuteriams?page=2"
  ],
  "selectors": {
    "title": "p.product-name > a",
    "price": "span.price notranslate"
  }
}
```

### Test case 2

Expect to return empty result

**Data**

```
{
    "urls": [
        "https://www.varle.lt/kompiuteriniai-zaidimai/",
        "https://www.varle.lt/kompiuteriniai-zaidimai/?p=2"
    ],
    "selectors": {
        "title": "p.product-name > a",
        "price": "span.price notranslate"
    }
}
```

#### Test case 3

Expect to fail

**Data**

```
{
  "selectors": {
    "title": "p.product-name > a",
    "price": "span.price notranslate"
  }
}
```
## Code quality

- **Code quality tools** (automatically run by the pre-commit hook) 
    - Enter the `ws-php` container and run:
    - `./vendor/bin/grumphp run`


- **Unit Tests** isolated from code styling
    - Enter the `ws-php` container and run:
    - `./vendor/bin/phpunit`
