# NEOM - New Order Management

**NEOM** è un sistema di gestione ordini e prodotti, sviluppato in Symfony, utilizzando Docker per la gestione dell'ambiente e PostgreSQL come database.

## Tecnologie utilizzate
- **Symfony**: Framework PHP per la gestione di ordini e prodotti.
- **Docker**: Containerizzazione dell'ambiente di sviluppo per garantire coerenza.
- **PostgreSQL**: Database relazionale per gestire ordini e prodotti.
- **Doctrine**: ORM per la gestione dei dati nel database.
- **Mailer (Mailpit)**: Per testare l'invio di email in un ambiente di sviluppo.

## Requisiti
- Docker
- Docker Compose

## Setup dell'ambiente di sviluppo

1. **Clona il repository:**

   ```bash
   git clone https://github.com/giacomopozzobon/neom.git
   cd neom
   ```

2. **Configura il file .env:**
Il progetto utilizza Docker e PostgreSQL, quindi dovrai configurare il file .env con le seguenti variabili:

   ```bash
   POSTGRES_VERSION=16
   POSTGRES_DB=neom
   POSTGRES_USER=neom_user
   POSTGRES_PASSWORD=neom_password
   DATABASE_URL="pgsql://{POSTGRES_USER}:{POSTGRES_PASSWORD}@postgres:5432/{POSTGRES_DB}"
   ```

3. **Avvia i container Docker:**
Usa Docker Compose per avviare il progetto. Assicurati di avere Docker e Docker Compose installati.

   ```bash
   docker-compose build
   docker-compose up
   ```

Questo avvierà i seguenti servizi:

- PostgreSQL sulla porta 5432.
- Mailer (Mailpit) sulle porte 1025 (SMTP) e 8025 (webmail).
- PHP con Symfony e Doctrine.

4. **Installa le dipendenze PHP:**

Una volta avviato il container, accedi al container PHP:

   ```bash
   docker exec -it neom_php bash
   ```

All'interno del container, esegui:

   ```bash
   composer install
   ```

5. **Crea le tabelle del database:**

Dopo aver configurato il database, puoi creare le tabelle tramite Doctrine:

   ```bash
   php bin/console doctrine:schema:create
   ```

6. **Accedi al progetto:**

Il progetto Symfony dovrebbe essere ora attivo. Puoi accedere all'applicazione tramite il tuo browser:

   ```bash
   http://localhost
   ```
Il progetto sarà in esecuzione sul server PHP all'interno del container.

## Esecuzione dei Test
Al momento, non sono stati implementati test nel progetto. Tuttavia, se necessario, puoi configurare PHPUnit per eseguire test unitari o di integrazione.

In futuro, eseguirai i test con il comando:

   ```bash
   php bin/phpunit
   ```

## Docker
Il progetto è già configurato per l'esecuzione tramite Docker. Puoi usare Docker Compose per eseguire i vari servizi come PostgreSQL, Mailpit e PHP.

1. **docker-compose.yml:** Configura i servizi di PostgreSQL, PHP e Mailpit.
2. **docker-compose.override.yml:** Configura porte specifiche per i servizi, come il database PostgreSQL sulla porta 5432 e il mailer Mailpit sulle porte 1025 e 8025.