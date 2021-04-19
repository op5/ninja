module WaitForAjax
  extend self
  
    #Helper Functions
  def wait_for_ajax
    require "timeout"
    Timeout.timeout(Capybara.default_max_wait_time) do
      loop until finished_all_ajax_requests?
    end
  end
  
  def finished_all_ajax_requests?
    Capybara.page.evaluate_script('jQuery.active').zero?
  end

  end
  