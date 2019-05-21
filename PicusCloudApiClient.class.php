<?php

/**
 * Libreria Client PHP per utilizzare le API di Picus Cloud - https://www.picus.cloud
 * @license GPL v3.0 <http://www.gnu.org/licenses/gpl.html>
 * @author Itala Tecnologia Informatica S.r.l. - www.itala.it
 * @version 1.0
 */


class PicusCloudApiClient
{
	
	function __construct($instance, $username, $password)
	{
		$this->instance = $instance;
		$this->username = $username;
		$this->password = $password;
	}
	
	
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
	function select($table, $keyName = NULL, $keyValue = NULL, $arrConditions = NULL, $arrFields = NULL)
	{
		if (!$this->ensureAuthentication()) {
			return array(
				'ack' => 'KO',
				'error' => $this->authError
			);
		}
		$data = array(
			'action' => 'SELECT',
			'token' => $this->authToken,
			'table' => $table
		);
		if ($keyName !== NULL && $keyValue !== NULL) {
			$data['keyname'] = $keyName;
			$data['keyvalue'] = $keyValue;
		}
		if ($arrConditions !== NULL) {
			$arr = [];
			foreach ($arrConditions as $arrCondition) {
				$arr[] = implode(',', $arrCondition);
			}
			$data['conditions'] = implode(';', $arr);
		}
		if ($arrFields !== NULL) {
			$data['fields'] = implode(',', $arrFields);
		}
		return $this->sendToEndpoint($data);
	}
	
	/**
	 * Inserisce un record
	 * @param string $table Il nome della tabella
	 * @param array $arrValues Array con coppie nomeCampo => valoreCampo. Es: ['nomeCampo' => 'valoreCampo', 'nomeCampo2' => 'valoreCampo2']
	 * @return array|NULL ['ack' => 'OK'|'KO', 'insertId' => 'nnnn', 'error' => 'xxxx']
	 */
	function insert($table, $arrValues)
	{
		if (!$this->ensureAuthentication()) {
			return array(
				'ack' => 'KO',
				'error' => $this->authError
			);
		}
		$data = array(
			'action' => 'INSERT',
			'token' => $this->authToken,
			'table' => $table
		);
		$arr = [];
		foreach ($arrValues as $fieldName => $fieldValue) {
			$arr[] = $fieldName . '=' . urlencode($fieldValue);
		}
		$data['values'] = implode('&', $arr);
		
		return $this->sendToEndpoint($data);
	}
	
	
	/**
	 * Aggiorna un record
	 * @param string $table Il nome della tabella
	 * @param integer $idValue Il valore ID del record da aggiornare
	 * @param array $arrValues Array con coppie nomeCampo => valoreCampo. Es: ['nomeCampo' => 'valoreCampo', 'nomeCampo2' => 'valoreCampo2']
	 * @return array|NULL ['ack' => 'OK'|'KO', 'affectedRows' => 'nnnn', 'error' => 'xxxx']
	 */
	function update($table, $idValue, $arrValues)
	{
		if (!$this->ensureAuthentication()) {
			return array(
				'ack' => 'KO',
				'error' => $this->authError
			);
		}
		$data = array(
			'action' => 'UPDATE',
			'token' => $this->authToken,
			'table' => $table,
			'idvalue' => $idValue
		);
		$arr = [];
		foreach ($arrValues as $fieldName => $fieldValue) {
			$arr[] = $fieldName . '=' . urlencode($fieldValue);
		}
		$data['values'] = implode('&', $arr);
		
		return $this->sendToEndpoint($data);
	}
	
	
	/**
	 * Elimina un record
	 * @param string $table Il nome della tabella
	 * @param integer $idValue Il valore ID del record da aggiornare
	 * @return array|NULL ['ack' => 'OK'|'KO', 'affectedRows' => 'nnnn', 'error' => 'xxxx']
	 */
	function delete($table, $idValue)
	{
		if (!$this->ensureAuthentication()) {
			return array(
				'ack' => 'KO',
				'error' => $this->authError
			);
		}
		$data = array(
			'action' => 'DELETE',
			'token' => $this->authToken,
			'table' => $table,
			'idvalue' => $idValue
		);
		
		return $this->sendToEndpoint($data);
	}
	
	
	/**
	 * Assicura che si disponga del token di autenticazione
	 * Ritorna true se siamo autenticati, false se non siam riusciti ad autenticarci
	 * Come side-effect valorizza $this->authError in caso di errore e $this->authToken in caso di autenticazione avvenuta con successo
	 * @return boolean
	 */
	private function ensureAuthentication()
	{
		if ($this->isAuthenticated) {
			return true;
		} else {
			$data = array(
				'action' => 'AUTH',
				'instance' => $this->instance,
				'username' => $this->username,
				'password' => $this->password
			);
			$result = $this->sendToEndpoint($data);
			if ($result['ack'] == 'OK') {
				$this->isAuthenticated = true;
				$this->authToken = $result['token'];
				return true;
			} else {
				$this->authError = $result['error'];
				return false;
			}
		}
	}
	
	
	/**
	 * Invia i dati all'endpoint del servizio API Picus Cloud
	 * @param array $data
	 * @return array|NULL
	 */
	private function sendToEndpoint($data)
	{
		$ch = curl_init($this->endpoint);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
		$res = json_decode($result, true);
		if ($res === NULL) {
			return array(
				'ack' => 'KO',
				'error' => 'Errore output dal server: ' . $result
			);
		} else {
			return $res;
		}
	}
	
	
	private $endpoint = 'https://picus.cloud/web-api/';
	private $instance;
	private $username;
	private $password;
	
	private $isAuthenticated = false;
	private $authToken = '';
	private $authError = '';
	
}