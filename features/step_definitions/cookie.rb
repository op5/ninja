require_relative '../support/wait_for_ajax'

Then(/^I should have a tasty cookie$/) do
	WaitForAjax.wait_for_ajax()
	# page.driver.cookies looks like this: {"PHPSESSID"=>#<Capybara::Poltergeist::Cookie:0x000000039668a8 @attributes={"domain"=>"localhost", "httponly"=>true, "name"=>"PHPSESSID", "path"=>"/", "secure"=>true, "value"=>"ke0haa7p9dc4vfaubd123tubg2"}>}
	page.driver.cookies.each {
		| (cookie_name, cookie) |
		expect(cookie.secure?).to be == true
		expect(cookie.httponly?).to be == true
	}
end

Then(/^I check for cookie bar$/) do
	steps %Q{
		And I should see "OP5 Monitor uses cookies"
		And I click the got it button
	}
end

And(/^I click the got it button$/) do
  WaitForAjax.wait_for_ajax
  page.find("a", :text => "Got it").click
  WaitForAjax.wait_for_ajax
end
