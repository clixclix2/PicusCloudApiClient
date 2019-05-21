<?php

// PicusCloudApiCliente - esempi

$instance = '......'; // Instance, username e password forniti dal servizio
$username = '......';
$password = '......';

require_once __DIR__ . '/../PicusCloudApiClient.class.php';


//
// Estrazione anagrafica clienti
//
$pcac = new PicusCloudApiClient($instance, $username, $password);

$res = $pcac->select('clienti_fornitori', 'cliente', '1');

if ($res['ack'] == 'OK') {

	$arrClienti = $res['data'];
	
	foreach ($arrClienti as $lineCliente) {
		var_dump($lineCliente);
	}
	
} else {
	die('Errore: ' . $res['error']);
}


//
// Inserimento nuovo cliente
//
$res = $pcac->insert('clienti_fornitori', [
	'ragione_sociale' => 'Azienda test S.r.l.',
	'cliente' => 1,
	'indirizzo' => 'Via di qua, 1',
	'cap' => '00100',
	'prov' => 'RM',
	'piva' => '12345678901',
	'cfis' => '12345678901'
]);

if ($res['ack'] == 'OK') {
	$insertId = $res['insertId'];
} else {
	die('Errore: ' . $res['error']);
}


//
// Aggiornamento cliente
//
$res = $pcac->update('clienti_fornitori', $insertId, [
	'www' => 'www.azienda.it',
	'email' => 'info@email.it'
]);

if ($res['ack'] == 'OK') {
	$affectedRows = $res['affectedRows'];
} else {
	die('Errore: ' . $res['error']);
}


//
// Eliminazione cliente
//
$res = $pcac->delete('clienti_fornitori', $insertId);

if ($res['ack'] == 'OK') {
	$affectedRows = $res['affectedRows'];
} else {
	die('Errore: ' . $res['error']);
}

?>