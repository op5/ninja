When /^I have these mocked (.*)$/ do |type, table|
  if @mock == nil
    @mock = Mock::Mock.new
  end
  @mock.mock(type, table.hashes)
  page.driver.headers = {'X-op5-mock' => @mock.filename}
end

Then /^I should see the mocked (.*)$/ do | type |
  @mock.data(type).all? { |obj|
    if type == 'services'
      expected_content = obj['description']
    else
      expected_content = obj['name']
    end

    page.should have_content(expected_content)
  }
end

After do |scenario|
  case scenario
  when Cucumber::Ast::Scenario
    name = scenario.name
  when Cucumber::Ast::OutlineTable::ExampleRow
    name = scenario.scenario_outline.name
  end

  if @mock != nil
    if scenario.failed?
      puts "Scenario '#{name}' failed, mock data stored in #{@mock.filename}"
    else
      @mock.delete()
    end
  end
end
