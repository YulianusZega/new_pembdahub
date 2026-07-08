<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Yayasan\InvitationController;
use Illuminate\Http\Request;

echo "--- START VERIFICATION OF INVITATIONS LOGIC ---\n";
try {
    $controller = app(InvitationController::class);
    
    // Test index action with empty request (all employees)
    $request = Request::create('/yayasan/invitations', 'GET');
    $response = $controller->index($request);
    
    $data = $response->getData();
    $list = $data['invitationsList'];
    
    echo "✓ Resolution: InvitationController resolved successfully.\n";
    echo "✓ Database Query: Retreived active employees list.\n";
    echo "✓ Total processed invitations: " . count($list) . "\n";
    
    $rolesCount = array_count_values(array_column($list, 'role_type'));
    echo "\n=== ROLE CLASSIFICATIONS ===\n";
    foreach ($rolesCount as $role => $count) {
        echo "- Role: {$role} | Count: {$count}\n";
    }
    
    // Output a sample preview for each role type
    $sampledRoles = [];
    echo "\n=== SAMPLE PREVIEWS ===\n";
    foreach ($list as $inv) {
        if (!in_array($inv['role_type'], $sampledRoles)) {
            $sampledRoles[] = $inv['role_type'];
            echo "\n--- Sample for role: {$inv['role_type']} ({$inv['name']}) ---\n";
            echo $inv['message'] . "\n";
        }
    }
    
    echo "\n[+] SUCCESS: Verification completed successfully. No database errors or templating exceptions occurred.\n";
} catch (\Exception $e) {
    echo "\n[x] ERROR: Verification failed with exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
