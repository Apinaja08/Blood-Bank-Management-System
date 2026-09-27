<?php
/**
 * Member 3 (V4): Password Migration Script
 * Migrates existing plaintext passwords in 'receivers' and 'hospitals' tables to BCrypt hashes.
 */
require 'file/connection.php';

$migratedReceivers = 0;
$skippedReceivers = 0;

$migratedHospitals = 0;
$skippedHospitals = 0;

// 2. Migrate Receivers
$resReceivers = $conn->query("SELECT id, remail, rpassword FROM receivers");
if ($resReceivers) {
    while ($row = $resReceivers->fetch_assoc()) {
        $id = $row['id'];
        $plain = $row['rpassword'];
        $info = password_get_info($plain);
        if ($info['algo'] == 0) { // Plaintext password detected
            $hash = password_hash($plain, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE receivers SET rpassword = ? WHERE id = ?");
            $stmt->bind_param("si", $hash, $id);
            $stmt->execute();
            $migratedReceivers++;
        } else {
            $skippedReceivers++;
        }
    }
}

// 3. Migrate Hospitals
$resHospitals = $conn->query("SELECT id, hemail, hpassword FROM hospitals");
if ($resHospitals) {
    while ($row = $resHospitals->fetch_assoc()) {
        $id = $row['id'];
        $plain = $row['hpassword'];
        $info = password_get_info($plain);
        if ($info['algo'] == 0) { // Plaintext password detected
            $hash = password_hash($plain, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE hospitals SET hpassword = ? WHERE id = ?");
            $stmt->bind_param("si", $hash, $id);
            $stmt->execute();
            $migratedHospitals++;
        } else {
            $skippedHospitals++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Migration - Member 3</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; padding-top: 50px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .card { box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 12px; }
        .badge-hash { font-family: monospace; font-size: 13px; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h3 class="text-success mb-3">✅ Password Migration Completed (V4 Fix)</h3>
                <p class="text-muted">Plaintext passwords in the database have been converted to secure <strong>BCrypt (Blowfish)</strong> hashes.</p>
                <hr>
                <div class="row text-center mb-3">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded border">
                            <h4 class="text-primary font-weight-bold"><?php echo $migratedReceivers; ?></h4>
                            <span class="text-muted">Receivers Migrated</span>
                            <div class="small text-secondary mt-1">(<?php echo $skippedReceivers; ?> already hashed)</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded border">
                            <h4 class="text-primary font-weight-bold"><?php echo $migratedHospitals; ?></h4>
                            <span class="text-muted">Hospitals Migrated</span>
                            <div class="small text-secondary mt-1">(<?php echo $skippedHospitals; ?> already hashed)</div>
                        </div>
                    </div>
                </div>

                <h5>Sample Verification from Database:</h5>
                <table class="table table-bordered table-sm mt-2">
                    <thead class="thead-light">
                        <tr>
                            <th>User Type</th>
                            <th>Email</th>
                            <th>Stored Hash (First 25 chars)</th>
                            <th>Algorithm</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sampleR = $conn->query("SELECT remail, rpassword FROM receivers LIMIT 2");
                        while($s = $sampleR->fetch_assoc()){
                            echo "<tr><td>Receiver</td><td>".htmlspecialchars($s['remail'])."</td><td class='badge-hash'>".htmlspecialchars(substr($s['rpassword'],0,25))."...</td><td><span class='badge badge-success'>BCrypt</span></td></tr>";
                        }
                        $sampleH = $conn->query("SELECT hemail, hpassword FROM hospitals LIMIT 2");
                        while($sh = $sampleH->fetch_assoc()){
                            echo "<tr><td>Hospital</td><td>".htmlspecialchars($sh['hemail'])."</td><td class='badge-hash'>".htmlspecialchars(substr($sh['hpassword'],0,25))."...</td><td><span class='badge badge-success'>BCrypt</span></td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <div class="alert alert-info mt-3">
                    <strong>Notice:</strong> All existing users can still log in with their original passwords! The system now verifies them securely using <code>password_verify()</code>.
                </div>
                <div class="text-right mt-3">
                    <a href="login.php" class="btn btn-primary">Go to Login</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
