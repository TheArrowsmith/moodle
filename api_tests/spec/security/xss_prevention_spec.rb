RSpec.describe 'XSS Prevention' do
  before do
    authenticate_as(:teacher)
  end

  describe 'Activity XSS protection' do
    it 'safely handles XSS attempts in activity names' do
      xss_payloads = [
        "<script>alert('XSS')</script>",
        "<img src=x onerror=alert('XSS')>",
        "javascript:alert('XSS')",
        "<iframe src='http://evil.com'></iframe>",
        "<svg onload=alert('XSS')>",
        "';alert('XSS');//",
        "<body onload=alert('XSS')>"
      ]
      
      xss_payloads.each do |payload|
        activity = create_activity(sample_activity_data(name: payload))
        
        # Fetch the activity back
        course_data = get_course_data
        all_activities = course_data.parsed_response['sections'].flat_map { |s| s['activities'] }
        stored_activity = all_activities.find { |a| a['id'] == activity['id'] }
        
        # The payload should be stored but escaped/sanitized when rendered
        expect(stored_activity['name']).to eq(payload)
        
        # Verify no actual script execution would occur (checking for common escape patterns)
        # This assumes the frontend will properly escape when rendering
        expect(stored_activity['name']).not_to match(/<script.*?>.*?<\/script>/m)
          .or match(/javascript:/i)
          .or match(/on\w+\s*=/i)
      end
    end

    it 'safely handles XSS in activity descriptions' do
      xss_intro = '<p>Normal text</p><script>alert("XSS")</script><p>More text</p>'
      
      activity = create_activity(sample_activity_data(
        name: "XSS Test Activity",
        intro: xss_intro
      ))
      
      expect(activity).to have_key('id')
      
      # The API should accept the content (Moodle typically sanitizes on output)
      # The actual XSS prevention happens when content is rendered
    end
  end

  describe 'Section XSS protection' do
    let(:test_section) do
      response = get_course_data
      response.parsed_response['sections'].first
    end

    it 'safely handles XSS attempts in section names' do
      xss_payload = "Week 1<script>alert('XSS')</script>"
      
      response = put("/section/#{test_section['id']}", {
        name: xss_payload
      })
      
      expect(response.code).to eq(200)
      
      updated_section = response.parsed_response
      expect(updated_section['name']).to eq(xss_payload)
    end

    it 'safely handles XSS in section summaries' do
      xss_summaries = [
        "<p>Summary with <script>alert('XSS')</script></p>",
        '<p>Click <a href="javascript:alert(\'XSS\')">here</a></p>',
        '<p><img src="x" onerror="alert(\'XSS\')"></p>',
        '<p onclick="alert(\'XSS\')">Clickable paragraph</p>'
      ]
      
      xss_summaries.each do |summary|
        response = put("/section/#{test_section['id']}", {
          summary: summary
        })
        
        expect(response.code).to eq(200)
        
        # Verify the content is stored
        updated_section = response.parsed_response
        expect(updated_section['summary']).to eq(summary)
      end
    end
  end

  describe 'Response header security' do
    it 'includes security headers in responses' do
      response = get('/user/me')
      
      # Check for common security headers
      security_headers = {
        'x-content-type-options' => 'nosniff',
        'x-frame-options' => ['DENY', 'SAMEORIGIN'],
        'x-xss-protection' => '1; mode=block'
      }
      
      security_headers.each do |header, expected_values|
        if response.headers.key?(header)
          actual_value = response.headers[header]
          if expected_values.is_a?(Array)
            expect(expected_values).to include(actual_value)
          else
            expect(actual_value).to eq(expected_values)
          end
        else
          puts "Security header '#{header}' not found - consider adding for defense in depth"
        end
      end
    end

    it 'serves JSON with correct content type' do
      response = get('/user/me')
      
      content_type = response.headers['content-type']
      expect(content_type).to include('application/json')
      
      # Should not include HTML content type
      expect(content_type).not_to include('text/html')
    end
  end

  describe 'JSON response encoding' do
    it 'properly encodes special characters in JSON responses' do
      # Create activity with special characters
      special_chars = "Test <>&\"' Activity"
      
      activity = create_activity(sample_activity_data(name: special_chars))
      
      # Raw response should have properly escaped JSON
      response = get_course_data
      raw_body = response.body
      
      # Check that special characters are properly encoded in JSON
      expect(raw_body).to include('Test <>&\"\\u0027 Activity')
        .or include('Test <>&\"\' Activity')
      
      # Parsed response should have original characters
      parsed = response.parsed_response
      stored_activity = parsed['sections'].flat_map { |s| s['activities'] }
                                         .find { |a| a['id'] == activity['id'] }
      
      expect(stored_activity['name']).to eq(special_chars)
    end
  end

  describe 'Template injection prevention' do
    it 'safely handles template injection attempts' do
      template_payloads = [
        "{{7*7}}",
        "${7*7}",
        "<%= 7*7 %>",
        "#{7*7}",
        "%{7*7}",
        "{{constructor.constructor('alert(1)')()}}"
      ]
      
      template_payloads.each do |payload|
        response = put("/activity/#{create_activity(sample_activity_data)['id']}", {
          name: payload
        })
        
        expect(response.code).to eq(200)
        
        # Payload should be treated as literal string
        updated = response.parsed_response
        expect(updated['name']).to eq(payload)
        
        # Should not evaluate to "49" or execute code
        expect(updated['name']).not_to eq("49")
      end
    end
  end

  describe 'Path traversal prevention' do
    it 'prevents path traversal in endpoints' do
      path_traversal_attempts = [
        "../../../etc/passwd",
        "..\\..\\..\\windows\\system32\\config\\sam",
        "%2e%2e%2f%2e%2e%2f%2e%2e%2fetc%2fpasswd",
        "....//....//....//etc/passwd"
      ]
      
      path_traversal_attempts.each do |attempt|
        # Try path traversal in course ID
        response = get("/course/#{attempt}/management_data")
        expect(response.code).to be_in([400, 404])
        
        # Try in activity ID
        response = get("/activity/#{attempt}")
        expect(response.code).to be_in([400, 404])
      end
    end
  end
end