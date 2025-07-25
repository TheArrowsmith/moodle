<?php
require_once('../config.php');
require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/course/test_ajax.php');
$PAGE->set_pagelayout('admin');
$PAGE->set_title('Test AJAX Endpoints');

echo $OUTPUT->header();
?>

<h1>Test AJAX Endpoints</h1>

<div style="margin: 20px;">
    <h3>Test 1: Direct AJAX Call to Categories</h3>
    <button id="test-categories">Test Categories Endpoint</button>
    <pre id="categories-result" style="background: #f0f0f0; padding: 10px; margin: 10px 0;"></pre>
</div>

<div style="margin: 20px;">
    <h3>Test 2: Direct AJAX Call to Courses</h3>
    <button id="test-courses">Test Courses Endpoint</button>
    <pre id="courses-result" style="background: #f0f0f0; padding: 10px; margin: 10px 0;"></pre>
</div>

<script>
document.getElementById('test-categories').addEventListener('click', async function() {
    const resultDiv = document.getElementById('categories-result');
    resultDiv.textContent = 'Loading...';
    
    try {
        const response = await fetch('<?php echo $CFG->wwwroot; ?>/course/ajax/categories.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'list',
                parentid: 0,
                sesskey: '<?php echo sesskey(); ?>'
            })
        });
        
        const text = await response.text();
        console.log('Response status:', response.status);
        console.log('Response text:', text);
        
        try {
            const data = JSON.parse(text);
            resultDiv.textContent = JSON.stringify(data, null, 2);
        } catch (e) {
            resultDiv.textContent = 'Response: ' + text;
        }
    } catch (error) {
        resultDiv.textContent = 'Error: ' + error.message;
        console.error('Fetch error:', error);
    }
});

document.getElementById('test-courses').addEventListener('click', async function() {
    const resultDiv = document.getElementById('courses-result');
    resultDiv.textContent = 'Loading...';
    
    try {
        const response = await fetch('<?php echo $CFG->wwwroot; ?>/course/ajax/courses.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'list',
                categoryid: 0,
                page: 0,
                perpage: 10,
                sesskey: '<?php echo sesskey(); ?>'
            })
        });
        
        const text = await response.text();
        console.log('Response status:', response.status);
        console.log('Response text:', text);
        
        try {
            const data = JSON.parse(text);
            resultDiv.textContent = JSON.stringify(data, null, 2);
        } catch (e) {
            resultDiv.textContent = 'Response: ' + text;
        }
    } catch (error) {
        resultDiv.textContent = 'Error: ' + error.message;
        console.error('Fetch error:', error);
    }
});
</script>

<?php
echo $OUTPUT->footer();
?>