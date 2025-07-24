module TestData
  def sample_activity_data(overrides = {})
    {
      courseid: ENV['TEST_COURSE_ID'].to_i,
      sectionid: 1,
      modname: 'assign',
      name: "Test Assignment #{Faker::Number.number(digits: 4)}",
      intro: Faker::Lorem.paragraph,
      visible: true
    }.merge(overrides)
  end

  def sample_section_data(overrides = {})
    {
      name: "Test Section #{Faker::Number.number(digits: 4)}",
      visible: true,
      summary: "<p>#{Faker::Lorem.paragraph}</p>"
    }.merge(overrides)
  end

  def malicious_strings
    [
      "'; DROP TABLE mdl_user; --",
      "<script>alert('XSS')</script>",
      "{{7*7}}",
      "${7*7}",
      "<img src=x onerror=alert('XSS')>",
      "javascript:alert('XSS')",
      "' OR '1'='1",
      "../../../etc/passwd",
      "%00",
      "\x00",
      "<iframe src='http://evil.com'></iframe>"
    ]
  end

  def invalid_json_strings
    [
      '{invalid json}',
      '{"unclosed": "string',
      '{"trailing": "comma",}',
      'null',
      '',
      '[]',
      'true',
      '{"nested": {"missing": }}'
    ]
  end

  def activity_types
    %w[assign quiz forum resource page url label]
  end

  def find_test_section_with_activities(min_activities = 3)
    response = get_course_data
    
    if response.success?
      sections = response.parsed_response['sections']
      suitable_section = sections.find { |s| s['activities'].size >= min_activities }
      
      if suitable_section.nil?
        # Create activities if needed
        first_section = sections.first
        activities_needed = min_activities - first_section['activities'].size
        
        activities_needed.times do
          create_activity(sample_activity_data(sectionid: first_section['id']))
        end
        
        # Refresh and return
        response = get_course_data
        sections = response.parsed_response['sections']
        sections.first
      else
        suitable_section
      end
    else
      raise "Failed to get course data: #{response.code}"
    end
  end

  def wait_for_propagation(seconds = 1)
    sleep seconds
  end
end