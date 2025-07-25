<?php
require_once('../config.php');
require_once($CFG->dirroot . '/local/courseapi/classes/jwt.php');

require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/course/test_api_direct.php');
$PAGE->set_pagelayout('embedded');
$PAGE->set_title('Direct API Test');

// Generate JWT token
$token = local_courseapi\jwt::create_token($USER->id);

echo $OUTPUT->header();
?>

<h1>Direct Course API Test</h1>

<div style="margin: 20px; padding: 20px; background: #f5f5f5;">
    <h3>Test Configuration</h3>
    <p><strong>API URL:</strong> <?php echo $CFG->wwwroot; ?>/local/courseapi/api/index.php/course/2/management_data</p>
    <p><strong>JWT Token:</strong> <code style="word-break: break-all;"><?php echo $token; ?></code></p>
    <p><strong>User:</strong> <?php echo fullname($USER); ?> (ID: <?php echo $USER->id; ?>)</p>
</div>

<button id="test-api" style="padding: 10px 20px; font-size: 16px;">Test API Call</button>

<div id="result" style="margin: 20px; padding: 20px; background: #f0f0f0; white-space: pre-wrap; font-family: monospace;"></div>

<script>
document.getElementById('test-api').addEventListener('click', async function() {
    const resultDiv = document.getElementById('result');
    resultDiv.textContent = 'Loading...';
    
    const url = '<?php echo $CFG->wwwroot; ?>/local/courseapi/api/index.php/course/2/management_data';
    const token = '<?php echo $token; ?>';
    
    console.log('Testing API call to:', url);
    
    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json'
            }
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        const text = await response.text();
        console.log('Response text:', text);
        
        if (response.ok) {
            try {
                const data = JSON.parse(text);
                resultDiv.textContent = 'Success!\n\n' + JSON.stringify(data, null, 2);
            } catch (e) {
                resultDiv.textContent = 'Response (not JSON):\n' + text;
            }
        } else {
            resultDiv.textContent = 'Error ' + response.status + ':\n' + text;
        }
    } catch (error) {
        console.error('Fetch error:', error);
        resultDiv.textContent = 'Network error: ' + error.message;
    }
});
</script>

<?php
echo $OUTPUT->footer();
?>