# Setup application

Entre no diretório .docker e siga as instruções a seguir.

#### Levantar os containes (laravel, nginx e mysql):
```docker compose up -d --build```

#### Define o arquivo .env
```docker compose exec app cp .env.example .env```

#### Instale as dependências
```docker compose exec app composer install```

#### Gerar o APP_KEY
```docker compose exec app php artisan key:generate```

#### Gerar migrations
```docker compose exec app php artisan migrate```

#### Popular o DB com dados de produtos falsos
```docker compose exec app php artisan db:seed```

#### Configurar o ASAAS
Defina no arquivo .env as ENV do ASAAS
```
ASAAS_API_BASE_URL=https://api-sandbox.asaas.com;
ASAAS_API_VERSION=v3
ASAAS_API_TOKEN=SEU_TOKEN
```

#### [Opcional] Rode os testes:
```docker compose exec app php artisan test```

Adicione o ```--coverage``` no final do comando acima para ver a % de cobertura do teste.



##### Obs.: Talvez seja necessário rodar os comandos docker com o usuário root.
```sudo docker ....```

