RSpec.describe 'SQL Injection Protection' do
  before do
    authenticate_as(:teacher)
  end

  describe 'Activity endpoints' do
    let(:test_activity) do
      create_activity(sample_activity_data(name: "SQL Test Activity"))
    end

    it 'safely handles SQL injection in activity names' do
      malicious_strings.select { |s| s.include?('DROP') || s.include?("'") }.each do |sql_injection|
        response = put("/activity/#{test_activity['id']}", {
          name: sql_injection
        })
        
        # Should either succeed (sanitized) or fail gracefully
        expect(response.code).to be_in([200, 400, 422])
        
        if response.code == 200
          # If successful, verify the string was stored safely
          updated = response.parsed_response
          expect(updated['name']).to eq(sql_injection)
          
          # Verify database is still intact by making another request
          verify_response = get('/user/me')
          expect(verify_response.code).to eq(200)
        end
      end
    end

    it 'handles SQL injection attempts in activity creation' do
      sql_injection = "'; DROP TABLE mdl_course_modules; --"
      
      response = post('/activity', sample_activity_data(
        name: sql_injection,
        intro: sql_injection
      ))
      
      # Should handle the request safely
      expect(response.code).to be_in([200, 400, 422])
      
      # Database should still be functional
      verify_response = get_course_data
      expect(verify_response.code).to eq(200)
      
      # Cleanup if created
      if response.code == 200
        @created_resources << { type: :activity, id: response.parsed_response['id'] }
      end
    end
  end

  describe 'Section endpoints' do
    let(:test_section) do
      response = get_course_data
      response.parsed_response['sections'].first
    end

    it 'safely handles SQL injection in section names' do
      sql_injection = "Section'; UPDATE mdl_course_sections SET visible=0; --"
      
      response = put("/section/#{test_section['id']}", {
        name: sql_injection
      })
      
      expect(response.code).to be_in([200, 400, 422])
      
      # Verify other sections weren't affected
      course_data = get_course_data
      sections = course_data.parsed_response['sections']
      
      # At least some sections should still be visible
      visible_sections = sections.select { |s| s['visible'] == true }
      expect(visible_sections).not_to be_empty
    end

    it 'safely handles SQL injection in section summaries' do
      sql_injection = "<p>Summary' OR '1'='1</p>"
      
      response = put("/section/#{test_section['id']}", {
        summary: sql_injection
      })
      
      expect(response.code).to be_in([200, 400, 422])
      
      if response.code == 200
        # Verify the string was stored as-is (properly escaped)
        expect(response.parsed_response['summary']).to eq(sql_injection)
      end
    end
  end

  describe 'Authentication endpoint' do
    it 'safely handles SQL injection in login attempts' do
      sql_injections = [
        "admin' OR '1'='1",
        "admin'; DROP TABLE mdl_user; --",
        "' OR '1'='1' --",
        "admin'--",
        "admin' /*"
      ]
      
      sql_injections.each do |injection|
        response = post('/auth/token', {
          username: injection,
          password: 'password'
        })
        
        # Should fail authentication, not execute SQL
        expect(response.code).to eq(401)
        expect(response.parsed_response).to have_key('error')
      end
      
      # Verify normal login still works
      normal_response = post('/auth/token', {
        username: ENV['TEST_TEACHER_USERNAME'],
        password: ENV['TEST_TEACHER_PASSWORD']
      })
      
      expect(normal_response.code).to eq(200)
    end
  end

  describe 'Search and filter operations' do
    it 'safely handles SQL injection in numeric parameters' do
      # Try SQL injection in course ID
      response = get("/course/1 OR 1=1/management_data")
      expect(response.code).to be_in([400, 404])
      
      # Try SQL injection in activity ID
      response = put("/activity/1; DELETE FROM mdl_course_modules", {
        name: "Test"
      })
      expect(response.code).to be_in([400, 404])
    end

    it 'safely handles SQL injection in array parameters' do
      section = find_test_section_with_activities(2)
      
      # Try SQL injection in activity IDs array
      response = post("/section/#{section['id']}/reorder_activities", {
        activity_ids: ["1", "2 OR 1=1", "3; DROP TABLE mdl_course_modules"]
      })
      
      # Should fail validation
      expect(response.code).to be_in([400, 422])
    end
  end

  describe 'Database integrity verification' do
    it 'maintains database integrity after injection attempts' do
      # Attempt various SQL injections
      malicious_strings.select { |s| s.include?('DROP') || s.include?('DELETE') }.each do |injection|
        post('/activity', sample_activity_data(name: injection)) rescue nil
        put('/section/1', { name: injection }) rescue nil
      end
      
      # Verify core functionality still works
      expect { authenticate_as(:teacher) }.not_to raise_error
      expect { get_course_data }.not_to raise_error
      expect { get('/user/me') }.not_to raise_error
      
      response = get_course_data
      expect(response.code).to eq(200)
      expect(response.parsed_response).to have_key('sections')
    end
  end
end