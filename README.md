# Picus Cloud API Client
Client PHP per utilizzare il servizio API di Picus Cloud (https://www.picus.cloud)


## Utilizzo
La libreria è composta da un'unica classe: *PicusCloudApiClient* e da quattro metodi: *select*, *insert*, *update*, *delete*

### Inizializzazone
```php
$instance = '......'; // Instance, username e password forniti dal servizio
$username = '......';
$password = '......';
$pcac = new PicusCloudApiClient($instance, $username, $password);
```
### Estrazione dati
```php
/**
 * Comando SELECT
 * @param string $table Il nome della tabella
 * @param string $keyName (opz.) Il nome del campo sul quale effettuare il filtro
 * @param string $keyValue (opz.) Il valore del campo sul quale effettuare il filtro
 * @param array $arrConditions (opz.) Array contenente condizioni di filtraggio. Es:
 * 					[
 * 						['id_cliente', 	'eq', 	'15'],
 * 						['data', 		'lt', 	'2010-02-01 00:00:00'],
 * 						['data', 		'gte', 	'2010-01-01 00:00:00'],
 * 						['esito', 		'in', 	'OK,KO,ERR,10'],
 * 					]
 * 					// tutti gli operatori: eq, neq, gt, gte, lt, lte, in, nin
 * @param array $arrFields (opz.) Lista di campi da estrarre. Default: tutti
 * @return array ['ack' => 'OK'|'KO', 'data' => [...], 'error' => 'xxxx']
 */
function select($table, $keyName = NULL, $keyValue = NULL, $arrConditions = NULL, $arrFields = NULL) {}
```
### Inserimento dati
```php
/**
 * Inserisce un record
 * @param string $table Il nome della tabella
 * @param array $arrValues Array con coppie nomeCampo => valoreCampo. Es: ['nomeCampo' => 'valoreCampo', 'nomeCampo2' => 'valoreCampo2']
 * @return array|NULL ['ack' => 'OK'|'KO', 'insertId' => 'nnnn', 'error' => 'xxxx']
 */
function insert($table, $arrValues) {}
```
### Aggiornamento dati
```php
/**
 * Aggiorna un record
 * @param string $table Il nome della tabella
 * @param integer $idValue Il valore ID del record da aggiornare
 * @param array $arrValues Array con coppie nomeCampo => valoreCampo. Es: ['nomeCampo' => 'valoreCampo', 'nomeCampo2' => 'valoreCampo2']
 * @return array|NULL ['ack' => 'OK'|'KO', 'affectedRows' => 'nnnn', 'error' => 'xxxx']
 */
function update($table, $idValue, $arrValues) {}
```
### Eliminaizone dati
```php
/**
 * Elimina un record
 * @param string $table Il nome della tabella
 * @param integer $idValue Il valore ID del record da aggiornare
 * @return array|NULL ['ack' => 'OK'|'KO', 'affectedRows' => 'nnnn', 'error' => 'xxxx']
 */
function delete($table, $idValue) {}
```

## Esempi di utilizzo
Vedere cartella **examples**
