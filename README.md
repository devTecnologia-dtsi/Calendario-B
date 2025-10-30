# Calendario-B
Back de la aplicacion de administracion de calendarios 



## DEPLEGAR CONTENEDORES EN AMBIENTES DEV (LOCAL)

```bash
docker compose -f docker-compose.yml -f docker-compose.dev.yml up --build -d
```


## DESPLEGAR LOS CONTENEDORES EN EL AMBIENTE QA

```bash
docker compose -f docker-compose.yml -f docker-compose.qa.yml up --build -d
```

## DEPLEGAR LOS CONTENEDORES EN EL AMBIENTE DE PRODUCCION

```bash
docker compose up -d
```