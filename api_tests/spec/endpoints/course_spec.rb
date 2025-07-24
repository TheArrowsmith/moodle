RSpec.describe 'Course Management API Endpoints' do
  describe 'GET /course/{courseId}/management_data' do
    before do
      authenticate_as(:teacher)
    end

    context 'with valid course access' do
      it 'returns complete course structure' do
        response = get_course_data
        
        expect(response.code).to eq(200)
        
        data = response.parsed_response
        expect(data).to have_key('course_name')
        expect(data).to have_key('sections')
        expect(data['sections']).to be_an(Array)
        expect(data['sections']).not_to be_empty
      end

      it 'returns properly structured section data' do
        response = get_course_data
        sections = response.parsed_response['sections']
        
        sections.each do |section|
          expect(section).to have_key('id')
          expect(section).to have_key('name')
          expect(section).to have_key('visible')
          expect(section).to have_key('summary')
          expect(section).to have_key('activities')
          
          expect(section['id']).to be_a(Integer)
          expect(section['visible']).to be_in([true, false])
          expect(section['activities']).to be_an(Array)
        end
      end

      it 'returns properly structured activity data' do
        response = get_course_data
        sections = response.parsed_response['sections']
        
        # Find a section with activities
        section_with_activities = sections.find { |s| s['activities'].any? }
        skip 'No sections with activities found' unless section_with_activities
        
        section_with_activities['activities'].each do |activity|
          expect(activity).to have_key('id')
          expect(activity).to have_key('name')
          expect(activity).to have_key('modname')
          expect(activity).to have_key('modicon')
          expect(activity).to have_key('visible')
          
          expect(activity['id']).to be_a(Integer)
          expect(activity['name']).to be_a(String)
          expect(activity['modname']).to be_a(String)
          expect(activity['visible']).to be_in([true, false])
        end
      end

      it 'includes all sections in the course' do
        response = get_course_data
        sections = response.parsed_response['sections']
        
        # Most Moodle courses have at least 1 section
        expect(sections.size).to be >= 1
        
        # Sections should be ordered by their position
        section_ids = sections.map { |s| s['id'] }
        expect(section_ids).to eq(section_ids.sort)
      end
    end

    context 'with different user roles' do
      it 'allows teacher access' do
        authenticate_as(:teacher)
        response = get_course_data
        
        expect(response.code).to eq(200)
      end

      it 'allows admin access' do
        authenticate_as(:admin)
        response = get_course_data
        
        expect(response.code).to eq(200)
      end

      it 'denies student access to management data' do
        authenticate_as(:student)
        response = get_course_data
        
        expect(response.code).to eq(403)
        expect(response.parsed_response).to have_key('error')
      end
    end

    context 'error handling' do
      it 'returns 404 for non-existent course' do
        response = get('/course/99999/management_data')
        
        expect(response.code).to eq(404)
        expect(response.parsed_response).to have_key('error')
      end

      it 'returns 403 for course without permissions' do
        skip 'Requires a course where teacher has no access'
        
        # This would test accessing a course where the authenticated
        # user doesn't have update permissions
      end

      it 'returns 401 without authentication' do
        clear_token
        response = get_course_data
        
        expect(response.code).to eq(401)
        expect(response.parsed_response).to have_key('error')
        expect(response.parsed_response['error']).to include('Authentication token is missing')
      end
    end

    context 'performance' do
      it 'returns data within acceptable time for large courses' do
        start_time = Time.now
        response = get_course_data
        end_time = Time.now
        
        expect(response.code).to eq(200)
        expect(end_time - start_time).to be < 2.0 # Should complete in under 2 seconds
      end
    end
  end
end